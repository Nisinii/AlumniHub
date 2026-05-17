<div class="card" style="padding: 1.5rem 2.5rem; border-bottom: 4px solid var(--teal); border-radius: 16px 16px 0 0;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
        <h2 style="display: flex; align-items: center; gap: 0.6rem; border: none; padding: 0; margin: 0; color: var(--dark-blue);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--teal)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
            Usage Stats — <span style="font-weight: 500; color: var(--text-muted); margin-left: 0.25rem;"><?= htmlspecialchars($stats['key_name']) ?></span>
        </h2>
        <a href="<?= site_url('apikey') ?>" class="btn btn-secondary btn-sm" style="display: flex; align-items: center; gap: 0.4rem; background: transparent; border: 1px solid var(--border-light);">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            Back
        </a>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 1rem;">
        <?php
        $scope_colors = [
            'read'       => ['var(--cream)', 'var(--dark-blue)'],
            'read_write' => ['var(--teal)', 'var(--white)'],
            'admin'      => ['var(--error-bg)', 'var(--error-text)']
        ];
        $sc = $scope_colors[$stats['scope']] ?? ['var(--border-light)', 'var(--text-muted)'];
        ?>
        <div style="background: #f9fafb; padding: 1.25rem; border-radius: 12px; border: 1px solid var(--border-light);">
            <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 0.5rem;">Scope</span>
            <span class="badge" style="background: <?= $sc[0] ?>; color: <?= $sc[1] ?>; font-size: 0.8rem;"><?= $stats['scope'] ?></span>
        </div>
        
        <div style="background: #f9fafb; padding: 1.25rem; border-radius: 12px; border: 1px solid var(--border-light);">
            <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 0.5rem;">Status</span>
            <?php if ($stats['is_active']): ?>
                <span class="badge" style="background: var(--success-bg); color: var(--success-text); font-size: 0.8rem;">Active</span>
            <?php else: ?>
                <span class="badge" style="background: var(--border-light); color: var(--text-muted); font-size: 0.8rem;">Revoked</span>
            <?php endif; ?>
        </div>
        
        <div style="background: rgba(98, 159, 173, 0.08); padding: 1.25rem; border-radius: 12px; border: 1px solid rgba(41, 99, 116, 0.1);">
            <span style="font-size: 0.75rem; color: var(--teal); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 0.25rem;">Total Calls</span>
            <strong style="font-size: 1.8rem; color: var(--dark-blue); line-height: 1;"><?= number_format($stats['total_calls']) ?></strong>
        </div>
        
        <div style="background: #f9fafb; padding: 1.25rem; border-radius: 12px; border: 1px solid var(--border-light);">
            <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 0.4rem;">Last Used</span>
            <span style="font-size: 0.95rem; font-weight: 600; color: var(--dark-blue);"><?= $stats['last_used_at'] ? date('d M Y H:i', strtotime($stats['last_used_at'])) : 'Never' ?></span>
        </div>
        
        <div style="background: #f9fafb; padding: 1.25rem; border-radius: 12px; border: 1px solid var(--border-light);">
            <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 0.4rem;">Created</span>
            <span style="font-size: 0.95rem; font-weight: 600; color: var(--text-main);"><?= date('d M Y', strtotime($stats['created_at'])) ?></span>
        </div>
    </div>
</div>

