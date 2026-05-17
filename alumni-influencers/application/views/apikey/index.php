<?php if ( ! empty($errors)): ?>
    <div class="alert alert-danger"><ul><?php foreach ($errors as $e): ?><li><?= $e ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<?php if ( ! empty($new_key)): ?>
<div style="background: var(--cream); border: 2px solid var(--teal); border-radius: 16px; padding: 1.5rem; margin-bottom: 2rem; box-shadow: 0 10px 25px rgba(41, 99, 116, 0.15);">
    <h3 style="color: var(--dark-blue); margin-bottom: 0.6rem; display: flex; align-items: center; gap: 0.5rem; font-size: 1.25rem;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--teal)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
        Copy Your API Key Now
    </h3>
    <p style="font-size: 0.95rem; color: var(--text-main); margin-bottom: 1rem; font-weight: 500;">
        This key will <strong style="color: var(--error-text);">never be shown again</strong>. Please store it securely in your client application.
    </p>
    <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
        <code style="flex: 1; display: block; background: var(--white); padding: 0.8rem 1.2rem; border-radius: 8px; font-size: 1rem; color: var(--dark-blue); font-weight: 700; word-break: break-all; border: 1px solid rgba(41, 99, 116, 0.2);">
            <?= htmlspecialchars($new_key) ?>
        </code>
        <button onclick="navigator.clipboard.writeText('<?= htmlspecialchars($new_key) ?>'); this.textContent='Copied!'; this.style.background='var(--dark-blue)';"
                class="btn btn-primary" style="white-space: nowrap;">Copy to Clipboard</button>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div class="section-header" style="align-items: center; margin-bottom: 1.5rem;">
        <h2 style="display: flex; align-items: center; gap: 0.6rem; border: none; padding: 0; margin: 0;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--teal)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"></path></svg>
            Generate New API Key
        </h2>
    </div>
    
    <p style="font-size: 0.95rem; color: var(--text-muted); margin-bottom: 1.5rem; line-height: 1.6;">
        API keys allow external clients to access platform data. Choose the scope carefully. Always give clients the minimum access they need.
    </p>

    <?= form_open('apikey/generate') ?>
        <div class="row2">
            <div>
                <label style="display: flex; align-items: center; gap: 0.4rem;">Key Name <span style="color: var(--error-text);">*</span></label>
                <input type="text" name="key_name" placeholder="e.g. AR Headset Client" required style="margin-bottom: 0.5rem;">
                <p class="hint" style="margin-top: 5px;">A friendly label to identify this key.</p>
            </div>
            <div>
                <label style="display: flex; align-items: center; gap: 0.4rem;">Scope <span style="color: var(--error-text);">*</span></label>
                <select name="scope" style="margin-bottom: 0.5rem; width: 100%; padding: 0.75rem 1rem; background: #f9fafb; border: 2px solid var(--border-light); border-radius: 10px; font-size: 0.95rem; font-family: inherit; color: var(--text-main); cursor: pointer;">
                    <option value="read">Read — GET endpoints only</option>
                    <option value="read_write">Read/Write — GET + POST</option>
                    <option value="admin">Admin — Full access</option>
                </select>
                <p class="hint" style="margin-top: 5px;"><strong>Read</strong> is recommended for the AR client.</p>
            </div>
        </div>
        <div style="margin-top: 1rem; padding-top: 1.5rem; border-top: 1px solid var(--border-light);">
            <button type="submit" class="btn btn-primary" style="padding: 0.8rem 2rem;">Generate Key</button>
        </div>
    <?= form_close() ?>
</div>

