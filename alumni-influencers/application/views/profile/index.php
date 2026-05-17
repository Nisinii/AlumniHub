<?php
$added   = $this->input->get('added');
$updated = $this->input->get('updated');
$deleted = $this->input->get('deleted');
?>

<?php if ($added || $updated || $deleted): ?>
<div class="alert alert-success">
    <?= ucfirst($added ?? $updated ?? $deleted) ?> <?= $added ? 'added' : ($updated ? 'updated' : 'deleted') ?> successfully.
</div>
<?php endif; ?>

<div class="card">
    <div style="display: flex; align-items: center; gap: 2rem; flex-wrap: wrap;">
        <?php if ( ! empty($profile['profile_image'])): ?>
            <img src="<?= $profile['profile_image'] ?>" class="profile-img" alt="Profile">
        <?php else: ?>
            <div class="profile-img" style="background: var(--cream); display: flex; align-items: center; justify-content: center;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--teal)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            </div>
        <?php endif; ?>
        
        <div style="flex: 1; min-width: 250px;">
            <h1 style="font-size: 1.8rem; font-weight: 800; color: var(--dark-blue); margin-bottom: 0.25rem; letter-spacing: -0.02em;">
                <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
            </h1>
            <p style="color: var(--text-muted); font-size: 0.95rem; font-weight: 500; margin-bottom: 0.5rem;">
                <?= htmlspecialchars($user['email']) ?>
            </p>
            
            <?php if ( ! empty($profile['linkedin_url'])): ?>
                <a href="<?= htmlspecialchars($profile['linkedin_url']) ?>" target="_blank" style="display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.9rem; font-weight: 600; color: var(--teal); text-decoration: none; margin-top: 0.5rem;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect x="2" y="9" width="4" height="12"></rect><circle cx="4" cy="4" r="2"></circle></svg>
                    LinkedIn Profile
                </a>
            <?php endif; ?>
        </div>
        
        <a href="<?= site_url('profile/edit') ?>" class="btn btn-secondary">Edit Profile</a>
    </div>

    <?php if ( ! empty($profile['bio'])): ?>
        <p style="margin-top: 1.5rem; color: var(--text-main); line-height: 1.7; font-size: 0.95rem; background: #f9fafb; padding: 1.5rem; border-radius: 12px; border: 1px solid var(--border-light);">
            <?= nl2br(htmlspecialchars($profile['bio'])) ?>
        </p>
    <?php endif; ?>
    
    <div style="margin-top: 2rem;">
        <div style="display: flex; justify-content: space-between; font-size: 0.85rem; font-weight: 700; color: var(--dark-blue); text-transform: uppercase; letter-spacing: 0.05em;">
            <span>Profile Completion</span>
            <span><?= $completion['percentage'] ?>%</span>
        </div>
        <div class="completion-bar" style="height: 8px; margin: 0.6rem 0;">
            <div class="completion-fill" style="width: <?= $completion['percentage'] ?>%;"></div>
        </div>
        <?php if ( ! empty($completion['missing'])): ?>
            <p style="font-size: 0.85rem; color: var(--error-text); margin-top: 0.5rem; display: flex; gap: 0.4rem;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-top: 2px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                <span><strong>Missing:</strong> <?= implode(', ', $completion['missing']) ?></span>
            </p>
        <?php endif; ?>
    </div>
</div>

<?php
// Define SVG Icons
$icon_degrees = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--teal)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>';
$icon_certs = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--teal)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>';
$icon_licences = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--teal)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"></rect><circle cx="8" cy="10" r="3"></circle><line x1="14" y1="10" x2="19" y2="10"></line><line x1="14" y1="14" x2="19" y2="14"></line></svg>';
$icon_courses = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--teal)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>';
$icon_employment = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--teal)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>';

