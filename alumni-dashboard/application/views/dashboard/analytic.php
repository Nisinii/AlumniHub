<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><path fill='%231F6F5F' d='M12 0C12 6.627 17.373 12 24 12C17.373 12 12 17.373 12 24C12 17.373 6.627 12 0 12C6.627 12 12 6.627 12 0Z'/></svg>">
    <title>Curriculum Analytics - University Dashboard</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    
    <style>
        :root {
            --primary-dark: #1F6F5F; --primary-mid: #2FA084; --primary-light: #6FCF97;
            --bg-main: #f3f4f6; --white: #ffffff; --text-main: #1f2937; --text-muted: #6b7280;
            --border-color: #e5e7eb; --shadow-sm: 0 4px 6px -1px rgba(31, 111, 95, 0.05);
            --shadow-md: 0 10px 15px -3px rgba(31, 111, 95, 0.08);
            --shadow-hover: 0 20px 25px -5px rgba(31, 111, 95, 0.15);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
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

        /* Layout */
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
        
        .container { padding: 2rem; max-width: 1400px; margin: 0 auto; width: 100%; }
        .page-title { font-size: 1.75rem; color: var(--primary-dark); font-weight: 800; margin-bottom: 1.5rem; }

        /* Action Toolbar */
        .action-toolbar {
            display: flex; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;
            background: var(--white); padding: 1rem 1.5rem; border-radius: 12px;
            box-shadow: var(--shadow-sm); align-items: center; justify-content: space-between;
        }
        .btn-action {
            padding: 0.6rem 1.25rem; border: none; border-radius: 8px; font-weight: 700;
            font-size: 0.85rem; cursor: pointer; transition: all 0.2s ease;
            font-family: inherit; display: inline-flex; align-items: center; gap: 0.5rem;
        }
        .btn-primary { background: var(--primary-dark); color: var(--white); }
        .btn-primary:hover { background: var(--primary-mid); transform: translateY(-2px); box-shadow: var(--shadow-sm); }
        .btn-outline { background: transparent; border: 2px solid var(--primary-mid); color: var(--primary-dark); }
        .btn-outline:hover { background: rgba(47, 160, 132, 0.1); }

        /* Filter Card */
        .card { background: var(--white); border-radius: 16px; box-shadow: var(--shadow-md); padding: 1.5rem; margin-bottom: 1.5rem; }
        .filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; }
        .form-group label { display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-muted); margin-bottom: 0.5rem; }
        .form-control { width: 100%; padding: 0.75rem 1rem; border: 2px solid var(--border-color); border-radius: 10px; font-family: inherit; font-size: 0.95rem; outline: none; transition: border-color 0.2s ease; }
        .form-control:focus { border-color: var(--primary-light); }

        /* Bento Grid Charts */
        .charts-grid { display: grid; grid-template-columns: repeat(12, 1fr); gap: 1.5rem; margin-bottom: 2rem; transition: opacity 0.3s ease; }
        .col-span-4 { grid-column: span 4; } .col-span-8 { grid-column: span 8; } .col-span-12 { grid-column: span 12; }
        @media (max-width: 1100px) { .col-span-4, .col-span-8 { grid-column: span 6; } }
        @media (max-width: 768px) { .col-span-4, .col-span-8, .col-span-12 { grid-column: span 12; } .sidebar { display: none; } .main-content { margin-left: 0; } }

        .chart-card { background: var(--white); border-radius: 16px; box-shadow: var(--shadow-md); padding: 1.5rem; position: relative; height: 380px; display: flex; flex-direction: column; cursor: pointer; transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .chart-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-hover); border: 1px solid var(--primary-light); }
        .chart-card h3 { font-size: 1.1rem; color: var(--primary-dark); margin-bottom: 1rem; text-align: center; }
        .canvas-container { flex-grow: 1; position: relative; }

        /* Loader & Modal */
        .loader-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.8); display: none; justify-content: center; align-items: center; z-index: 10; border-radius: 16px; }
        .spinner { width: 40px; height: 40px; border: 4px solid rgba(47, 160, 132, 0.2); border-top-color: var(--primary-mid); border-radius: 50%; animation: spin 1s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }

        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); align-items: center; justify-content: center; }
        .modal.active { display: flex; animation: fadeIn 0.2s; }
        .modal-content { background: var(--white); padding: 2.5rem; border-radius: 20px; width: 90%; max-width: 900px; max-height: 90vh; overflow-y: auto; position: relative; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); }
        .close-btn { position: absolute; top: 1.5rem; right: 1.5rem; font-size: 2rem; line-height: 1; cursor: pointer; color: var(--text-muted); transition: color 0.2s; }
        .close-btn:hover { color: #991b1b; }
        .modal-details-box { margin-top: 1.5rem; padding: 1.5rem; background: var(--bg-main); border-radius: 12px; border-left: 4px solid var(--primary-mid); }
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
            <li><a href="<?= site_url('dashboard') ?>">Alumni Directory</a></li>
            <li><a href="<?= site_url('analytics') ?>" class="active">Curriculum Analytics</a></li>
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

        <div class="container" style="position: relative;">
            <h1 class="page-title">Curriculum Analytics & Skills Gaps</h1>

            <div class="action-toolbar" id="export-toolbar">
                <div style="display: flex; gap: 0.75rem;">
                    <button class="btn-action btn-outline" onclick="savePreset()">💾 Save Preset</button>
                    <button class="btn-action btn-outline" onclick="loadPreset()">📂 Load Preset</button>
                </div>
                <div style="display: flex; gap: 0.75rem;">
                    <!-- <button class="btn-action btn-primary" onclick="exportToCSV()">📊 Export CSV</button> -->
                    <button class="btn-action btn-primary" onclick="downloadAllCharts()">🖼️ Download Charts</button>
                    <button class="btn-action btn-primary" onclick="exportToPDF()">📄 Generate PDF Report</button>
                </div>
            </div>

            <div class="card" id="filter-card">
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

            <div class="loader-overlay" id="loading-spinner"><div class="spinner"></div></div>

            <div class="charts-grid" id="charts-container">
                <div class="chart-card col-span-8" onclick="openModal('skillsGapChart', 'Curriculum Skills Gap Analysis')">
                    <h3>Curriculum Skills Gap Analysis</h3>
                    <div class="canvas-container"><canvas id="skillsGapChart"></canvas></div>
                </div>
                <div class="chart-card col-span-4" onclick="openModal('programmeChart', 'Alumni by Programme')">
                    <h3>Alumni by Programme</h3>
                    <div class="canvas-container"><canvas id="programmeChart"></canvas></div>
                </div>
                <div class="chart-card col-span-4" onclick="openModal('employerChart', 'Top Employers')">
                    <h3>Top Employers</h3>
                    <div class="canvas-container"><canvas id="employerChart"></canvas></div>
                </div>
                <div class="chart-card col-span-4" onclick="openModal('industryChart', 'Employment by Industry Sector')">
                    <h3>Employment by Industry Sector</h3>
                    <div class="canvas-container"><canvas id="industryChart"></canvas></div>
                </div>
                <div class="chart-card col-span-4" onclick="openModal('coursesRadarChart', 'Popular Professional Courses')">
                    <h3>Popular Professional Courses</h3>
                    <div class="canvas-container"><canvas id="coursesRadarChart"></canvas></div>
                </div>
                <div class="chart-card col-span-12" onclick="openModal('certTimelineChart', 'Certifications Acquired Over Time')">
                    <h3>Certifications Acquired Over Time</h3>
                    <div class="canvas-container"><canvas id="certTimelineChart"></canvas></div>
                </div>
            </div>
        </div>
    </main>

    <div class="modal" id="chartModal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle" style="color: var(--primary-dark); font-size: 1.5rem; margin-bottom: 1.5rem;">Chart Details</h2>
            <div style="height: 400px; position: relative;"><canvas id="modalCanvas"></canvas></div>
            <div class="modal-details-box" id="modalInsights"></div>
        </div>
    </div>

    <div id="hidden-report-container" style="position: absolute; top: -9999px; left: -9999px; width: 800px; background: #ffffff;"></div>

    <script>
        // Global variables
        window.chartInstances = {};
        window.chartConfigs = {}; 
        let modalChartInstance = null;
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
            const chartsContainer = document.getElementById('charts-container');

            spinner.style.display = 'flex';
            chartsContainer.style.opacity = '0.3';

            try {
                const response = await fetch('<?= site_url("analytics/get_chart_data") ?>');
                const result = await response.json();
                if (result.data) {
                    allAlumniData = result.data; 
                    currentFilteredData = result.data;
                    renderCharts(allAlumniData); 
                }
            } catch (error) {
                console.error('Error fetching analytics:', error);
            } finally {
                spinner.style.display = 'none';
                chartsContainer.style.opacity = '1';
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

            renderCharts(currentFilteredData);
        }

        function renderCharts(data) {
            const initChart = (ctxId, config) => {
                const canvas = document.getElementById(ctxId);
                const ctx = canvas.getContext('2d');
                
                if (window.chartInstances[ctxId]) window.chartInstances[ctxId].destroy();

                const hasData = config.data.datasets.some(ds => ds.data && ds.data.length > 0);
                if (!hasData) {
                    ctx.save();
                    ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
                    ctx.font = '14px Plus Jakarta Sans'; ctx.fillStyle = '#6b7280';
                    ctx.fillText('No data matches these filters', canvas.width / 2, canvas.height / 2);
                    ctx.restore();
                    window.chartInstances[ctxId] = { destroy: () => { ctx.clearRect(0, 0, canvas.width, canvas.height); }, canvas: null };
                    window.chartConfigs[ctxId] = null; 
                    return;
                }
                
                window.chartConfigs[ctxId] = JSON.parse(JSON.stringify(config));
                window.chartInstances[ctxId] = new Chart(canvas, config);
            };

            const colors = ['#1F6F5F', '#2FA084', '#6FCF97', '#A7F3D0', '#064E3B', '#34D399'];
            const totalAlumni = data.length || 1; 

            // 1. Skills Gap
            const certCounts = {};
            data.forEach(a => { (a.certifications || []).forEach(c => { certCounts[c.cert_name] = (certCounts[c.cert_name] || 0) + 1; }); });
            const topGaps = Object.entries(certCounts).sort((a, b) => b[1] - a[1]).slice(0, 7);
            const gapColors = topGaps.map(gap => {
                const perc = (gap[1] / totalAlumni) * 100;
                if (perc >= 30) return '#ef4444'; 
                if (perc >= 15) return '#f59e0b'; 
                return '#10b981'; 
            });
            initChart('skillsGapChart', {
                type: 'bar',
                data: { labels: topGaps.map(g => g[0]), datasets: [{ label: 'Alumni with Cert', data: topGaps.map(g => g[1]), backgroundColor: gapColors }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
            });

            // 2. Programme
            const progCounts = {};
            data.forEach(a => { (a.degrees || []).forEach(d => { progCounts[d.programme || 'Unknown'] = (progCounts[d.programme || 'Unknown'] || 0) + 1; }); });
            initChart('programmeChart', {
                type: 'doughnut',
                data: { labels: Object.keys(progCounts), datasets: [{ data: Object.values(progCounts), backgroundColor: colors }] },
                options: { responsive: true, maintainAspectRatio: false }
            });

            // 3. Top Employers
            const employerCounts = {};
            data.forEach(a => { (a.employment || []).forEach(e => { if (e.company) employerCounts[e.company] = (employerCounts[e.company] || 0) + 1; }); });
            const topEmployers = Object.entries(employerCounts).sort((a, b) => b[1] - a[1]).slice(0, 5);
            initChart('employerChart', {
                type: 'bar',
                data: { labels: topEmployers.map(e => e[0]), datasets: [{ label: 'Alumni', data: topEmployers.map(e => e[1]), backgroundColor: '#2FA084' }] },
                options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
            });

            // 4. Industry
            const industryCounts = {};
            data.forEach(a => { (a.employment || []).forEach(e => { industryCounts[e.industry_sector || 'Other'] = (industryCounts[e.industry_sector || 'Other'] || 0) + 1; }); });
            initChart('industryChart', {
                type: 'pie',
                data: { labels: Object.keys(industryCounts), datasets: [{ data: Object.values(industryCounts), backgroundColor: colors }] },
                options: { responsive: true, maintainAspectRatio: false }
            });

            // 5. Popular Courses
            const courseCounts = {};
            data.forEach(a => { (a.courses || []).forEach(c => { if (c.course_name) courseCounts[c.course_name] = (courseCounts[c.course_name] || 0) + 1; }); });
            const topCourses = Object.entries(courseCounts).sort((a, b) => b[1] - a[1]).slice(0, 5);
            initChart('coursesRadarChart', {
                type: 'radar',
                data: { labels: topCourses.map(c => c[0]), datasets: [{ label: 'Completions', data: topCourses.map(c => c[1]), backgroundColor: 'rgba(111, 207, 151, 0.5)', borderColor: '#6FCF97' }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
            });

            // 6. Timeline
            const yearCounts = {};
            data.forEach(a => { (a.certifications || []).forEach(c => { if (c.completion_date) { const year = c.completion_date.substring(0, 4); yearCounts[year] = (yearCounts[year] || 0) + 1; } }); });
            const sortedYears = Object.keys(yearCounts).sort();
            initChart('certTimelineChart', {
                type: 'line',
                data: { labels: sortedYears, datasets: [{ label: 'Certifications Acquired', data: sortedYears.map(y => yearCounts[y]), borderColor: '#1F6F5F', fill: true, backgroundColor: 'rgba(31, 111, 95, 0.1)', tension: 0.3 }] },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
            });
        }

        // --- Insight Generation Logic ---

        function getInsightsHTML(chartId) {
            const config = window.chartConfigs[chartId];
            if (!config || !config.data.datasets[0].data.length) {
                return "<p style='color: #6b7280;'>No detailed data available.</p>"; 
            }

            const labels = config.data.labels;
            const dataPts = config.data.datasets[0].data;
            const combined = labels.map((label, index) => ({ name: label, value: dataPts[index] }));
            combined.sort((a, b) => b.value - a.value);

            const topItem = combined[0];
            const total = dataPts.reduce((acc, curr) => acc + curr, 0);

            let html = `<p style="margin-bottom: 10px; line-height: 1.5; color: #111111;">The highest recorded metric in this category is <strong>${topItem.name}</strong>, accounting for <strong>${topItem.value}</strong> entries (approx. ${Math.round((topItem.value/total)*100)}% of the charted data).</p>`;
            html += `<ul style="margin: 0; padding-left: 20px; line-height: 1.5; color: #374151;">`;
            combined.slice(0, 3).forEach(item => { html += `<li><strong>${item.name}:</strong> ${item.value} entries</li>`; });
            html += `</ul>`;
            
            return html;
        }

        function generateInsights(chartId) {
            const insightsBox = document.getElementById('modalInsights');
            let html = `<h4 style="margin-bottom: 10px; color: #111111; font-weight: 800;">Key Insights & Data Breakdown</h4>`;
            html += getInsightsHTML(chartId);
            insightsBox.innerHTML = html;
        }

        // --- Modal Functions ---
        function openModal(chartId, title) {
            const config = window.chartConfigs[chartId];
            if (!config) return; 

            const modal = document.getElementById('chartModal');
            document.getElementById('modalTitle').innerText = title;
            generateInsights(chartId);

            if (modalChartInstance) modalChartInstance.destroy();

            const modalConfig = JSON.parse(JSON.stringify(config));
            if(modalConfig.options.plugins && modalConfig.options.plugins.legend) {
                modalConfig.options.plugins.legend.display = true;
                modalConfig.options.plugins.legend.position = 'right';
            }

            const canvas = document.getElementById('modalCanvas');
            modalChartInstance = new Chart(canvas, modalConfig);
            modal.classList.add('active');
        }

        function closeModal() { document.getElementById('chartModal').classList.remove('active'); }
        window.onclick = function(event) { if (event.target == document.getElementById('chartModal')) closeModal(); }

        // --- Export & Toolbar Functions ---

        // function exportToCSV() {
        //     if(currentFilteredData.length === 0) return alert("No data to export!");

        //     let csvContent = "data:text/csv;charset=utf-8,";
        //     csvContent += "First Name,Last Name,Current Job,Company,Industry,Degrees,Certifications\n";

        //     currentFilteredData.forEach(user => {
        //         const job = user.employment?.[0]?.job_title || "N/A";
        //         const comp = user.employment?.[0]?.company || "N/A";
        //         const indus = user.employment?.[0]?.industry_sector || "N/A";
        //         const degs = (user.degrees || []).map(d => d.degree_name).join(' | ');
        //         const certs = (user.certifications || []).map(c => c.cert_name).join(' | ');

        //         const row = `"${user.first_name}","${user.last_name}","${job}","${comp}","${indus}","${degs}","${certs}"`;
        //         csvContent += row + "\n";
        //     });

        //     const encodedUri = encodeURI(csvContent);
        //     const link = document.createElement("a");
        //     link.setAttribute("href", encodedUri);
        //     link.setAttribute("download", `alumni_report_${new Date().toISOString().split('T')[0]}.csv`);
        //     document.body.appendChild(link);
        //     link.click();
        //     link.remove();
        // }

        function exportToPDF() {
            // Change button text to show loading state
            const pdfBtn = document.querySelector('button[onclick="exportToPDF()"]');
            const originalText = pdfBtn.innerHTML;
            pdfBtn.innerHTML = "⏳ Generating...";
            pdfBtn.disabled = true;

            const prog = document.getElementById('filter-programme').value || 'All Programmes';
            const date = document.getElementById('filter-date').value || 'All Time';
            const ind = document.getElementById('filter-industry').value || 'All Sectors';
            const today = new Date().toLocaleDateString();

            // We explicitly set a width (800px) on the wrapper so the PDF engine knows how to scale it
            let html = `
                <div style="font-family: 'Plus Jakarta Sans', sans-serif; padding: 40px; color: #111111; background: #ffffff; width: 800px;">
                    <div style="text-align: center; border-bottom: 4px solid #111111; padding-bottom: 20px; margin-bottom: 30px;">
                        <h1 style="font-weight: 800; font-size: 28px; margin: 0 0 10px 0; letter-spacing: -0.02em;">Curriculum Analytics Report</h1>
                        <p style="font-size: 14px; color: #4b5563; margin: 0;"><strong>Generated:</strong> ${today}</p>
                        <p style="font-size: 14px; color: #4b5563; margin: 5px 0 0 0;"><strong>Filters:</strong> Programme: ${prog} &bull; Date: ${date} &bull; Industry: ${ind}</p>
                    </div>
            `;

            const chartTitles = {
                skillsGapChart: 'Curriculum Skills Gap Analysis',
                programmeChart: 'Alumni by Programme',
                employerChart: 'Top Employers',
                industryChart: 'Employment by Industry Sector',
                coursesRadarChart: 'Popular Professional Courses',
                certTimelineChart: 'Certifications Acquired Over Time'
            };

            Object.keys(window.chartInstances).forEach(chartId => {
                const chart = window.chartInstances[chartId];
                if(chart && chart.canvas && window.chartConfigs[chartId]) {
                    const imgURI = chart.toBase64Image();
                    const insights = getInsightsHTML(chartId);
                    const title = chartTitles[chartId] || 'Chart';

                    html += `
                        <div style="page-break-inside: avoid; margin-bottom: 40px;">
                            <h2 style="font-weight: 700; font-size: 18px; margin-bottom: 15px; padding-bottom: 5px; border-bottom: 1px solid #e5e7eb;">${title}</h2>
                            <div style="display: flex; gap: 20px; align-items: flex-start;">
                                <div style="flex: 1; text-align: center;">
                                    <img src="${imgURI}" style="max-width: 100%; height: auto; max-height: 250px; object-fit: contain;" />
                                </div>
                                <div style="flex: 1; background: #f9fafb; padding: 20px; border-radius: 8px;">
                                    <h4 style="margin: 0 0 10px 0; font-weight: 700; font-size: 14px; text-transform: uppercase; letter-spacing: 0.05em;">Data Insights</h4>
                                    <div style="font-size: 13px;">${insights}</div>
                                </div>
                            </div>
                        </div>
                    `;
                }
            });

            html += `</div>`; 

            const opt = {
                margin:       [0.4, 0, 0.4, 0], 
                filename:     `analytics_report_${new Date().toISOString().split('T')[0]}.pdf`,
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true, backgroundColor: '#ffffff' },
                jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
            };

            // Pass the raw HTML string directly into the PDF engine
            html2pdf().set(opt).from(html).save().then(() => {
                pdfBtn.innerHTML = originalText; 
                pdfBtn.disabled = false;
            });
        }

        function downloadAllCharts() {
            Object.keys(window.chartInstances).forEach(chartId => {
                const chart = window.chartInstances[chartId];
                if(chart && chart.canvas && typeof chart.destroy === 'function') {
                    const a = document.createElement('a');
                    a.href = chart.toBase64Image();
                    a.download = `${chartId}_export.png`;
                    a.click();
                }
            });
        }

        function savePreset() {
            const preset = {
                prog: document.getElementById('filter-programme').value,
                date: document.getElementById('filter-date').value,
                ind: document.getElementById('filter-industry').value
            };
            localStorage.setItem('analyticsPreset', JSON.stringify(preset));
            alert("Filter preset saved successfully!");
        }

        function loadPreset() {
            const saved = localStorage.getItem('analyticsPreset');
            if (saved) {
                const preset = JSON.parse(saved);
                document.getElementById('filter-programme').value = preset.prog;
                document.getElementById('filter-date').value = preset.date;
                document.getElementById('filter-industry').value = preset.ind;
                applyLocalFilters(); 
            } else {
                alert("No saved preset found.");
            }
        }
    </script>
</body>
</html>