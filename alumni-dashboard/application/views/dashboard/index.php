<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><path fill='%231F6F5F' d='M12 0C12 6.627 17.373 12 24 12C17.373 12 12 17.373 12 24C12 17.373 6.627 12 0 12C6.627 12 12 6.627 12 0Z'/></svg>">
    <title>Curriculum Analytics - University Dashboard</title>
    <title>Alumni Directory - University Dashboard</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-dark: #1F6F5F; --primary-mid: #2FA084; --primary-light: #6FCF97;
            --bg-main: #f3f4f6; --white: #ffffff; --text-main: #1f2937; --text-muted: #6b7280;
            --border-color: #e5e7eb; --shadow-sm: 0 4px 6px -1px rgba(31, 111, 95, 0.05);
            --shadow-md: 0 10px 15px -3px rgba(31, 111, 95, 0.08);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        /* Clean Dot Grid Background */
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #fcfcfc; /* Crisp, clean white base */
            color: var(--text-main); 
            display: flex; 
            min-height: 100vh;
            /* Creates a beautiful, subtle dot matrix pattern */
            background-image: radial-gradient(rgba(31, 111, 95, 0.12) 1px, transparent 1px);
            background-size: 30px 30px;
        }
        
        /* Geometric Shape Container */
        .bg-shapes {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; 
            z-index: -1; overflow: hidden; pointer-events: none;
        }
        
        /* Crisp, charming geometric outlines */
        .shape {
            position: absolute;
            border: 2px solid rgba(47, 160, 132, 0.08); /* Faint outline using your primary color */
        }
        
        /* Massive sweeping ring at the top right */
        .shape-1 {
            width: 1200px; height: 1200px; border-radius: 50%;
            top: -500px; right: -300px;
        }
        
        /* Inner sweeping ring (creates a ripple effect) */
        .shape-2 {
            width: 900px; height: 900px; border-radius: 50%;
            top: -350px; right: -150px;
        }
        
        /* Soft, rotated "squircle" in the bottom left */
        .shape-3 {
            width: 600px; height: 600px; border-radius: 120px;
            transform: rotate(35deg);
            bottom: -200px; left: -200px;
        }
        
        /* Inner nested squircle with a subtle frosted glass fill */
        .shape-4 {
            width: 400px; height: 400px; border-radius: 80px;
            transform: rotate(35deg);
            bottom: -100px; left: -100px;
            background: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(8px);
        }

        /* Sidebar & Header */
        .sidebar { width: 260px; background: var(--primary-dark); color: var(--white); display: flex; flex-direction: column; position: fixed; height: 100vh; z-index: 100; }
        .sidebar-header { padding: 2rem 1.5rem; font-size: 1.25rem; font-weight: 800; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .nav-links { list-style: none; padding: 1.5rem 0; flex-grow: 1; }
        .nav-links li a { display: flex; align-items: center; padding: 1rem 1.5rem; color: rgba(255,255,255,0.8); text-decoration: none; font-weight: 600; transition: all 0.2s ease; }
        .nav-links li a:hover, .nav-links li a.active { background: rgba(255,255,255,0.1); color: var(--white); border-left: 4px solid var(--primary-light); }
        
        .main-content { flex-grow: 1; margin-left: 260px; display: flex; flex-direction: column; }
        .top-header { background: var(--white); padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .user-info { font-weight: 600; color: var(--primary-dark); }
        .user-dept { font-size: 0.85rem; color: var(--text-muted); font-weight: 500; }
        .btn-logout { padding: 0.5rem 1.25rem; background: #fef2f2; color: #991b1b; border: 1px solid rgba(153, 27, 27, 0.1); border-radius: 50px; text-decoration: none; font-weight: 700; font-size: 0.9rem; }
        
        .container { padding: 2rem; max-width: 1400px; margin: 0 auto; width: 100%; position: relative; }
        .page-title { font-size: 1.75rem; color: var(--primary-dark); font-weight: 800; margin-bottom: 1.5rem; }

        /* Filter Card */
        .card { background: var(--white); border-radius: 16px; box-shadow: var(--shadow-md); padding: 1.5rem; margin-bottom: 1.5rem; }
        .filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; }
        .form-group label { display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-muted); margin-bottom: 0.5rem; }
        .form-control { width: 100%; padding: 0.75rem 1rem; border: 2px solid var(--border-color); border-radius: 10px; font-family: inherit; font-size: 0.95rem; outline: none; transition: border-color 0.2s ease; }
        .form-control:focus { border-color: var(--primary-light); }

        /* Table Styles */
        .table-responsive { overflow-x: auto; position: relative; min-height: 200px; border-radius: 16px; background: var(--white); box-shadow: var(--shadow-md); }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { padding: 1.25rem 1rem; background: #f9fafb; color: var(--text-muted); font-weight: 700; font-size: 0.85rem; border-bottom: 2px solid var(--border-color); text-transform: uppercase; letter-spacing: 0.05em; }
        td { padding: 1rem; border-bottom: 1px solid var(--border-color); font-size: 0.95rem; vertical-align: middle; }
        tbody tr { transition: background 0.2s ease; }
        tbody tr:hover { background: #f0fdf4; } /* Subtle green highlight on hover */
        
        .badge { background: rgba(47, 160, 132, 0.1); color: var(--primary-dark); padding: 0.35rem 0.75rem; border-radius: 50px; font-size: 0.75rem; font-weight: 700; display: inline-block; margin: 0.25rem 0.25rem 0 0; }

        /* Action Buttons in Table */
        .btn-view { background: transparent; border: 2px solid var(--primary-mid); color: var(--primary-dark); padding: 0.4rem 1rem; border-radius: 8px; font-weight: 700; font-size: 0.8rem; cursor: pointer; transition: all 0.2s ease; font-family: inherit; }
        .btn-view:hover { background: var(--primary-mid); color: var(--white); }

        /* Loader & Modal */
        .loader-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.8); display: none; justify-content: center; align-items: center; z-index: 10; border-radius: 16px; }
        .spinner { width: 40px; height: 40px; border: 4px solid rgba(47, 160, 132, 0.2); border-top-color: var(--primary-mid); border-radius: 50%; animation: spin 1s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }

        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); align-items: center; justify-content: center; }
        .modal.active { display: flex; animation: fadeIn 0.2s; }
        .modal-content { background: var(--white); padding: 2.5rem; border-radius: 20px; width: 90%; max-width: 700px; max-height: 90vh; overflow-y: auto; position: relative; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); }
        .close-btn { position: absolute; top: 1.5rem; right: 1.5rem; font-size: 2rem; line-height: 1; cursor: pointer; color: var(--text-muted); transition: color 0.2s; }
        .close-btn:hover { color: #991b1b; }
        
        /* Modal Content Styling */
        .modal-header { text-align: center; margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); }
        .modal-name { font-size: 1.75rem; font-weight: 800; color: var(--primary-dark); margin-bottom: 0.25rem; }
        .modal-links a { display: inline-block; margin-top: 0.5rem; color: #0a66c2; text-decoration: none; font-weight: 600; font-size: 0.9rem; }
        .modal-links a:hover { text-decoration: underline; }
        
        .section-title { font-size: 1.1rem; font-weight: 800; color: var(--text-main); margin: 1.5rem 0 1rem 0; display: flex; align-items: center; gap: 0.5rem; }
        .section-title::before { content: ''; display: block; width: 8px; height: 8px; background: var(--primary-mid); border-radius: 50%; }
        
        .timeline-item { margin-bottom: 1rem; padding-left: 1rem; border-left: 2px solid var(--border-color); position: relative; }
        .timeline-item::before { content: ''; position: absolute; left: -5px; top: 5px; width: 8px; height: 8px; background: var(--white); border: 2px solid var(--border-color); border-radius: 50%; }
        .timeline-title { font-weight: 700; color: var(--text-main); }
        .timeline-sub { font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.25rem; }

        @keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
    </style>
</head>
<body>
    <div class="bg-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
        <div class="shape shape-4"></div>
    </div>

    <aside class="sidebar">
        <div class="sidebar-header">Phantasmagoria</div>
        <ul class="nav-links">
            <li><a href="<?= site_url('dashboard') ?>" class="active">Alumni Directory</a></li>
            <li><a href="<?= site_url('analytics') ?>">Curriculum Analytics</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="top-header">
            <div>
                <div class="user-info">Welcome, <?= html_escape($this->session->userdata('staff_name') ?? 'Admin'); ?></div>
                <div class="user-dept"><?= html_escape($this->session->userdata('department') ?? 'Analytics'); ?></div>
            </div>
            <a href="<?= site_url('auth/logout') ?>" class="btn-logout">Logout</a>
        </header>

        <div class="container">
            <h1 class="page-title">Alumni Directory</h1>

            <div class="card">
                <div class="filter-grid">
                    <div class="form-group">
                        <label>Programme</label>
                        <select class="form-control filter-input" id="filter-programme">
                            <option value="">All Programmes</option>
                            <option value="Computer Science">Computer Science</option>
                            <option value="Software Engineering">Software Engineering</option>
                            <option value="Business Management">Business Management</option>
                            <option value="Artificial Intelligence">Artificial Intelligence</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Graduation Date</label>
                        <input type="month" class="form-control filter-input" id="filter-date">
                    </div>
                    <div class="form-group">
                        <label>Industry Sector</label>
                        <select class="form-control filter-input" id="filter-industry">
                            <option value="">All Sectors</option>
                            <option value="IT">IT</option>
                            <option value="Finance">Finance</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <div class="loader-overlay" id="loading-spinner"><div class="spinner"></div></div>
                <table>
                    <thead>
                        <tr>
                            <th>Alumnus</th>
                            <th>Current Role</th>
                            <th>Degrees</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="alumni-table-body">
                        </tbody>
                </table>
            </div>
        </div>
    </main>

    <div class="modal" id="profileModal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            
            <div class="modal-header">
                <div class="modal-name" id="modal-name">John Doe</div>
                <div style="color: var(--text-muted); font-size: 0.95rem;" id="modal-bio">No bio available.</div>
                <div class="modal-links" id="modal-linkedin"></div>
            </div>

            <h3 class="section-title">Employment History</h3>
            <div id="modal-employment"></div>

            <h3 class="section-title">Academic Background</h3>
            <div id="modal-education"></div>

            <h3 class="section-title">Certifications & Skills</h3>
            <div id="modal-skills"></div>
            
            <div style="margin-top: 1rem;" id="modal-courses"></div>
            <div style="margin-top: 1rem;" id="modal-licences"></div>
        </div>
    </div>

    <script>
        let allAlumniData = []; 
        let currentFilteredData = [];

        document.addEventListener('DOMContentLoaded', () => {
            fetchInitialData();
            document.querySelectorAll('.filter-input').forEach(input => {
                input.addEventListener('change', applyLocalFilters);
            });
        });

        async function fetchInitialData() {
            const spinner = document.getElementById('loading-spinner');
            spinner.style.display = 'flex';

            try {
                // Ensure this URL points to your correct CI3 controller
                const response = await fetch('<?= site_url("dashboard/get_alumni_data") ?>');
                const result = await response.json();
                
                if (result.data) {
                    allAlumniData = result.data;
                    currentFilteredData = result.data;
                    renderTable(currentFilteredData);
                }
            } catch (error) {
                console.error('Fetch Error:', error);
                document.getElementById('alumni-table-body').innerHTML = '<tr><td colspan="4" style="text-align:center; padding: 2rem; color: #991b1b;">Error fetching data. Ensure API is running.</td></tr>';
            } finally {
                spinner.style.display = 'none';
            }
        }

        function applyLocalFilters() {
            const prog = document.getElementById('filter-programme').value;
            const date = document.getElementById('filter-date').value;
            const ind = document.getElementById('filter-industry').value;

            currentFilteredData = allAlumniData.filter(alumnus => {
                let matchesProg = true;
                if (prog !== "") matchesProg = alumnus.degrees && alumnus.degrees.some(d => d.programme === prog);

                let matchesDate = true;
                if (date !== "") matchesDate = alumnus.degrees && alumnus.degrees.some(d => d.completion_date && d.completion_date.startsWith(date));

                let matchesInd = true;
                if (ind !== "") matchesInd = alumnus.employment && alumnus.employment.some(e => e.industry_sector === ind);

                return matchesProg && matchesDate && matchesInd;
            });

            renderTable(currentFilteredData);
        }

        // Changed from renderCards back to renderTable!
        function renderTable(data) {
            const tbody = document.getElementById('alumni-table-body');
            tbody.innerHTML = '';

            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" style="text-align:center; padding: 2rem; color: var(--text-muted);">No alumni match your filters.</td></tr>';
                return;
            }

            data.forEach((alumnus, index) => {
                // Extract job info
                const currentJob = alumnus.employment && alumnus.employment.length > 0 ? alumnus.employment[0] : null;
                const jobTitle = currentJob ? currentJob.job_title : 'Seeking Opportunities';
                const company = currentJob ? currentJob.company : 'N/A';
                
                // Format ALL degrees to stack vertically
                const degreesHtml = alumnus.degrees && alumnus.degrees.length > 0 
                    ? alumnus.degrees.map(d => `<div style="margin-bottom: 0.4rem;"><span class="badge" style="margin: 0;">${d.degree_name}</span></div>`).join('') 
                    : '<span style="color: var(--text-muted); font-size: 0.85rem;">No Degree Listed</span>';

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>
                        <strong>${alumnus.first_name || ''} ${alumnus.last_name || ''}</strong>
                    </td>
                    <td>
                        <strong>${jobTitle}</strong><br>
                        <span style="font-size: 0.85rem; color: var(--text-muted);">@ ${company}</span>
                    </td>
                    <td>
                        ${degreesHtml}
                    </td>
                    <td>
                        <button class="btn-view" onclick="openModal(${index})">View Profile</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        // --- Modal Functions (Exactly the same as before!) ---

        function openModal(index) {
            const alumnus = currentFilteredData[index];
            if (!alumnus) return;

            // 1. Populate Header
            document.getElementById('modal-name').innerText = `${alumnus.first_name || ''} ${alumnus.last_name || ''}`;
            document.getElementById('modal-bio').innerText = alumnus.bio || 'No bio provided.';
            
            const linkedinDiv = document.getElementById('modal-linkedin');
            if (alumnus.linkedin_url) {
                linkedinDiv.innerHTML = `<a href="${alumnus.linkedin_url}" target="_blank">🔗 View LinkedIn Profile</a>`;
            } else {
                linkedinDiv.innerHTML = '';
            }

            // 2. Populate Employment Timeline
            const empDiv = document.getElementById('modal-employment');
            if (alumnus.employment && alumnus.employment.length > 0) {
                empDiv.innerHTML = alumnus.employment.map(emp => `
                    <div class="timeline-item">
                        <div class="timeline-title">${emp.job_title} @ ${emp.company}</div>
                        <div class="timeline-sub">${emp.start_date} to ${emp.end_date ? emp.end_date : 'Present'} | ${emp.industry_sector || 'Sector N/A'}</div>
                    </div>
                `).join('');
            } else {
                empDiv.innerHTML = '<p style="color: var(--text-muted); font-size: 0.9rem;">No employment history listed.</p>';
            }

            // 3. Populate Education Timeline
            const eduDiv = document.getElementById('modal-education');
            if (alumnus.degrees && alumnus.degrees.length > 0) {
                eduDiv.innerHTML = alumnus.degrees.map(deg => `
                    <div class="timeline-item">
                        <div class="timeline-title">${deg.degree_name}</div>
                        <div class="timeline-sub">Graduated: ${deg.completion_date} | Programme: ${deg.programme || 'N/A'}</div>
                    </div>
                `).join('');
            } else {
                eduDiv.innerHTML = '<p style="color: var(--text-muted); font-size: 0.9rem;">No degree information available.</p>';
            }

            // 4. Populate Certifications
            const certDiv = document.getElementById('modal-skills');
            if (alumnus.certifications && alumnus.certifications.length > 0) {
                certDiv.innerHTML = alumnus.certifications.map(cert => `
                    <span class="badge">${cert.cert_name} (${cert.completion_date.substring(0,4)})</span>
                `).join('');
            } else {
                certDiv.innerHTML = '<p style="color: var(--text-muted); font-size: 0.9rem;">No certifications listed.</p>';
            }

            // 5. Populate Courses & Licences
            const courseDiv = document.getElementById('modal-courses');
            if (alumnus.courses && alumnus.courses.length > 0) {
                courseDiv.innerHTML = `<div style="font-size:0.85rem; color:var(--text-muted); margin-bottom:0.25rem;">Professional Courses:</div>` + 
                    alumnus.courses.map(c => `<span class="badge" style="background:#f3f4f6;">${c.course_name}</span>`).join('');
            } else {
                courseDiv.innerHTML = '';
            }

            const licDiv = document.getElementById('modal-licences');
            if (alumnus.licences && alumnus.licences.length > 0) {
                licDiv.innerHTML = `<div style="font-size:0.85rem; color:var(--text-muted); margin-bottom:0.25rem;">Licences:</div>` + 
                    alumnus.licences.map(l => `<span class="badge" style="background:#f3f4f6;">${l.licence_name}</span>`).join('');
            } else {
                licDiv.innerHTML = '';
            }

            // Show Modal
            document.getElementById('profileModal').classList.add('active');
        }

        function closeModal() { document.getElementById('profileModal').classList.remove('active'); }
        window.onclick = function(event) { if (event.target == document.getElementById('profileModal')) closeModal(); }
    </script>
</body>
</html>