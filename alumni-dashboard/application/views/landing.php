<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><path fill='%231F6F5F' d='M12 0C12 6.627 17.373 12 24 12C17.373 12 12 17.373 12 24C12 17.373 6.627 12 0 12C6.627 12 12 6.627 12 0Z'/></svg>">
    <title>Welcome - University Analytics Dashboard</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            /* CW2 Custom Palette */
            --primary-dark: #1F6F5F;
            --primary-mid: #2FA084;
            --primary-light: #6FCF97;
            --bg-main: #EEEEEE;
            --white: #ffffff;
            --text-main: #1f2937;
            --text-muted: #6b7280;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: var(--bg-main);
            color: var(--text-main); 
            line-height: 1.6; 
            -webkit-font-smoothing: antialiased;
            position: relative;
            overflow-x: hidden;
        }
        
        /* Abstract Background Shapes */
        .bg-shape {
            position: absolute;
            border-radius: 50%;
            z-index: -1;
            opacity: 0.4;
        }
        .bg-shape-1 {
            width: 400px; height: 400px;
            background: var(--primary-light);
            top: 40%; left: -150px;
        }
        .bg-shape-2 {
            width: 350px; height: 350px;
            background: var(--primary-mid);
            bottom: 5%; right: -100px;
        }

        a { text-decoration: none; transition: all 0.2s ease; }
        .container { max-width: 1100px; margin: 0 auto; padding: 0 1.5rem; }
        
        /* Hero Section with Curved Bottom */
        .hero-wrapper {
            background-color: var(--primary-dark);
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
            background: rgba(111, 207, 151, 0.2); /* primary-light */
            border-radius: 50%;
        }
        .hero-wrapper::after {
            content: '';
            position: absolute;
            bottom: -50px; right: -50px;
            width: 400px; height: 400px;
            background: rgba(47, 160, 132, 0.3); /* primary-mid */
            border-radius: 50%;
        }
        
        .hero { position: relative; z-index: 2; }
        .hero h1 { font-size: 3.2rem; font-weight: 800; margin-bottom: 0.5rem; letter-spacing: -0.02em; }
        .hero p { color: var(--bg-main); font-size: 1.25rem; font-weight: 500; opacity: 0.9; }

        .main-content {
            margin-top: -6rem;
            margin-bottom: 3rem;
            position: relative;
            z-index: 10;
        }

        .featured-intro { text-align: center; margin-bottom: 1.5rem; }
        
        .featured-badge {
            display: inline-block;
            background: var(--bg-main);
            color: var(--primary-dark);
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
            box-shadow: 0 25px 50px rgba(31, 111, 95, 0.08); 
            padding: 3.5rem; 
            position: relative; 
            overflow: hidden; 
        }

        .intro-text {
            text-align: center;
            max-width: 800px;
            margin: 0 auto 3rem;
            font-size: 1.15rem;
            color: var(--text-muted);
        }

        /* Features Grid */
        .info-grid {
            display: grid; 
            grid-template-columns: repeat(3, 1fr); 
            gap: 2rem; 
        }

        .ar-item {
            background: rgba(47, 160, 132, 0.06); 
            padding: 2rem 1.5rem;
            border-radius: 16px;
            text-align: center;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .ar-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(31, 111, 95, 0.08);
            background: rgba(47, 160, 132, 0.1);
        }

        .item-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            display: block;
        }

        .item-title { color: var(--primary-dark); font-weight: 800; font-size: 1.25rem; display: block; margin-bottom: 0.5rem; }
        .date-label { color: var(--text-muted); font-size: 0.95rem; display: block; line-height: 1.5;}

        /* CTA Section */
        .cta-box { 
            background: var(--primary-mid); 
            border-radius: 24px; 
            padding: 4rem 2rem; 
            text-align: center; 
            color: var(--white); 
            margin-top: 4rem;
            box-shadow: 0 20px 40px rgba(31, 111, 95, 0.2);
            position: relative;
            overflow: hidden;
        }

        .cta-box::after {
            content: '';
            position: absolute;
            top: -50px; left: -50px;
            width: 200px; height: 200px;
            background: rgba(238, 238, 238, 0.15);
            border-radius: 50%;
        }
        
        .cta-box h3 { font-size: 2.2rem; margin-bottom: 1rem; font-weight: 800; position: relative; z-index: 2; }
        .cta-box p { color: var(--bg-main); margin-bottom: 2.5rem; font-size: 1.15rem; position: relative; z-index: 2;}
        
        .btn-group { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; position: relative; z-index: 2;}
        
        .btn { 
            display: inline-block; 
            padding: 0.9rem 2.5rem; 
            border-radius: 50px; 
            font-weight: 700; 
            cursor: pointer; 
            border: 2px solid transparent; 
        }
        
        .btn-primary { background: var(--bg-main); color: var(--primary-dark); }
        .btn-primary:hover { background: var(--white); transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.15); }
        .btn-outline { border-color: var(--bg-main); color: var(--bg-main); }
        .btn-outline:hover { background: rgba(238, 238, 238, 0.15); transform: translateY(-2px); }

        /* API Link Footer Section */
        .api-doc-footer { text-align: center; margin-top: 2.5rem; padding-bottom: 3rem; }
        .api-doc-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.8rem 1.5rem;
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            border: 1px solid rgba(47, 160, 132, 0.2);
            color: var(--primary-mid);
            font-weight: 600;
            font-size: 0.95rem;
        }
        .api-doc-link a { color: var(--primary-dark); text-decoration: underline; text-decoration-thickness: 2px; text-underline-offset: 3px; }
        .api-doc-link a:hover { color: var(--primary-light); }

        @media (max-width: 768px) {
            .hero-wrapper { border-radius: 0 0 10vw 10vw; padding-bottom: 8rem; }
            .hero h1 { font-size: 2.5rem; }
            .info-grid { grid-template-columns: 1fr; gap: 1.5rem; }
            .card { padding: 2rem; }
        }
    </style>