<div class="card">
    <div class="section-header" style="align-items: center; margin-bottom: 1.5rem;">
        <h2 style="display: flex; align-items: center; gap: 0.6rem; border: none; padding: 0; margin: 0;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--teal)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
            Your API Keys
        </h2>
    </div>

    <?php if (empty($keys)): ?>
        <div class="empty" style="text-align: left; background: #f9fafb; padding: 2.5rem; border: 2px dashed var(--border-light); border-radius: 16px;">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem; color: var(--dark-blue);">
                 <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"></path></svg>
                 <h3 style="margin: 0; font-size: 1.2rem;">No keys generated</h3>
            </div>
            <p style="color: var(--text-muted); margin: 0;">You haven't generated any API keys yet. Create one above to get started.</p>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table style="width: 100%; min-width: 600px;">
                <tr>
                    <th>Key Details</th>
                    <th>Status</th>
                    <th>Usage</th>
                    <th>Created</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
                <?php foreach ($keys as $key): ?>
                <tr>
                    <td>
                        <div style="color: var(--dark-blue); font-weight: 700; margin-bottom: 0.4rem;"><?= htmlspecialchars($key['key_name']) ?></div>
                        <?php
                        $scope_colors = [
                            'read'       => ['var(--cream)', 'var(--dark-blue)'],
                            'read_write' => ['var(--teal)', 'var(--white)'],
                            'admin'      => ['var(--error-bg)', 'var(--error-text)']
                        ];
                        $sc = $scope_colors[$key['scope']] ?? ['var(--border-light)', 'var(--text-muted)'];
                        ?>
                        <span class="badge" style="background: <?= $sc[0] ?>; color: <?= $sc[1] ?>; font-size: 0.7rem; padding: 0.2rem 0.5rem;"><?= $key['scope'] ?></span>
                    </td>
                    <td>
                        <?php if ($key['is_active']): ?>
                            <span class="badge" style="background: var(--success-bg); color: var(--success-text);">Active</span>
                        <?php else: ?>
                            <span class="badge" style="background: var(--border-light); color: var(--text-muted);">Revoked</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="font-weight: 800; color: var(--teal);"><?= number_format($key['total_calls']) ?> calls</div>
                        <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.2rem;">
                            Last: <?= $key['last_used_at'] ? date('d M Y', strtotime($key['last_used_at'])) : 'Never' ?>
                        </div>
                    </td>
                    <td style="font-size: 0.85rem; color: var(--text-muted);"><?= date('d M Y', strtotime($key['created_at'])) ?></td>
                    <td style="text-align: right; white-space: nowrap;">
                        <a href="<?= site_url('apikey/stats/' . $key['id']) ?>" class="btn btn-secondary btn-sm" style="background: transparent; border: 1px solid var(--border-light); margin-right: 0.4rem;">Stats</a>
                        <?php if ($key['is_active']): ?>
                            <?= form_open('apikey/revoke/' . $key['id'], ['style' => 'display:inline']) ?>
                                <button type="submit" class="btn btn-danger btn-sm" style="background: transparent; color: var(--error-text); border: 1px solid var(--error-text);"
                                    onclick="return confirm('Revoke this key? It will stop working immediately.')">Revoke</button>
                            <?= form_close() ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php endif; ?>
</div>

<div class="card" style="background: var(--dark-blue); color: var(--white);">
    <div class="section-header" style="align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 1rem;">
        <h2 style="display: flex; align-items: center; gap: 0.6rem; border: none; padding: 0; margin: 0; color: var(--white);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--light-teal)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>
            How to Use Your API Key
        </h2>
    </div>
    
    <p style="font-size: 0.95rem; color: rgba(255,255,255,0.8); margin-bottom: 0.8rem;">
        Send your API key in the <strong style="color: var(--light-teal);">Authorization</strong> header with every request:
    </p>
    <code style="display: block; background: rgba(0,0,0,0.25); padding: 1.2rem; border-radius: 8px; font-size: 0.95rem; font-family: monospace; color: var(--cream); border: 1px solid rgba(98, 159, 173, 0.2); margin-bottom: 1.5rem;">
        Authorization: Bearer YOUR_API_KEY_HERE
    </code>
    
    <p style="font-size: 0.95rem; color: rgba(255,255,255,0.8); margin-bottom: 0.8rem;">
        Or use the <strong style="color: var(--light-teal);">X-API-Key</strong> header:
    </p>
    <code style="display: block; background: rgba(0,0,0,0.25); padding: 1.2rem; border-radius: 8px; font-size: 0.95rem; font-family: monospace; color: var(--cream); border: 1px solid rgba(98, 159, 173, 0.2); margin-bottom: 1.5rem;">
        X-API-Key: YOUR_API_KEY_HERE
    </code>
    
    <div style="background: rgba(98, 159, 173, 0.1); padding: 1rem 1.2rem; border-radius: 8px; display: flex; align-items: flex-start; gap: 0.75rem;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--light-teal)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 2px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
        <p style="font-size: 0.9rem; color: rgba(255,255,255,0.8); margin: 0;">
            The primary endpoint for the AR client is:<br>
            <strong style="color: var(--cream); letter-spacing: 0.02em; font-family: monospace; display: block; margin-top: 0.4rem;"><?= site_url('bidding/api/today') ?></strong>
        </p>
    </div>
</div>