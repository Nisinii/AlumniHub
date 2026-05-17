<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><path fill='%23629FAD' d='M12 0C12 6.627 17.373 12 24 12C17.373 12 12 17.373 12 24C12 17.373 6.627 12 0 12C6.627 12 12 6.627 12 0Z'/></svg>">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) : 'Welcome' ?> - AR Alumni</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            /* Custom Palette */
            --dark-blue: #0C2C55;
            --teal: #296374;
            --light-teal: #629FAD;
            --cream: #EDEDCE;
            --white: #ffffff;
            --text-main: #1f2937;
            --text-muted: #6b7280;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: var(--cream);
            color: var(--text-main); 
            line-height: 1.6; 
            -webkit-font-smoothing: antialiased;
            position: relative;
            overflow-x: hidden;
        }
        
        /* Abstract Background Shapes for Creative Vibe */
        .bg-shape {
            position: absolute;
            border-radius: 50%;
            z-index: -1;
            opacity: 0.4;
        }
        .bg-shape-1 {
            width: 400px; height: 400px;
            background: var(--light-teal);
            top: 40%; left: -150px;
        }
        .bg-shape-2 {
            width: 350px; height: 350px;
            background: var(--teal);
            bottom: 5%; right: -100px;
        }

        a { text-decoration: none; transition: all 0.2s ease; }

        .container { max-width: 1100px; margin: 0 auto; padding: 0 1.5rem; }
        
        /* Hero Section with Curved Bottom */
        .hero-wrapper {
            background-color: var(--dark-blue);
            color: var(--white);
            padding: 6rem 1.5rem 10rem;
            text-align: center;
            border-radius: 0 0 5vw 5vw;
            position: relative;
            overflow: hidden;
        }

        .hero-wrapper::before {
            content: '';
            position: absolute;
            top: -100px; left: -50px;
            width: 300px; height: 300px;
            background: rgba(98, 159, 173, 0.2);
            border-radius: 50%;
        }
        .hero-wrapper::after {
            content: '';
            position: absolute;
            bottom: -50px; right: -50px;
            width: 400px; height: 400px;
            background: rgba(41, 99, 116, 0.3);
            border-radius: 50%;
        }
        
        .hero { position: relative; z-index: 2; }
        .hero h1 { font-size: 3.2rem; font-weight: 800; margin-bottom: 0.5rem; letter-spacing: -0.02em; }
        .hero p { color: var(--cream); font-size: 1.25rem; font-weight: 500; opacity: 0.9; }

        .main-content {
            margin-top: -6rem;
            margin-bottom: 3rem;
            position: relative;
            z-index: 10;
        }

        .featured-intro {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        
        .featured-badge {
            display: inline-block;
            background: var(--cream);
            color: var(--dark-blue);
            padding: 0.6rem 2rem;
            border-radius: 50px;
            font-weight: 800;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .card { 
            background: var(--white); 
            border-radius: 24px; 
            box-shadow: 0 25px 50px rgba(12, 44, 85, 0.08); 
            padding: 3.5rem; 
            position: relative; 
            overflow: hidden; 
        }

        .profile-header { 
            display: flex; 
            gap: 3rem; 
            align-items: center; 
            flex-wrap: wrap; 
            padding-bottom: 2.5rem;
            border-bottom: 2px solid rgba(41, 99, 116, 0.1);
        }
        
        .profile-img { 
            width: 220px; 
            height: 220px; 
            border-radius: 50%; 
            object-fit: cover; 
            box-shadow: 0 12px 30px rgba(12, 44, 85, 0.15); 
            background: var(--cream); 
        }
        
        .profile-info { flex: 1; min-width: 300px; }
        .profile-info h2 { font-size: 2.5rem; color: var(--dark-blue); margin-bottom: 0.5rem; font-weight: 800; letter-spacing: -0.02em; }
        .bio { color: var(--text-muted); margin-bottom: 1.5rem; font-size: 1.1rem; max-width: 800px; }

        .info-grid {
            display: grid; 
            grid-template-columns: repeat(2, 1fr); 
            gap: 3rem; 
            margin-top: 3rem;
        }

        .section-title { 
            color: var(--teal); 
            font-size: 1.25rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }

        .ar-item {
            background: rgba(98, 159, 173, 0.06); 
            padding: 1.25rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .ar-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(41, 99, 116, 0.08);
            background: rgba(98, 159, 173, 0.1);
        }

        .item-title { color: var(--dark-blue); font-weight: 700; font-size: 1.1rem; display: block; margin-bottom: 0.25rem; }
        .item-link { color: var(--teal); font-size: 0.9rem; font-weight: 600; display: inline-block; margin-top: 0.25rem; }
        .item-link:hover { color: var(--dark-blue); }
        .date-label { color: var(--text-muted); font-size: 0.9rem; display: block; }

        .cta-box { 
            background: var(--teal); 
            border-radius: 24px; 
            padding: 4rem 2rem; 
            text-align: center; 
            color: var(--white); 
            margin-top: 4rem;
            box-shadow: 0 20px 40px rgba(41, 99, 116, 0.2);
            position: relative;
            overflow: hidden;
        }

        .cta-box::after {
            content: '';
            position: absolute;
            top: -50px; left: -50px;
            width: 200px; height: 200px;
            background: rgba(237, 237, 206, 0.1);
            border-radius: 50%;
        }
        
        .cta-box h3 { font-size: 2.2rem; margin-bottom: 1rem; font-weight: 800; position: relative; z-index: 2; }
        .cta-box p { color: var(--cream); margin-bottom: 2.5rem; font-size: 1.15rem; position: relative; z-index: 2;}
        
        .btn-group { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; position: relative; z-index: 2;}
        
        .btn { 
            display: inline-block; 
            padding: 0.9rem 2.5rem; 
            border-radius: 50px; 
            font-weight: 700; 
            cursor: pointer; 
            border: 2px solid transparent; 
        }
        
        .btn-primary { background: var(--dark-blue); color: var(--white); }
        .btn-primary:hover { background: var(--teal); color: var(--white); box-shadow: 0 4px 15px rgba(12, 44, 85, 0.3); transform: translateY(-2px); }
        
        .cta-box .btn-primary { background: var(--cream); color: var(--dark-blue); }
        .cta-box .btn-primary:hover { background: var(--white); transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.15); }
        .cta-box .btn-outline { border-color: var(--cream); color: var(--cream); }
        .cta-box .btn-outline:hover { background: rgba(237, 237, 206, 0.15); transform: translateY(-2px); }

        /* API Link Footer Section */
        .api-doc-footer {
            text-align: center;
            margin-top: 2.5rem;
            padding-bottom: 3rem;
        }
        .api-doc-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.8rem 1.5rem;
            background: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            border: 1px solid rgba(41, 99, 116, 0.1);
            color: var(--teal);
            font-weight: 600;
            font-size: 0.95rem;
        }
        .api-doc-link a {
            color: var(--dark-blue);
            text-decoration: underline;
            text-decoration-thickness: 2px;
            text-underline-offset: 3px;
        }
        .api-doc-link a:hover {
            color: var(--light-teal);
        }

        .empty-state { text-align: center; padding: 5rem 2rem; }
        .empty-state h2 { color: var(--dark-blue); font-size: 2rem; margin-bottom: 1rem; }
        .empty-state p { color: var(--text-muted); font-size: 1.1rem; }

        @media (max-width: 768px) {
            .hero-wrapper { border-radius: 0 0 10vw 10vw; padding-bottom: 8rem; }
            .profile-header { flex-direction: column; text-align: center; gap: 2rem; }
            .profile-info h2 { font-size: 2rem; }
            .hero h1 { font-size: 2.5rem; }
            .info-grid { grid-template-columns: 1fr; gap: 2rem; }
            .card { padding: 2rem; }
        }
    </style>
