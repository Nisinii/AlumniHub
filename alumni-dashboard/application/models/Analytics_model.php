<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Analytics_model extends CI_Model {

    // Make sure this points to your actual CW1 endpoint
    private $cw1_api_url = 'http://localhost/alumni-influencers/alumni/get_chartdata';

    // Store the specific API key for the Analytics Dashboard
    // This key MUST exist in your CW1 `api_keys` table with the 'read:alumni' or 'read:analytics' scope
    private $api_key = 'dash_key_1234567890abcdef'; 

    public function fetch_data_for_charts() {
        $ch = curl_init();
        
        // Target the API directly using GET
        curl_setopt($ch, CURLOPT_URL, $this->cw1_api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        // NEW: Attach the API Key via Headers so CW1 knows who is asking!
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-API-KEY: ' . $this->api_key,
            'Accept: application/json'
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        return [
            'status_code' => $http_code,
            'body' => $response,
            'error' => $error
        ];
    }

}