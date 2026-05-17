<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><path fill='%23629FAD' d='M12 0C12 6.627 17.373 12 24 12C17.373 12 12 17.373 12 24C12 17.373 6.627 12 0 12C6.627 12 12 6.627 12 0Z'/></svg>">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) . ' - AR Alumni' : 'AR Alumni Platform' ?></title>
    
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
            --border-light: #e5e7eb;
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
            -webkit-font-smoothing: antialiased;
            position: relative;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Abstract Background Shapes */
        .bg-shape {
            position: absolute;
            border-radius: 50%;
            z-index: -1;
            opacity: 0.15;
        }
        .bg-shape-1 {
            width: 400px; height: 400px;
            background: var(--cream);
            top: 10%; left: -150px;
        }
        .bg-shape-2 {
            width: 600px; height: 600px;
            background: var(--light-teal);
            bottom: -200px; right: -200px;
        }

        /* Navigation Bar */
        nav { 
            background: var(--cream);
            padding: 2rem 2.5rem; 
            display: flex; 
            align-items: center; 
            justify-content: space-between;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        nav .brand { 
            color: var(--dark-blue);
            font-weight: 800; 
            font-size: 1.5rem; 
            text-decoration: none;
            letter-spacing: -0.02em;
        }
        
        nav div { display: flex; gap: 1.5rem; align-items: center; }
        
        nav a:not(.brand) { 
            color: var(--teal);
            text-decoration: none; 
            font-size: 1.2rem;
            font-weight: 700;
            transition: color 0.2s ease;
        }
        
        nav a:not(.brand):hover { color: var(--dark-blue); }

        /* Main Container & Cards */
        .container { max-width: 900px; margin: 3rem auto; padding: 0 1.5rem; position: relative; z-index: 10; }
        
        .card { 
            background: var(--white); 
            border-radius: 20px; 
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25); 
            padding: 2.5rem; 
            margin-bottom: 2rem; 
        }
        
        .card h2 { 
            font-size: 1.4rem; 
            color: var(--dark-blue); 
            margin-bottom: 1.5rem; 
            border-bottom: 2px solid rgba(41, 99, 116, 0.1); 
            padding-bottom: 0.8rem;
            font-weight: 800;
        }

        /* Alerts */
        .alert { padding: 1rem 1.2rem; border-radius: 12px; margin-bottom: 1.5rem; font-size: 0.95rem; font-weight: 500; }
        .alert-danger { background: var(--error-bg); color: var(--error-text); border: 1px solid rgba(153, 27, 27, 0.1); }
        .alert-success { background: var(--success-bg); color: var(--success-text); border: 1px solid rgba(22, 101, 52, 0.1); }
        .alert-info { background: #f0f9ff; color: #0369a1; border: 1px solid rgba(3, 105, 161, 0.1); }
        .alert ul { margin: 0.4rem 0 0 1.2rem; }

        /* Forms & Inputs */
        label { display: block; font-size: 0.9rem; font-weight: 700; color: var(--dark-blue); margin-bottom: 0.4rem; }
        
        input[type=text], input[type=url], input[type=date], input[type=email], 
        input[type=number], input[type=password], input[type=file], textarea, select { 
            width: 100%; 
            padding: 0.75rem 1rem; 
            background: #f9fafb;
            border: 2px solid var(--border-light); 
            border-radius: 10px; 
            font-size: 0.95rem; 
            margin-bottom: 1.25rem; 
            font-family: inherit; 
            color: var(--text-main);
            transition: all 0.2s ease; 
        }
        
        input:focus, textarea:focus, select:focus { 
            outline: none; 
            border-color: var(--teal); 
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(41, 99, 116, 0.15); 
        }
        
        textarea { resize: vertical; min-height: 100px; }
        .hint { font-size: 0.8rem; color: var(--text-muted); margin-top: -0.8rem; margin-bottom: 1.2rem; display: block; }

        /* Buttons */
        .btn { 
            display: inline-block; 
            padding: 0.7rem 1.5rem; 
            border-radius: 50px; 
            font-size: 0.95rem; 
            font-weight: 700; 
            cursor: pointer; 
            border: none; 
            text-decoration: none;
            transition: all 0.2s ease;
            font-family: inherit;
        }
        
        .btn-primary { background: var(--teal); color: var(--white); }
        .btn-primary:hover { background: var(--dark-blue); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(12, 44, 85, 0.2); }
        
        .btn-danger { background: #ef4444; color: var(--white); }
        .btn-danger:hover { background: #dc2626; transform: translateY(-2px); }
        
        .btn-secondary { background: var(--border-light); color: var(--text-main); }
        .btn-secondary:hover { background: #d1d5db; transform: translateY(-2px); }
        
        .btn-sm { padding: 0.4rem 1rem; font-size: 0.85rem; border-radius: 8px; }

        /* Tables */
        table { width: 100%; border-collapse: collapse; font-size: 0.95rem; margin-bottom: 1rem; }
        table th { background: rgba(98, 159, 173, 0.08); text-align: left; padding: 0.8rem 1rem; color: var(--dark-blue); font-weight: 700; border-bottom: 2px solid rgba(41, 99, 116, 0.1); }
        table td { padding: 0.8rem 1rem; border-bottom: 1px solid var(--border-light); vertical-align: middle; }
        table tr:last-child td { border-bottom: none; }
        table tr:hover td { background: #fdfdfd; }

        /* Utility Classes */
        .badge { display: inline-block; padding: 0.25rem 0.75rem; border-radius: 99px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
        .row2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
        
        .section-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; }
        .section-header h2 { margin-bottom: 0; border: none; padding: 0; }
        
        .empty { text-align: center; padding: 3rem 1.5rem; color: var(--text-muted); font-size: 1rem; background: rgba(98, 159, 173, 0.05); border-radius: 12px; }
        
        /* Progress Bar */
        .completion-bar { background: var(--border-light); border-radius: 99px; height: 12px; margin: 0.5rem 0; overflow: hidden; }
        .completion-fill { background: var(--teal); border-radius: 99px; height: 100%; transition: width 0.5s ease; }
        
        /* Profile Image */
        .profile-img { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 4px solid var(--white); box-shadow: 0 4px 12px rgba(12, 44, 85, 0.15); background: var(--cream); }

        @media (max-width: 768px) {
            nav { padding: 1rem 1.5rem; flex-wrap: wrap; gap: 1rem; }
            nav div { flex-wrap: wrap; gap: 1rem; }
            .row2 { grid-template-columns: 1fr; gap: 1rem; }
        }
    </style>
</head>
<body>

<div class="bg-shape bg-shape-1"></div>
<div class="bg-shape bg-shape-2"></div>

<nav>
    <a class="brand" href="<?= site_url('profile') ?>">✦ AR Alumni</a>
    <div>
        <a href="<?= site_url('profile') ?>">Profile</a>
        <a href="<?= site_url('bidding') ?>">Bidding</a>
        <?php 
            $role = $this->session->userdata('user_role'); 
            if ($role === 'developer' || $role === 'admin'): 
        ?>
            <a href="<?= site_url('apikey') ?>">Manage API Keys</a>
        <?php endif; ?>
        <a href="<?= site_url('api-docs') ?>">API Docs</a>
        <a href="<?= site_url('auth/logout') ?>">Logout</a>
    </div>
</nav>
<div class="container">