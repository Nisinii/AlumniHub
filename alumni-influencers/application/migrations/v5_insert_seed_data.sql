-- ============================================================
-- V5: Seed Data (v5_insert_seed_data.sql)
-- Populates dummy users, complete 3NF profiles, and sponsor offers.
-- Default password for all users is: password
-- ============================================================

USE `ar_alumni`;

-- 1. ── USERS ────────────────────────────────────────────────
-- The password hash below is a valid bcrypt hash for the string 'password'.
INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password_hash`, `role`, `is_verified`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Learner', 'Student', 'nisiniweerathunga1814@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'alumnus', 1, 1, NOW(), NOW()),
(2, 'Teacher', 'Instructor', 'yaweenrox@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'alumnus', 1, 1, NOW(), NOW());

-- 2. ── CORE PROFILES ────────────────────────────────────────
INSERT INTO `profiles` (`user_id`, `bio`, `linkedin_url`, `created_at`, `updated_at`) VALUES
(1, 'Software Engineer passionate about full-stack development, AI/ML, and building immersive, high-design web experiences.', 'https://linkedin.com/in/learner-student', NOW(), NOW()),
(2, 'Experienced developer and mentor specializing in backend architecture, API design, and multiplayer game servers.', 'https://linkedin.com/in/teacher-instructor', NOW(), NOW());

-- 3. ── DEGREES ──────────────────────────────────────────────
INSERT INTO `degrees` (`user_id`, `degree_name`, `institution`, `degree_url`, `completion_date`, `created_at`) VALUES
(1, 'Bachelor of Engineering in Software Engineering (First Class Honours)', 'Informatics Institute of Technology', 'https://iit.ac.lk', '2024-06-30', NOW()),
(1, 'MSc Software Engineering and Management', 'Chalmers University of Technology / University of Gothenburg', 'https://chalmers.se', '2026-06-30', NOW()),
(2, 'BSc Computer Science', 'University of Westminster', 'https://westminster.ac.uk', '2023-06-30', NOW());

-- 4. ── CERTIFICATIONS ───────────────────────────────────────
INSERT INTO `certifications` (`user_id`, `cert_name`, `issuing_body`, `cert_url`, `completion_date`, `created_at`) VALUES
(1, 'AWS Certified Solutions Architect – Associate', 'Amazon Web Services', 'https://aws.amazon.com', '2025-05-15', NOW()),
(2, 'Certified ScrumMaster (CSM)', 'Scrum Alliance', 'https://scrumalliance.org', '2024-11-10', NOW());

-- 5. ── LICENCES ─────────────────────────────────────────────
INSERT INTO `licences` (`user_id`, `licence_name`, `awarding_body`, `completion_date`, `created_at`) VALUES
(1, 'Professional Software Engineering Licence', 'Swedish Association of Professional Engineers', '2025-08-01', NOW());

-- 6. ── COURSES ──────────────────────────────────────────────
INSERT INTO `courses` (`user_id`, `course_name`, `provider`, `course_url`, `completion_date`, `created_at`) VALUES
(1, 'Agile Development Processes', 'University of Gothenburg', NULL, '2026-01-15', NOW()),
(1, 'Advanced Full-Stack Web Development', 'Coursera', 'https://coursera.org', '2025-10-20', NOW()),
(2, 'Network Architecture & Multiplayer Logic', 'Udemy', 'https://udemy.com', '2024-03-12', NOW());

-- 7. ── EMPLOYMENT ───────────────────────────────────────────
-- Note: end_date is NULL for current jobs.
INSERT INTO `employment` (`user_id`, `job_title`, `company`, `start_date`, `end_date`, `description`, `created_at`) VALUES
(1, 'Trainee Programmer', 'InLead Automation AB', '2025-11-01', NULL, 'Working on software automation solutions and full-stack API integration.', NOW()),
(1, 'Summer Intern', 'Ericsson', '2026-06-01', '2026-08-31', 'Assisted in telecommunications software engineering and cloud infrastructure.', NOW()),
(2, 'Senior Backend Engineer', 'Mojang Studios', '2023-08-15', NULL, 'Developing and maintaining core multiplayer server architecture.', NOW());

-- 8. ── SPONSOR OFFERS (VIRTUAL WALLET) ──────────────────────
-- These give the users a balance so you can immediately test the blind bidding system.
INSERT INTO `sponsor_offers` (`user_id`, `sponsor_name`, `amount`, `spent_amount`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Phantasmagoria Ltd', 500.00, 0.00, 'We would love to sponsor your AR profile to promote our new development tools.', 'accepted', NOW(), NOW()),
(1, 'TechNova Solutions', 250.00, 0.00, 'Sponsorship for featuring our logo on your Alumni card.', 'pending', NOW(), NOW()),
(2, 'GameDev Corp', 1000.00, 200.00, 'Premium sponsorship for top-tier alumni.', 'accepted', NOW(), NOW());