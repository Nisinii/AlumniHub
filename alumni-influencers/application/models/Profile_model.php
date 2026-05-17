<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Profile Model
 * Manages the data layer for the comprehensive Alumni Profile system.
 * Architecture Note:
 * The user profile is distributed across 6 different normalized database tables 
 * (profiles, degrees, certifications, licences, courses, employment). This model 
 * provides an abstraction layer to aggregate that data into a single object, 
 * while providing secure, DRY-compliant CRUD operations for each sub-section.
 */
class Profile_model extends CI_Model {

    // ── FULL PROFILE AGGREGATION ──────────────────────────────

    /**
     * Retrieve the comprehensive profile payload.
     * Data Aggregation Algorithm:
     * Executes targeted queries across all related profile tables and packages 
     * the results into a cohesive Data Transfer Object (DTO). This is consumed 
     * by both the Web UI Dashboard and the AR Headset API.
     *
     * @param  int $user_id
     * @return array|null 
     */
    public function get_full_profile($user_id)
    {
        if (!$user_id) return NULL;

        $user = $this->db->where('id', $user_id)->get('users')->row();
        
        // Safety check: Halt execution immediately if the core user record is missing
        if (!$user) return NULL; 

        $profile = $this->db->where('user_id', $user_id)->get('profiles')->row();

        return [
            'user' => [
                'id'         => $user->id,
                'first_name' => $user->first_name,
                'last_name'  => $user->last_name,
                'email'      => $user->email,
            ],
            'profile' => $profile ? [
                'bio'           => $profile->bio,
                'linkedin_url'  => $profile->linkedin_url,
                // Dynamically construct the full absolute URL for the frontend
                'profile_image' => $profile->profile_image 
                    ? base_url('uploads/profiles/' . $profile->profile_image) 
                    : NULL,
            ] : NULL,
            // Utilize generic getters to fetch normalized 3NF data sets
            'degrees'        => $this->get_section($user_id, 'degrees'),
            'certifications' => $this->get_section($user_id, 'certifications'),
            'licences'       => $this->get_section($user_id, 'licences'),
            'courses'        => $this->get_section($user_id, 'courses'),
            'employment'     => $this->get_section($user_id, 'employment'),
            // Dynamically calculate how much of the profile is filled out
            'completion'     => $this->get_completion_status($user_id),
        ];
    }

    // ── CORE PROFILE (Bio, LinkedIn, Image) ───────────────────

