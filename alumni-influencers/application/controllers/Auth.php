<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Auth Controller
 * Handles user authentication, registration, password recovery, and email verification.
 * Architecture Note:
 * This controller is strictly "Client Agnostic". It uses a custom `_get_input()` 
 * method and the centralized `render()` method to seamlessly handle both standard 
 * browser requests (returning HTML views) and external API requests (returning JSON).
 */
class Auth extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library(['session', 'email']);
        $this->load->helper(['url', 'security']);
    }

    /**
     * GET/POST /auth/login
     * Authenticates a user and establishes a secure session.
     * Security Implementation:
     * 1. Rate Limiting: Prevents brute-force dictionary attacks.
     * 2. Session Regeneration: Mitigates Session Fixation vulnerabilities.
     * 3. Role-Based Routing: Directs developers to the API dashboard and alumni to bidding.
     */
    public function login() {
        // If it's just a browser requesting the page, show the form
        if ($this->input->method() === 'get') return $this->load->view('auth/login');

        // Security: Block IPs that fail login more than 10 times in 15 minutes
        if (!check_rate_limit('login', 10, 900)) {
            return $this->render(429, 'Too many attempts. Locked for 15 mins.', 'auth/login');
        }

        $input = $this->_get_input();
        
        // Model handles hashing and verification
        $result = $this->User_model->attempt_login($input['email'] ?? '', $input['password'] ?? '');

        if (isset($result['error'])) {
            return $this->render(401, 'Invalid credentials or unverified account.', 'auth/login');
        }

        // Security: Destroy the old session ID and create a new one to prevent Session Fixation
        $this->session->sess_regenerate(TRUE);
        
        // Store essential identity data in the session cookie
        $this->session->set_userdata([
            'user_id'       => $result['id'],
            'user_email'    => $result['email'],
            'user_name'     => $result['first_name'] . ' ' . $result['last_name'],
            'user_role'     => $result['role'],
            'last_activity' => time()
        ]);

        // Smart Routing: Redirect based on RBAC (Role-Based Access Control)
        if ($result['role'] === 'developer' || $result['role'] === 'admin') {
            return $this->render(200, 'Welcome back, Developer!', '', [], 'apikey');
        } else {
            return $this->render(200, 'Login successful', '', [], 'bidding');
        }
    }

    /**
     * GET/POST /auth/register
     * Registers a new Alumni account and triggers the verification flow.
     */
    public function register() {
        if ($this->input->method() === 'get') return $this->load->view('auth/register');

        $post = $this->_get_input();
        
        // Delegate complex validation rules to the model
        $errors = $this->User_model->validate_registration($post);

        if (!empty($errors)) {
            return $this->render(422, 'Validation failed', 'auth/register', ['errors' => $errors, 'old' => $post]);
        }

        // Insert user and generate a secure, random verification token
        $result = $this->User_model->create_user($post);
        if (!$result) return $this->render(500, 'Server error. Try again.', 'auth/register');

        // Dispatch background email
        $this->_send_verification_email($post['email'], $result['token']);

        // Return the dev URL in the JSON response to assist with rapid API testing
        return $this->render(201, 'Registration successful. Please verify your email.', 'auth/login', [
            'dev_verify_url' => site_url('auth/verify/' . $result['token'])
        ]);
    }

    /**
     * GET /auth/verify/{token}
     * Validates an email token and activates the account.
     */
    public function verify($token = NULL) {
        if (empty($token)) {
            return $this->render(400, 'Verification token is required.', 'auth/login');
        }

        $result = $this->User_model->verify_email($token);
        
        if ($result === 'ok') {
            return $this->render(200, 'Email verified! Please log in.', 'auth/login');
        } else {
            $msg = ($result === 'already_verified') ? 'Email is already verified.' : 'Invalid or expired link.';
            return $this->render(400, $msg, 'auth/login');
        }
    }

    /**
     * POST /auth/resend_verification
     * Generates a fresh token and resends the verification email.
     * Security: Rate limited to prevent SMTP spamming.
     */
    public function resend_verification() {
        // Prevent abuse: Max 3 requests per 15 minutes per IP
        if (!check_rate_limit('resend', 3, 900)) {
            return $this->render(429, 'Too many requests. Wait 15 mins.', 'auth/login');
        }

        $email = trim($this->_get_input()['email'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->render(422, 'Invalid email.', 'auth/login');
        }

        $token = $this->User_model->resend_verification_token($email);
        if ($token) $this->_send_verification_email($email, $token);

        // Security: Always return a generic success message to prevent Email Enumeration attacks
        return $this->render(200, 'If unverified, a new link has been sent.', 'auth/login');
    }

    /**
     * GET/POST /auth/forgot_password
     * Initiates the password recovery flow.
     */
    public function forgot_password() {
        if ($this->input->method() === 'get') return $this->load->view('auth/forgot_password');
        
        $email = $this->_get_input()['email'] ?? '';
        $token = $this->User_model->create_reset_token($email);

        $data = [];

        if ($token) {
            $this->_send_reset_email($email, $token);
            
            if (defined('ENVIRONMENT') && ENVIRONMENT !== 'production') {
                $data['dev_reset_url'] = base_url('auth/reset_password/' . $token);
            }
        }

        return $this->render(200, 'If email exists, a reset link was sent.', 'auth/forgot_password', $data);
    }

    /**
     * GET/POST /auth/reset_password/{token}
     * Validates the reset token and securely updates the user's password hash.
     */
    public function reset_password($token = NULL) {
        $user = $this->User_model->get_user_by_reset_token($token);
        
        // Tokens expire automatically (checked in the model)
        if (!$user) return $this->render(410, 'Link expired or invalid.', 'auth/forgot_password');

        if ($this->input->method() === 'get') return $this->load->view('auth/reset_password', ['token' => $token]);

        $input = $this->_get_input();
        
        // Validate password complexity
        $pw_errors = $this->User_model->validate_password_strength($input['password'] ?? '');
        
        if (!empty($pw_errors) || $input['password'] !== $input['confirm_password']) {
            return $this->render(422, 'Password mismatch or too weak.', 'auth/reset_password', ['token' => $token]);
        }

        $this->User_model->reset_password($user->id, $input['password']);
        return $this->render(200, 'Password updated.', 'auth/login');
    }

    /**
     * POST /auth/logout
     * Destroys the active session safely.
     */
    public function logout() {
        $this->session->sess_destroy();
        return $this->render(200, 'Logged out successfully.', 'auth/login', [], 'auth/login');
    }

    // ── PRIVATE HELPERS ───────────────────────────────────────

    /**
     * Client Agnostic Parsing Algorithm
     * Detects if the request is standard Form-Data (Browser) or raw JSON (Postman/AR Client)
     * and sanitizes the payload against XSS injections.
     */
    private function _get_input() {
        $json = json_decode($this->input->raw_input_stream, TRUE);
        $data = (json_last_error() === JSON_ERROR_NONE) ? $json : $this->input->post(NULL, TRUE);
        return xss_clean($data);
    }

    /**
     * Packages the verification token into the standardized email template
     */
    private function _send_verification_email($to, $token) {
        $url = site_url('auth/verify/' . $token);
        $body = $this->_get_email_template(
            "Verify Your Account",
            "Welcome to the AR Alumni Platform! Please click the button below to verify your email address and activate your account.",
            $url,
            "Verify My Email"
        );
        return $this->_mail($to, 'Verify Your Email – AR Alumni', $body);
    }

    /**
     * Packages the reset token into the standardized email template
     */
    private function _send_reset_email($to, $token) {
        $url = site_url('auth/reset_password/' . $token);
        $body = $this->_get_email_template(
            "Reset Your Password",
            "We received a request to reset your password. If you didn't make this request, you can safely ignore this email. Otherwise, click the button below to set a new password.",
            $url,
            "Reset Password"
        );
        return $this->_mail($to, 'Password Reset – AR Alumni', $body);
    }

    /**
     * Inline HTML Email Template Generator
     * Ensures all outgoing communications maintain brand consistency.
     */
    private function _get_email_template($title, $message, $button_url, $button_text) {
        return "
        <div style='font-family: \"Plus Jakarta Sans\", \"Segoe UI\", Arial, sans-serif; background-color: #EDEDCE; padding: 40px 20px; color: #1f2937;'>
            <table align='center' border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(12, 44, 85, 0.1);'>
                <tr>
                    <td style='padding: 40px 30px; background-color: #0C2C55; text-align: center;'>
                        <h1 style='color: #ffffff; margin: 0; font-size: 24px; font-weight: 800; letter-spacing: -0.02em;'>✦ AR Alumni Platform</h1>
                    </td>
                </tr>
                <tr>
                    <td style='padding: 45px 40px;'>
                        <h2 style='color: #0C2C55; margin-top: 0; font-size: 22px; font-weight: 800;'>{$title}</h2>
                        <p style='line-height: 1.7; font-size: 16px; color: #4b5563;'>{$message}</p>
                        
                        <div style='text-align: center; margin: 40px 0;'>
                            <a href='{$button_url}' style='background-color: #296374; color: #ffffff; padding: 16px 32px; text-decoration: none; border-radius: 50px; font-weight: 700; display: inline-block; font-size: 16px;'>{$button_text}</a>
                            </div>
                        
                        <div style='padding-top: 25px; border-top: 1px solid #e5e7eb;'>
                            <p style='font-size: 13px; color: #6b7280; line-height: 1.5; margin: 0;'>
                                If the button above doesn't work, copy and paste this link into your browser:<br>
                                <a href='{$button_url}' style='color: #629FAD; text-decoration: none;'>{$button_url}</a>
                            </p>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style='padding: 30px; background-color: #f9fafb; text-align: center; font-size: 12px; color: #9ca3af;'>
                        <p style='margin: 0 0 10px 0;'>Connecting excellence within our alumni community.</p>
                        &copy; " . date('Y') . " AR Alumni Platform. All rights reserved.
                    </td>
                </tr>
            </table>
        </div>";
    }

    /**
     * SMTP Email Wrapper
     * Abstracted function to handle the actual sending of emails.
     * Uses environment variables to keep credentials secure and out of version control.
     */
    private function _mail($to, $subject, $body) {
        // Fetch sender details directly from the environment variables loaded by index.php
        $from_email = getenv('SMTP_FROM_EMAIL');
        $from_name  = trim(getenv('SMTP_FROM_NAME'), '"\''); 

        $this->email->from($from_email, $from_name);
        $this->email->to($to); 
        $this->email->subject($subject); 
        $this->email->message($body);
        
        return $this->email->send();
    }
}