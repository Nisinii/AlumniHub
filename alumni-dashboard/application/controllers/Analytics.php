<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Analytics extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Analytics_model');
        
        // Ensure only logged-in staff can access
        if (!$this->session->userdata('staff_id')) {
            redirect('auth/login');
        }
    }

    // Loads the view from the Dashboard folder
    public function index() {
        $this->load->view('Dashboard/analytic');
    }

    // API endpoint for the charts to fetch data securely via POST
    public function get_chart_data() {
        // Just fetch everything, no filters needed
        $api_response = $this->Analytics_model->fetch_data_for_charts();

        $this->output
            ->set_status_header($api_response['status_code'] ?: 500)
            ->set_content_type('application/json')
            ->set_output($api_response['body'] ?: json_encode(['error' => 'Failed to connect to API']));
    }
    
}