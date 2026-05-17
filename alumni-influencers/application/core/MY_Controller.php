<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MY_Controller — Core Application Base Controller
 * All functional controllers in this application extend this class rather than CI_Controller.
 * Architecture & Security Note:
 * This foundational class guarantees that critical operations—such as injecting 
 * HTTP security headers, managing session states, and handling Content Negotiation—
 * are executed uniformly across the entire platform. This mimics the behavior of 
 * middleware (like Helmet.js in Express/Node) within the CodeIgniter ecosystem.
 */
class MY_Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        // Globally enforce strict HTTP security headers (XSS Protection, HSTS, Frame Options)
        $this->load->helper('security_headers');
        set_security_headers();
        
        $this->load->library('session');
        $this->load->helper('url');
    }

    /**
     * Global Authentication & Idle Timeout Manager
     * Algorithm Explanation:
     * 1. Whitelists the Auth controller to prevent infinite redirect loops.
     * 2. Validates the existence of a user session.
     * 3. Calculates the idle time (current time minus last activity).
     * 4. Executes a "Smart Redirect" that responds dynamically based on the client type.
     */
    protected function authenticate() {
        $class  = $this->router->class;
        $method = $this->router->method;

        // Exception: Allow unrestricted access to the Auth controller (except logout)
        if ($class === 'auth' && $method !== 'logout') {
            return TRUE;
        }

        // 1. Identity Check
        $user_id = $this->session->userdata('user_id');
        
        // 2. Idle Timeout Check
        $last = $this->session->userdata('last_activity');
        // SESSION_TIMEOUT_MINUTES must be defined in constants.php
        $timed_out = $last && (time() - $last) > (SESSION_TIMEOUT_MINUTES * 60);

        // State: Unauthenticated OR Session Expired
        if (!$user_id || $timed_out) {
            
            if ($timed_out) {
                // Safely destroy the expired session to prevent hijacking
                $this->session->sess_destroy();
                $this->session->set_flashdata('error', 'Session expired. Please log in again.');
            } else {
                $this->session->set_flashdata('error', 'Authentication required. Please log in.');
            }

            // --- THE SMART REDIRECT ALGORITHM ---
            // Detects if the request is from a Machine (API/AJAX/AR Headset) or a Human (Browser)
            $is_api = $this->input->is_ajax_request() 
                || $this->input->get('format') === 'json' 
                || $this->input->get_request_header('Accept') === 'application/json';

            if ($is_api) {
                // Machine Response: Halt execution and return a standard HTTP 401 JSON payload
                $this->render(401, 'Session expired or invalid.');
            } else {
                // Human Response: Issue an HTTP 302 Redirect to the login UI
                redirect('auth/login');
            }
            
            // CRITICAL: Prevent "View Leakage" by terminating script execution immediately
            exit; 
        }

        // 3. Keep-Alive: Update the last activity timestamp for valid requests
        $this->session->set_userdata('last_activity', time());
        return TRUE;
    }
    
    /**
     * Master Content Negotiation & Rendering Engine
     * Complex Logic Explanation:
     * This method abstracts the view logic away from individual controllers. 
     * It uses "Content Negotiation" to inspect HTTP Headers (Accept, Content-Type) 
     * and query parameters to decide whether to serialize data into a JSON response 
     * or inject it into the HTML layout wrappers.
     *
     * @param int    $status       HTTP Status Code (e.g., 200, 404, 422)
     * @param string $message      Status message
     * @param string $view         Path to the CodeIgniter view file
     * @param array  $data         Payload array to be passed to the view or JSON
     * @param string $redirect_url Optional URL to redirect to upon success
     */
    protected function render($status, $message, $view = '', $data = [], $redirect_url = '') {
        
        // 1. Detect JSON intent across multiple signals (Headers vs. Query Params)
        $is_json = 
            $this->input->is_ajax_request() || 
            strpos($this->input->get_request_header('Accept', TRUE), 'application/json') !== FALSE ||
            strpos($this->input->get_request_header('Content-Type', TRUE), 'application/json') !== FALSE ||
            $this->input->get('format') === 'json'; // URL fail-safe override

        // --- API / MACHINE LOGIC (JSON) ---
        if ($is_json) {
            $this->output->set_status_header($status);
            $this->output->set_content_type('application/json');
            
            // Schema Normalization: Ensure validation errors are always an array
            if (isset($data['errors']) && !is_array($data['errors'])) {
                $data['errors'] = [$data['errors']];
            }

            echo json_encode(array_merge(['status' => $status, 'message' => $message], $data), JSON_PRETTY_PRINT);
            exit; 
        }

        // --- BROWSER LOGIC (HTML) ---
        
        // Handle Error States (400+)
        if ($status >= 400 && !empty($view)) {
            $data['errors'] = isset($data['errors']) ? $data['errors'] : [$message];
            
            // Layout Wrapper Logic: Auth views are standalone; dashboard views get the nav bar.
            if ($this->router->class !== 'auth') {
                $this->load->view('shared/layout_header', $data);
                $this->load->view($view, $data);
                return $this->load->view('shared/layout_footer');
            }
            return $this->load->view($view, $data);
        }

        // Handle Redirections
        if (!empty($redirect_url)) {
            redirect($redirect_url);
        }

        // Handle Success States (200-299)
        if (!empty($view)) {
            $data['success'] = $message;

            if ($this->router->class !== 'auth') {
                $this->load->view('shared/layout_header', $data);
                $this->load->view($view, $data);
                return $this->load->view('shared/layout_footer');
            }
            return $this->load->view($view, $data);
        }
    }

    /**
     * API Key Validator & Usage Logger
     * Algorithm Explanation:
     * Used exclusively for authenticating external machine-to-machine requests (e.g., AR Headset).
     * 1. Uses Regex to extract the token from the standard 'Bearer' header.
     * 2. Validates cryptographically against the database.
     * 3. Logs the endpoint, IP, and method for usage tracking and rate-limit auditing.
     */
    function require_api_key($min_scope = 'read') {
        $CI =& get_instance();
        $CI->load->model('ApiKey_model');

        // 1. Extract Token
        $auth_header = $CI->input->get_request_header('Authorization', TRUE);
        $api_key = NULL;

        if ($auth_header && preg_match('/Bearer\s+(.*)$/i', $auth_header, $matches)) {
            $api_key = $matches[1];
        } else {
            $api_key = $CI->input->get_request_header('X-API-Key', TRUE);
        }

        // 2. Validate Token and Scope
        $key_record = $CI->ApiKey_model->validate_key($api_key, $min_scope);

        if (!$key_record) {
            $CI->output->set_status_header(401);
            echo json_encode([
                'status'  => 401,
                'message' => 'Invalid or missing API key with ' . $min_scope . ' scope.'
            ], JSON_PRETTY_PRINT);
            exit;
        }

        // 3. Telemetry: Log API usage to the database
        $CI->ApiKey_model->log_usage(
            $key_record->id,
            $_SERVER['REQUEST_URI'],
            $CI->input->method(),
            $CI->input->ip_address(),
            200 
        );

        return $key_record;
    }

    /**
     * Role-Based Access Control (RBAC) Gatekeeper
     * Logic Explanation:
     * Uses a hierarchical integer mapping to determine permissions.
     * Admin (3) > Developer (2) > Alumnus (1). 
     * This mathematical comparison allows Admins to automatically inherit Developer rights.
     */
    protected function require_role($min_role = 'alumnus') {
        $current_role = $this->session->userdata('user_role');

        $hierarchy = [
            'alumnus'   => 1,
            'developer' => 2,
            'admin'     => 3
        ];

        // Default to 0 (Guest) if role is undefined
        $user_level = $hierarchy[$current_role] ?? 0;
        $required_level = $hierarchy[$min_role] ?? 1;

        if ($user_level < $required_level) {
            // Security Telemetry: Log unauthorized access attempts
            log_message('error', "Unauthorized access attempt by User ID: " . $this->session->userdata('user_id'));
            
            return $this->render(403, "Access Denied: You need {$min_role} status to view this page.");
        }
        
        return TRUE;
    }
}