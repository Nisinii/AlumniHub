<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Bidding Model
 * The core algorithmic engine of the AR Alumni Platform.
 * Architectural Flow:
 * 1. Wallet: Alumni accept sponsor offers to build a virtual bidding balance.
 * 2. Auction: Alumni place blind bids for tomorrow's AR slot using their balance.
 * 3. Lockout: At 6 PM (BID_CLOSE_HOUR), the auction freezes.
 * 4. Resolution: At Midnight, the system uses an ACID transaction to crown the 
 * highest bidder, activate their AR profile, and deduct their funds via a waterfall algorithm.
 */
class Bidding_model extends CI_Model {

    // ── VIRTUAL WALLET & SPONSOR OFFERS ───────────────────────

    /** 
     * Get all sponsor offers for a user, newest first. 
     */
    public function get_sponsor_offers($user_id)
    {
        return $this->db
            ->where('user_id', $user_id)
            ->order_by('created_at', 'DESC')
            ->get('sponsor_offers')
            ->result_array();
    }

    /**
     * Accept a sponsor offer.
     * State Machine Logic: Only 'pending' offers can be accepted. Once accepted,
     * the funds are mathematically unlocked for the get_available_balance() calculation.
     */
    public function accept_offer($offer_id, $user_id)
    {
        $offer = $this->db->where('id', $offer_id)->where('user_id', $user_id)->get('sponsor_offers')->row();
        
        if ( ! $offer)                      return ['error' => 'Offer not found.'];
        if ($offer->status !== 'pending')   return ['error' => 'This offer has already been responded to.'];

        $this->db->where('id', $offer_id)->update('sponsor_offers', [
            'status' => 'accepted', 
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        return TRUE;
    }

    /** 
     * Decline a sponsor offer. 
     */
    public function decline_offer($offer_id, $user_id)
    {
        $offer = $this->db->where('id', $offer_id)->where('user_id', $user_id)->get('sponsor_offers')->row();
        
        if ( ! $offer)                      return ['error' => 'Offer not found.'];
        if ($offer->status !== 'pending')   return ['error' => 'This offer has already been responded to.'];

        $this->db->where('id', $offer_id)->update('sponsor_offers', [
            'status' => 'declined', 
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        return TRUE;
    }

    /**
     * Dynamic Wallet Calculation
     * Algorithm:
     * Instead of storing a static "balance" integer on the user table (which can fall out of sync), 
     * we dynamically aggregate the net balance on the fly. 
     * Balance = Sum of all (Accepted Offer Amounts - Amounts Already Spent).
     */
    public function get_available_balance($user_id)
    {
        $result = $this->db
            ->select('SUM(amount - spent_amount) as net_balance')
            ->where('user_id', $user_id)
            ->where('status', 'accepted')
            ->get('sponsor_offers')
            ->row();
            
        return (float) ($result->net_balance ?? 0);
    }

    // ── GAMIFICATION & MONTHLY LIMITS ─────────────────────────

    /**
     * Count how many times this user has WON a slot this calendar month.
     */
    public function get_monthly_win_count($user_id)
    {
        return $this->db
            ->where('user_id', $user_id)
            ->where('status', 'won')
            ->where('MONTH(bid_date)', date('n'))
            ->where('YEAR(bid_date)',  date('Y'))
            ->count_all_results('bids');
    }

    /** 
     * Event Verification: Checks if user attended a university event this month. 
     */
    public function has_event_this_month($user_id)
    {
        return $this->db
            ->where('user_id', $user_id)
            ->where('MONTH(event_date)', date('n'))
            ->where('YEAR(event_date)',  date('Y'))
            ->count_all_results('alumni_events') > 0;
    }

    /** 
     * Dynamic Constraint Resolution
     * Base limit is typically 3. If the user engages with the university (attends an event), 
     * the system dynamically expands their limit (e.g., +1 slot).
     */
    public function get_monthly_limit($user_id)
    {
        return MONTHLY_BID_LIMIT + ($this->has_event_this_month($user_id) ? EVENT_BONUS_SLOTS : 0);
    }

    /** 
     * Return full monthly status summary for the dashboard UI. 
     */
    public function get_monthly_limit_status($user_id)
    {
        $wins  = $this->get_monthly_win_count($user_id);
        $limit = $this->get_monthly_limit($user_id);
        return [
            'wins_this_month' => $wins,
            'monthly_limit'   => $limit,
            'remaining_slots' => max(0, $limit - $wins),
            'has_event_bonus' => $this->has_event_this_month($user_id),
            'limit_reached'   => $wins >= $limit,
            'month'           => date('F Y'),
        ];
    }

    // ── BLIND AUCTION MECHANICS ───────────────────────────────

    /**
     * Temporal Lockout
     * Bidding strictly closes at BID_CLOSE_HOUR (e.g., 6 PM).
     * This creates artificial scarcity and urgency in the gamification loop.
     */
    public function is_bidding_open()
    {
        return (int) date('H') < BID_CLOSE_HOUR;
    }

    /**
     * Place a new bid for tomorrow's featured slot.
     * Business Rule Enforcement:
     * 1. Temporal Check: Is the auction open?
     * 2. Financial Check: Does the user have enough sponsor balance?
     * 3. Constraint Check: Has the user maxed out their monthly appearances?
     * 4. Duplication Check: Does the user already have an active bid for tomorrow?
     */
    public function place_bid($user_id, $amount)
    {
        $amount = (float) $amount;

        if ( ! $this->is_bidding_open()) {
            return ['error' => 'Bidding is closed for today. It opens again tomorrow morning.'];
        }
        if ($amount <= 0) {
            return ['error' => 'Bid amount must be greater than £0.'];
        }

        $balance = $this->get_available_balance($user_id);
        if ($amount > $balance) {
            return ['error' => "Bid (£{$amount}) exceeds your available sponsor balance (£{$balance})."];
        }

        if ($this->get_monthly_win_count($user_id) >= $this->get_monthly_limit($user_id)) {
            return ['error' => 'You have reached your monthly limit of ' . $this->get_monthly_limit($user_id) . ' featured slots.'];
        }

        // Target Date Injection: Bids placed today are explicitly locked to tomorrow's date
        $bid_date = date('Y-m-d', strtotime('+1 day'));

        if ($this->get_active_bid($user_id, $bid_date)) {
            return ['error' => 'You already have a bid for tomorrow. Use the update endpoint to increase it.'];
        }

        $this->db->insert('bids', [
            'user_id'    => $user_id,
            'bid_amount' => $amount,
            'bid_date'   => $bid_date,
            'status'     => 'active',
            'is_winner'  => 0,
            'notified'   => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return ['bid_id' => $this->db->insert_id(), 'bid_date' => $bid_date];
    }

    /**
     * Increase an existing bid.
     * Gamification Rule: Bids can only go UP. You cannot lower your bid to game the system.
     */
    public function update_bid($user_id, $new_amount)
    {
        $new_amount = (float) $new_amount;

        if ( ! $this->is_bidding_open()) {
            return ['error' => 'Bidding is closed. You cannot update your bid after 6PM.'];
        }

        $bid_date = date('Y-m-d', strtotime('+1 day'));
        $bid      = $this->get_active_bid($user_id, $bid_date);

        if ( ! $bid) {
            return ['error' => 'No active bid found for tomorrow. Place a bid first.'];
        }

        // Strict enforcement of the "Increase Only" rule
        if ($new_amount <= (float) $bid->bid_amount) {
            return ['error' => "New amount (£{$new_amount}) must be higher than your current bid (£{$bid->bid_amount})."];
        }

        $balance = $this->get_available_balance($user_id);
        if ($new_amount > $balance) {
            return ['error' => "New amount (£{$new_amount}) exceeds your available balance (£{$balance})."];
        }

        $this->db->where('id', $bid->id)->update('bids', [
            'bid_amount' => $new_amount,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return TRUE;
    }

    /** Cancel an active bid (Allowed only before the 6PM lockout). */
    public function cancel_bid($user_id)
    {
        if ( ! $this->is_bidding_open()) {
            return ['error' => 'Bidding is closed. You cannot cancel after 6PM.'];
        }

        $bid_date = date('Y-m-d', strtotime('+1 day'));
        $bid      = $this->get_active_bid($user_id, $bid_date);

        if ( ! $bid) return ['error' => 'No active bid found for tomorrow.'];

        $this->db->where('id', $bid->id)->update('bids', [
            'status'     => 'cancelled',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return TRUE;
    }

    /**
     * Get bid status for a user — Data Isolation Rule.
     * Logic:
     * To maintain a true "Blind Auction", this method determines if the user is currently 
     * winning, but strictly suppresses the actual highest bid value. The frontend 
     * never receives the top amount, making it impossible to scrape.
     */
    public function get_bid_status($user_id)
    {
        $bid_date = date('Y-m-d', strtotime('+1 day'));
        $bid      = $this->get_active_bid($user_id, $bid_date);

        if ( ! $bid) {
            return [
                'has_bid'      => FALSE,
                'bidding_open' => $this->is_bidding_open(),
                'message'      => 'You have not placed a bid for tomorrow yet.',
            ];
        }

        // Secure internal call. The raw $highest variable is never passed to the return array.
        $highest    = $this->_get_highest_bid_amount($bid_date);
        $is_winning = ((float) $bid->bid_amount >= (float) $highest);

        return [
            'has_bid'      => TRUE,
            'bid_date'     => $bid_date,
            'your_amount'  => (float) $bid->bid_amount,
            'bidding_open' => $this->is_bidding_open(),
            'status'       => $is_winning ? 'winning' : 'losing',
            'message'      => $is_winning
                ? 'You are currently winning! Winner selected at midnight.'
                : 'You are not currently winning. Consider increasing your bid before 6PM.',
        ];
    }

    // ── MIDNIGHT RESOLUTION & CRON LOGIC ──────────────────────

    /**
     * The Midnight Selection Process
     * Complex Logic & ACID Compliance:
     * This function resolves the blind auction. It uses $this->db->trans_start() to ensure 
     * ACID (Atomicity, Consistency, Isolation, Durability) database compliance. 
     * If the fund deduction fails halfway through, the entire process rolls back, 
     * preventing database corruption where a user wins but doesn't "pay".
     */
    public function run_midnight_process($date = NULL)
    {
        $date = $date ?? date('Y-m-d');

        // 1. Idempotency Check: Ensure this script can't run twice for the same date
        $already = $this->db->where('feature_date', $date)->get('featured_alumni')->row();
        if ($already) return ['error' => "Midnight process already ran for {$date}."];

        // 2. Aggregate all active bids for the target date, sorting by highest amount first.
        // Secondary sort by created_at ensures the earlier bidder wins in a tie.
        $bids = $this->db
            ->where('bid_date', $date)
            ->where('status', 'active')
            ->order_by('bid_amount', 'DESC')
            ->order_by('created_at', 'ASC')
            ->get('bids')
            ->result();

        if (empty($bids)) return ['error' => "No active bids for {$date}."];

        $winner = $bids[0];

        // --- START ACID TRANSACTION ---
        $this->db->trans_start(); 

        // 3. Update Bidding Ledger
        $this->db->where('id', $winner->id)->update('bids', ['status' => 'won', 'is_winner' => 1]);
        
        $loser_ids = array_slice(array_column($bids, 'id'), 1);
        if (!empty($loser_ids)) {
            $this->db->where_in('id', $loser_ids)->update('bids', ['status' => 'lost']);
        }

        // 4. Activate Profile in the featured_alumni table (Making it visible to the AR API)
        $this->db->insert('featured_alumni', [
            'user_id'      => $winner->user_id,
            'bid_id'       => $winner->id,
            'feature_date' => $date,
            'activated_at' => date('Y-m-d H:i:s'),
        ]);

        // 5. Financial Settlement: Run the Waterfall Deduction Algorithm
        $this->deduct_bid_from_offers($winner->user_id, (float)$winner->bid_amount);

        // --- COMMIT ACID TRANSACTION ---
        $this->db->trans_complete(); 

        // Fail-safe check
        if ($this->db->trans_status() === FALSE) {
            return ['error' => 'Database transaction failed during midnight resolution.'];
        }

        return [
            'success'        => TRUE,
            'winner_user_id' => $winner->user_id,
            'bid_amount'     => $winner->bid_amount, 
            'feature_date'   => $date,
            'total_bids'     => count($bids),
        ];
    }

    /**
     * Financial Ledger "Waterfall" Deduction Algorithm
     * Algorithm Explanation:
     * A user's total balance is often made up of multiple smaller sponsor offers.
     * This algorithm iterates through the user's available offers chronologically (oldest first).
     * It deducts funds from the first "bucket" until it's empty, then "spills over" to 
     * the next bucket, repeating until the total bid amount is fully paid off.
     */
    public function deduct_bid_from_offers($user_id, $amount_to_deduct)
    {
        $offers = $this->db
            ->where('user_id', $user_id)
            ->where('status', 'accepted')
            ->where('spent_amount < amount') // Only grab offers with remaining funds
            ->order_by('created_at', 'ASC')  // Spend oldest funds first
            ->get('sponsor_offers')
            ->result();

        foreach ($offers as $offer) {
            // Break the loop early if the debt is fully settled
            if ($amount_to_deduct <= 0) break;

            $available = $offer->amount - $offer->spent_amount;
            
            // Determine if we empty this bucket, or just take a fraction
            $take = min($available, $amount_to_deduct);

            $this->db->where('id', $offer->id)->update('sponsor_offers', [
                'spent_amount' => $offer->spent_amount + $take,
                'updated_at'   => date('Y-m-d H:i:s')
            ]);

            // Reduce the remaining debt
            $amount_to_deduct -= $take;
        }
    }

    // ── PUBLIC API AGGREGATION ────────────────────────────────

    /**
     * Master Profile Aggregator (For Public API)
     * Logic:
     * Used exclusively by the AR client. It cross-references the featured_alumni table 
     * with the current date. If a winner is found, it hydrates a massive, deeply nested 
     * array containing their entire 3NF database profile.
     */
    public function get_todays_featured()
    {
        $today    = date('Y-m-d');
        $featured = $this->db
            ->where('fa.feature_date', $today)
            ->where('fa.activated_at IS NOT NULL', NULL, FALSE)
            ->join('users u',    'u.id = fa.user_id')
            ->join('profiles p', 'p.user_id = fa.user_id', 'left')
            ->select('fa.feature_date, fa.activated_at, u.id as user_id, u.first_name, u.last_name, u.email, p.bio, p.linkedin_url, p.profile_image')
            ->get('featured_alumni fa')
            ->row_array();

        if ( ! $featured) return NULL;

        $uid = $featured['user_id'];
        
        // Assemble the JSON-ready DTO (Data Transfer Object)
        return [
            'feature_date'  => $featured['feature_date'],
            'activated_at'  => $featured['activated_at'],
            'alumni' => [
                'id'             => $uid,
                'name'           => $featured['first_name'] . ' ' . $featured['last_name'],
                'email'          => $featured['email'],
                'bio'            => $featured['bio'],
                'linkedin_url'   => $featured['linkedin_url'],
                'profile_image'  => $featured['profile_image']
                    ? base_url('uploads/profiles/' . $featured['profile_image']) : NULL,
                'degrees'        => $this->_get_section($uid, 'degrees'),
                'certifications' => $this->_get_section($uid, 'certifications'),
                'licences'       => $this->_get_section($uid, 'licences'),
                'courses'        => $this->_get_section($uid, 'courses'),
                'employment'     => $this->_get_section($uid, 'employment'),
            ],
        ];
    }

    // ── GENERAL DASHBOARD HELPERS ─────────────────────────────

    /** Get all bids for a user, newest first. */
    public function get_bid_history($user_id)
    {
        return $this->db
            ->where('user_id', $user_id)
            ->order_by('bid_date', 'DESC')
            ->get('bids')
            ->result_array();
    }

    /** 
     * Get metadata for tomorrow's auction (Total competitors, timestamps). 
     */
    public function get_tomorrows_slot()
    {
        $bid_date = date('Y-m-d', strtotime('+1 day'));
        return [
            'slot_date'      => $bid_date,
            'total_bids'     => $this->db->where('bid_date', $bid_date)->where('status', 'active')->count_all_results('bids'),
            'bidding_open'   => $this->is_bidding_open(),
            'closes_at'      => '18:00 (6PM today)',
            'winner_at'      => '00:00 (Midnight)',
        ];
    }

    // ── EVENT ATTENDANCE ──────────────────────────────────────

    /** Record attendance at a university event (grants 4th slot this month). */
    public function record_event($user_id, array $data)
    {
        if (empty($data['event_name'])) return ['error' => 'Event name is required.'];
        if (empty($data['event_date'])) return ['error' => 'Event date is required.'];

        $this->db->insert('alumni_events', [
            'user_id'    => $user_id,
            // XSS Protection on textual input
            'event_name' => htmlspecialchars(strip_tags(trim($data['event_name'])), ENT_QUOTES, 'UTF-8'),
            'event_date' => date('Y-m-d', strtotime($data['event_date'])),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return TRUE;
    }

    /** Get all event attendances for a user. */
    public function get_events($user_id)
    {
        return $this->db->where('user_id', $user_id)->order_by('event_date', 'DESC')->get('alumni_events')->result_array();
    }

    // ── PRIVATE DB HELPERS ────────────────────────────────────

    /** Get the user's active bid for a given date. */
    public function get_active_bid($user_id, $bid_date)
    {
        return $this->db
            ->where('user_id', $user_id)
            ->where('bid_date', $bid_date)
            ->where('status', 'active')
            ->get('bids')
            ->row();
    }

    /** Appearance stats for the frontend dashboard widget. */
    public function get_appearance_count($user_id)
    {
        return [
            'this_month' => $this->get_monthly_win_count($user_id),
            'all_time'   => $this->db->where('user_id', $user_id)->where('status', 'won')->count_all_results('bids'),
        ];
    }

    /**
     * Get the highest current bid for a date.
     * PRIVATE ENFORCEMENT: By explicitly scoping this as `private`, we mathematically 
     * guarantee that no frontend controller can accidentally expose the winning bid amount.
     */
    private function _get_highest_bid_amount($bid_date)
    {
        $result = $this->db
            ->select_max('bid_amount')
            ->where('bid_date', $bid_date)
            ->where('status', 'active')
            ->get('bids')
            ->row();
        return $result->bid_amount ?? 0;
    }

    /** Helper to grab sub-tables for the 3NF profile aggregation. */
    private function _get_section($user_id, $table)
    {
        return $this->db->where('user_id', $user_id)->get($table)->result_array();
    }

}