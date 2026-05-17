<div class="card">
    <div class="section-header" style="align-items: center; margin-bottom: 1.5rem;">
        <h2 style="display: flex; align-items: center; gap: 0.6rem; border: none; padding: 0; margin: 0;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--teal)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Bid History
        </h2>
    </div>

    <?php if (empty($history)): ?>
        <div class="empty" style="text-align: left; background: #f9fafb; padding: 2.5rem; border: 2px dashed var(--border-light); border-radius: 16px;">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem; color: var(--dark-blue);">
                 <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                 <h3 style="margin: 0; font-size: 1.2rem;">No history yet</h3>
            </div>
            <p style="color: var(--text-muted); margin: 0;">You haven't placed any bids yet. Once you do, they will appear here.</p>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table style="min-width: 600px;">
                <tr>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Result</th>
                </tr>
                <?php foreach ($history as $bid): ?>
                <?php
                $colors = [
                    'active'    => ['var(--cream)', 'var(--dark-blue)'],
                    'won'       => ['var(--teal)', 'var(--white)'],
                    'lost'      => ['rgba(12, 44, 85, 0.08)', 'var(--text-main)'],
                    'cancelled' => ['var(--border-light)', 'var(--text-muted)'],
                ];
                $c = $colors[$bid['status']] ?? $colors['cancelled'];
                ?>
                <tr>
                    <td style="color: var(--dark-blue); font-weight: 600;"><?= date('d M Y', strtotime($bid['bid_date'])) ?></td>
                    <td style="color: var(--teal); font-weight: 800;">£<?= number_format($bid['bid_amount'], 2) ?></td>
                    <td>
                        <span class="badge" style="background: <?= $c[0] ?>; color: <?= $c[1] ?>;">
                            <?= ucfirst($bid['status']) ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($bid['is_winner']): ?>
                            <span style="display: flex; align-items: center; gap: 0.4rem; color: var(--teal); font-weight: 700;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                Featured Alumni
                            </span>
                        <?php elseif ($bid['status'] === 'active'): ?>
                            <span style="display: flex; align-items: center; gap: 0.4rem; color: var(--dark-blue); font-weight: 500;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                Awaiting midnight
                            </span>
                        <?php elseif ($bid['status'] === 'lost'): ?>
                            <span style="display: flex; align-items: center; gap: 0.4rem; color: var(--text-muted);">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                                Not selected
                            </span>
                        <?php else: ?>
                            <span style="color: var(--border-light); font-weight: bold;">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php endif; ?>
    
    <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border-light);">
        <a href="<?= site_url('bidding') ?>" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 0.5rem; background: transparent; border: 1px solid var(--border-light);">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            Back to Dashboard
        </a>
    </div>
</div>