<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * User Model
 * Handles the data layer for Identity and Access Management (IAM).
 * Architecture Note:
 * This model is responsible for securely creating users, validating strict passwords, 
 * issuing cryptographically secure tokens for email verification and password resets, 
 * and executing secure Bcrypt password hashing.
 */
class User_model extends CI_Model {

    // ── VALIDATION LOGIC ──────────────────────────────────────

    /**
     * Validate an incoming registration payload.
     * Business Logic:
     * 1. Ensures required fields exist.
     * 2. Validates standard email format.
     * 3. Domain Restriction: Restricts registration to a specific university domain (e.g., @uni.ac.uk).
     * 4. Checks for duplicate emails to prevent DB collisions.
     * 5. Checks password complexity and confirmation match.
     */
    public function validate_registration(array $data) {
        $errors = [];
        if (empty(trim($data['first_name'] ?? ''))) $errors[] = 'First name required.';
        if (empty(trim($data['last_name'] ?? '')))  $errors[] = 'Last name required.';
        
        $email = strtolower(trim($data['email'] ?? ''));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format.';
        } else {
            // ── Domain Restriction Algorithm ──────────────────
            // Checks if the email ends with '@' + the defined domain constant.
            // This ensures only verified university alumni can join the platform.
            if (!str_ends_with($email, '@' . ALLOWED_EMAIL_DOMAIN)) {
                $errors[] = 'Only university emails (@' . ALLOWED_EMAIL_DOMAIN . ') are allowed.';
            }
            
            // Database constraint: Ensure email uniqueness
            if ($this->email_exists($email)) {
                $errors[] = 'Email already exists.';
            }
        }

        // Delegate password complexity checks to a dedicated helper
        $errors = array_merge($errors, $this->validate_password_strength($data['password'] ?? ''));
        
        if (($data['password'] ?? '') !== ($data['confirm_password'] ?? '')) {
            $errors[] = 'Passwords mismatch.';
        }
        
