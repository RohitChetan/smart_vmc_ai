<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Smart Vadodara Command Center</title>

    @vite(['resources/js/admin/dashboard.js'])

    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    >

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Inter, Arial, sans-serif;
            background: #f4f7fb;
            color: #172033;
        }

        .admin-layout {
            min-height: 100vh;
        }

        .topbar {
            height: 70px;
            background: #111827;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
        }

        .brand {
            font-size: 21px;
            font-weight: 700;
        }

        .brand small {
            display: block;
            font-size: 11px;
            font-weight: 400;
            opacity: .65;
            margin-top: 2px;
        }

        .admin-badge {
            background: rgba(255,255,255,.1);
            border: 1px solid rgba(255,255,255,.15);
            padding: 8px 13px;
            border-radius: 20px;
            font-size: 13px;
        }

        .container {
            max-width: 1500px;
            margin: auto;
            padding: 24px;
        }

        .page-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 22px;
        }

        .page-title h1 {
            margin: 0;
            font-size: 27px;
        }

        .page-title p {
            margin: 5px 0 0;
            color: #6b7280;
            font-size: 14px;
        }

        .refresh-btn {
            border: 0;
            background: #2563eb;
            color: white;
            padding: 10px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }

        .refresh-btn:hover {
            background: #1d4ed8;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .card {
            background: white;
            border-radius: 13px;
            padding: 18px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 2px 8px rgba(0,0,0,.04);
        }

        .kpi-label {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 9px;
        }

        .kpi-value {
            font-size: 29px;
            font-weight: 750;
        }

        .critical {
            color: #dc2626;
        }

        .warning {
            color: #d97706;
        }

        .success {
            color: #16a34a;
        }

        .blue {
            color: #2563eb;
        }

        .main-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        #incidentMap {
            height: 480px;
            width: 100%;
            border-radius: 10px;
        }

        .stats-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .stat-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            background: #f8fafc;
            border-radius: 8px;
        }

        .stat-name {
            font-size: 14px;
            font-weight: 600;
        }

        .stat-count {
            font-weight: 700;
        }

        .alert {
            border-left: 4px solid #dc2626;
            background: #fff7f7;
            padding: 12px;
            border-radius: 7px;
            margin-bottom: 10px;
        }

        .alert.warning-alert {
            border-left-color: #f59e0b;
            background: #fffbeb;
        }

        .alert-title {
            font-weight: 700;
            font-size: 13px;
        }

        .alert-text {
            color: #6b7280;
            font-size: 12px;
            margin-top: 4px;
        }

        .table-card {
            margin-top: 20px;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            font-size: 12px;
            color: #6b7280;
            background: #f8fafc;
            padding: 12px;
            white-space: nowrap;
        }

        td {
            padding: 13px 12px;
            border-top: 1px solid #edf0f4;
            font-size: 13px;
            white-space: nowrap;
        }

        .status {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }

        .status-open {
            background: #fee2e2;
            color: #b91c1c;
        }

        .status-assigned {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .status-progress {
            background: #fef3c7;
            color: #92400e;
        }

        .status-resolved {
            background: #dcfce7;
            color: #166534;
        }

        .status-closed {
            background: #e5e7eb;
            color: #374151;
        }

        .priority-critical {
            color: #dc2626;
            font-weight: 700;
        }

        .priority-high {
            color: #ea580c;
            font-weight: 700;
        }

        .priority-medium {
            color: #ca8a04;
            font-weight: 700;
        }

        .priority-low {
            color: #16a34a;
            font-weight: 700;
        }

        .admin-filter {
            width:100%;
            padding:11px 12px;
            border:1px solid #d1d5db;
            border-radius:8px;
            background:white;
            color:#172033;
            font-size:13px;
        }

        .admin-filter:focus {
            outline:none;
            border-color:#2563eb;
        }

        .incident-click {
            cursor:pointer;
        }

        .incident-click:hover {
            background:#f8fafc;
        }

        .loading {
            text-align: center;
            padding: 30px;
            color: #6b7280;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
            display: none;
        }

        @media(max-width: 1100px) {
            .cards {
                grid-template-columns: repeat(3, 1fr);
            }

            .main-grid {
                grid-template-columns: 1fr;
            }
        }

        @media(max-width: 700px) {
            .container {
                padding: 15px;
            }

            .topbar {
                padding: 0 15px;
            }

            .cards {
                grid-template-columns: repeat(2, 1fr);
            }

            .page-title {
                align-items: flex-start;
                gap: 10px;
            }

            .page-title h1 {
                font-size: 21px;
            }
        }
    </style>
</head>

<body>

<div class="admin-layout">

    <header class="topbar">
        <div class="brand">
            Smart Vadodara
            <small>AI Civic Command Center</small>
        </div>

        <div class="admin-badge">
            Administrator
        </div>
    </header>

    <main class="container">

        <div class="page-title">
            <div>
                <h1>Command Center</h1>
                <p>Live civic incident monitoring & AI-powered ward operations</p>
            </div>

            <button class="refresh-btn" onclick="loadDashboard()">
                ↻ Refresh
            </button>
        </div>

        <div id="errorBox" class="error"></div>

        <!-- KPI -->
        <div class="cards">

            <div class="card">
                <div class="kpi-label">Total Incidents</div>
                <div id="totalIncidents" class="kpi-value blue">—</div>
            </div>

            <div class="card">
                <div class="kpi-label">Critical</div>
                <div id="criticalIncidents" class="kpi-value critical">—</div>
            </div>

            <div class="card">
                <div class="kpi-label">In Progress</div>
                <div id="progressIncidents" class="kpi-value warning">—</div>
            </div>

            <div class="card">
                <div class="kpi-label">SLA Breached</div>
                <div id="breachedIncidents" class="kpi-value critical">—</div>
            </div>

            <div class="card">
                <div class="kpi-label">Closed</div>
                <div id="closedIncidents" class="kpi-value success">—</div>
            </div>

        </div>

        <!-- FILTERS -->
        <div class="card" style="margin-bottom:20px;">
            <div class="section-title">🔎 Incident Filters</div>

            <div style="
                display:grid;
                grid-template-columns:repeat(4,1fr);
                gap:12px;
            ">

                <select id="filterWard" class="admin-filter">
                    <option value="">All Wards</option>
                </select>

                <select id="filterPriority" class="admin-filter">
                    <option value="">All Priorities</option>
                    <option value="critical">Critical</option>
                    <option value="high">High</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select>

                <select id="filterStatus" class="admin-filter">
                    <option value="">All Statuses</option>
                    <option value="open">Open</option>
                    <option value="assigned">Assigned</option>
                    <option value="in_progress">In Progress</option>
                    <option value="verification_pending">Verification Pending</option>
                    <option value="resolved">Resolved</option>
                    <option value="closed">Closed</option>
                    <option value="reopened">Reopened</option>
                </select>

                <select id="filterDepartment" class="admin-filter">
                    <option value="">All Departments</option>
                </select>

            </div>

            <div style="margin-top:12px;">
                <button class="refresh-btn" onclick="applyFilters()">
                    Apply Filters
                </button>

                <button
                    class="refresh-btn"
                    style="background:#6b7280;margin-left:8px;"
                    onclick="clearFilters()"
                >
                    Clear
                </button>
            </div>
        </div>

        <!-- MAP + SLA -->
        <div class="main-grid">

            <div class="card">
                <div class="section-title">
                    🗺️ Live Incident Map
                </div>

                <div id="incidentMap"></div>
            </div>

            <div>

                <div class="card">
                    <div class="section-title">
                        🚨 SLA Alerts
                    </div>

                    <div id="slaAlerts">
                        <div class="loading">Loading...</div>
                    </div>
                </div>

                <div class="card" style="margin-top:20px;">
                    <div class="section-title">
                        🏘️ Ward Performance
                    </div>

                    <div id="wardStats" class="stats-list">
                        <div class="loading">Loading...</div>
                    </div>
                </div>

            </div>

        </div>

        <!-- INCIDENT TABLE -->
        <div class="card table-card">

            <div class="section-title">
                📋 Active Incidents
            </div>

            <div class="table-wrap">

                <table>

                    <thead>
                        <tr>
                            <th>Incident</th>
                            <th>Category</th>
                            <th>Ward</th>
                            <th>Department</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Officer</th>
                            <th>Reports</th>
                        </tr>
                    </thead>

                    <tbody id="incidentTable">

                        <tr>
                            <td colspan="8" class="loading">
                                Loading incidents...
                            </td>
                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </main>

</div>


<div id="incidentModal" style="
    display:none;
    position:fixed;
    inset:0;
    background:rgba(15,23,42,.55);
    z-index:9999;
    padding:20px;
    overflow:auto;
">
    <div style="
        max-width:700px;
        margin:40px auto;
        background:white;
        border-radius:15px;
        padding:24px;
        box-shadow:0 20px 50px rgba(0,0,0,.2);
    ">
        <div style="
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:20px;
        ">
            <div class="section-title" style="margin:0;">
                Incident Details
            </div>

            <button
                onclick="closeIncidentModal()"
                style="
                    border:0;
                    background:#e5e7eb;
                    border-radius:8px;
                    padding:7px 11px;
                    cursor:pointer;
                "
            >✕</button>
        </div>

        <div id="incidentDetails"></div>
    </div>
</div>

</body>
</html>
