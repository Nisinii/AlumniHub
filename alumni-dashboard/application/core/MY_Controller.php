<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MY_Controller — Core Application Base Controller (CW2)
 * All dashboard controllers extend this class.
 */
class MY_Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        // Globally enforce strict HTTP security headers
        $this->load->helper('security_headers');
        if (function_exists('set_security_headers')) {
            set_security_headers();
        }
        
        $this->load->library('session');
        $this->load->helper('url');
    }

    /**
     * Global Authentication & Idle Timeout Manager (Updated for Staff)
     */
    protected function authenticate() {
        $class  = $this->router->class;
        $method = $this->router->method;

        // Exception: Allow unrestricted access to the Auth controller (except logout)
        if ($class === 'auth' && $method !== 'logout') {
            return TRUE;
        }

        // 1. Identity Check (CHANGED: Looking for staff_id instead of user_id)
        $staff_id = $this->session->userdata('staff_id');
        
        // 2. Idle Timeout Check
        $last = $this->session->userdata('last_activity');
        
        // Define timeout fallback if constant is missing (e.g., 30 mins)
        $timeout_mins = defined('SESSION_TIMEOUT_MINUTES') ? SESSION_TIMEOUT_MINUTES : 30;
        $timed_out = $last && (time() - $last) > ($timeout_mins * 60);

        // State: Unauthenticated OR Session Expired
        if (!$staff_id || $timed_out) {
            
            if ($timed_out) {
                $this->session->sess_destroy();
                $this->session->set_flashdata('error', 'Session expired. Please log in again.');
            } else {
                $this->session->set_flashdata('error', 'Authentication required. Please log in.');
            }

            // Smart Redirect Algorithm
            $is_api = $this->input->is_ajax_request() 
                || $this->input->get('format') === 'json' 
                || $this->input->get_request_header('Accept') === 'application/json';

            if ($is_api) {
                $this->render(401, 'Session expired or invalid.');
            } else {
                redirect('auth/login');
            }
            exit; 
        }

        // 3. Keep-Alive: Update the last activity timestamp
        $this->session->set_userdata('last_activity', time());
        return TRUE;
    }
    
    /**
     * Master Content Negotiation & Rendering Engine
     */
    protected function render($status, $message, $view = '', $data = [], $redirect_url = '') {
        
        $is_json = 
            $this->input->is_ajax_request() || 
            strpos($this->input->get_request_header('Accept', TRUE), 'application/json') !== FALSE ||
            strpos($this->input->get_request_header('Content-Type', TRUE), 'application/json') !== FALSE ||
            $this->input->get('format') === 'json';

        // --- API / MACHINE LOGIC (JSON) ---
        if ($is_json) {
            $this->output->set_status_header($status);
            $this->output->set_content_type('application/json');
            
            if (isset($data['errors']) && !is_array($data['errors'])) {
                $data['errors'] = [$data['errors']];
            }

            echo json_encode(array_merge(['status' => $status, 'message' => $message], $data), JSON_PRETTY_PRINT);
            exit; 
        }

        // --- BROWSER LOGIC (HTML) ---
        if ($status >= 400 && !empty($view)) {
            $data['errors'] = isset($data['errors']) ? $data['errors'] : [$message];
            
            if ($this->router->class !== 'auth') {
                // Assuming you will have layout wrappers for the dashboard later
                $this->load->view('shared/layout_header', $data);
                $this->load->view($view, $data);
                return $this->load->view('shared/layout_footer');
            }
            return $this->load->view($view, $data);
        }

        if (!empty($redirect_url)) {
            redirect($redirect_url);
        }

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
}