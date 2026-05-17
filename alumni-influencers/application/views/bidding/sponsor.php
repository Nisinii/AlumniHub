<?php if ( ! empty($errors)): ?>
    <div class="alert alert-danger">
        <ul><?php foreach ($errors as $e): ?><li><?= $e ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<div class="card">
    <div class="section-header" style="align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <h2 style="display: flex; align-items: center; gap: 0.6rem; border: none; padding: 0; margin: 0;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--teal)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
            </svg>
            Sponsor Offers
        </h2>
        <div style="background: rgba(98, 159, 173, 0.1); padding: 0.6rem 1.25rem; border-radius: 50px; font-size: 0.95rem; font-weight: 600; color: var(--text-main); display: flex; align-items: center; gap: 0.5rem;">
            Balance: <strong style="color: var(--dark-blue); font-size: 1.15rem;">£<?= number_format($balance, 2) ?></strong>
        </div>
    </div>
    
    <p style="font-size: 0.95rem; color: var(--text-muted); margin-bottom: 2rem; line-height: 1.6; max-width: 800px;">
        Sponsors pay you to promote their courses or certifications. Accepting an offer adds to your bidding balance — you only pay back if you <strong style="color: var(--teal);">win</strong> a featured slot.
    </p>

    <?php if (empty($offers)): ?>
        <div class="empty" style="text-align: left; background: #f9fafb; padding: 2.5rem; border: 2px dashed var(--border-light); border-radius: 16px;">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem; color: var(--dark-blue);">
                 <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                 <h3 style="margin: 0; font-size: 1.2rem;">No offers yet</h3>
            </div>
            <p style="color: var(--text-muted); margin: 0;">You don't have any pending sponsor offers at the moment. Check back later!</p>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table style="min-width: 600px;">
                <tr>
                    <th>Sponsor</th>
                    <th>Amount</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($offers as $offer): ?>
                <?php
                $sc = [
                    'pending'  => ['var(--cream)', 'var(--dark-blue)'],
                    'accepted' => ['var(--teal)', 'var(--white)'],     
                    'declined' => ['rgba(12, 44, 85, 0.08)', 'var(--text-muted)']
                ];
                $c  = $sc[$offer['status']] ?? $sc['pending'];
                ?>
                <tr>
                    <td><strong style="color: var(--dark-blue);"><?= htmlspecialchars($offer['sponsor_name']) ?></strong></td>
                    <td style="color: var(--teal); font-weight: 800;">£<?= number_format($offer['amount'], 2) ?></td>
                    <td style="font-size: 0.85rem; color: var(--text-muted); max-width: 250px;"><?= htmlspecialchars($offer['description'] ?? '—') ?></td>
                    <td>
                        <span class="badge" style="background: <?= $c[0] ?>; color: <?= $c[1] ?>;">
                            <?= ucfirst($offer['status']) ?>
                        </span>
                    </td>
                    <td style="display: flex; gap: 0.5rem; align-items: center;">
                        <?php if ($offer['status'] === 'pending'): ?>
                            <?= form_open('bidding/accept_offer/'.$offer['id'], ['style'=>'margin: 0;']) ?>
                                <button type="submit" class="btn btn-primary btn-sm" style="padding: 0.4rem 1rem;">Accept</button>
                            <?= form_close() ?>
                            
                            <?= form_open('bidding/decline_offer/'.$offer['id'], ['style'=>'margin: 0;']) ?>
                                <button type="submit" class="btn btn-secondary btn-sm" style="padding: 0.4rem 1rem; background: transparent; border: 1px solid var(--border-light);">Decline</button>
                            <?= form_close() ?>
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