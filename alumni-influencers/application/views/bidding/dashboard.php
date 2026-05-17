<?php if ( ! empty($errors)): ?>
    <div class="alert alert-danger"><ul><?php foreach ($errors as $e): ?><li><?= $e ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<div class="card">
    <h2 style="display: flex; align-items: center; gap: 0.5rem;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--teal)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
        Monthly Status — <?= $monthly_status['month'] ?>
    </h2>
    <div style="display: flex; gap: 2.5rem; align-items: center; flex-wrap: wrap;">
        <div style="text-align: center; min-width: 120px;">
            <div style="font-size: 2.5rem; font-weight: 800; color: <?= $monthly_status['limit_reached'] ? 'var(--error-text)' : 'var(--teal)' ?>; line-height: 1;">
                <?= $monthly_status['wins_this_month'] ?>/<?= $monthly_status['monthly_limit'] ?>
            </div>
            <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Slots Used</div>
        </div>
        <div style="flex: 1; min-width: 250px;">
            <div class="completion-bar">
                <div class="completion-fill" style="width: <?= $monthly_status['monthly_limit'] > 0 ? ($monthly_status['wins_this_month']/$monthly_status['monthly_limit'])*100 : 0 ?>%; background: <?= $monthly_status['limit_reached'] ? 'var(--error-text)' : 'var(--teal)' ?>;"></div>
            </div>
            <p style="font-size: 0.9rem; color: var(--text-muted); margin-top: 0.5rem; font-weight: 500; display: flex; align-items: center; gap: 0.5rem;">
                <strong><?= $monthly_status['remaining_slots'] ?></strong> slot(s) remaining
                <?php if ($monthly_status['has_event_bonus']): ?> 
                    <span style="color: var(--success-text); margin-left: 0.5rem; font-weight: 600; display: flex; align-items: center; gap: 0.25rem;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>
                        Event bonus active (+1)
                    </span>
                <?php endif; ?>
            </p>
        </div>
    </div>
</div>

