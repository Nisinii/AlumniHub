<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password – AR Alumni Platform</title>
    
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
        p.sub { color: var(--teal); margin-bottom: 2.5rem; font-size: 1rem; font-weight: 500; text-align: center; line-height: 1.5; }

        /* Alerts */
        .alert { padding: 1rem 1.25rem; border-radius: 12px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 500; }
        .alert-danger  { background: var(--error-bg); color: var(--error-text); border: 1px solid rgba(153, 27, 27, 0.1); }
        .alert-success { background: var(--success-bg); color: var(--success-text); border: 1px solid rgba(22, 101, 52, 0.1); }
        .alert p { margin: 0.25rem 0; }
        .alert p:first-child { margin-top: 0; }
        .alert p:last-child { margin-bottom: 0; }

        /* Form Elements */
        label { display: block; font-size: 0.9rem; font-weight: 700; color: var(--dark-blue); margin-bottom: 0.5rem; }
        
        input[type="email"] { 
            width: 100%; 
            padding: 0.85rem 1rem; 
            background: #f9fafb;
            border: 2px solid #e5e7eb; 
            border-radius: 12px; 
            font-size: 1rem; 
            font-family: inherit;
            color: var(--text-main);
            margin-bottom: 1.5rem; 
            transition: all 0.2s ease; 
        }
        
        input:focus { 
            outline: none; 
            border-color: var(--light-teal); 
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(98, 159, 173, 0.15); 
        }

        /* Buttons */
        button[type=submit] { 
            width: 100%; 
            padding: 0.9rem; 
            background: var(--dark-blue); 
            color: var(--white); 
            border: none; 
            border-radius: 50px; 
            font-size: 1rem; 
            font-weight: 700; 
            cursor: pointer; 
            transition: all 0.2s ease; 
            font-family: inherit;
        }
        
        button[type=submit]:hover { 
            background: var(--teal); 
            transform: translateY(-2px); 
            box-shadow: 0 4px 12px rgba(12, 44, 85, 0.2); 
        }

        .footer-link { text-align: center; margin-top: 2rem; font-size: 0.95rem; }
        .footer-link a { color: var(--text-muted); text-decoration: none; font-weight: 600; transition: color 0.2s; display: inline-flex; align-items: center; gap: 0.5rem; }
        .footer-link a:hover { color: var(--dark-blue); }

        @media (max-width: 480px) {
            .card { padding: 2.5rem 2rem; }
            h1 { font-size: 1.75rem; }
        }
    </style>
</head>
<body>

<div class="bg-shape bg-shape-1"></div>
<div class="bg-shape bg-shape-2"></div>

<div class="card">
    <h1>Forgot Password</h1>
    <p class="sub">Enter your email address and we'll send you a secure link to reset your password.</p>

    <?php if ( ! empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $e): ?><p><?= $e ?></p><?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ( ! empty($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php else: ?>

    <?= form_open('auth/forgot_password') ?>
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" placeholder="jane.doe@university.edu" required autofocus>
        <button type="submit">Send Reset Link</button>
    <?= form_close() ?>

    <?php endif; ?>

    <div class="footer-link">
        <a href="<?= site_url('auth/login') ?>">← Back to Login</a>
    </div>
</div>

</body>
</html>