<div class="card" style="border-radius: 0 0 16px 16px; margin-top: -2rem; padding-top: 3.5rem;">
    <div class="section-header" style="margin-bottom: 1.5rem;">
        <h2 style="display: flex; align-items: center; gap: 0.6rem; border: none; padding: 0; margin: 0; font-size: 1.25rem;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--teal)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            Daily Activity (Last 7 Days)
        </h2>
    </div>

    <?php if (empty($stats['daily_last_7_days'])): ?>
        <div class="empty" style="background: #f9fafb; border: 2px dashed var(--border-light); border-radius: 12px; padding: 2rem;">
            <p style="margin: 0; color: var(--text-muted);">No calls in the last 7 days.</p>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table style="min-width: 500px;">
                <tr><th>Date</th><th>Calls</th><th style="width: 50%;">Activity Volume</th></tr>
                <?php
                $max = max(array_column($stats['daily_last_7_days'], 'call_count'));
                foreach ($stats['daily_last_7_days'] as $day):
                $pct = $max > 0 ? ($day['call_count'] / $max) * 100 : 0;
                ?>
                <tr>
                    <td style="color: var(--dark-blue); font-weight: 600;"><?= date('d M', strtotime($day['date'])) ?></td>
                    <td style="font-weight: 800; color: var(--teal);"><?= $day['call_count'] ?></td>
                    <td>
                        <div style="background: var(--border-light); border-radius: 99px; height: 8px; width: 100%; overflow: hidden;">
                            <div style="background: var(--teal); border-radius: 99px; height: 100%; width: <?= $pct ?>%; transition: width 0.5s ease;"></div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php endif; ?>
</div>

<div class="card">
    <div class="section-header" style="margin-bottom: 1.5rem;">
        <h2 style="display: flex; align-items: center; gap: 0.6rem; border: none; padding: 0; margin: 0; font-size: 1.25rem;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--teal)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
            Calls by Endpoint
        </h2>
    </div>
        
    <?php if (empty($stats['by_endpoint'])): ?>
        <div class="empty" style="background: #f9fafb; border: 2px dashed var(--border-light); border-radius: 12px;">
            <p style="margin: 0; color: var(--text-muted);">No endpoints hit yet.</p>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table style="min-width: 300px;">
                <tr><th>Method</th><th>Endpoint</th><th style="text-align: right;">Hits</th></tr>
                <?php foreach ($stats['by_endpoint'] as $ep): ?>
                <tr>
                    <td><span class="badge" style="background: rgba(98, 159, 173, 0.15); color: var(--dark-blue);"><?= $ep['method'] ?></span></td>
                    <td style="font-family: monospace; font-size: 0.85rem; color: var(--text-main);"><?= htmlspecialchars($ep['endpoint']) ?></td>
                    <td style="text-align: right; font-weight: 700; color: var(--teal);"><?= number_format($ep['call_count']) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php endif; ?>
</div>
<div class="card">
    <div class="section-header" style="margin-bottom: 1.5rem;">
        <h2 style="display: flex; align-items: center; gap: 0.6rem; border: none; padding: 0; margin: 0; font-size: 1.25rem;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--teal)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            Recent Calls
        </h2>
    </div>
        
    <?php if (empty($stats['recent_calls'])): ?>
        <div class="empty" style="background: #f9fafb; border: 2px dashed var(--border-light); border-radius: 12px;">
            <p style="margin: 0; color: var(--text-muted);">No call logs available.</p>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table style="min-width: 450px;">
                <tr><th>Time</th><th>Details</th><th>Status</th></tr>
                <?php foreach ($stats['recent_calls'] as $log): ?>
                <tr>
                    <td style="font-size: 0.82rem; color: var(--text-muted); white-space: nowrap;"><?= date('d M H:i:s', strtotime($log['called_at'])) ?></td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.2rem;">
                            <span class="badge" style="background: rgba(98, 159, 173, 0.15); color: var(--dark-blue); font-size: 0.65rem; padding: 0.15rem 0.4rem;"><?= $log['method'] ?></span>
                            <span style="font-family: monospace; font-size: 0.82rem; color: var(--text-main);"><?= htmlspecialchars($log['endpoint']) ?></span>
                        </div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">IP: <?= htmlspecialchars($log['ip_address'] ?? '—') ?></div>
                    </td>
                    <td>
                        <?php $rc = $log['response_code']; ?>
                        <span class="badge" style="background: <?= $rc < 300 ? 'var(--success-bg)' : 'var(--error-bg)' ?>; color: <?= $rc < 300 ? 'var(--success-text)' : 'var(--error-text)' ?>; font-family: monospace;">
                            <?= $rc ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php endif; ?>
</div>