</head>
<body>

<div class="bg-shape bg-shape-1"></div>
<div class="bg-shape bg-shape-2"></div>

<div class="hero-wrapper">
    <header class="hero container">
        <h1>Welcome to AR Alumni</h1>
        <p>Highlighting excellence in our alumni community.</p>
    </header>
</div>

<div class="container main-content">
    <?php if (isset($details['user']['id'])): ?>
    
    <div class="featured-intro">
        <span class="featured-badge">✦ Today's Featured Alumnus</span>
    </div>

    <main class="card">
        <div class="profile-header">
            <img src="<?= $details['profile']['profile_image'] ?? base_url('uploads/profiles/default.png') ?>" class="profile-img" alt="Profile Image">
            <div class="profile-info">
                <h2><?= htmlspecialchars($details['user']['first_name'] . ' ' . $details['user']['last_name']) ?></h2>
                <p class="bio"><?= nl2br(htmlspecialchars($details['profile']['bio'] ?? '')) ?></p>
                <?php if(!empty($details['profile']['linkedin_url'])): ?>
                    <a href="<?= $details['profile']['linkedin_url'] ?>" target="_blank" class="btn btn-primary">View LinkedIn Profile</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="info-grid">
            <section>
                <h3 class="section-title">Qualifications</h3>
                <?php foreach($details['degrees'] as $d): ?>
                    <div class="ar-item">
                        <span class="item-title"><?= htmlspecialchars($d['degree_name']) ?></span>
                        <span class="date-label"><?= htmlspecialchars($d['institution']) ?></span>
                    </div>
                <?php endforeach; ?>
            </section>

            <section>
                <h3 class="section-title">Certifications & Licences</h3>
                <?php foreach($details['certifications'] as $c): ?>
                    <div class="ar-item">
                        <span class="item-title"><?= htmlspecialchars($c['cert_name']) ?></span>
                        <a href="<?= $c['cert_url'] ?>" class="item-link" target="_blank">View Certificate →</a>
                    </div>
                <?php endforeach; ?>
                <?php foreach($details['licences'] as $l): ?>
                    <div class="ar-item">
                        <span class="item-title"><?= htmlspecialchars($l['licence_name']) ?></span>
                        <span class="date-label"><?= htmlspecialchars($l['awarding_body']) ?></span>
                    </div>
                <?php endforeach; ?>
            </section>

            <section>
                <h3 class="section-title">Short Courses</h3>
                <?php foreach($details['courses'] as $course): ?>
                    <div class="ar-item">
                        <span class="item-title"><?= htmlspecialchars($course['course_name']) ?></span>
                        <span class="date-label"><?= htmlspecialchars($course['provider']) ?></span>
                    </div>
                <?php endforeach; ?>
            </section>

            <section>
                <h3 class="section-title">Employment</h3>
                <?php foreach($details['employment'] as $e): ?>
                    <div class="ar-item">
                        <span class="item-title"><?= htmlspecialchars($e['job_title']) ?></span>
                        <span class="date-label"><?= htmlspecialchars($e['company']) ?></span>
                    </div>
                <?php endforeach; ?>
            </section>
        </div>
    </main>
    <?php else: ?>
        <div class="card empty-state">
            <h2>No winner selected for today yet!</h2>
            <p>Check back soon to see our next featured alumnus.</p>
        </div>
    <?php endif; ?>

    <section class="cta-box">
        <h3>Are you an alumnus?</h3>
        <p>Log in to your profile to place a bid for tomorrow's featured slot.</p>
        <div class="btn-group">
            <a href="<?= site_url('auth/login') ?>" class="btn btn-primary">Alumni Login</a>
            <a href="<?= site_url('auth/register') ?>" class="btn btn-outline">Create Account</a>
        </div>
    </section>

    <footer class="api-doc-footer">
        <div class="api-doc-link">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>
            <span>Wanna check out our API doc? Heres the link: <a href="<?= site_url('api-docs') ?>">API Docs</a></span>
        </div>
    </footer>
</div>

</body>
</html>