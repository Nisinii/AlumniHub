<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * API Key Middleware Helper
 * 
 * Architecture Note:
 * This helper acts as a "Pseudo-Middleware" for Machine-to-Machine (M2M) authentication.
 * It is specifically designed for external clients (like the AR Headset or 3rd-party 
 * developers) who use stateless API Keys rather than stateful Session Cookies.
 * 
 * Usage Example:
 * Place `$key = require_api_key('read_write'); if (!$key) return;` at the top 
 * of any controller method to instantly lock it down to authenticated machines.
 */

if ( ! function_exists('require_api_key'))
{
    /**
     * Master API Key Gateway
     * Security Logic:
     * This function operates on a "Fail-Fast" principle. If a key is missing, invalid, 
     * or lacks the required scope, it immediately hijacks the HTTP response, injects 
     * a 401 Unauthorized status, outputs a JSON error, and returns FALSE to halt 
     * the controller's execution.
     *
     * @param  string $min_scope The minimum required permission ('read' | 'read_write' | 'admin')
     * @return object|false      Returns the validated key object, or FALSE on failure.
     */
    function require_api_key($min_scope = 'read')
    {
        // Access the global CodeIgniter super-object
        $CI =& get_instance();
        $CI->load->model('ApiKey_model');

        // Step 1: Attempt to extract the token from HTTP headers
        $raw_key = _extract_api_key();

        if ( ! $raw_key) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode([
                'status'  => 401,
                'message' => 'API key required. Send your key in the Authorization header as: Bearer YOUR_KEY',
            ]);
            return FALSE;
        }

        // Step 2: Cryptographic & Scope Validation via the Model
        $key = $CI->ApiKey_model->validate_key($raw_key, $min_scope);

        if ( ! $key) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode([
                'status'  => 401,
                'message' => "Invalid, revoked, or insufficient scope. Minimum required: {$min_scope}.",
            ]);
            return FALSE;
        }

        // Step 3: Automatic Telemetry Logging
        // If validation succeeds, log the request before passing control back to the controller
        _log_api_call($CI, $key->id);

        return $key;
    }
}

if ( ! function_exists('_extract_api_key'))
{
    /**
     * HTTP Header Extraction Algorithm
     * Logic:
     * APIs receive tokens in different ways depending on the client. This function 
     * gracefully supports the modern OAuth 2.0 standard (`Authorization: Bearer <token>`) 
     * while providing a fallback for simpler clients using `X-API-Key: <token>`.
     * * @return string The extracted raw API key, or an empty string.
     */
    function _extract_api_key()
    {
        // Strategy A: Regex parsing of the standard Authorization header
        $auth_header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        
        // Uses Case-Insensitive Regex (/i) to capture the token after the word "Bearer "
        if (preg_match('/^Bearer\s+(\S+)$/i', $auth_header, $matches)) {
            return $matches[1];
        }

        // Strategy B: Direct check of the custom X-API-Key header
        $xkey = $_SERVER['HTTP_X_API_KEY'] ?? '';
        if ( ! empty($xkey)) {
            return $xkey;
        }

        return '';
    }
}

if ( ! function_exists('_log_api_call'))
{
    /**
     * Asynchronous Telemetry Logger
     * Architecture Note:
     * This function constructs a clean representation of the current request 
     * (combining the URI string, the HTTP Method, and the client IP) and passes 
     * it to the database to maintain a strict audit trail of all API usage.
     *
     * @param object $CI     The CodeIgniter super-object
     * @param int    $key_id The Database ID of the validated API Key
     */
    function _log_api_call($CI, $key_id)
    {
        // Construct the exact URI path requested (e.g., /bidding/api_today)
        $endpoint = '/' . ltrim($CI->uri->uri_string(), '/');
        
        $method   = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $ip       = $_SERVER['REMOTE_ADDR']    ?? 'unknown';

        // Execute the database insertion via the model
        // Note: Defaults to 200 OK since execution only reaches here if validation passed
        $CI->ApiKey_model->log_usage($key_id, $endpoint, $method, $ip, 200);
    }
}