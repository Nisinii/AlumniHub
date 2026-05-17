<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Bidding Controller
 * This is the core engine of the AR Alumni Platform's gamification system.
 * It manages the "Blind Auction" mechanics where alumni use virtual currency 
 * (or sponsor funds) to bid for the "Alumnus of the Day" AR spotlight.
 * 
 * Architecture Note (Client Agnostic):
 * Every endpoint (except `api_today`) requires authentication.
 * Controllers pass sanitized data to models, receive standardized arrays 
 * back (either success data or ['error' => 'msg']), and route them to `render()`.
 */
class Bidding extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Bidding_model');
        $this->load->library(['session', 'email']);
        $this->load->helper(['url', 'form']);

        /**
         * Security Algorithm: Global Lockdown with Exception
         * We lock down the entire controller by default to prevent accidental data leaks.
         * The ONLY exception is `api_today`, which must remain public so the AR Headset 
         * (and unauthenticated guests) can view today's winner.
         */
        if ($this->router->method !== 'api_today') {
            $this->authenticate();
        }
    }

    /**
     * GET /bidding
     * Assembles and serves the complex Bidding Dashboard.
     * Data Aggregation Logic:
     * To prevent massive, unreadable SQL joins, we query the model sequentially 
     * for 7 distinct operational states (active bids, monthly limits, wallet balance, etc.)
     * and compile them into a single state object for the frontend/API.
     */
    public function index()
    {
        $user_id  = $this->session->userdata('user_id');
        // Bids placed today are always for tomorrow's slot
        $bid_date = date('Y-m-d', strtotime('+1 day'));

        $data = [
            'active_bid'     => $this->Bidding_model->get_active_bid($user_id, $bid_date),
            'bid_status'     => $this->Bidding_model->get_bid_status($user_id),
            'monthly_status' => $this->Bidding_model->get_monthly_limit_status($user_id),
            'tomorrow_slot'  => $this->Bidding_model->get_tomorrows_slot(),
            'balance'        => $this->Bidding_model->get_available_balance($user_id),
            'appearance'     => $this->Bidding_model->get_appearance_count($user_id),
            'bidding_open'   => $this->Bidding_model->is_bidding_open(),
        ];

        return $this->render(200, 'Bidding dashboard retrieved.', 'bidding/dashboard', $data);
    }

    /**
     * GET/POST /bidding/place
     * Handles the creation of a new bid in the blind auction.
     * Business Logic:
     * Before placing a bid, the system must verify:
     * 1. The user has enough funds (Wallet Balance).
     * 2. The user hasn't exceeded their 3-win monthly limit (or 4, if they have an event bonus).
     * 3. The auction is actually open (before 6 PM).
     */
    public function place()
    {
        $user_id = $this->session->userdata('user_id');
        
        if ($this->input->method() === 'post') {
            $input = $this->_get_input();
            
            // The model executes the transaction and business rule checks
            $result = $this->Bidding_model->place_bid($user_id, $input['bid_amount'] ?? 0);

            // Error Handling: If the model rejects the bid (e.g., insufficient funds)
            if (isset($result['error'])) {
                return $this->render(422, $result['error'], 'bidding/place', [
                    'balance' => $this->Bidding_model->get_available_balance($user_id),
                    'monthly' => $this->Bidding_model->get_monthly_limit_status($user_id)
                ]);
            }

            return $this->render(201, 'Bid placed successfully.', '', $result, 'bidding');
        }

        // If GET request, render the submission form with required constraint data
        $data = [
            'balance'      => $this->Bidding_model->get_available_balance($user_id),
            'monthly'      => $this->Bidding_model->get_monthly_limit_status($user_id),
            'bidding_open' => $this->Bidding_model->is_bidding_open()
        ];
        return $this->render(200, 'Place bid form loaded.', 'bidding/place', $data);
    }

    /**
     * POST /bidding/update
     * Allows a user to increase their bid amount before the auction closes.
     */
    public function update()
    {
        if ($this->input->method() !== 'post') return $this->render(405, 'Method not allowed.');

        $input = $this->_get_input();
        $result = $this->Bidding_model->update_bid($this->session->userdata('user_id'), $input['new_amount'] ?? 0);

        if (isset($result['error'])) {
            return $this->render(422, $result['error'], '', [], 'bidding');
        }

        return $this->render(200, 'Bid increased successfully.', '', [], 'bidding');
    }

    /**
     * POST /bidding/cancel
     * Completely removes the user's active bid for tomorrow's slot.
     */
    public function cancel()
    {
        if ($this->input->method() !== 'post') return $this->render(405, 'Method not allowed.');

        $result = $this->Bidding_model->cancel_bid($this->session->userdata('user_id'));
        
        if (isset($result['error'])) {
            return $this->render(400, $result['error'], '', [], 'bidding');
        }

        return $this->render(200, 'Bid cancelled.', '', [], 'bidding');
    }

    /**
     * GET /bidding/history
     * Returns a chronological ledger of all bids placed by the user.
     */
    public function history()
    {
        $user_id = $this->session->userdata('user_id');
        $data = [
            'page_title' => 'Bid History',
            'history'    => $this->Bidding_model->get_bid_history($user_id)
        ];
        return $this->render(200, 'History retrieved.', 'bidding/history', $data);
    }

    // ── WEB & API: Sponsor Offers ─────────────────────────────

    public function sponsors()
    {
        $user_id = $this->session->userdata('user_id');
        $data = [
            'offers'  => $this->Bidding_model->get_sponsor_offers($user_id),
            'balance' => $this->Bidding_model->get_available_balance($user_id)
        ];
        return $this->render(200, 'Sponsor offers retrieved.', 'bidding/sponsor', $data);
    }

    public function accept_offer($id = NULL)
    {
        $result = $this->Bidding_model->accept_offer($id, $this->session->userdata('user_id'));
        if (isset($result['error'])) return $this->render(400, $result['error'], '', [], 'bidding/sponsor');
        return $this->render(200, 'Offer accepted. Balance updated.', '', [], 'bidding/sponsor');
    }

    public function decline_offer($id = NULL)
    {
        $result = $this->Bidding_model->decline_offer($id, $this->session->userdata('user_id'));
        if (isset($result['error'])) return $this->render(400, $result['error'], '', [], 'bidding/sponsor');
        return $this->render(200, 'Offer declined.', '', [], 'bidding/sponsor');
    }

    /**
     * POST /bidding/record_event
     * Unlocks a bonus 4th featured slot for the month.
     */
    public function record_event()
    {
        if ($this->input->method() !== 'post') return $this->render(405, 'Method not allowed.');

        $input = $this->_get_input();
        $result = $this->Bidding_model->record_event($this->session->userdata('user_id'), $input);

        if (isset($result['error'])) return $this->render(422, $result['error'], '', [], 'bidding');
        return $this->render(201, 'Event recorded! 4th slot unlocked this month.', '', [], 'bidding');
    }

    // ── PUBLIC API & LANDING PAGE ─────────────────────────────

    /**
     * GET /bidding/api_today
     * The Public Endpoint consumed by the AR Headset and the Web Landing Page.
     * Smart Rendering Algorithm:
     * This endpoint dynamically changes its output format based on the client:
     * - If Postman or the AR App requests it (via ?format=json or Accept headers), 
     * it returns a raw JSON payload of the winner's data.
     * - If a human opens it in Chrome, it bypasses the standard layout (no nav bar) 
     * and renders the standalone AR-themed HTML Landing Page.
     */
    public function api_today()
    {
        // $this->require_scope('read:alumni_of_the_day');
        $featured = $this->Bidding_model->get_todays_featured();

        if (!$featured) {
            return $this->render(200, 'Welcome to AR Alumni.', 'public/featured_landing');
        }

        $user_id = $featured['alumni']['id']; 

        $this->load->model('Profile_model');
        $full_data = $this->Profile_model->get_full_profile($user_id);

        $data = [
            'page_title' => 'Alumnus of the Day',
            'featured'   => $featured,
            'details'    => $full_data
        ];

        if ($this->input->get('format') === 'json' || $this->input->get_request_header('Accept') === 'application/json') {
            return $this->render(200, 'Featured alumnus retrieved.', '', $data);
        }

        $this->load->view('public/featured_landing', $data);
    }

    // ── SYSTEM TASKS ──────────────────────────────────────────

    /**
     * POST /bidding/run_midnight
     * The Cron Job / System trigger that evaluates the blind auction.
     * Business Logic:
     * 1. Closes the auction for the target date.
     * 2. Identifies the highest bidder (or defaults to the most recent winner).
     * 3. Deducts funds ONLY from the winner.
     * 4. Updates statuses (Won/Lost) and dispatches result emails.
     */
    public function run_midnight()
    {
        if ($this->input->method() !== 'post') return $this->render(405, 'Method not allowed.');

        $input = $this->_get_input();
        $date  = $input['date'] ?? date('Y-m-d');

        // The model handles the heavy SQL logic for winner selection and fund deduction
        $result = $this->Bidding_model->run_midnight_process($date);

        if (isset($result['error'])) {
            return $this->render(400, $result['error']);
        }

        // Dispatch emails to all participants (Winners and Losers)
        $this->_notify_bidders($date);

        return $this->render(200, 'Midnight selection complete. Winner processed and funds deducted.', '', $result);
    }

    // ── PRIVATE HELPERS ───────────────────────────────────────

    /** 
     * Parses incoming data from either JSON APIs or standard HTML form submissions. 
     */
    private function _get_input()
    {
        $json = json_decode($this->input->raw_input_stream, TRUE);
        return (json_last_error() === JSON_ERROR_NONE) ? $json : $this->input->post(NULL, TRUE);
    }

    /** 
     * Automated Email Dispatcher
     * Loops through all resolved bids for a given date and notifies the users.
     */
    private function _notify_bidders($date)
    {
        $bids = $this->db
            ->where('bid_date', $date)
            ->where_in('status', ['won', 'lost'])
            ->where('notified', 0) // Ensure we only notify once
            ->join('users', 'users.id = bids.user_id')
            ->select('bids.id as bid_id, bids.status, users.email, users.first_name')
            ->get('bids')
            ->result();

        foreach ($bids as $bid) {
            $subject = ($bid->status === 'won') ? '🏆 You won the featured slot!' : 'Bid result for ' . $date;
            
            $body = ($bid->status === 'won') 
                ? "<h2>Congratulations {$bid->first_name}!</h2><p>You have won the Blind Auction. You are Alumni of the Day for {$date}.</p>"
                : "<h2>Hi {$bid->first_name}</h2><p>Your bid was not the highest for {$date}. Better luck next time!</p>";
            
            $this->_mail($bid->email, $subject, $body);
            
            // Mark as notified to prevent duplicate emails
            $this->db->where('id', $bid->bid_id)->update('bids', ['notified' => 1]);
        }
    }

    /**
     * SMTP Email Wrapper
     * Abstracted function to handle email dispatch using environment variables.
     */
    private function _mail($to, $subject, $body)
    {
        // Load configurations dynamically from the .env file via getenv()
        $from_email = getenv('SMTP_FROM_EMAIL');
        $from_name  = trim(getenv('SMTP_FROM_NAME'), '"\''); 

        $this->email->from($from_email, $from_name);
        $this->email->to($to);
        $this->email->subject($subject);
        $this->email->message($body);
        
        return $this->email->send();
    }
}