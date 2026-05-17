<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><path fill='%231F6F5F' d='M12 0C12 6.627 17.373 12 24 12C17.373 12 12 17.373 12 24C12 17.373 6.627 12 0 12C6.627 12 12 6.627 12 0Z'/></svg>">
    <title>Staff Registration – University Analytics Dashboard</title>
    
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
            /* Kept the inverted dark background from your original register design */
            background: var(--primary-dark);
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
            max-width: 500px;
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
        .alert strong { display: block; margin-bottom: 0.5rem; }

        /* Form Elements */
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        
        label { display: block; font-size: 0.9rem; font-weight: 700; color: var(--primary-dark); margin-bottom: 0.5rem; }
        
        input[type="text"],
        input[type="email"], 
        input[type="password"],
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
        }
        
        input:focus, select:focus { 
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

        .footer-link { text-align: center; margin-top: 1.5rem; font-size: 0.95rem; color: var(--text-muted); }
        .footer-link a { color: var(--primary-mid); text-decoration: none; font-weight: 700; transition: color 0.2s; }
        .footer-link a:hover { color: var(--primary-dark); }

        @media (max-width: 480px) {
            .card { padding: 2.5rem 2rem; }
            h1 { font-size: 1.75rem; }
            .row { grid-template-columns: 1fr; gap: 0; }
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
    <h1>Create Staff Account</h1>
    <p class="sub">University Analytics Dashboard</p>

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

        <label for="department">Department</label>
        <select id="department" name="department" required>
            <option value="" disabled selected>Select your department</option>
            <option value="Computer Science">Computer Science</option>
            <option value="Business School">Business School</option>
            <option value="Careers & Employability">Careers & Employability</option>
            <option value="Alumni Relations">Alumni Relations</option>
            <option value="Executive Board">Executive Board</option>
        </select>

        <label for="email">Staff Email Address</label>
        <input type="email" id="email" name="email" placeholder="jane.doe@iit.ac.lk" 
               value="<?= htmlspecialchars(isset($old['email']) ? $old['email'] : '', ENT_QUOTES) ?>" required>
        <span class="hint">Please use your official university email (@<?= ALLOWED_EMAIL_DOMAIN ?>)</span>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required>
        <span class="hint">Min 8 chars · uppercase · number · special character</span>

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