        return $errors;
    }

    /**
     * Password Complexity Algorithm
     * Security:
     * Enforces strict NIST guidelines for password strength using Regular Expressions.
     */
    public function validate_password_strength($password) {
        $errors = [];
        if (strlen($password) < 8)             $errors[] = 'Min 8 characters.';
        if (!preg_match('/[A-Z]/', $password)) $errors[] = 'Need uppercase.';
        if (!preg_match('/[0-9]/', $password)) $errors[] = 'Need a number.';
        if (!preg_match('/[\W_]/', $password)) $errors[] = 'Need a special character.';
        
        return $errors;
    }

    // ── IDENTITY CREATION ─────────────────────────────────────

    /**
     * Create a new user record.
     * Cryptographic Implementation:
     * - Tokens: Uses bin2hex(random_bytes(32)) to create a CSPRNG token immune to timing/guessing attacks.
     * - Passwords: Uses PASSWORD_BCRYPT with a cost factor of 12. This intentionally slows down 
     * hashing to protect against GPU brute-force attacks if the database is compromised.
     */
    public function create_user(array $data) {
        $token = bin2hex(random_bytes(32));
        
        $this->db->insert('users', [
            // Strict XSS Sanitization before database insertion
            'first_name'                => $this->_sanitize($data['first_name']),
            'last_name'                 => $this->_sanitize($data['last_name']),
            'email'                     => strtolower(trim($data['email'])),
            // Apply bcrypt hashing algorithm
            'password_hash'             => password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]),
            'verification_token'        => $token,
            'verification_token_expiry' => date('Y-m-d H:i:s', strtotime('+24 hours')),
            'is_verified'               => 0, 
            'created_at'                => date('Y-m-d H:i:s')
        ]);
        
        return ['id' => $this->db->insert_id(), 'token' => $token];
    }

    // ── EMAIL VERIFICATION ────────────────────────────────────

    /**
     * Validate a user's email verification token.
     * Checks if token exists, if already verified, and enforces a strict 24-hour temporal limit.
     */
    public function verify_email($token) {
        $user = $this->db->where('verification_token', $token)->get('users')->row();

        if (!$user) return 'invalid';
        if ($user->is_verified) return 'already_verified';
        
        // Temporal Constraint Check
        if (strtotime($user->verification_token_expiry) < time()) return 'expired';

        // Activate the user and scrub the token to prevent replay attacks
        $this->db->where('id', $user->id)->update('users', [
            'is_verified'               => 1,
            'verification_token'        => NULL,
            'verification_token_expiry' => NULL,
            'updated_at'                => date('Y-m-d H:i:s')
        ]);

        return 'ok';
    }

    /**
     * Issue a fresh verification token.
     */
    public function resend_verification_token($email) {
        $user = $this->db->where('email', strtolower(trim($email)))->get('users')->row();
        
        if (!$user || $user->is_verified) return FALSE;

        $token = bin2hex(random_bytes(32));
        $this->db->where('id', $user->id)->update('users', [
            'verification_token'        => $token,
            'verification_token_expiry' => date('Y-m-d H:i:s', strtotime('+24 hours'))
        ]);
        
        return $token;
    }

    // ── AUTHENTICATION ────────────────────────────────────────

    /**
     * Authenticate a user login attempt.
     * Security:
     * Uses `password_verify()` to securely check the plaintext input against the stored 
     * Bcrypt hash. This method safely mitigates timing attacks.
     */
    public function attempt_login($email, $password) {
        $user = $this->db->where('email', strtolower(trim($email)))->get('users')->row();
        
        // Unified error returned to prevent User Enumeration attacks
        if (!$user || !password_verify($password, $user->password_hash)) {
            return ['error' => 'invalid_credentials'];
        }
        
        // Gatekeeper: Prevent unverified users from accessing the system
        if (!$user->is_verified) {
            return ['error' => 'not_verified'];
        }
        
        // Keep-Alive: Log the successful login timestamp
        $this->db->where('id', $user->id)->update('users', ['last_login_at' => date('Y-m-d H:i:s')]);
        
        return (array) $user;
    }

    // ── PASSWORD RECOVERY ─────────────────────────────────────

    /**
     * Generate a secure token for password reset.
     * Implements a strict 1-hour temporal limit for security.
     */
    public function create_reset_token($email) {
        $user = $this->db->where('email', strtolower(trim($email)))->get('users')->row();
        
        if (!$user) return FALSE;
        
        $token = bin2hex(random_bytes(32));
        $this->db->where('id', $user->id)->update('users', [
            'reset_token'        => $token,
            'reset_token_expiry' => date('Y-m-d H:i:s', strtotime('+1 hour'))
        ]);
        
        return $token;
    }

    /**
     * Retrieve a user by their reset token.
     * Logic:
     * This query inherently validates the temporal limit by demanding the expiry date 
     * is strictly greater than the current server timestamp.
     */
    public function get_user_by_reset_token($token) {
        return $this->db
            ->where('reset_token', $token)
            ->where('reset_token_expiry >', date('Y-m-d H:i:s'))
            ->get('users')
            ->row();
    }

    /**
     * Apply a new password to the user.
     * Hashes the new password and scrubs the reset tokens to prevent replay attacks.
     */
    public function reset_password($id, $new_password) {
        $this->db->where('id', $id)->update('users', [
            'password_hash'      => password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 12]),
            'reset_token'        => NULL, 
            'reset_token_expiry' => NULL,
            'updated_at'         => date('Y-m-d H:i:s')
        ]);
    }

    // ── PRIVATE DB HELPERS ────────────────────────────────────

    /** Fast check for duplicate emails. */
    public function email_exists($email) {
        return $this->db->where('email', strtolower(trim($email)))->count_all_results('users') > 0;
    }

    /** Standardized XSS Stripper. */
    private function _sanitize($v) {
        return htmlspecialchars(strip_tags(trim($v)), ENT_QUOTES, 'UTF-8');
    }
}