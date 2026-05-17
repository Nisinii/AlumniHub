<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_model extends CI_Model {

    // URL to your CW1 API endpoint.
    private $cw1_api_url = 'http://localhost/alumni-influencers/alumni/get_alumni';
    
    // Store the specific API key for the Analytics Dashboard
    // This key MUST exist in your CW1 `api_keys` table with the 'read:alumni' scope
    private $api_key = 'dash_key_1234567890abcdef'; 

    public function fetch_alumni_from_api($filters = []) {
        
        // 1. Build the query string if filters are provided
        $query_string = '';
        if (!empty($filters)) {
            // Remove empty filters so we don't send blank parameters
            $active_filters = array_filter($filters); 
            if (!empty($active_filters)) {
                $query_string = '?' . http_build_query($active_filters);
            }
        }

        $final_url = $this->cw1_api_url . $query_string;

        // 2. Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $final_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        // 3. Attach the API Key via Headers
        // This proves to CW1 exactly which client is making the request
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-API-KEY: ' . $this->api_key,
            'Accept: application/json'
        ]);

        // 4. Execute request
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        // 5. Return the raw response and status back to the controller
        return [
            'status_code' => $http_code,
            'body' => $response,
            'error' => $error
        ];
    }
}