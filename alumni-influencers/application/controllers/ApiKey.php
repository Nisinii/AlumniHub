<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ApiKey Controller
 * Manages the creation, revocation, and tracking of API Keys for external clients.
 * Architecture Note:
 * This controller serves the "Developer Dashboard" and is strictly protected 
 * by Role-Based Access Control (RBAC). It allows developers to generate tokens 
 * that grant machine-to-machine access to the platform's API endpoints.
 */
class ApiKey extends MY_Controller {

    /**
     * Constructor
     * Security Logic:
     * Implements a multi-layered security checkpoint before any method in this 
     * controller can be executed.
     */
    public function __construct() {
        parent::__construct();
        $this->load->model('ApiKey_model');
        
        // Layer 1: Global Authentication Check (Ensures the user has a valid session)
        $this->authenticate(); 

        // Layer 2: Role-Based Access Control (RBAC)
        $role = $this->session->userdata('user_role');

        // Strict Block: Only 'developer' and 'admin' roles can access API management.
        if ($role !== 'developer' && $role !== 'admin') {
            
            // Force a 403 Forbidden HTTP response
            $this->render(403, 'Access Denied: You do not have Developer permissions.');
            
            // CRITICAL: The 'exit' acts as a hard stop. It prevents CodeIgniter 
            // from continuing execution and accidentally rendering protected views.
            exit; 
        }
    }

    /**
     * GET /apikey
     * Renders the main API Key management dashboard.
     * Logic:
     * Fetches all keys belonging to the current user along with their usage statistics.
     * It also checks session flashdata for a 'new_key' to securely display a newly 
     * generated key exactly once before it is lost forever.
     */
    public function index() {
        $user_id = $this->session->userdata('user_id');
        
        $data = [
            'page_title' => 'API Key Management',
            'keys'       => $this->ApiKey_model->get_keys_with_stats($user_id),
            // Retrieve the unhashed key if one was just generated
            'new_key'    => $this->session->flashdata('new_key')
        ];
        
        return $this->render(200, 'API Key dashboard.', 'apikey/index', $data);
    }

    /**
     * POST /apikey/generate
     * Generates a new cryptographically secure API key.
     * Security Implementation:
     * The raw key is returned by the model, stored in CodeIgniter's 'flashdata' 
     * (which survives only one redirect/request), and passed to the view. This ensures
     * the user can copy it once, fulfilling standard API security best practices.
     */
    public function generate() {
        // Enforce strict HTTP method checking
        if ($this->input->method() !== 'post') {
            return $this->render(405, 'Method not allowed.');
        }

        // Parse input using the client-agnostic helper
        $input = $this->_get_input();
        
        $result = $this->ApiKey_model->generate_key(
            $this->session->userdata('user_id'),
            $input['key_name'] ?? '',
            $input['scope'] ?? 'read' // Default to 'read' scope if not provided
        );

        // Handle validation or generation errors from the model
        if (isset($result['error'])) {
            return $this->render(422, $result['error'], '', [], 'apikey');
        }

        // Securely store the newly minted key in flashdata to display it ONCE
        $this->session->set_flashdata('new_key', $result['key']);
        
        return $this->render(201, 'API key generated. Store it securely.', '', $result, 'apikey');
    }

    /**
     * POST /apikey/revoke/{id}
     * Deactivates an existing API key.
     * Logic:
     * Passes both the key ID and the active user_id to the model to ensure a user 
     * can only revoke their own keys (preventing Insecure Direct Object Reference vulnerabilities).
     */
    public function revoke($id = NULL) {
        if ($this->input->method() !== 'post') {
            return $this->render(405, 'Method not allowed.');
        }

        $result = $this->ApiKey_model->revoke_key($id, $this->session->userdata('user_id'));

        if (isset($result['error'])) {
            return $this->render(422, $result['error'], '', [], 'apikey');
        }

        return $this->render(200, 'API key revoked.', '', [], 'apikey');
    }

    /**
     * GET /apikey/stats/{id}
     * Retrieves detailed usage logs for a specific API Key.
     */
    public function stats($id = NULL) {
        // The model verifies ownership based on the session user_id
        $stats = $this->ApiKey_model->get_key_stats($id, $this->session->userdata('user_id'));
        
        if (!$stats) {
            return $this->render(404, 'Key not found or unauthorized.');
        }

        return $this->render(200, 'Usage stats retrieved.', 'apikey/stats', ['stats' => $stats]);
    }

    /**
     * Private Helper: Get Input
     * Client Agnostic Parsing Algorithm:
     * This function checks if the incoming request contains a raw JSON payload 
     * (from tools like Postman, Fetch API, or the AR Client) or standard 
     * application/x-www-form-urlencoded data (from a standard browser HTML form).
     * @return array The parsed and sanitized input data array.
     */
    private function _get_input() {
        // Attempt to decode a raw JSON body
        $json = json_decode($this->input->raw_input_stream, TRUE);
        
        // If JSON is valid, return it. Otherwise, fall back to standard POST variables.
        return (json_last_error() === JSON_ERROR_NONE) ? $json : $this->input->post(NULL, TRUE);
    }

}