<div class="card">
    <div class="section-header">
        <h2 style="display: flex; align-items: center; gap: 0.5rem;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--teal)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            Tomorrow's Slot <?= $tomorrow_slot['slot_date'] ?>
        </h2>
        <span class="badge" style="background: rgba(98, 159, 173, 0.1); color: var(--dark-blue);">
            Closes <?= $tomorrow_slot['closes_at'] ?> · Winner at <?= $tomorrow_slot['winner_at'] ?>
        </span>
    </div>

    <?php if ($bid_status['has_bid']): ?>
        <div style="background: <?= $bid_status['status'] === 'winning' ? 'var(--success-bg)' : '#fffbeb' ?>; border: 1px solid <?= $bid_status['status'] === 'winning' ? 'rgba(22, 101, 52, 0.1)' : 'rgba(180, 83, 9, 0.1)' ?>; border-radius: 16px; padding: 1.5rem; margin-bottom: 1.5rem;">
            <div style="font-size: 1.25rem; font-weight: 800; color: <?= $bid_status['status'] === 'winning' ? 'var(--success-text)' : '#b45309' ?>; display: flex; align-items: center; gap: 0.5rem;">
                <?php if ($bid_status['status'] === 'winning'): ?>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path><path d="M4 22h16"></path><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"></path><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"></path><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"></path></svg>
                    Currently Winning
                <?php else: ?>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    Currently Losing
                <?php endif; ?>
            </div>
            <p style="margin-top: 0.5rem; color: var(--text-main); font-size: 0.95rem; font-weight: 500;"><?= $bid_status['message'] ?></p>
            <div style="background: rgba(255,255,255,0.5); padding: 0.75rem 1rem; border-radius: 8px; margin-top: 1rem; display: inline-block;">
                <p style="font-size: 0.85rem; color: var(--text-muted); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                    Your bid: <strong style="color: var(--dark-blue); font-size: 1rem;">£<?= number_format($bid_status['your_amount'], 2) ?></strong> 
                    <span style="margin: 0 0.5rem;">|</span> 
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    Total bids: <strong style="color: var(--dark-blue);"><?= $tomorrow_slot['total_bids'] ?></strong>
                </p>
            </div>
        </div>

        <?php if ($bidding_open): ?>
        <div style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end; background: #f9fafb; padding: 1.5rem; border-radius: 16px; border: 1px solid var(--border-light);">
            <?= form_open('bidding/update', ['style'=>'display: flex; gap: 0.8rem; align-items: flex-end; flex-wrap: wrap; flex: 1;']) ?>
                <div style="flex: 1; min-width: 150px;">
                    <label style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.3rem;">
                        Increase Bid to (£)
                    </label>
                    <input type="number" name="new_amount" step="0.01" min="<?= $bid_status['your_amount'] + 0.01 ?>" max="<?= $balance ?>" placeholder="Higher amount" style="margin-bottom: 0; width: 100%;">
                </div>
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 1.5rem;">Increase Bid</button>
            <?= form_close() ?>
            
            <?= form_open('bidding/cancel') ?>
                <button type="submit" class="btn btn-danger" style="padding: 0.75rem 1.5rem; background: transparent; color: var(--error-text); border: 2px solid var(--error-text);" onclick="return confirm('Cancel your bid?')">Cancel Bid</button>
            <?= form_close() ?>
        </div>
        <?php else: ?>
            <div class="alert alert-info" style="margin-bottom: 0; display: flex; align-items: center; gap: 0.5rem;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                <span><strong>Bidding is closed.</strong> Winner will be activated at midnight.</span>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <?php if ($bidding_open && ! $monthly_status['limit_reached']): ?>
            <div class="empty" style="text-align: left; background: #f9fafb; padding: 2rem; border: 2px dashed var(--border-light);">
                <h3 style="color: var(--dark-blue); margin-bottom: 0.5rem;">Ready to grab tomorrow's slot?</h3>
                <p style="color: var(--text-muted); margin-bottom: 1.5rem;">You haven't placed a bid for tomorrow yet.</p>
                <a href="<?= site_url('bidding/place') ?>" class="btn btn-primary">Place a Bid Now</a>
            </div>
        <?php elseif ( ! $bidding_open): ?>
            <div class="alert alert-info" style="margin-bottom: 0;">
                Bidding closed for today. Opens again tomorrow morning.
            </div>
        <?php else: ?>
            <div class="alert alert-danger" style="margin-bottom: 0;">
                Monthly limit reached. You cannot bid again this month.
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<div class="row2">
    <div class="card" style="display: flex; flex-direction: column; justify-content: center; text-align: center;">
        <h2 style="border: none; padding: 0; margin-bottom: 0.5rem; font-size: 1.1rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--teal); display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="12" rx="2"></rect><circle cx="12" cy="12" r="2"></circle><path d="M6 12h.01M18 12h.01"></path></svg>
            Sponsor Balance
        </h2>
        <div style="font-size: 3rem; font-weight: 800; color: var(--dark-blue); line-height: 1; margin: 0.5rem 0;">£<?= number_format($balance, 2) ?></div>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.5rem;">Available from accepted offers</p>
        <div>
            <a href="<?= site_url('bidding/sponsors') ?>" class="btn btn-secondary btn-sm" style="border-radius: 50px;">View Offers</a>
        </div>
    </div>
    
    <div class="card">
        <h2 style="border-bottom: none; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--teal)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>
            Record Attendance
        </h2>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.25rem;">Attending a university event unlocks a <strong style="color: var(--teal);">4th bid slot</strong> this month.</p>
        <?= form_open('bidding/record_event') ?>
            <label style="font-size: 0.8rem;">Event Name</label>
            <input type="text" name="event_name" placeholder="e.g. Career Fair 2026" required style="margin-bottom: 1rem;">
            
            <label style="font-size: 0.8rem;">Event Date</label>
            <input type="date" name="event_date" required style="margin-bottom: 1.25rem;">
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">Record Attendance</button>
        <?= form_close() ?>
    </div>
</div>

<div class="card">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="border: none; margin-bottom: 0.5rem; padding: 0; display: flex; align-items: center; gap: 0.5rem;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--teal)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                Your Stats
            </h2>
            <div style="display: flex; gap: 3rem; margin-top: 1rem;">
                <div>
                    <div style="font-size: 2rem; font-weight: 800; color: var(--teal); line-height: 1;"><?= $appearance['this_month'] ?></div>
                    <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; margin-top: 0.25rem;">This Month</div>
                </div>
                <div>
                    <div style="font-size: 2rem; font-weight: 800; color: var(--dark-blue); line-height: 1;"><?= $appearance['all_time'] ?></div>
                    <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; margin-top: 0.25rem;">All Time</div>
                </div>
            </div>
        </div>
        <div>
            <a href="<?= site_url('bidding/history') ?>" class="btn btn-secondary">View Full History</a>
        </div>
    </div>
</div>