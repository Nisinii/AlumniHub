<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><path fill='%231F6F5F' d='M12 0C12 6.627 17.373 12 24 12C17.373 12 12 17.373 12 24C12 17.373 6.627 12 0 12C6.627 12 12 6.627 12 0Z'/></svg>">
    <title>Reset Password – University Analytics Dashboard</title>
    
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
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: var(--primary-dark);
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
            opacity: 0.15; 
        }
        .bg-shape-1 {
            width: 450px; height: 450px;
            background: var(--bg-main);
            top: -100px; left: -100px;
        }
        .bg-shape-2 {
            width: 550px; height: 550px;
            background: var(--bg-main);
            bottom: -150px; right: -150px;
        }

        /* Main Card */
        .card { 
            background: var(--white); 
            border-radius: 24px; 
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25); 
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
        .alert ul { margin: 0.3rem 0 0 1.2rem; }

        /* Form Elements */
        label { display: block; font-size: 0.9rem; font-weight: 700; color: var(--primary-dark); margin-bottom: 0.5rem; }
        
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
            border-color: var(--primary-mid); 
            background: var(--white);
            /* Focus ring using primary-mid RGB */
            box-shadow: 0 0 0 4px rgba(47, 160, 132, 0.15); 
        }

        .hint { font-size: 0.8rem; color: var(--text-muted); margin-top: -0.8rem; margin-bottom: 1.2rem; display: block; }

        /* Buttons */
        button[type=submit] { 
            width: 100%; 
            padding: 0.9rem; 
            color: var(--white); 
            background: var(--primary-mid);
            border: none; 
            border-radius: 50px; 
            font-size: 1rem; 
            font-weight: 700; 
            cursor: pointer; 
            transition: all 0.2s ease; 
            font-family: inherit;
            margin-top: 0.5rem;
        }
        
        button[type=submit]:hover { 
            background: var(--primary-dark); 
            transform: translateY(-2px); 
            /* Shadow using primary-dark RGB */
            box-shadow: 0 4px 12px rgba(31, 111, 95, 0.2); 
        }

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
    <h1>Set New Password</h1>
    <p class="sub">Choose a strong password for your staff account.</p>

    <?php if ( ! empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= $e ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?= form_open('auth/reset_password/' . $token) ?>
        <label for="password">New Password</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required autofocus>
        <span class="hint">Min 8 chars · uppercase · number · special character</span>

        <label for="confirm_password">Confirm New Password</label>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="••••••••" required>

        <button type="submit">Reset Password</button>
    <?= form_close() ?>
</div>

</body>
</html>