// Reusable function to render a section table
function render_section($title, $icon, $items, $section, $columns) {
    $CI =& get_instance();
    echo '<div class="card">';
    echo '<div class="section-header" style="align-items: center; margin-bottom: 1.5rem;">';
    echo '<h2 style="display: flex; align-items: center; gap: 0.6rem; border: none; padding: 0; margin: 0;">' . $icon . ' ' . $title . '</h2>';
    echo '<a href="' . site_url('profile/add/' . $section) . '" class="btn btn-primary btn-sm">+ Add New</a></div>';
    
    if (empty($items)) { 
        echo '<div class="empty" style="text-align: left; background: #f9fafb; padding: 2rem; border: 2px dashed var(--border-light); border-radius: 16px;">
                <p style="color: var(--text-muted); margin: 0;">No ' . strtolower($title) . ' added yet.</p>
              </div>'; 
    } else {
        echo '<div style="overflow-x: auto;"><table style="min-width: 600px;"><tr>';
        foreach ($columns as $col) echo '<th>' . $col . '</th>';
        echo '<th style="text-align: right;">Action</th></tr>';
        
        foreach ($items as $item) {
            echo '<tr>';
            foreach (array_keys($columns) as $key) {
                $val = $item[$key] ?? '—';
                if (str_contains($key, 'url') && $val !== '—') {
                    echo '<td><a href="' . htmlspecialchars($val) . '" target="_blank" style="color: var(--teal); font-weight: 600; text-decoration: none;">View Link ↗</a></td>';
                } elseif (str_contains($key, 'date') && $val && $val !== '—') {
                    echo '<td style="color: var(--dark-blue); font-weight: 500;">' . date('M Y', strtotime($val)) . '</td>';
                } else {
                    echo '<td>' . htmlspecialchars($val) . '</td>';
                }
            }
            echo '<td style="text-align: right; white-space: nowrap;">
                <a href="' . site_url('profile/edit_item/' . $section . '/' . $item['id']) . '" class="btn btn-secondary btn-sm" style="margin-right: 0.5rem; background: transparent; border: 1px solid var(--border-light);">Edit</a>
                ' . form_open('profile/delete/' . $section . '/' . $item['id'], ['style' => 'display:inline']) . '
                <button type="submit" class="btn btn-danger btn-sm" style="background: transparent; color: var(--error-text); border: 1px solid var(--error-text);" onclick="return confirm(\'Are you sure you want to delete this item?\')">Delete</button>
                ' . form_close() . '
            </td></tr>';
        }
        echo '</table></div>';
    }
    echo '</div>';
}
?>

<?php render_section('Degrees', $icon_degrees, $degrees, 'degrees', ['degree_name' => 'Degree', 'programme' => 'Programme', 'institution' => 'Institution', 'completion_date' => 'Completed', 'degree_url' => 'Link']); ?>
<?php render_section('Certifications', $icon_certs, $certifications, 'certifications', ['cert_name' => 'Certification', 'issuing_body' => 'Issuing Body', 'completion_date' => 'Completed', 'cert_url' => 'Link']); ?>
<?php render_section('Licences', $icon_licences, $licences, 'licences', ['licence_name' => 'Licence', 'awarding_body' => 'Awarding Body', 'completion_date' => 'Completed', 'licence_url' => 'Link']); ?>
<?php render_section('Short Courses', $icon_courses, $courses, 'courses', ['course_name' => 'Course', 'provider' => 'Provider', 'completion_date' => 'Completed', 'course_url' => 'Link']); ?>

<div class="card">
    <div class="section-header" style="align-items: center; margin-bottom: 1.5rem;">
        <h2 style="display: flex; align-items: center; gap: 0.6rem; border: none; padding: 0; margin: 0;">
            <?= $icon_employment ?>
            Employment History
        </h2>
        <a href="<?= site_url('profile/add/employment') ?>" class="btn btn-primary btn-sm">+ Add New</a>
    </div>
    
    <?php if (empty($employment)): ?>
        <div class="empty" style="text-align: left; background: #f9fafb; padding: 2rem; border: 2px dashed var(--border-light); border-radius: 16px;">
            <p style="color: var(--text-muted); margin: 0;">No employment history added yet.</p>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table style="min-width: 600px;">
                <tr>
                    <th>Job Title</th>
                    <th>Company</th>
                    <th>Industry</th>
                    <th>Period</th>
                    <th style="text-align: right;">Action</th>
                </tr>
                <?php foreach ($employment as $e): ?>
                <tr>
                    <td style="font-weight: 700; color: var(--dark-blue);"><?= htmlspecialchars($e['job_title']) ?></td>
                    <td><?= htmlspecialchars($e['company']) ?></td>
                    <td><span class="badge" style="background: rgba(41, 99, 116, 0.1); color: var(--teal); padding: 0.3rem 0.6rem; border-radius: 6px; font-size: 0.85rem; font-weight: 600;"><?= htmlspecialchars($e['industry_sector'] ?? '—') ?></span></td>
                    <td style="color: var(--text-muted); font-size: 0.95rem;">
                        <?= date('M Y', strtotime($e['start_date'])) ?> – 
                        <?= $e['end_date'] ? date('M Y', strtotime($e['end_date'])) : '<span class="badge" style="background: var(--success-bg); color: var(--success-text);">Current</span>' ?>
                    </td>
                    <td style="text-align: right; white-space: nowrap;">
                        <a href="<?= site_url('profile/edit_item/employment/' . $e['id']) ?>" class="btn btn-secondary btn-sm" style="margin-right: 0.5rem; background: transparent; border: 1px solid var(--border-light);">Edit</a>
                        <?= form_open('profile/delete/employment/' . $e['id'], ['style' => 'display:inline']) ?>
                        <button type="submit" class="btn btn-danger btn-sm" style="background: transparent; color: var(--error-text); border: 1px solid var(--error-text);" onclick="return confirm('Are you sure you want to delete this employment record?')">Delete</button>
                        <?= form_close() ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php endif; ?>
</div>