<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ApiDocs Controller
 * * This controller dynamically generates and serves the API documentation using the OpenAPI 3.0 specification and Swagger UI.
 * Architecture Note:
 * This controller handles two main responsibilities:
 * 1. Serving the frontend UI (HTML/JS) via the index() method.
 * 2. Compiling and serving the raw OpenAPI JSON specification via the spec() method.
 * No authentication is required here, as the documentation itself is public.
 */
class ApiDocs extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
    }

    /**
     * GET /api-docs
     * Renders the interactive Swagger UI interface.
     * Logic:
     * Loads the standalone Swagger UI library from a CDN and configures it 
     * to point to our internal spec() endpoint. We inject custom CSS to 
     * match the brand identity (AR Alumni) instead of the default Swagger green.
     */
    public function index()
    {
        $spec_url = site_url('api-docs/spec');
        $back_url = site_url('');
        ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><path fill='%23629FAD' d='M12 0C12 6.627 17.373 12 24 12C17.373 12 12 17.373 12 24C12 17.373 6.627 12 0 12C6.627 12 12 6.627 12 0Z'/></svg>">
    <title>API Documentation - AR Alumni</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/5.11.0/swagger-ui.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* CSS variables for consistent AR Alumni branding */
        :root {
            --dark-blue: #0C2C55;
            --teal: #296374;
            --light-teal: #629FAD;
            --cream: #EDEDCE;
            --white: #ffffff;
            --border-light: #e5e7eb;
        }

        body { 
            margin: 0; 
            background: #fafafa; 
            font-family: 'Plus Jakarta Sans', sans-serif !important; 
        }

        /* Custom Top Navigation Bar */
        .custom-nav {
            background: var(--dark-blue);
            padding: 1rem 2.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .nav-brand {
            color: var(--white);
            font-weight: 800;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            letter-spacing: -0.02em;
        }
        .btn-back {
            background: rgba(255,255,255,0.1);
            color: var(--white);
            text-decoration: none;
            padding: 0.6rem 1.4rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            transition: all 0.2s ease;
            border: 1px solid rgba(255,255,255,0.2);
        }
        .btn-back:hover {
            background: var(--light-teal);
            border-color: var(--light-teal);
            transform: translateY(-1px);
        }

        /* Swagger UI Custom Overrides */
        .swagger-ui .topbar { display: none; } 
        
        .swagger-ui .info .title { 
            color: var(--dark-blue); 
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            letter-spacing: -0.03em;
        }

        .swagger-ui .info p, .swagger-ui .info li {
            color: #4b5563;
            line-height: 1.6;
        }

        /* Customizing the visual blocks for HTTP methods */
        .swagger-ui .opblock { border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); border: none !important; margin-bottom: 1rem; }
        
        .swagger-ui .opblock.opblock-post { background: rgba(41, 99, 116, 0.05); }
        .swagger-ui .opblock.opblock-post .opblock-summary-method { background: var(--teal); border-radius: 8px; font-weight: 700; }
        .swagger-ui .opblock.opblock-post .opblock-summary { border-bottom: 1px solid rgba(41, 99, 116, 0.1); }
        
        .swagger-ui .opblock.opblock-get { background: rgba(12, 44, 85, 0.05); }
        .swagger-ui .opblock.opblock-get .opblock-summary-method { background: var(--dark-blue); border-radius: 8px; font-weight: 700; }
        .swagger-ui .opblock.opblock-get .opblock-summary { border-bottom: 1px solid rgba(12, 44, 85, 0.1); }

        .swagger-ui .btn.execute {
            background-color: var(--teal);
            border-color: var(--teal);
            color: var(--white);
            border-radius: 50px;
            font-weight: 700;
            transition: all 0.2s;
        }
        .swagger-ui .btn.execute:hover { background-color: var(--dark-blue); }

        .swagger-ui section.models { border: 1px solid var(--border-light); border-radius: 12px; margin-top: 2rem; }
        .swagger-ui section.models.is-open h4 { border-bottom: 1px solid var(--border-light); }
        .swagger-ui table.model { margin-top: 15px !important; margin-bottom: 10px !important; }
        .swagger-ui .inner-object { padding-left: 20px !important; }

        @media (max-width: 640px) {
            .custom-nav { padding: 1rem; }
            .nav-brand span { display: none; }
        }
    </style>
</head>
<body>

<nav class="custom-nav">
    <div class="nav-brand">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--light-teal)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
            <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
        </svg>
        <span>AR Alumni Platform API</span>
    </div>
    <a href="<?= $back_url ?>" class="btn-back">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        Back
    </a>
