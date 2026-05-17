<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MY_Security — Core Security Override
 * Architecture Note:
 * CodeIgniter's default CSRF (Cross-Site Request Forgery) protection expects a 
 * traditional HTML form with a hidden token field. However, our platform acts as 
 * both a web app AND a headless API for the AR Headset. 
 * This class overrides the core security layer to dynamically disable CSRF token 
 * checks for API/AJAX requests, while maintaining strict protection for standard 
 * browser-based HTML form submissions.
 */
class MY_Security extends CI_Security {

    /**
     * CSRF Verification Interceptor
     * Complex Logic Explanation:
     * This method acts as a pre-flight gateway. Before CodeIgniter rejects a POST 
     * request for missing a CSRF token, we analyze the HTTP Request Headers to 
     * determine the client's identity and intent.
     * * @return object Returns the security object itself to bypass, or the parent method to enforce.
     */
    public function csrf_verify()
    {
        // 1. The Heuristic Detection Algorithm
        // We evaluate 5 distinct signals to determine if the request is from a machine/API
        $is_api_request = (
            // Signal A: The client explicitly asked for a JSON response (Standard API behavior)
            (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'json') !== FALSE) ||
            
            // Signal B: The client is sending a raw JSON payload (e.g., Postman / Fetch API)
            (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'json') !== FALSE) ||
            
            // Signal C: The client is uploading a file via an API (multipart/form-data)
            // Required for the Profile Image upload endpoint to work seamlessly via AJAX
            (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'multipart/form-data') !== FALSE) ||
            
            // Signal D: The URL explicitly requests the JSON format fallback (?format=json)
            (isset($_GET['format']) && $_GET['format'] === 'json') ||
            
            // Signal E: The request was sent via modern JavaScript (XMLHttpRequest/AJAX)
            (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest')
        );

        // 2. The API Bypass
        // If any of the above heuristic signals are true, we are dealing with an API client.
        // We bypass the traditional HTML CSRF check and return the current instance.
        if ($is_api_request) {
            return $this;
        }

        // 3. The Strict Fallback
        // If none of the signals match, we assume this is a standard human browser 
        // submitting an HTML form. We pass the request back up to CodeIgniter's 
        // core security bouncer to enforce strict CSRF token validation.
        return parent::csrf_verify();
    }
}