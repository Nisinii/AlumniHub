<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Staff Model (Dashboard)
 * Handles the data layer for Identity and Access Management (IAM) for University Staff.
 */
class Staff_model extends CI_Model {

    // ── VALIDATION LOGIC ──────────────────────────────────────

    public function validate_registration(array $data) {
        $errors = [];
        if (empty(trim($data['first_name'] ?? ''))) $errors[] = 'First name required.';
        if (empty(trim($data['last_name'] ?? '')))  $errors[] = 'Last name required.';
        
        $email = strtolower(trim($data['email'] ?? ''));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format.';
        } else {
            // Uses your defined constant!
            if (!str_ends_with($email, '@' . ALLOWED_EMAIL_DOMAIN)) {
                $errors[] = 'Only university emails (@' . ALLOWED_EMAIL_DOMAIN . ') are allowed.';
            }
            
            if ($this->email_exists($email)) {
                $errors[] = 'Email already exists.';
            }
        }

        $errors = array_merge($errors, $this->validate_password_strength($data['password'] ?? ''));
        
        if (($data['password'] ?? '') !== ($data['confirm_password'] ?? '')) {
            $errors[] = 'Passwords mismatch.';
        }
        
        return $errors;
    }

    public function validate_password_strength($password) {
        $errors = [];
        if (strlen($password) < 8)             $errors[] = 'Min 8 characters.';
        if (!preg_match('/[A-Z]/', $password)) $errors[] = 'Need uppercase.';
        if (!preg_match('/[0-9]/', $password)) $errors[] = 'Need a number.';
        if (!preg_match('/[\W_]/', $password)) $errors[] = 'Need a special character.';
        
        return $errors;
    }

    // ── IDENTITY CREATION ─────────────────────────────────────

    public function create_staff_user(array $data) {
        $token = bin2hex(random_bytes(32));
        
        // CHANGED: Insert into staff_users table and added department field
        $this->db->insert('staff_users', [
            'first_name'          => $this->_sanitize($data['first_name']),
            'last_name'           => $this->_sanitize($data['last_name']),
            'email'               => strtolower(trim($data['email'])),
            'department'          => $this->_sanitize($data['department'] ?? 'General'),
            'password_hash'       => password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]),
            'verification_token'  => $token,
            'verification_expiry' => date('Y-m-d H:i:s', strtotime('+24 hours')), // Adjusted column name
            'is_verified'         => 0, 
            'created_at'          => date('Y-m-d H:i:s')
        ]);
        
        return ['id' => $this->db->insert_id(), 'token' => $token];
    }

    // ── EMAIL VERIFICATION ────────────────────────────────────

    public function verify_email($token) {
        // CHANGED: Query staff_users table
        $user = $this->db->where('verification_token', $token)->get('staff_users')->row();

        if (!$user) return 'invalid';
        if ($user->is_verified) return 'already_verified';
        
        if (strtotime($user->verification_expiry) < time()) return 'expired';

        $this->db->where('id', $user->id)->update('staff_users', [
            'is_verified'         => 1,
            'verification_token'  => NULL,
            'verification_expiry' => NULL
        ]);

        return 'ok';
    }

    public function resend_verification_token($email) {
        $user = $this->db->where('email', strtolower(trim($email)))->get('staff_users')->row();
        
        if (!$user || $user->is_verified) return FALSE;

        $token = bin2hex(random_bytes(32));
        $this->db->where('id', $user->id)->update('staff_users', [
            'verification_token'  => $token,
            'verification_expiry' => date('Y-m-d H:i:s', strtotime('+24 hours'))
        ]);
        
        return $token;
    }

    // ── AUTHENTICATION ────────────────────────────────────────

    public function attempt_login($email, $password) {
        $user = $this->db->where('email', strtolower(trim($email)))->get('staff_users')->row();
        
        if (!$user || !password_verify($password, $user->password_hash)) {
            return ['error' => 'invalid_credentials'];
        }
        
        if (!$user->is_verified) {
            return ['error' => 'not_verified'];
        }
        
        $this->db->where('id', $user->id)->update('staff_users', ['last_login_at' => date('Y-m-d H:i:s')]);
        
        return (array) $user;
    }

    // ── PASSWORD RECOVERY ─────────────────────────────────────

    public function create_reset_token($email) {
        $user = $this->db->where('email', strtolower(trim($email)))->get('staff_users')->row();
        
        if (!$user) return FALSE;
        
        $token = bin2hex(random_bytes(32));
        $this->db->where('id', $user->id)->update('staff_users', [
            'reset_token'  => $token,
            'reset_expiry' => date('Y-m-d H:i:s', strtotime('+1 hour'))
        ]);
        
        return $token;
    }

    public function get_user_by_reset_token($token) {
        return $this->db
            ->where('reset_token', $token)
            ->where('reset_expiry >', date('Y-m-d H:i:s'))
            ->get('staff_users')
            ->row();
    }

    public function reset_password($id, $new_password) {
        $this->db->where('id', $id)->update('staff_users', [
            'password_hash' => password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 12]),
            'reset_token'   => NULL, 
            'reset_expiry'  => NULL
        ]);
    }

    // ── PRIVATE DB HELPERS ────────────────────────────────────

    public function email_exists($email) {
        // CHANGED: Query staff_users table
        return $this->db->where('email', strtolower(trim($email)))->count_all_results('staff_users') > 0;
    }

    private function _sanitize($v) {
        return htmlspecialchars(strip_tags(trim($v)), ENT_QUOTES, 'UTF-8');
    }
}