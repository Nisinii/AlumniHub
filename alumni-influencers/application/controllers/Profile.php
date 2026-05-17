<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Profile Controller
 * Manages the CRUD (Create, Read, Update, Delete) operations for a user's 
 * comprehensive profile, including degrees, certifications, licences, courses, and employment.
 * Architecture Note:
 * This controller is completely protected by session authentication. It employs a 
 * "Dynamic Method Routing" algorithm to handle multiple profile sections through 
 * a single unified set of endpoints, ensuring DRY (Don't Repeat Yourself) code principles.
 */
class Profile extends MY_Controller {

    /**
     * Dynamic Method Routing Map
     * Algorithm Explanation:
     * Instead of writing 15 separate methods (e.g., add_degree(), add_course(), delete_licence()), 
     * this map acts as a translation layer. It takes a URL parameter (like 'degrees') 
     * and dynamically routes the request to the correct corresponding model method.
     */
    private $section_map = [
        'degrees'        => ['add' => 'add_degree',        'update' => 'update_degree',        'delete' => 'delete_degree'],
        'certifications' => ['add' => 'add_certification', 'update' => 'update_certification', 'delete' => 'delete_certification'],
        'licences'       => ['add' => 'add_licence',       'update' => 'update_licence',       'delete' => 'delete_licence'],
        'courses'        => ['add' => 'add_course',        'update' => 'update_course',        'delete' => 'delete_course'],
        'employment'     => ['add' => 'add_employment',    'update' => 'update_employment',    'delete' => 'delete_employment'],
    ];

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Profile_model');
        $this->load->library(['upload']);
        $this->load->helper(['url', 'form', 'security']);

        // [Security Constraint]
        // Every single method in this controller requires a valid, active session.
        $this->authenticate(); 
    }

    /**
     * GET /profile
     * Compiles and retrieves the user's comprehensive profile dashboard.
     * Logic:
     * Pulls data from 6 different database tables, aggregates it into a single 
     * nested array structure, and passes it to the smart render() engine.
     */
    public function index()
    {
        $user_id = $this->session->userdata('user_id');
        $data = $this->Profile_model->get_full_profile($user_id);
        $data['page_title'] = 'My Profile';

        // Content Negotiation: Automatically serves the HTML view for browsers 
        // or a raw JSON payload for API clients (Postman/AR Headset).
        return $this->render(200, 'Profile retrieved.', 'profile/index', $data);
    }

    /**
     * GET/POST /profile/edit
     * Handles the editing of the core profile (Bio, LinkedIn) and Profile Image uploads.
     */
    public function edit()
    {
        $user_id = $this->session->userdata('user_id');
        $profile = $this->db->where('user_id', $user_id)->get('profiles')->row();
        $data = ['page_title' => 'Edit Profile', 'profile' => $profile];

        if ($this->input->method() === 'post') {
            // Client-agnostic input parser to support both Form Data and raw JSON
            $input = $this->_get_input();
            
            $post = [
                'bio'          => $input['bio'] ?? '',
                'linkedin_url' => $input['linkedin_url'] ?? '',
            ];

            // Image Upload Handling Workflow
            if (!empty($_FILES['profile_image']['name'])) {
                $image = $this->_upload_image($user_id);
                
                // If _upload_image returns an array, it signifies a validation error 
                // (e.g., file too large, wrong MIME type). We abort and display the error.
                if (is_array($image)) {
                    return $this->render(400, $image[0], 'profile/edit', $data);
                }
                
                // Attach the successful filename to our database update payload
                $post['profile_image'] = $image;
            }

            // Execute the database update with strict XSS sanitization
            $result = $this->Profile_model->save_profile($user_id, xss_clean($post));

            if ($result === TRUE) {
                // Re-fetch the updated profile data to ensure the view displays the freshest state
                $data['profile'] = $this->db->where('user_id', $user_id)->get('profiles')->row();
                return $this->render(200, 'Profile updated successfully.', 'profile/edit', $data, 'profile');
            } else {
                return $this->render(422, 'Validation failed.', 'profile/edit', array_merge($data, ['errors' => $result['errors']]));
            }
        }

        // Handle standard GET request to view the form
        return $this->render(200, 'Edit form loaded.', 'profile/edit', $data);
    }
    
    /**
     * POST /profile/add/{section}
     * Dynamically adds a new entry to a specific profile section.
     * Routing Logic:
     * Uses the $section_map array to translate the URL parameter (e.g., 'degrees') 
     * into the corresponding model method (e.g., 'add_degree').
     */
    public function add($section = NULL)
    {
        // Validation: Ensure the requested section actually exists in our mapping
        if (!isset($this->section_map[$section])) {
            return $this->render(404, 'Unknown section.');
        }

        $user_id = $this->session->userdata('user_id');
        $data = ['page_title' => 'Add ' . ucfirst($section), 'section' => $section, 'item' => NULL];

        if ($this->input->method() === 'post') {
            // Dynamically assign the target method based on the dictionary map
            $method = $this->section_map[$section]['add'];
            $input = $this->_get_input();
            
            // Execute the dynamically mapped method on the model
            $result = $this->Profile_model->$method($user_id, xss_clean($input));

            if (isset($result['errors'])) {
                return $this->render(422, 'Validation failed.', 'profile/section_form', array_merge($data, ['errors' => $result['errors']]));
            }
            
            return $this->render(201, ucfirst($section) . ' added.', '', [], 'profile');
        }

        return $this->render(200, 'Form loaded.', 'profile/section_form', $data);
    }

    /**
     * POST /profile/edit_item/{section}/{id}
     * Dynamically updates a specific entry within a profile section.
     */
    public function edit_item($section = NULL, $id = NULL)
    {
        if (!isset($this->section_map[$section]) || !$id) {
            return $this->render(404, 'Invalid request.');
        }

        $user_id = $this->session->userdata('user_id');
        
        // Security: The get_single method ensures the user_id matches the owner of the record,
        // preventing Insecure Direct Object Reference (IDOR) vulnerabilities.
        $item = $this->Profile_model->get_single($id, $user_id, $section);
        if (!$item) return $this->render(404, 'Item not found or unauthorized.');

        $data = ['page_title' => 'Edit ' . ucfirst($section), 'section' => $section, 'item' => $item, 'id' => $id];

        if ($this->input->method() === 'post') {
            $method = $this->section_map[$section]['update'];
            $input = $this->_get_input();
            $result = $this->Profile_model->$method($id, $user_id, xss_clean($input));

            if (isset($result['errors'])) {
                return $this->render(422, 'Validation failed.', 'profile/section_form', array_merge($data, ['errors' => $result['errors']]));
            }

            return $this->render(200, ucfirst($section) . ' updated.', '', [], 'profile');
        }

        return $this->render(200, 'Edit form loaded.', 'profile/section_form', $data);
    }

    /**
     * POST /profile/delete/{section}/{id}
     * Dynamically removes an entry from a profile section.
     */
    public function delete($section = NULL, $id = NULL)
    {
        if (!isset($this->section_map[$section]) || !$id) return $this->render(404, 'Invalid request.');

        $user_id = $this->session->userdata('user_id');
        $method  = $this->section_map[$section]['delete'];
        
        // The model method inherently checks user ownership before deleting
        $result  = $this->Profile_model->$method($id, $user_id);

        if ($result) {
            return $this->render(200, ucfirst($section) . ' deleted.', '', [], 'profile');
        }
        return $this->render(404, 'Could not delete item. It may not exist or you lack permission.');
    }

    /**
     * GET /profile/api
     * Legacy support endpoint to fetch the full JSON profile.
     * Maintained for backward compatibility if an older frontend client hardcodes this route.
     */
    public function api()
    {
        $data = $this->Profile_model->get_full_profile($this->session->userdata('user_id'));
        $this->output->set_content_type('application/json');
        echo json_encode(['status' => 200, 'message' => 'Profile retrieved.', 'data' => $data], JSON_PRETTY_PRINT);
    }

    // ── PRIVATE HELPERS ───────────────────────────────────────

    /**
     * Client Agnostic Input Parser
     */
    private function _get_input()
    {
        $json = json_decode($this->input->raw_input_stream, TRUE);
        return (json_last_error() === JSON_ERROR_NONE) ? $json : $this->input->post(NULL, TRUE);
    }

    /**
     * File Upload Handling Algorithm
     * Logic:
     * Checks if the upload directory exists, establishes constraints (size/type),
     * and renames the file using a safe, randomized timestamp convention to prevent 
     * collisions or malicious script execution.
     * * @return string|array The new filename string on success, or an array with an error message on failure.
     */
    private function _upload_image($user_id)
    {
        if (empty($_FILES['profile_image']['name'])) return NULL;

        $path = FCPATH . 'uploads/profiles/';
        // Ensure directory exists, create if not
        if (!is_dir($path)) mkdir($path, 0755, TRUE);

        $this->upload->initialize([
            'upload_path'   => $path,
            'allowed_types' => 'jpg|jpeg|png|webp',
            'max_size'      => 2048, // Limit size to 2MB to prevent storage exhaustion
            // Standardize the filename format: user_ID_TIMESTAMP.ext
            'file_name'     => 'user_' . $user_id . '_' . time(),
            'overwrite'     => TRUE,
        ]);

        if (!$this->upload->do_upload('profile_image')) {
            // Return error string encapsulated in an array to signal failure
            return [$this->upload->display_errors('', '')];
        }
        
        return $this->upload->data('file_name');
    }
}