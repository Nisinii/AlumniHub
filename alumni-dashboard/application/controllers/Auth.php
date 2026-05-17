<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Auth Controller (CW2: University Dashboard)
 * Handles staff authentication, registration, and secure sessions.
 */
class Auth extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Staff_model');
        $this->load->library(['session', 'email']);
        $this->load->helper(['url', 'security']);
    }

    public function login() {
        if ($this->input->method() === 'get') return $this->load->view('auth/login');

        if (!check_rate_limit('login', 10, 900)) {
            return $this->render(429, 'Too many attempts. Locked for 15 mins.', 'auth/login');
        }

        $input = $this->_get_input();
        
        $result = $this->Staff_model->attempt_login($input['email'] ?? '', $input['password'] ?? '');

        if (isset($result['error'])) {
            return $this->render(401, 'Invalid credentials or unverified account.', 'auth/login');
        }

        $this->session->sess_regenerate(TRUE);
        
        // Store staff identity
        $this->session->set_userdata([
            'staff_id'      => $result['id'],
            'staff_email'   => $result['email'],
            'staff_name'    => $result['first_name'] . ' ' . $result['last_name'],
            'department'    => $result['department'],
            'last_activity' => time()
        ]);

        // Redirect directly to the analytics dashboard
        return $this->render(200, 'Login successful', '', [], 'dashboard');
    }

    public function register() {
        if ($this->input->method() === 'get') return $this->load->view('auth/register');

        $post = $this->_get_input();
        
        // Use Staff_model for validation
        $errors = $this->Staff_model->validate_registration($post);

        if (!empty($errors)) {
            return $this->render(422, 'Validation failed', 'auth/register', ['errors' => $errors, 'old' => $post]);
        }

        $result = $this->Staff_model->create_staff_user($post);
        if (!$result) return $this->render(500, 'Server error. Try again.', 'auth/register');

        $this->_send_verification_email($post['email'], $result['token']);

        return $this->render(201, 'Registration successful. Please verify your email.', 'auth/login', [
            'dev_verify_url' => site_url('auth/verify/' . $result['token'])
        ]);
    }

    public function verify($token = NULL) {
        if (empty($token)) {
            return $this->render(400, 'Verification token is required.', 'auth/login');
        }

        $result = $this->Staff_model->verify_email($token);
        
        if ($result === 'ok') {
            return $this->render(200, 'Email verified! Please log in.', 'auth/login');
        } else {
            $msg = ($result === 'already_verified') ? 'Email is already verified.' : 'Invalid or expired link.';
            return $this->render(400, $msg, 'auth/login');
        }
    }

    public function resend_verification() {
        if (!check_rate_limit('resend', 3, 900)) {
            return $this->render(429, 'Too many requests. Wait 15 mins.', 'auth/login');
        }

        $email = trim($this->_get_input()['email'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->render(422, 'Invalid email.', 'auth/login');
        }

        $token = $this->Staff_model->resend_verification_token($email);
        if ($token) $this->_send_verification_email($email, $token);

        return $this->render(200, 'If unverified, a new link has been sent.', 'auth/login');
    }

    public function forgot_password() {
        if ($this->input->method() === 'get') return $this->load->view('auth/forgot_password');
        
        $email = $this->_get_input()['email'] ?? '';
        $token = $this->Staff_model->create_reset_token($email);
        $data = [];

        if ($token) {
            $this->_send_reset_email($email, $token);
            if (defined('ENVIRONMENT') && ENVIRONMENT !== 'production') {
                $data['dev_reset_url'] = base_url('auth/reset_password/' . $token);
            }
        }

        return $this->render(200, 'If email exists, a reset link was sent.', 'auth/forgot_password', $data);
    }

    public function reset_password($token = NULL) {
        $staff = $this->Staff_model->get_user_by_reset_token($token);
        
        if (!$staff) return $this->render(410, 'Link expired or invalid.', 'auth/forgot_password');
        if ($this->input->method() === 'get') return $this->load->view('auth/reset_password', ['token' => $token]);

        $input = $this->_get_input();
        $pw_errors = $this->Staff_model->validate_password_strength($input['password'] ?? '');
        
        if (!empty($pw_errors) || $input['password'] !== $input['confirm_password']) {
            return $this->render(422, 'Password mismatch or too weak.', 'auth/reset_password', ['token' => $token]);
        }

        $this->Staff_model->reset_password($staff->id, $input['password']);
        return $this->render(200, 'Password updated.', 'auth/login');
    }

    public function logout() {
        $this->session->sess_destroy();
        return $this->render(200, 'Logged out successfully.', 'auth/login', [], 'auth/login');
    }

    // ── PRIVATE HELPERS ───────────────────────────────────────

    private function _get_input() {
        $json = json_decode($this->input->raw_input_stream, TRUE);
        $data = (json_last_error() === JSON_ERROR_NONE) ? $json : $this->input->post(NULL, TRUE);
        return xss_clean($data);
    }

    private function _send_verification_email($to, $token) {
        $url = site_url('auth/verify/' . $token);
        // Branding to University Dashboard
        $body = $this->_get_email_template(
            "Verify Staff Account",
            "Welcome to the University Analytics Dashboard! Please verify your staff email address to access alumni intelligence.",
            $url,
            "Verify My Email"
        );
        return $this->_mail($to, 'Verify Your Email – Analytics Dashboard', $body);
    }

    private function _send_reset_email($to, $token) {
        $url = site_url('auth/reset_password/' . $token);
        $body = $this->_get_email_template(
            "Reset Dashboard Password",
            "We received a request to reset your Analytics Dashboard password. Click the button below to securely set a new one.",
            $url,
            "Reset Password"
        );
        return $this->_mail($to, 'Password Reset – Analytics Dashboard', $body);
    }

    private function _get_email_template($title, $message, $button_url, $button_text) {
        // Branding updated to reflect the new system
        return "
        <div style='font-family: \"Plus Jakarta Sans\", \"Segoe UI\", Arial, sans-serif; background-color: #f3f4f6; padding: 40px 20px; color: #1f2937;'>
            <table align='center' border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);'>
                <tr>
                    <td style='padding: 30px; background-color: #1e3a8a; text-align: center;'>
                        <h1 style='color: #ffffff; margin: 0; font-size: 24px; font-weight: bold;'>University Analytics Dashboard</h1>
                    </td>
                </tr>
                <tr>
                    <td style='padding: 40px;'>
                        <h2 style='color: #111827; margin-top: 0;'>{$title}</h2>
                        <p style='line-height: 1.6; color: #4b5563;'>{$message}</p>
                        <div style='text-align: center; margin: 30px 0;'>
                            <a href='{$button_url}' style='background-color: #2563eb; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;'>{$button_text}</a>
                        </div>
                        <p style='font-size: 12px; color: #6b7280;'>If the button doesn't work, use this link:<br><a href='{$button_url}'>{$button_url}</a></p>
                    </td>
                </tr>
            </table>
        </div>";
    }

    private function _mail($to, $subject, $body) {
        $from_email = getenv('SMTP_FROM_EMAIL');
        $from_name  = trim(getenv('SMTP_FROM_NAME'), '"\''); 

        $this->email->from($from_email, $from_name);
        $this->email->to($to); 
        $this->email->subject($subject); 
        $this->email->message($body);
        
        return $this->email->send();
    }
}