    /**
     * "Upsert" the core profile data.
     * Logic:
     * Checks if a profile row already exists. If TRUE, executes an UPDATE. 
     * If FALSE, executes an INSERT. This "Upsert" pattern prevents primary key collisions.
     */
    public function save_profile($user_id, array $data)
    {
        // Validation: Ensure valid URI format to prevent broken links on the frontend
        if ( ! empty($data['linkedin_url']) && ! filter_var($data['linkedin_url'], FILTER_VALIDATE_URL)) {
            return ['errors' => ['LinkedIn URL must be a valid URL (e.g. https://linkedin.com/in/yourname).']];
        }

        $existing = $this->db->where('user_id', $user_id)->get('profiles')->row();

        $fields = [
            // Strict XSS Sanitization applied before hitting the database
            'bio'          => $this->_sanitize($data['bio'] ?? ''),
            'linkedin_url' => ! empty($data['linkedin_url']) ? trim($data['linkedin_url']) : NULL,
            'updated_at'   => date('Y-m-d H:i:s'),
        ];

        // Conditional Image Update: Only overwrite if a new file was actually uploaded
        if ( ! empty($data['profile_image'])) {
            $fields['profile_image'] = $data['profile_image'];
        }

        if ($existing) {
            $this->db->where('user_id', $user_id)->update('profiles', $fields);
        } else {
            $fields['user_id']    = $user_id;
            $fields['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('profiles', $fields);
        }

        return TRUE;
    }

    // ── SECTION: DEGREES ──────────────────────────────────────

    public function add_degree($user_id, array $data)
    {
        $errors = $this->_require(['degree_name' => 'Degree name', 'institution' => 'Institution', 'programme' => 'Programme'], $data);
        if ( ! empty($errors)) return ['errors' => $errors];

        return $this->_insert('degrees', $user_id, [
            'degree_name'     => $this->_sanitize($data['degree_name']),
            'institution'     => $this->_sanitize($data['institution']),
            'programme'       => $this->_sanitize($data['programme']),
            'degree_url'      => $this->_clean_url($data['degree_url'] ?? ''),
            'completion_date' => $this->_clean_date($data['completion_date'] ?? ''),
        ]);
    }

    public function update_degree($id, $user_id, array $data)
    {
        $errors = $this->_require(['degree_name' => 'Degree name', 'institution' => 'Institution', 'programme' => 'Programme'], $data);
        if ( ! empty($errors)) return ['errors' => $errors];

        return $this->_update('degrees', $id, $user_id, [
            'degree_name'     => $this->_sanitize($data['degree_name']),
            'institution'     => $this->_sanitize($data['institution']),
            'programme'       => $this->_sanitize($data['programme']),
            'degree_url'      => $this->_clean_url($data['degree_url'] ?? ''),
            'completion_date' => $this->_clean_date($data['completion_date'] ?? ''),
        ]);
    }

    public function delete_degree($id, $user_id)         { return $this->_delete('degrees', $id, $user_id); }

    // ── SECTION: CERTIFICATIONS ───────────────────────────────

    public function add_certification($user_id, array $data)
    {
        $errors = $this->_require(['cert_name' => 'Certification name', 'issuing_body' => 'Issuing body'], $data);
        if ( ! empty($errors)) return ['errors' => $errors];

        return $this->_insert('certifications', $user_id, [
            'cert_name'       => $this->_sanitize($data['cert_name']),
            'issuing_body'    => $this->_sanitize($data['issuing_body']),
            'cert_url'        => $this->_clean_url($data['cert_url'] ?? ''),
            'completion_date' => $this->_clean_date($data['completion_date'] ?? ''),
        ]);
    }

    public function update_certification($id, $user_id, array $data)
    {
        $errors = $this->_require(['cert_name' => 'Certification name', 'issuing_body' => 'Issuing body'], $data);
        if ( ! empty($errors)) return ['errors' => $errors];

        return $this->_update('certifications', $id, $user_id, [
            'cert_name'       => $this->_sanitize($data['cert_name']),
            'issuing_body'    => $this->_sanitize($data['issuing_body']),
            'cert_url'        => $this->_clean_url($data['cert_url'] ?? ''),
            'completion_date' => $this->_clean_date($data['completion_date'] ?? ''),
        ]);
    }

    public function delete_certification($id, $user_id) { return $this->_delete('certifications', $id, $user_id); }

    // ── SECTION: LICENCES ─────────────────────────────────────

    public function add_licence($user_id, array $data)
    {
        $errors = $this->_require(['licence_name' => 'Licence name', 'awarding_body' => 'Awarding body'], $data);
        if ( ! empty($errors)) return ['errors' => $errors];

        return $this->_insert('licences', $user_id, [
            'licence_name'    => $this->_sanitize($data['licence_name']),
            'awarding_body'   => $this->_sanitize($data['awarding_body']),
            'licence_url'     => $this->_clean_url($data['licence_url'] ?? ''),
            'completion_date' => $this->_clean_date($data['completion_date'] ?? ''),
        ]);
    }

    public function update_licence($id, $user_id, array $data)
    {
        $errors = $this->_require(['licence_name' => 'Licence name', 'awarding_body' => 'Awarding body'], $data);
        if ( ! empty($errors)) return ['errors' => $errors];

        return $this->_update('licences', $id, $user_id, [
            'licence_name'    => $this->_sanitize($data['licence_name']),
            'awarding_body'   => $this->_sanitize($data['awarding_body']),
            'licence_url'     => $this->_clean_url($data['licence_url'] ?? ''),
            'completion_date' => $this->_clean_date($data['completion_date'] ?? ''),
        ]);
    }

    public function delete_licence($id, $user_id)       { return $this->_delete('licences', $id, $user_id); }

    // ── SECTION: COURSES ──────────────────────────────────────

    public function add_course($user_id, array $data)
    {
        $errors = $this->_require(['course_name' => 'Course name', 'provider' => 'Provider'], $data);
        if ( ! empty($errors)) return ['errors' => $errors];

        return $this->_insert('courses', $user_id, [
            'course_name'     => $this->_sanitize($data['course_name']),
            'provider'        => $this->_sanitize($data['provider']),
            'course_url'      => $this->_clean_url($data['course_url'] ?? ''),
            'completion_date' => $this->_clean_date($data['completion_date'] ?? ''),
        ]);
    }

    public function update_course($id, $user_id, array $data)
    {
        $errors = $this->_require(['course_name' => 'Course name', 'provider' => 'Provider'], $data);
        if ( ! empty($errors)) return ['errors' => $errors];

        return $this->_update('courses', $id, $user_id, [
            'course_name'     => $this->_sanitize($data['course_name']),
            'provider'        => $this->_sanitize($data['provider']),
            'course_url'      => $this->_clean_url($data['course_url'] ?? ''),
            'completion_date' => $this->_clean_date($data['completion_date'] ?? ''),
        ]);
    }

    public function delete_course($id, $user_id)        { return $this->_delete('courses', $id, $user_id); }

    // ── SECTION: EMPLOYMENT ───────────────────────────────────

    public function add_employment($user_id, array $data)
    {
        // Employment requires custom chronological date validation
        $errors = $this->_validate_employment($data);
        if ( ! empty($errors)) return ['errors' => $errors];

        return $this->_insert('employment', $user_id, [
            'job_title'   => $this->_sanitize($data['job_title']),
            'company'     => $this->_sanitize($data['company']),
            'industry_sector' => $this->_sanitize($data['industry_sector']),
            'start_date'  => $this->_clean_date($data['start_date']),
            'end_date'    => $this->_clean_date($data['end_date'] ?? ''),
            'description' => $this->_sanitize($data['description'] ?? ''),
        ]);
    }

    public function update_employment($id, $user_id, array $data)
    {
        $errors = $this->_validate_employment($data);
        if ( ! empty($errors)) return ['errors' => $errors];

        return $this->_update('employment', $id, $user_id, [
            'job_title'   => $this->_sanitize($data['job_title']),
            'company'     => $this->_sanitize($data['company']),
            'industry_sector' => $this->_sanitize($data['industry_sector']),
            'start_date'  => $this->_clean_date($data['start_date']),
            'end_date'    => $this->_clean_date($data['end_date'] ?? ''),
            'description' => $this->_sanitize($data['description'] ?? ''),
        ]);
    }

    public function delete_employment($id, $user_id)    { return $this->_delete('employment', $id, $user_id); }

    // ── PROFILE COMPLETION METRICS ────────────────────────────

    /**
     * Gamification Algorithm: Profile Completion
     * Logic:
     * Calculates the percentage of the profile that is "complete" by mapping 
     * specific database conditions to a boolean array. It filters the array 
     * to perform the final mathematical calculation, providing feedback to the user 
     * on what they need to do next.
     */
    public function get_completion_status($user_id)
    {
        $profile = $this->db->where('user_id', $user_id)->get('profiles')->row();

        // Map expected milestones to boolean evaluation checks
        $checks = [
            'bio'           => $profile && ! empty($profile->bio),
            'linkedin_url'  => $profile && ! empty($profile->linkedin_url),
            'profile_image' => $profile && ! empty($profile->profile_image),
            'degree'        => $this->db->where('user_id', $user_id)->count_all_results('degrees') > 0,
            'certification' => $this->db->where('user_id', $user_id)->count_all_results('certifications') > 0,
            'licence'       => $this->db->where('user_id', $user_id)->count_all_results('licences') > 0,
            'course'        => $this->db->where('user_id', $user_id)->count_all_results('courses') > 0,
            'employment'    => $this->db->where('user_id', $user_id)->count_all_results('employment') > 0,
        ];

        $done    = count(array_filter($checks));
        $total   = count($checks);
        // Identify specifically which constraints failed
        $missing = array_keys(array_filter($checks, fn($v) => ! $v));

        return [
            'percentage' => round(($done / $total) * 100),
            'completed'  => $done,
            'total'      => $total,
            'missing'    => $missing,
        ];
    }

    // ── GENERIC GETTERS ───────────────────────────────────────

    /** Retrieve all normalized records for a user from a specific table. */
    public function get_section($user_id, $table)
    {
        return $this->db
            ->where('user_id', $user_id)
            ->order_by('created_at', 'ASC')
            ->get($table)
            ->result_array();
    }

    /** Retrieve a single record, bound securely to the requesting user_id. */
    public function get_single($id, $user_id, $table)
    {
        return $this->db
            ->where('id', $id)
            ->where('user_id', $user_id)
            ->get($table)
            ->row_array();
    }

    // ── DRY HELPERS & SECURITY ALGORITHMS ─────────────────────

    /** 
     * Generic Insert Factory
     * Abstracts standard insert boilerplate and applies timestamps uniformly.
     */
    private function _insert($table, $user_id, array $fields)
    {
        $fields['user_id']    = $user_id;
        $fields['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($table, $fields);
        $id = $this->db->insert_id();
        
        return $id ? ['id' => $id] : FALSE;
    }

    /**
     * Generic Update Factory — [IDOR Protection]
     * Security:
     * This method explicitly requires the $user_id. It guarantees that an attacker 
     * cannot guess another user's Record ID and modify it via an API call.
     */
    private function _update($table, $id, $user_id, array $fields)
    {
        // Establish Proof of Ownership
        $exists = $this->db->where('id', $id)->where('user_id', $user_id)->count_all_results($table);
        if ( ! $exists) return ['errors' => ['Record not found or access denied.']];

        $fields['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id)->where('user_id', $user_id)->update($table, $fields);
        
        return TRUE;
    }

    /**
     * Generic Delete Factory — [IDOR Protection]
     */
    private function _delete($table, $id, $user_id)
    {
        // Establish Proof of Ownership before destructive action
        $exists = $this->db->where('id', $id)->where('user_id', $user_id)->count_all_results($table);
        if ( ! $exists) return FALSE;

        $this->db->where('id', $id)->where('user_id', $user_id)->delete($table);
        return TRUE;
    }

    /** Data integrity check: ensures required payload fields are populated. */
    private function _require(array $fields, array $data)
    {
        $errors = [];
        foreach ($fields as $key => $label) {
            if (empty(trim($data[$key] ?? ''))) $errors[] = $label . ' is required.';
        }
        return $errors;
    }

    /** 
     * Employment Constraint Validator
     * Ensures temporal logic is sound (a job cannot end before it begins).
     */
    private function _validate_employment(array $data)
    {
        $errors = $this->_require([
            'job_title'  => 'Job title',
            'company'    => 'Company',
            'start_date' => 'Start date',
        ], $data);

        if ( ! empty($data['end_date']) && ! empty($data['start_date'])) {
            if (strtotime($data['end_date']) < strtotime($data['start_date'])) {
                $errors[] = 'End date cannot be before start date.';
            }
        }
        return $errors;
    }

    /** URL Formatter: Guarantees links are safely formatted. */
    private function _clean_url($url)
    {
        $url = trim($url);
        return (empty($url) || ! filter_var($url, FILTER_VALIDATE_URL)) ? NULL : $url;
    }

    /** Date Normalizer: Ensures all dates entering the database match the schema strictly. */
    private function _clean_date($date)
    {
        $date = trim($date);
        if (empty($date)) return NULL;
        $ts = strtotime($date);
        return $ts ? date('Y-m-d', $ts) : NULL;
    }

    /** 
     * Ultimate XSS Firewall
     * Strips dangerous HTML tags and encodes special characters before they are stored,
     * protecting the platform against Cross-Site Scripting payloads.
     */
    private function _sanitize($value)
    {
        return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
    }
}