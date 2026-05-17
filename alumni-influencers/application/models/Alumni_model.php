<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Alumni_model extends CI_Model {

    public function get_all_alumni($filters = []) {
        
        // ==========================================
        // PART 1: FILTERING THE USERS
        // ==========================================
        
        $this->db->select('users.id, users.first_name, users.last_name');
        $this->db->from('users');
        $this->db->where('users.role', 'alumnus');

        // 1. Filter by Programme and/or Graduation Date
        // Both of these live in the 'degrees' table, so we handle the JOIN together
        if (!empty($filters['programme']) || !empty($filters['graduation_date'])) {
            $this->db->join('degrees', 'degrees.user_id = users.id', 'left');
            
            if (!empty($filters['programme'])) {
                // Now filtering using your new 'programme' column
                $this->db->where('degrees.programme', $filters['programme']);
            }
            
            if (!empty($filters['graduation_date'])) {
                $this->db->like('degrees.completion_date', $filters['graduation_date'], 'after');
            }
        }

        // 2. Filter by Industry Sector
        // Now filtering by joining your 'employment' table
        if (!empty($filters['industry_sector'])) {
            $this->db->join('employment', 'employment.user_id = users.id', 'left');
            $this->db->where('employment.industry_sector', $filters['industry_sector']);
        }

        // Group by user ID to prevent duplicate user rows if they have multiple jobs/degrees
        $this->db->group_by('users.id');

        // Execute the filtered query
        $users = $this->db->get()->result_array();

        $alumni_data = [];

        // ==========================================
        // PART 2: FETCHING DETAILS FOR FILTERED USERS
        // ==========================================

        foreach ($users as $user) {
            $user_id = $user['id'];
            
            // Get Profile Data
            $this->db->select('bio, linkedin_url, profile_image');
            $this->db->where('user_id', $user_id);
            $profile = $this->db->get('profiles')->row_array();
            
            $user['bio'] = $profile ? $profile['bio'] : null;
            $user['linkedin_url'] = $profile ? $profile['linkedin_url'] : null;
            $user['profile_image'] = $profile ? $profile['profile_image'] : null;

            // Get Degrees
            // ADDED: selecting your new 'programme' column
            $this->db->select('degree_name, programme, completion_date');
            $this->db->where('user_id', $user_id);
            $user['degrees'] = $this->db->get('degrees')->result_array();

            // Get Certifications
            $this->db->select('cert_name, completion_date');
            $this->db->where('user_id', $user_id);
            $user['certifications'] = $this->db->get('certifications')->result_array();

            // Get Licences
            $this->db->select('licence_name');
            $this->db->where('user_id', $user_id);
            $user['licences'] = $this->db->get('licences')->result_array();

            // Get Courses
            $this->db->select('course_name, completion_date');
            $this->db->where('user_id', $user_id);
            $user['courses'] = $this->db->get('courses')->result_array();

            // Get Employment History
            // ADDED: selecting your new 'industry_sector' column
            $this->db->select('job_title, company, industry_sector, start_date, end_date');
            $this->db->where('user_id', $user_id);
            $this->db->order_by('start_date', 'DESC');
            $user['employment'] = $this->db->get('employment')->result_array();

            unset($user['id']);
            $alumni_data[] = $user;
        }

        return $alumni_data;
    }
}