<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – AR Alumni Platform</title>
    
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
            --error-bg: #fef2f2;
            --error-text: #991b1b;
            --success-bg: #f0fdf4;
            --success-text: #166534;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: var(--cream); 
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
            background: var(--light-teal);
            top: -100px; left: -100px;
        }
        .bg-shape-2 {
            width: 500px; height: 500px;
            background: var(--teal);
            bottom: -150px; right: -150px;
        }

        /* Main Card */
        .card { 
            background: var(--white); 
            border-radius: 24px; 
            box-shadow: 0 25px 50px rgba(12, 44, 85, 0.08); 
            padding: 3.5rem 3rem; 
            width: 100%; 
            max-width: 440px; 
            position: relative;
            z-index: 10;
        }

        h1 { font-size: 2rem; color: var(--dark-blue); margin-bottom: 0.25rem; font-weight: 800; letter-spacing: -0.02em; text-align: center; }
        p.sub { color: var(--teal); margin-bottom: 2rem; font-size: 1rem; font-weight: 500; text-align: center; }

        /* Alerts */
        .alert { padding: 1rem 1.25rem; border-radius: 12px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 500; }
        .alert-danger  { background: var(--error-bg); color: var(--error-text); border: 1px solid rgba(153, 27, 27, 0.1); }
        .alert-success { background: var(--success-bg); color: var(--success-text); border: 1px solid rgba(22, 101, 52, 0.1); }
        .alert ul { margin: 0.3rem 0 0 1.2rem; }

        /* Form Elements */
        label { display: block; font-size: 0.9rem; font-weight: 700; color: var(--dark-blue); margin-bottom: 0.5rem; }
        
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
            border-color: var(--light-teal); 
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(98, 159, 173, 0.15); 
        }

        .forgot { text-align: right; margin-top: -0.75rem; margin-bottom: 1.5rem; font-size: 0.85rem; }
        .forgot a { color: var(--teal); text-decoration: none; font-weight: 600; transition: color 0.2s; }
        .forgot a:hover { color: var(--dark-blue); }

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
        
        button[type=submit] { background: var(--dark-blue); }
        button[type=submit]:hover { background: var(--teal); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(12, 44, 85, 0.2); }

        .footer-link { text-align: center; margin-top: 1.5rem; font-size: 0.95rem; color: var(--text-muted); }
        .footer-link a { color: var(--dark-blue); text-decoration: none; font-weight: 700; transition: color 0.2s; }
        .footer-link a:hover { color: var(--light-teal); }

        /* Resend Section */
        .resend-container { margin-top: 2rem; padding-top: 1.5rem; border-top: 2px solid rgba(41, 99, 116, 0.1); }
        .resend-link { display: block; text-align: center; font-size: 0.85rem; color: var(--teal); text-decoration: none; font-weight: 600; cursor: pointer; transition: color 0.2s; }
        .resend-link:hover { color: var(--dark-blue); }
        
        .resend-form { display: none; margin-top: 1.25rem; }
        .resend-form.active { display: block; animation: fadeIn 0.3s ease; }
        
        .resend-flex { display: flex; gap: 0.75rem; align-items: center; }
        .resend-flex input { margin-bottom: 0 !important; }
        
        .btn-inline { background: var(--teal); width: auto; padding: 0.85rem 1.5rem; border-radius: 12px; }
        .btn-inline:hover { background: var(--dark-blue); transform: translateY(0) !important; box-shadow: none !important; }

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
    </style>
</head>
<body>

<div class="bg-shape bg-shape-1"></div>
<div class="bg-shape bg-shape-2"></div>

<div class="card">
    <h1>Alumni Login</h1>
    <p class="sub">AR Alumni Platform</p>

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
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" placeholder="john@example.com" required autofocus>

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