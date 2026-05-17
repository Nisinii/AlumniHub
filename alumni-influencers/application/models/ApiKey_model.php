<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ApiKey Model
 * Manages the data layer for Machine-to-Machine (M2M) API Authentication.
 * Architecture Note:
 * This model handles the lifecycle of external API keys used by clients (like the AR Headset).
 * It includes cryptographic generation, hierarchical scope validation, soft-deletion 
 * for auditing, and complex SQL aggregations for the usage telemetry dashboard.
 */
class ApiKey_model extends CI_Model {

    // ── KEY GENERATION ────────────────────────────────────────

    /**
     * Generate a new API key for a user.
     * Cryptographic Logic:
     * Uses `random_bytes()`, a Cryptographically Secure Pseudo-Random Number Generator (CSPRNG),
     * rather than standard `rand()` or `uniqid()`. It generates 32 random bytes and encodes 
     * them into a 64-character hexadecimal string, making it mathematically invulnerable 
     * to brute-force guessing.
     *
     * @param  int    $user_id  The owner of the key
     * @param  string $key_name Friendly label (e.g., "AR Headset Client")
     * @param  string $scope    Access level (read | read_write | admin)
     * @return array            Array containing the new key and ID, or an error message.
     */
    public function generate_key($user_id, $key_name, $scope = 'read')
    {
        if (empty(trim($key_name))) {
            return ['error' => 'Key name is required.'];
        }

        // Security: Whitelist validation for scopes to prevent privilege escalation
        $valid_scopes = ['read', 'read_write', 'admin'];
        if ( ! in_array($scope, $valid_scopes)) {
            return ['error' => 'Invalid scope. Use: read, read_write, or admin.'];
        }

        // Generate the 64-character CSPRNG key
        $api_key = bin2hex(random_bytes(32));

        $this->db->insert('api_keys', [
            'user_id'    => $user_id,
            // XSS Prevention: Clean the user-provided key name before it touches the database
            'key_name'   => htmlspecialchars(strip_tags(trim($key_name)), ENT_QUOTES, 'UTF-8'),
            'api_key'    => $api_key,
            'scope'      => $scope,
            'is_active'  => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $id = $this->db->insert_id();
        
        // Return the raw key ONLY once to the controller so it can be shown to the user
        return $id ? ['id' => $id, 'key' => $api_key, 'scope' => $scope] : ['error' => 'Failed to generate key.'];
    }

    // ── KEY LISTING ───────────────────────────────────────────

    /**
     * Get all API keys for a specific user.
     * @param  int $user_id
     * @return array
     */
    public function get_keys($user_id)
    {
        return $this->db
            ->where('user_id', $user_id)
            ->order_by('created_at', 'DESC')
            ->get('api_keys')
            ->result_array();
    }

    /**
     * Get a single key by ID.
     * Security: Requires the $user_id to ensure a user cannot query 
     * the details of a key belonging to another account (IDOR protection).
     *
     * @param  int $key_id
     * @param  int $user_id
     * @return array|false
     */
    public function get_key($key_id, $user_id)
    {
        return $this->db
            ->where('id', $key_id)
            ->where('user_id', $user_id)
            ->get('api_keys')
            ->row_array() ?: FALSE;
    }

    // ── KEY VALIDATION ────────────────────────────────────────

    /**
     * Validate an incoming API key against the required scope.
     * Scope Hierarchy Algorithm:
     * Instead of complex IF/ELSE strings, this maps string-based scopes to integer 
     * levels (1, 2, 3). This allows the system to easily verify if a key has *at least* * the required permission level using a simple mathematical comparison (`>=`).
     *
     * @param  string $api_key   The raw key string from the Authorization header
     * @param  string $min_scope Minimum scope required ('read' | 'read_write' | 'admin')
     * @return object|false      Key row object or FALSE if invalid/revoked/wrong scope
     */
    public function validate_key($api_key, $min_scope = 'read')
    {
        if (empty($api_key)) return FALSE;

        $key = $this->db
            ->where('api_key', $api_key)
            ->where('is_active', 1) // Ensure the key hasn't been revoked
            ->get('api_keys')
            ->row();

        if ( ! $key) return FALSE;

        // The Hierarchical Integer Map
        $scope_levels   = ['read' => 1, 'read_write' => 2, 'admin' => 3];
        $key_level      = $scope_levels[$key->scope]      ?? 0;
        $required_level = $scope_levels[$min_scope]       ?? 1;

        // Fail if the key's power level is lower than the endpoint requires
        if ($key_level < $required_level) return FALSE;

        // Keep-Alive: Update the timestamp so admins can track inactive keys
        $this->db->where('id', $key->id)->update('api_keys', [
            'last_used_at' => date('Y-m-d H:i:s'),
        ]);

        return $key;
    }

    // ── KEY REVOCATION ────────────────────────────────────────

    /**
     * Revoke an API key.
     * Soft Deletion Pattern:
     * Instead of running a SQL `DELETE`, we update the `is_active` flag to 0.
     * This "soft delete" prevents the key from being used immediately, but preserves 
     * the database record so the system can maintain accurate historical usage logs.
     *
     * @param  int $key_id
     * @param  int $user_id  Ownership check to prevent unauthorized revocations
     * @return bool|array
     */
    public function revoke_key($key_id, $user_id)
    {
        $key = $this->get_key($key_id, $user_id);
        if ( ! $key)               return ['error' => 'Key not found.'];
        if ( ! $key['is_active'])  return ['error' => 'Key is already revoked.'];

        $this->db->where('id', $key_id)->where('user_id', $user_id)->update('api_keys', [
            'is_active'  => 0,
            'revoked_at' => date('Y-m-d H:i:s'),
        ]);

        return TRUE;
    }

    // ── USAGE LOGGING (TELEMETRY) ─────────────────────────────

    /**
     * Insert a telemetry log for an API call.
     * Called by MY_Controller->require_api_key() after a successful validation.
     *
     * @param  int    $key_id
     * @param  string $endpoint      e.g. /bidding/api_today
     * @param  string $method        GET | POST | DELETE
     * @param  string $ip_address    Caller's IP for audit trails
     * @param  int    $response_code HTTP status code returned
     */
    public function log_usage($key_id, $endpoint, $method, $ip_address, $response_code)
    {
        $this->db->insert('api_usage_logs', [
            'api_key_id'    => $key_id,
            'endpoint'      => $endpoint,
            'method'        => strtoupper($method),
            'ip_address'    => $ip_address,
            'response_code' => $response_code,
            'called_at'     => date('Y-m-d H:i:s'),
        ]);
    }

    // ── USAGE STATISTICS (DATA AGGREGATION) ───────────────────

    /**
     * Compile comprehensive telemetry data for a specific API key.
     * SQL Aggregation Logic:
     * Executes multiple targeted queries using `COUNT()`, `GROUP BY`, and `LIMIT` 
     * to shape raw log rows into actionable dashboard metrics (e.g., most used 
     * endpoints, 7-day rolling averages).
     *
     * @param  int $key_id
     * @param  int $user_id
     * @return array|false
     */
    public function get_key_stats($key_id, $user_id)
    {
        // 1. Ownership Verification
        $key = $this->get_key($key_id, $user_id);
        if ( ! $key) return FALSE;

        // 2. Global Call Volume
        $total = $this->db
            ->where('api_key_id', $key_id)
            ->count_all_results('api_usage_logs');

        // 3. Endpoint Popularity (Groups by URL and Method)
        $by_endpoint = $this->db
            ->select('endpoint, method, COUNT(*) as call_count')
            ->where('api_key_id', $key_id)
            ->group_by('endpoint, method')
            ->order_by('call_count', 'DESC')
            ->get('api_usage_logs')
            ->result_array();

        // 4. Recent Activity Stream
        $recent = $this->db
            ->where('api_key_id', $key_id)
            ->order_by('called_at', 'DESC')
            ->limit(20)
            ->get('api_usage_logs')
            ->result_array();

        // 5. 7-Day Rolling Average (Groups by Date string)
        $daily = $this->db
            ->select('DATE(called_at) as date, COUNT(*) as call_count')
            ->where('api_key_id', $key_id)
            ->where('called_at >=', date('Y-m-d', strtotime('-7 days')))
            ->group_by('DATE(called_at)')
            ->order_by('date', 'ASC')
            ->get('api_usage_logs')
            ->result_array();

        // Package and return the compiled dashboard data
        return [
            'key_id'      => $key_id,
            'key_name'    => $key['key_name'],
            'scope'       => $key['scope'],
            'is_active'   => (bool) $key['is_active'],
            'created_at'  => $key['created_at'],
            'last_used_at'=> $key['last_used_at'],
            'total_calls' => $total,
            'by_endpoint' => $by_endpoint,
            'recent_calls'=> $recent,
            'daily_last_7_days' => $daily,
        ];
    }

    /**
     * Retrieve all keys for a user and append their total lifetime call count.
     * Used to populate the primary table on the Developer Dashboard.
     *
     * @param  int $user_id
     * @return array
     */
    public function get_keys_with_stats($user_id)
    {
        $keys = $this->get_keys($user_id);

        // Inject the aggregate call count into each key's array by reference
        foreach ($keys as &$key) {
            $key['total_calls'] = $this->db
                ->where('api_key_id', $key['id'])
                ->count_all_results('api_usage_logs');
        }

        return $keys;
    }
}