</nav>

<div id="swagger-ui"></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/5.11.0/swagger-ui-bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/5.11.0/swagger-ui-standalone-preset.min.js"></script>
<script>
    window.onload = function() {
        const ui = SwaggerUIBundle({
            url: "<?= $spec_url ?>",
            dom_id: '#swagger-ui',
            presets: [
                SwaggerUIBundle.presets.apis,
                SwaggerUIStandalonePreset
            ],
            layout: "BaseLayout",
            deepLinking: true,
            displayRequestDuration: true,
            tryItOutEnabled: true,
            // CRITICAL ALGORITHM: Intercepts outgoing requests from the UI 
            // and forces the browser to attach the CodeIgniter Session Cookie.
            // Without this, testing protected endpoints in Swagger would fail with a 401.
            requestInterceptor: req => { 
                req.credentials = 'include'; 
                return req; 
            }
        });
        window.ui = ui;
    };
</script>
</body>
</html>
        <?php
    }

    /**
     * GET /api-docs/spec
     * Dynamically constructs and returns the OpenAPI 3.0 JSON specification.
     * Logic:
     * We define the root structure of the OpenAPI doc (Info, Servers, Tags).
     * The dynamic path definitions and component schemas are pulled from 
     * helper methods below to keep the code organized and modular.
     */
    public function spec()
    {
        // Force the browser to read the output as a strict JSON file
        header('Content-Type: application/json');
        
        $base = rtrim(base_url(), '/');

        $spec = [
            'openapi' => '3.0.0',
            'info'    => [
                'title'        => 'AR Alumni Platform API',
                'version'      => '1.0.0',
                'description'  => 
                    "REST API for the AR Alumni Platform by Phantasmagoria Ltd.\n\n" .
                    "**Authentication:** Call `POST /auth/login` first. The session cookie is sent automatically by your browser or Postman (ensure 'Include Cookies' is enabled).\n\n" .
                    "**Smart Rendering:** Endpoints return HTML for browsers and JSON for APIs. Swagger UI receives JSON automatically via the `Accept: application/json` header.\n\n" .
                    "**Bidding:** Blind auction — bids close at 6PM. Winner selected and profile activated at midnight.\n\n" .
                    "**Public endpoint:** `GET /bidding/api_today` — no login required (intended for the AR headset client).",
            ],
            'servers' => [['url' => $base, 'description' => 'Current Environment']],
            'tags'    => [
                ['name' => 'Auth',            'description' => 'Registration, login, email verification, password reset'],
                ['name' => 'Profile',         'description' => 'Alumni profile — all sections'],
                ['name' => 'Bidding',         'description' => 'Blind bidding system & dashboard'],
                ['name' => 'Sponsors',        'description' => 'Sponsor offer management'],
                ['name' => 'Featured Alumni', 'description' => 'Public AR client endpoint'],
                ['name' => 'Developer',       'description' => 'API Key Management (Requires Developer/Admin role)'],
                ['name' => 'System',          'description' => 'Cron / admin triggers'],
            ],
            'paths'      => $this->_paths(),
            'components' => [
                'schemas' => $this->_schemas(), 
                // Define the security scheme used across the protected APIs
                'securitySchemes' => [
                    'sessionCookie' => ['type' => 'apiKey', 'in' => 'cookie', 'name' => 'ci_session'],
                ]
            ],
        ];

        // Print the array as a deeply nested JSON object
        echo json_encode($spec, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Defines all API endpoints.
     * Uses the _op() factory function to minimize code duplication.
     */
    private function _paths()
    {
        // Security requirement array applied to protected routes
        $auth = [['sessionCookie' => []]];
        
        return [
            // AUTHENTICATION
            '/auth/register'                => ['post' => $this->_op('Auth','Register new alumni','RegisterRequest',['201'=>'RegisterResponse','422'=>'ValidationError'])],
            '/auth/verify/{token}'          => ['get'  => $this->_op('Auth','Verify email address',NULL,['200'=>NULL,'410'=>NULL,'404'=>NULL],[$this->_pp('token','Verification token')])],
            '/auth/resend_verification'     => ['post' => $this->_op('Auth','Resend verification email','EmailOnly',['200'=>NULL])],
            '/auth/login'                   => ['post' => $this->_op('Auth','Login','LoginRequest',['200'=>'LoginResponse','401'=>NULL,'429'=>NULL])],
            '/auth/logout'                  => ['post' => $this->_op('Auth','Logout',NULL,['200'=>NULL,'401'=>NULL],$auth)],
            '/auth/forgot_password'         => ['post' => $this->_op('Auth','Request password reset','EmailOnly',['200'=>'ForgotResponse'])],
            '/auth/reset_password/{token}'  => ['post' => $this->_op('Auth','Reset password','ResetPasswordRequest',['200'=>NULL,'410'=>NULL,'422'=>'ValidationError'],[$this->_pp('token','Reset token')])],

            // PROFILE
            '/profile/api'                  => ['get'  => $this->_op('Profile','Get full profile (Legacy API)',NULL,['200'=>'FullProfile','401'=>NULL],[],$auth)],
            '/profile/edit'                 => ['post' => $this->_op('Profile','Save bio and LinkedIn','SaveProfile',['200'=>NULL,'422'=>'ValidationError'],[],$auth)],
            '/profile/add/{section}'        => ['post' => $this->_op('Profile','Add section entry','SectionRequest',['201'=>'InsertResponse','422'=>'ValidationError'],[$this->_pp('section','Section name',['degrees','certifications','licences','courses','employment'])],$auth)],
            '/profile/edit_item/{section}/{id}'=> ['post' => $this->_op('Profile','Update section entry','SectionRequest',['200'=>NULL,'422'=>'ValidationError'],[$this->_pp('section','Section'),  $this->_pp('id','Entry ID')],$auth)],
            '/profile/delete/{section}/{id}'=> ['post' => $this->_op('Profile','Delete section entry',NULL,['200'=>NULL,'404'=>NULL],[$this->_pp('section','Section'), $this->_pp('id','Entry ID')],$auth)],

            // BIDDING & SPONSORS
            '/bidding'                      => ['get'  => $this->_op('Bidding','Get complete Bidding Dashboard',NULL,['200'=>'BiddingDashboard'],[],$auth)],
            '/bidding/place'                => ['post' => $this->_op('Bidding','Place a bid','PlaceBid',['201'=>'PlaceBidResponse','422'=>NULL],[],$auth)],
            '/bidding/update'               => ['post' => $this->_op('Bidding','Increase bid (only)','UpdateBid',['200'=>NULL,'422'=>NULL],[],$auth)],
            '/bidding/cancel'               => ['post' => $this->_op('Bidding','Cancel bid',NULL,['200'=>NULL,'400'=>NULL],[],$auth)],
            '/bidding/history'              => ['get'  => $this->_op('Bidding','Get bid history',NULL,['200'=>'BidHistory'],[],$auth)],
            '/bidding/sponsors'             => ['get'  => $this->_op('Sponsors','Get sponsor offers & balance',NULL,['200'=>'SponsorOffers'],[],$auth)],
            '/bidding/accept_offer/{id}'    => ['post' => $this->_op('Sponsors','Accept sponsor offer',NULL,['200'=>NULL,'400'=>NULL],[$this->_pp('id','Offer ID')],$auth)],
            '/bidding/decline_offer/{id}'   => ['post' => $this->_op('Sponsors','Decline sponsor offer',NULL,['200'=>NULL,'400'=>NULL],[$this->_pp('id','Offer ID')],$auth)],
            '/bidding/record_event'         => ['post' => $this->_op('Bidding','Record event attendance','RecordEvent',['201'=>NULL,'422'=>NULL],[],$auth)],
            
            // PUBLIC AR ENDPOINT
            '/bidding/api_today'            => ['get'  => $this->_op('Featured Alumni',"Get today's featured alumnus (PUBLIC)",NULL,['200'=>'FeaturedResponse','404'=>NULL])],
            
            // SYSTEM
            '/bidding/run_midnight'         => ['post' => $this->_op('System','Run midnight process (select winner + activate)','MidnightRequest',['200'=>'MidnightResponse','400'=>NULL],[],$auth)],

            // API KEYS (Developers Only)
            '/apikey'                       => ['get'  => $this->_op('Developer','Get API Keys Dashboard',NULL,['200'=>NULL,'403'=>NULL],[],$auth)],
            '/apikey/generate'              => ['post' => $this->_op('Developer','Generate new API Key','GenerateKeyRequest',['201'=>'GenerateKeyResponse','422'=>NULL,'403'=>NULL],[],$auth)],
            '/apikey/revoke/{id}'           => ['post' => $this->_op('Developer','Revoke API Key',NULL,['200'=>NULL,'422'=>NULL,'403'=>NULL],[$this->_pp('id','Key ID')],$auth)],
            '/apikey/stats/{id}'            => ['get'  => $this->_op('Developer','Get API Key Usage Stats',NULL,['200'=>NULL,'404'=>NULL,'403'=>NULL],[$this->_pp('id','Key ID')],$auth)],
        ];
    }

    /**
     * The Operation Factory Algorithm
     * * Complex Logic Explanation:
     * Writing pure OpenAPI JSON by hand requires massive, deeply nested arrays 
     * that are difficult to maintain. This algorithm acts as an abstraction layer.
     * * 1. It creates the base structure (tags, summary).
     * 2. It applies security rules dynamically if provided.
     * 3. It separates path parameters from the input array.
     * 4. Crucially, it dynamically generates the nested `$ref` pointers to link
     * the request/response schemas to the `#components/schemas` list automatically.
     * @param string $tag       The grouping category (e.g., 'Auth', 'Bidding')
     * @param string $summary   Short description of the endpoint
     * @param string $reqSchema The name of the Request Body schema (or NULL)
     * @param array  $responses Key-value pair of HTTP Codes to Response Schemas
     * @param array  $params    Optional array of Path/Query parameters
     * @param array  $security  Optional array requiring sessionCookie authentication
     * @return array            A perfectly formatted OpenAPI Operation Object
     */
    private function _op($tag, $summary, $reqSchema, $responses, $params = [], $security = [])
    {
        $op = ['tags' => [$tag], 'summary' => $summary];
        
        // Inject auth requirements if the route is protected
        if ( ! empty($security)) $op['security'] = $security;

        // Filter the incoming params array to isolate valid OpenAPI parameter definitions
        $path_params = array_filter($params, fn($p) => is_array($p) && isset($p['in']));
        if ( ! empty($path_params)) $op['parameters'] = array_values($path_params);

        // Dynamically build the deep requestBody reference tree
        if ($reqSchema) {
            $op['requestBody'] = ['required' => true, 'content' => [
                'application/json' => ['schema' => ['$ref' => '#/components/schemas/' . $reqSchema]],
            ]];
        }

        // Dynamically map HTTP status codes to their response definitions
        $op['responses'] = [];
        foreach ($responses as $code => $schema) {
            $op['responses'][(string)$code] = $schema
                ? ['description' => $schema, 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/' . $schema]]]]
                : ['description' => 'See summary or HTTP status'];
        }

        return $op;
    }

    /**
     * Helper to generate standardized Path Parameters
     * Used primarily for {id} or {token} variables in the URL string.
     */
    private function _pp($name, $desc, $enum = NULL)
    {
        $p = ['name' => $name, 'in' => 'path', 'required' => true, 'description' => $desc, 'schema' => ['type' => 'string']];
        if ($enum) $p['schema']['enum'] = $enum;
        return $p;
    }

    /**
     * Components & Schemas Dictionary
     * * Defines the exact data structures for every input and output in the application.
     * Swagger UI uses this to generate interactive form fields and display 
     * example JSON payloads for the developers to understand the data contracts.
     */
    private function _schemas()
    {
        return [
            // --- REQUEST SCHEMAS ---
            'RegisterRequest'    => ['type'=>'object','required'=>['first_name','last_name','email','password','confirm_password'],'properties'=>['first_name'=>['type'=>'string','example'=>'John'],'last_name'=>['type'=>'string','example'=>'Doe'],'email'=>['type'=>'string','example'=>'john@university.edu'],'password'=>['type'=>'string','example'=>'Test@1234'],'confirm_password'=>['type'=>'string','example'=>'Test@1234']]],
            'LoginRequest'       => ['type'=>'object','required'=>['email','password'],'properties'=>['email'=>['type'=>'string','example'=>'john@university.edu'],'password'=>['type'=>'string','example'=>'Test@1234']]],
            'EmailOnly'          => ['type'=>'object','required'=>['email'],'properties'=>['email'=>['type'=>'string','example'=>'john@university.edu']]],
            'ResetPasswordRequest'=> ['type'=>'object','required'=>['password','confirm_password'],'properties'=>['password'=>['type'=>'string','example'=>'NewPass@5678'],'confirm_password'=>['type'=>'string','example'=>'NewPass@5678']]],
            'SaveProfile'        => ['type'=>'object','properties'=>['bio'=>['type'=>'string','example'=>'Experienced software engineer.'],'linkedin_url'=>['type'=>'string','example'=>'https://linkedin.com/in/johndoe']]],
            'SectionRequest'     => ['type'=>'object','properties'=>['degree_name'=>['type'=>'string','example'=>'BSc Computer Science'],'institution'=>['type'=>'string','example'=>'University of Gothenburg'],'job_title'=>['type'=>'string','example'=>'Senior Developer'],'company'=>['type'=>'string','example'=>'Ericsson'],'start_date'=>['type'=>'string','example'=>'2020-01-15']]],
            'PlaceBid'           => ['type'=>'object','required'=>['bid_amount'],'properties'=>['bid_amount'=>['type'=>'number','example'=>250.00]]],
            'UpdateBid'          => ['type'=>'object','required'=>['new_amount'],'properties'=>['new_amount'=>['type'=>'number','example'=>300.00]]],
            'RecordEvent'        => ['type'=>'object','required'=>['event_name','event_date'],'properties'=>['event_name'=>['type'=>'string','example'=>'Career Fair 2026'],'event_date'=>['type'=>'string','example'=>'2026-04-01']]],
            'MidnightRequest'    => ['type'=>'object','properties'=>['date'=>['type'=>'string','example'=>date('Y-m-d')]]],
            'GenerateKeyRequest' => ['type'=>'object','required'=>['key_name','scope'],'properties'=>['key_name'=>['type'=>'string','example'=>'AR App'],'scope'=>['type'=>'string','example'=>'read']]],
            
            // --- RESPONSE SCHEMAS ---
            'RegisterResponse'   => ['type'=>'object','properties'=>['status'=>['type'=>'integer','example'=>201],'message'=>['type'=>'string']]],
            'LoginResponse'      => ['type'=>'object','properties'=>['status'=>['type'=>'integer','example'=>200],'user'=>['type'=>'object']]],
            'ForgotResponse'     => ['type'=>'object','properties'=>['status'=>['type'=>'integer','example'=>200],'message'=>['type'=>'string']]],
            'ValidationError'    => ['type'=>'object','properties'=>['status'=>['type'=>'integer','example'=>422],'errors'=>['type'=>'array','items'=>['type'=>'string']]]],
            'FullProfile'        => ['type'=>'object','properties'=>['status'=>['type'=>'integer'],'user'=>['type'=>'object'],'profile'=>['type'=>'object']]],
            'InsertResponse'     => ['type'=>'object','properties'=>['status'=>['type'=>'integer','example'=>201],'id'=>['type'=>'integer']]],
            'BiddingDashboard'   => ['type'=>'object','properties'=>['status'=>['type'=>'integer'],'balance'=>['type'=>'number']]],
            'BidHistory'         => ['type'=>'object','properties'=>['status'=>['type'=>'integer'],'history'=>['type'=>'array','items'=>['type'=>'object']]]],
            'SponsorOffers'      => ['type'=>'object','properties'=>['status'=>['type'=>'integer'],'balance'=>['type'=>'number']]],
            'FeaturedResponse'   => ['type'=>'object','properties'=>['status'=>['type'=>'integer','example'=>200],'featured'=>['type'=>'object'],'details'=>['type'=>'object']]],
            'GenerateKeyResponse'=> ['type'=>'object','properties'=>['status'=>['type'=>'integer','example'=>201],'key'=>['type'=>'string']]],
            'PlaceBidResponse'   => ['type'=>'object','properties'=>['status'=>['type'=>'integer','example'=>201],'bid_id'=>['type'=>'integer'],'bid_date'=>['type'=>'string']]],
            'MidnightResponse'   => ['type'=>'object','properties'=>['status'=>['type'=>'integer'],'winner_user_id'=>['type'=>'integer'],'feature_date'=>['type'=>'string'],'total_bids'=>['type'=>'integer']]],
        ];
    }
}