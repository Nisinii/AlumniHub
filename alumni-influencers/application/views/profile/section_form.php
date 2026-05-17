<?php
$is_edit  = ! empty($item);
$form_url = $is_edit
    ? site_url('profile/edit_item/' . $section . '/' . $id)
    : site_url('profile/add/' . $section);

function v($item, $key) { return htmlspecialchars($item[$key] ?? ''); }
?>
<style>
    /* Ensures the new select dropdowns match your custom text inputs */
    select { 
        width: 100%; 
        padding: 0.85rem 1rem; 
        background: #f9fafb;
        border: 2px solid #e5e7eb; 
        border-radius: 12px; 
        font-size: 1rem; 
        font-family: inherit;
        color: var(--text-main);
        margin-bottom: 1.25rem; 
        transition: all 0.2s ease; 
        appearance: none; /* Removes default browser styling for a cleaner look */
    }
    select:focus { 
        outline: none; 
        border-color: var(--teal); 
        background: var(--white);
        box-shadow: 0 0 0 4px rgba(41, 99, 116, 0.15); 
    }
</style>

<div class="card">
    <h2><?= $is_edit ? 'Edit' : 'Add' ?> <?= ucfirst($section) ?></h2>

    <?php if ( ! empty($errors)): ?>
        <div class="alert alert-danger"><ul><?php foreach ($errors as $e): ?><li><?= $e ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>

    <?= form_open($form_url) ?>

    <?php if ($section === 'degrees'): ?>
        <label>Degree Name *</label>
        <input type="text" name="degree_name" value="<?= v($item,'degree_name') ?>" placeholder="e.g. BSc Computer Science" required>
        
        <label>Programme *</label>
        <select name="programme" required>
            <option value="" disabled <?= empty(v($item,'programme')) ? 'selected' : '' ?>>Select your programme</option>
            <option value="Computer Science" <?= v($item,'programme') === 'Computer Science' ? 'selected' : '' ?>>Computer Science</option>
            <option value="Software Engineering" <?= v($item,'programme') === 'Software Engineering' ? 'selected' : '' ?>>Software Engineering</option>
            <option value="Business Management" <?= v($item,'programme') === 'Business Management' ? 'selected' : '' ?>>Business Management</option>
            <option value="Artificial Intelligence" <?= v($item,'programme') === 'Artificial Intelligence' ? 'selected' : '' ?>>Artificial Intelligence</option>
        </select>

        <label>Institution *</label>
        <input type="text" name="institution" value="<?= v($item,'institution') ?>" placeholder="e.g. University of Eastminster" required>
        <label>Official Degree Page URL</label>
        <input type="url" name="degree_url" value="<?= v($item,'degree_url') ?>" placeholder="https://university.ac.uk/degrees/...">
        <label>Completion Date</label>
        <input type="date" name="completion_date" value="<?= v($item,'completion_date') ?>">

    <?php elseif ($section === 'certifications'): ?>
        <label>Certification Name *</label>
        <input type="text" name="cert_name" value="<?= v($item,'cert_name') ?>" placeholder="e.g. AWS Solutions Architect" required>
        <label>Issuing Body *</label>
        <input type="text" name="issuing_body" value="<?= v($item,'issuing_body') ?>" placeholder="e.g. Amazon Web Services" required>
        <label>Course Page URL</label>
        <input type="url" name="cert_url" value="<?= v($item,'cert_url') ?>" placeholder="https://aws.amazon.com/certification/...">
        <label>Completion Date</label>
        <input type="date" name="completion_date" value="<?= v($item,'completion_date') ?>">

    <?php elseif ($section === 'licences'): ?>
        <label>Licence Name *</label>
        <input type="text" name="licence_name" value="<?= v($item,'licence_name') ?>" placeholder="e.g. Professional Engineer Licence" required>
        <label>Awarding Body *</label>
        <input type="text" name="awarding_body" value="<?= v($item,'awarding_body') ?>" placeholder="e.g. Engineering Council UK" required>
        <label>Awarding Body URL</label>
        <input type="url" name="licence_url" value="<?= v($item,'licence_url') ?>" placeholder="https://engineeringcouncil.org/...">
        <label>Completion Date</label>
        <input type="date" name="completion_date" value="<?= v($item,'completion_date') ?>">

    <?php elseif ($section === 'courses'): ?>
        <label>Course Name *</label>
        <input type="text" name="course_name" value="<?= v($item,'course_name') ?>" placeholder="e.g. Machine Learning A-Z" required>
        <label>Provider *</label>
        <input type="text" name="provider" value="<?= v($item,'provider') ?>" placeholder="e.g. Udemy / Coursera" required>
        <label>Course Page URL</label>
        <input type="url" name="course_url" value="<?= v($item,'course_url') ?>" placeholder="https://udemy.com/course/...">
        <label>Completion Date</label>
        <input type="date" name="completion_date" value="<?= v($item,'completion_date') ?>">

    <?php elseif ($section === 'employment'): ?>
        <label>Job Title *</label>
        <input type="text" name="job_title" value="<?= v($item,'job_title') ?>" placeholder="e.g. Senior Software Engineer" required>
        
        <label>Industry Sector *</label>
        <select name="industry_sector" required>
            <option value="" disabled <?= empty(v($item,'industry_sector')) ? 'selected' : '' ?>>Select industry sector</option>
            <option value="IT" <?= v($item,'industry_sector') === 'IT' ? 'selected' : '' ?>>IT</option>
            <option value="Finance" <?= v($item,'industry_sector') === 'Finance' ? 'selected' : '' ?>>Finance</option>
            <option value="Other" <?= v($item,'industry_sector') === 'Other' ? 'selected' : '' ?>>Other</option>
        </select>

        <label>Company *</label>
        <input type="text" name="company" value="<?= v($item,'company') ?>" placeholder="e.g. Google" required>
        <div class="row2">
            <div>
                <label>Start Date *</label>
                <input type="date" name="start_date" value="<?= v($item,'start_date') ?>" required>
            </div>
            <div>
                <label>End Date</label>
                <input type="date" name="end_date" value="<?= v($item,'end_date') ?>">
                <p class="hint">Leave blank if currently employed here.</p>
            </div>
        </div>
        <label>Description</label>
        <textarea name="description" placeholder="Brief description of your role..."><?= v($item,'description') ?></textarea>
    <?php endif; ?>

    <div style="display:flex;gap:.8rem;margin-top:.5rem">
        <button type="submit" class="btn btn-primary"><?= $is_edit ? 'Save Changes' : 'Add' ?></button>
        <a href="<?= site_url('profile') ?>" class="btn btn-secondary">Cancel</a>
    </div>
    <?= form_close() ?>
</div>