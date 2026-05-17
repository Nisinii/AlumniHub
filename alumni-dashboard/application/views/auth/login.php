<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><path fill='%231F6F5F' d='M12 0C12 6.627 17.373 12 24 12C17.373 12 12 17.373 12 24C12 17.373 6.627 12 0 12C6.627 12 12 6.627 12 0Z'/></svg>">
    <title>Staff Login – University Analytics Dashboard</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            /* New CW2 Custom Palette */
            --primary-dark: #1F6F5F;
            --primary-mid: #2FA084;
            --primary-light: #6FCF97;
            --bg-main: #EEEEEE;
            
            --white: #ffffff;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --error-bg: #fef2f2;
            --error-text: #991b1b;
            --success-bg: #f0fdf4;
            --success-text: #166534;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: var(--bg-main); 
            color: var(--text-main); 
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center;
            -webkit-font-smoothing: antialiased;
            position: relative;
            overflow: hidden;
            padding: 1.5rem;
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
            top: -100px; left: -100px;
        }
        .bg-shape-2 {
            width: 500px; height: 500px;
            background: var(--primary-mid);
            bottom: -150px; right: -150px;
        }

        /* Main Card */
        .card { 
            background: var(--white); 
            border-radius: 24px; 
            /* Shadow adjusted to use #1F6F5F RGB */
            box-shadow: 0 25px 50px rgba(31, 111, 95, 0.08); 
            padding: 3.5rem 3rem; 
            width: 100%; 
            max-width: 440px; 
            position: relative;
            z-index: 10;
        }

        h1 { font-size: 2rem; color: var(--primary-dark); margin-bottom: 0.25rem; font-weight: 800; letter-spacing: -0.02em; text-align: center; }
        p.sub { color: var(--primary-mid); margin-bottom: 2rem; font-size: 1rem; font-weight: 500; text-align: center; }

        /* Alerts */
        .alert { padding: 1rem 1.25rem; border-radius: 12px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 500; }
        .alert-danger  { background: var(--error-bg); color: var(--error-text); border: 1px solid rgba(153, 27, 27, 0.1); }
        .alert-success { background: var(--success-bg); color: var(--success-text); border: 1px solid rgba(22, 101, 52, 0.1); }
        .alert ul { margin: 0.3rem 0 0 1.2rem; }

        /* Form Elements */
        label { display: block; font-size: 0.9rem; font-weight: 700; color: var(--primary-dark); margin-bottom: 0.5rem; }
        
        input[type="email"], 
        input[type="password"] { 
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
        }
        
        input:focus { 
            outline: none; 
            border-color: var(--primary-light); 
            background: var(--white);
            /* Focus ring adjusted to use #6FCF97 RGB */
            box-shadow: 0 0 0 4px rgba(111, 207, 151, 0.25); 
        }

        .forgot { text-align: right; margin-top: -0.75rem; margin-bottom: 1.5rem; font-size: 0.85rem; }
        .forgot a { color: var(--primary-mid); text-decoration: none; font-weight: 600; transition: color 0.2s; }
        .forgot a:hover { color: var(--primary-dark); }

        /* Buttons */
        button[type=submit], .btn-secondary { 
            width: 100%; 
            padding: 0.9rem; 
            color: var(--white); 
            border: none; 
            border-radius: 50px; 
            font-size: 1rem; 
            font-weight: 700; 
            cursor: pointer; 
            transition: all 0.2s ease; 
            font-family: inherit;
        }
        
        button[type=submit] { background: var(--primary-dark); }
        button[type=submit]:hover { 
            background: var(--primary-mid); 
            transform: translateY(-2px); 
            /* Shadow adjusted to use #1F6F5F RGB */
            box-shadow: 0 4px 12px rgba(31, 111, 95, 0.2); 
        }

        .footer-link { text-align: center; margin-top: 1.5rem; font-size: 0.95rem; color: var(--text-muted); }
        .footer-link a { color: var(--primary-dark); text-decoration: none; font-weight: 700; transition: color 0.2s; }
        .footer-link a:hover { color: var(--primary-light); }

        /* Resend Section */
        /* Border top adjusted to use #2FA084 RGB */
        .resend-container { margin-top: 2rem; padding-top: 1.5rem; border-top: 2px solid rgba(47, 160, 132, 0.15); }
        .resend-link { display: block; text-align: center; font-size: 0.85rem; color: var(--primary-mid); text-decoration: none; font-weight: 600; cursor: pointer; transition: color 0.2s; }
        .resend-link:hover { color: var(--primary-dark); }
        
        .resend-form { display: none; margin-top: 1.25rem; }
        .resend-form.active { display: block; animation: fadeIn 0.3s ease; }
        
        .resend-flex { display: flex; gap: 0.75rem; align-items: center; }
        .resend-flex input { margin-bottom: 0 !important; }
        
        .btn-inline { background: var(--primary-mid); width: auto; padding: 0.85rem 1.5rem; border-radius: 12px; }
        .btn-inline:hover { background: var(--primary-dark); transform: translateY(0) !important; box-shadow: none !important; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 480px) {
            .card { padding: 2.5rem 2rem; }
            h1 { font-size: 1.75rem; }
            .resend-flex { flex-direction: column; }
            .btn-inline { width: 100%; border-radius: 50px; }
        }

        /* Back to Home Button */
        .back-home {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            transition: color 0.2s ease;
        }
        .back-home:hover {
            color: var(--primary-dark);
        }
        .back-home svg {
            transition: transform 0.2s ease;
        }
        .back-home:hover svg {
            transform: translateX(-4px); /* Smooth slide effect on hover */
        }

    </style>
</head>
<body>

<div class="bg-shape bg-shape-1"></div>
<div class="bg-shape bg-shape-2"></div>

<div class="card">
    <a href="<?= base_url() ?>" class="back-home">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 12H5"></path>
            <path d="M12 19l-7-7 7-7"></path>
        </svg>
        Back to Home
    </a>
    <h1>Staff Login</h1>
    <p class="sub">University Analytics Dashboard</p>

    <?php if ( ! empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= $e ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ( ! empty($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <?= form_open('auth/login') ?>
        <label for="email">Staff Email</label>
        <input type="email" id="email" name="email" placeholder="admin@iit.ac.lk" required autofocus>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required>

        <div class="forgot">
            <a href="<?= site_url('auth/forgot_password') ?>">Forgot password?</a>
        </div>

        <button type="submit">Log In</button>
    <?= form_close() ?>

    <div class="footer-link">
        Don't have an account? <a href="<?= site_url('auth/register') ?>">Register</a>
    </div>

    <div class="resend-container">
        <a class="resend-link" onclick="toggleResendForm()">Didn't get the verification email?</a>
        
        <div id="resendForm" class="resend-form <?= (!empty($errors) && strpos(implode(' ', $errors), 'expired') !== false) ? 'active' : '' ?>">
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1rem; text-align: center;">
                Enter your email to request a new verification link.
            </p>
            <?= form_open('auth/resend_verification') ?>
                <div class="resend-flex">
                    <input type="email" name="email" placeholder="Email address" required>
                    <button type="submit" class="btn-inline btn-secondary">Resend</button>
                </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<script>
    function toggleResendForm() {
        const form = document.getElementById('resendForm');
        form.classList.toggle('active');
    }
</script>

</body>
</html>