</head>
<body>

<div class="bg-shape bg-shape-1"></div>
<div class="bg-shape bg-shape-2"></div>

<div class="hero-wrapper">
    <header class="hero container">
        <h1>University Analytics</h1>
        <p>Transforming alumni data into actionable academic intelligence.</p>
    </header>
</div>

<div class="container main-content">
    
    <div class="featured-intro">
        <span class="featured-badge">✦ Secure Staff Portal</span>
    </div>

    <main class="card">
        <p class="intro-text">
            Welcome to the centralized hub for analyzing graduate outcomes. Connect securely to the Alumni Influencer API to generate real-time reports, track emerging career pathways, and proactively detect curriculum skills gaps.
        </p>

        <div class="info-grid">
            <div class="ar-item">
                <span class="item-icon">📊</span>
                <span class="item-title">Interactive Insights</span>
                <span class="date-label">Visualize alumni data through dynamic charts and filter by program or graduation date.</span>
            </div>
            
            <div class="ar-item">
                <span class="item-icon">🎯</span>
                <span class="item-title">Skills Gap Detection</span>
                <span class="date-label">Identify the certifications and short courses graduates pursue after leaving university.</span>
            </div>
            
            <div class="ar-item">
                <span class="item-icon">📑</span>
                <span class="item-title">Export & Reporting</span>
                <span class="date-label">Generate custom reports and export critical intelligence to CSV or PDF formats.</span>
            </div>
        </div>
    </main>

    <section class="cta-box">
        <h3>Are you University Staff?</h3>
        <p>Log in to access your dashboard and manage your scoped API keys securely.</p>
        <div class="btn-group">
            <a href="<?= site_url('auth/login') ?>" class="btn btn-primary">Staff Login</a>
            <a href="<?= site_url('auth/register') ?>" class="btn btn-outline">Register Account</a>
        </div>
    </section>

    <footer class="api-doc-footer">
        <div class="api-doc-link">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>
            <span>Need to review the architecture? Check out the <a href="<?= site_url('docs') ?>">System Docs</a></span>
        </div>
    </footer>
</div>

</body>
</html>