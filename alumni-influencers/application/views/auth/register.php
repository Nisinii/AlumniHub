<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register – AR Alumni Platform</title>
    
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
            background: var(--dark-blue);
            color: var(--text-main); 
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center;
            -webkit-font-smoothing: antialiased;
            position: relative;
            overflow: hidden;
            padding: 2rem 1.5rem; 
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
            background: var(--cream);
            top: -100px; left: -100px;
        }
        .bg-shape-2 {
            width: 550px; height: 550px;
            background: var(--cream);
            bottom: -150px; right: -150px;
        }

        /* Main Card */
        .card { 
            background: var(--white); 
            border-radius: 24px; 
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25); 
            padding: 3.5rem 3rem; 
            width: 100%; 
            max-width: 500px;
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
        .alert strong { display: block; margin-bottom: 0.5rem; }

        /* Form Elements */
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        
        label { display: block; font-size: 0.9rem; font-weight: 700; color: var(--dark-blue); margin-bottom: 0.5rem; }
        
        input[type="text"],
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
            border-color: var(--teal); 
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(41, 99, 116, 0.15); 
        }

        .hint { font-size: 0.8rem; color: var(--text-muted); margin-top: -0.8rem; margin-bottom: 1.2rem; display: block; }

        /* Buttons */
        button[type=submit] { 
            width: 100%; 
            padding: 0.9rem; 
            color: var(--white); 
            background: var(--teal);
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
            background: var(--dark-blue); 
            transform: translateY(-2px); 
            box-shadow: 0 4px 12px rgba(12, 44, 85, 0.2); 
        }

        .footer-link { text-align: center; margin-top: 1.5rem; font-size: 0.95rem; color: var(--text-muted); }
        .footer-link a { color: var(--teal); text-decoration: none; font-weight: 700; transition: color 0.2s; }
        .footer-link a:hover { color: var(--dark-blue); }

        @media (max-width: 480px) {
            .card { padding: 2.5rem 2rem; }
            h1 { font-size: 1.75rem; }
            .row { grid-template-columns: 1fr; gap: 0; }
        }
    </style>
</head>
<body>

<div class="bg-shape bg-shape-1"></div>
<div class="bg-shape bg-shape-2"></div>

<div class="card">
    <h1>Create Your Account</h1>
    <p class="sub">Join the AR Alumni Platform</p>

    <?php if ( ! empty($errors)): ?>
        <div class="alert alert-danger">
            <strong>Please fix the following:</strong>
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

    <?php if (empty($success)):?>
    <?= form_open('auth/register') ?>

        <div class="row">
            <div>
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" placeholder="Jane"
                       value="<?= htmlspecialchars(isset($old['first_name']) ? $old['first_name'] : '', ENT_QUOTES) ?>"
                       required>
            </div>
            <div>
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" placeholder="Doe"
                       value="<?= htmlspecialchars(isset($old['last_name']) ? $old['last_name'] : '', ENT_QUOTES) ?>"
                       required>
            </div>
        </div>

        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" placeholder="jane.doe@university.edu" 
               value="<?= htmlspecialchars(isset($old['email']) ? $old['email'] : '', ENT_QUOTES) ?>"required>
        <span class="hint">Please use your official university email (@<?= ALLOWED_EMAIL_DOMAIN ?>)</span>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required>
        <span class="hint">Min 8 chars · uppercase · lowercase · number · special character</span>

        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="••••••••" required>

        <button type="submit">Create Account</button>
    <?= form_close() ?>
    <?php endif; ?>

    <div class="footer-link">
        Already have an account? <a href="<?= site_url('auth/login') ?>">Log in</a>
    </div>
</div>
</body>
</html>