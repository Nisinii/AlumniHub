<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Alumni extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Alumni_model');
    }

    //Private Helper Function for API KEY managment
    private function require_scope($required_scope) {
        $api_key = $this->input->get_request_header('X-API-KEY', TRUE);

        if (empty($api_key)) {
            $this->output->set_status_header(401);
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized: API Key is missing.']);
            exit;
        }

        // Fetch the key from the database
        $this->db->where('api_key', $api_key);
        $key_record = $this->db->get('api_keys')->row();

        // Check if key exists and is active
        if (!$key_record || $key_record->is_active == 0) {
            $this->output->set_status_header(401);
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized: Invalid or revoked API Key.']);
            exit;
        }

        // Check if the required scope exists in the key's allowed scopes
        $allowed_scopes = array_map('trim', explode(',', $key_record->scope));

        if (!in_array($required_scope, $allowed_scopes)) {
            $this->output->set_status_header(403);
            echo json_encode([
                'status' => 'error', 
                'message' => "Forbidden: Your API key lacks the '{$required_scope}' permission."
            ]);
            exit;
        }
    }

    //Endpoint to retrieve all information of alumni for client dashbaord alumni section
    public function get_alumni() {
        // Set the header so Postman and browsers know it's JSON data
        $this->require_scope('read:alumni');
        $this->output->set_content_type('application/json');

        // Fetch the data from the model
        $alumni = $this->Alumni_model->get_all_alumni();

        if (!empty($alumni)) {
            $this->output->set_status_header(200);
            echo json_encode([
                'status' => 'success',
                'count' => count($alumni),
                'data' => $alumni
            ]);
        } else {
            $this->output->set_status_header(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'No alumni records found.'
            ]);
        }
    }

    //Endpoint to retrieve all information of alumni for client dashbaord analytic section
    public function get_chartdata() {
        // Set the header so Postman and browsers know it's JSON data
        $this->require_scope('read:analytics');
        $this->output->set_content_type('application/json');

        // Fetch the data from the model
        $alumni = $this->Alumni_model->get_all_alumni();

        if (!empty($alumni)) {
            $this->output->set_status_header(200);
            echo json_encode([
                'status' => 'success',
                'count' => count($alumni),
                'data' => $alumni
            ]);
        } else {
            $this->output->set_status_header(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'No alumni records found.'
            ]);
        }
    }
}