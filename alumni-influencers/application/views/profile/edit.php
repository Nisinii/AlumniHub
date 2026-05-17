<div class="card" style="max-width: 650px; margin: 0 auto;">
    <div class="section-header" style="align-items: center; margin-bottom: 2rem;">
        <h2 style="display: flex; align-items: center; gap: 0.6rem; border: none; padding: 0; margin: 0;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--teal)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            Edit Profile
        </h2>
    </div>

    <?php if ( ! empty($errors)): ?>
        <div class="alert alert-danger">
            <ul><?php foreach ($errors as $e): ?><li><?= $e ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <?= form_open_multipart('profile/edit') ?>
        
        <div style="background: #f9fafb; padding: 1.5rem; border-radius: 16px; border: 1px dashed var(--border-light); margin-bottom: 1.5rem;">
            <label style="display: flex; align-items: center; gap: 0.4rem; margin-bottom: 1rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                Profile Image
            </label>
            
            <?php if ( ! empty($profile->profile_image)): ?>
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                    <img src="<?= base_url('uploads/profiles/' . $profile->profile_image) ?>" style="width: 70px; height: 70px; border-radius: 50%; object-fit: cover; border: 3px solid var(--white); box-shadow: 0 4px 12px rgba(12, 44, 85, 0.1);">
                    <p class="hint" style="margin: 0;">Upload a new image below to replace your current one.</p>
                </div>
            <?php endif; ?>
            
            <input type="file" name="profile_image" accept="image/*" style="background: var(--white); margin-bottom: 0.5rem; padding: 0.6rem;">
            <p class="hint" style="margin: 0; font-size: 0.75rem;">JPG, PNG, GIF or WebP. Max 2MB.</p>
        </div>

        <label style="display: flex; align-items: center; gap: 0.4rem;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            Bio
        </label>
        <textarea name="bio" placeholder="Tell us a little bit about yourself, your goals, and your current role..." style="min-height: 120px;"><?= htmlspecialchars($profile->bio ?? '') ?></textarea>

        <label style="display: flex; align-items: center; gap: 0.4rem;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect x="2" y="9" width="4" height="12"></rect><circle cx="4" cy="4" r="2"></circle></svg>
            LinkedIn URL
        </label>
        <input type="url" name="linkedin_url" value="<?= htmlspecialchars($profile->linkedin_url ?? '') ?>" placeholder="https://linkedin.com/in/yourname">

        <div style="display: flex; gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border-light);">
            <button type="submit" class="btn btn-primary" style="padding: 0.8rem 2rem;">Save Changes</button>
            <a href="<?= site_url('profile') ?>" class="btn btn-secondary" style="padding: 0.8rem 2rem; background: transparent; border: 1px solid var(--border-light);">Cancel</a>
        </div>
    <?= form_close() ?>
</div>