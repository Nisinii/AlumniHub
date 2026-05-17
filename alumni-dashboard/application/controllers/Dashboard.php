<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Dashboard_model');
        
        // Security: Ensure only logged-in staff can see the dashboard
        // Assuming 'staff_id' is set during your login process
        if (!$this->session->userdata('staff_id')) {
            redirect('auth/login');
        }
    }

    // Loads the main HTML view
    public function index() {
        $this->load->view('dashboard/index');
    }

    // Called via AJAX from the dashboard view
    public function get_alumni_data() {
        // 1. Gather filters sent via GET request
        $filters = [
            'programme' => $this->input->get('programme', TRUE),
            'graduation_date' => $this->input->get('graduation_date', TRUE),
            'industry_sector' => $this->input->get('industry_sector', TRUE)
        ];

        // 2. Ask the model to fetch data from CW1
        $api_response = $this->Dashboard_model->fetch_alumni_from_api($filters);

        // 3. Output the exact JSON back to the View
        $this->output
            ->set_status_header($api_response['status_code'] ?: 500)
            ->set_content_type('application/json')
            ->set_output($api_response['body'] ?: json_encode(['error' => 'Failed to connect to API']));
    }
}