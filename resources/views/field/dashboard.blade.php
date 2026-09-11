<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Smart Vadodara — Field Officer</title>

    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    >

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f5f7fb;
            color: #111827;
            font-family:
                Inter,
                ui-sans-serif,
                system-ui,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;
        }

        button,
        input {
            font: inherit;
        }

        .app {
            min-height: 100vh;
        }

        /* --------------------------------------------------
           Header
        -------------------------------------------------- */

        .topbar {
            position: sticky;
            top: 0;
            z-index: 1000;

            background: rgba(255,255,255,.94);
            backdrop-filter: blur(14px);

            border-bottom: 1px solid #e5e7eb;

            padding: 16px 24px;

            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;

            background: #111827;
            color: white;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 20px;
            font-weight: 800;
        }

        .brand-title {
            font-size: 17px;
            font-weight: 800;
        }

        .brand-subtitle {
            margin-top: 2px;
            color: #6b7280;
            font-size: 12px;
        }

        .officer-box {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .officer-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;

            background: #e0e7ff;
            color: #3730a3;

            display: flex;
            align-items: center;
            justify-content: center;

            font-weight: 800;
        }

        .officer-name {
            font-size: 13px;
            font-weight: 700;
        }

        .officer-ward {
            color: #6b7280;
            font-size: 11px;
            margin-top: 2px;
        }

        .logout {
            margin-left: 8px;
            border: 1px solid #e5e7eb;
            background: white;
            color: #374151;
            padding: 9px 12px;
            border-radius: 9px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 700;
        }

        /* --------------------------------------------------
           Main
        -------------------------------------------------- */

        .container {
            width: min(1500px, 100%);
            margin: auto;
            padding: 24px;
        }

        .page-heading {
            margin-bottom: 20px;
        }

        .page-heading h1 {
            margin: 0;
            font-size: 28px;
            letter-spacing: -.5px;
        }

        .page-heading p {
            margin: 6px 0 0;
            color: #6b7280;
            font-size: 14px;
        }

        /* --------------------------------------------------
           Summary
        -------------------------------------------------- */

        .summary {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 14px;
            margin-bottom: 20px;
        }

        .summary-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 17px;
            box-shadow: 0 2px 8px rgba(15,23,42,.03);
        }

        .summary-label {
            color: #6b7280;
            font-size: 12px;
            font-weight: 600;
        }

        .summary-value {
            margin-top: 8px;
            font-size: 27px;
            font-weight: 800;
        }

        .summary-danger .summary-value {
            color: #dc2626;
        }

        .summary-warning .summary-value {
            color: #d97706;
        }

        .summary-success .summary-value {
            color: #059669;
        }

        /* --------------------------------------------------
           Dashboard grid
        -------------------------------------------------- */

        .dashboard-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.35fr) minmax(370px, .65fr);
            gap: 18px;
        }

        .panel {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(15,23,42,.03);
        }

        .panel-header {
            padding: 16px 18px;
            border-bottom: 1px solid #eef0f3;

            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }

        .panel-title {
            font-size: 15px;
            font-weight: 800;
        }

        .panel-subtitle {
            margin-top: 3px;
            color: #6b7280;
            font-size: 11px;
        }

        .refresh {
            border: 1px solid #e5e7eb;
            background: white;
            border-radius: 9px;
            padding: 8px 11px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 700;
        }

        .refresh:hover {
            background: #f9fafb;
        }

        /* --------------------------------------------------
           Map
        -------------------------------------------------- */

        #map {
            width: 100%;
            height: 650px;
        }

        .map-legend {
            padding: 10px 14px;

            display: flex;
            flex-wrap: wrap;
            gap: 12px;

            border-top: 1px solid #eef0f3;
            color: #6b7280;
            font-size: 11px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .dot-critical {
            background: #dc2626;
        }

        .dot-high {
            background: #ea580c;
        }

        .dot-medium {
            background: #d97706;
        }

        .dot-low {
            background: #16a34a;
        }

        /* --------------------------------------------------
           Incident list
        -------------------------------------------------- */

        .incident-list {
            max-height: 690px;
            overflow-y: auto;
        }

        .incident {
            padding: 16px 18px;
            border-bottom: 1px solid #eef0f3;
            cursor: pointer;
            transition: background .15s ease;
        }

        .incident:hover {
            background: #f8fafc;
        }

        .incident:last-child {
            border-bottom: 0;
        }

        .incident-top {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .incident-number {
            color: #6b7280;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .3px;
        }

        .incident-title {
            margin-top: 5px;
            font-size: 14px;
            font-weight: 800;
            line-height: 1.35;
        }

        .badges {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 10px;
        }

        .badge {
            display: inline-flex;
            align-items: center;

            padding: 5px 8px;
            border-radius: 999px;

            font-size: 10px;
            font-weight: 800;
        }

        .priority-critical {
            background: #fee2e2;
            color: #991b1b;
        }

        .priority-high {
            background: #ffedd5;
            color: #9a3412;
        }

        .priority-medium {
            background: #fef3c7;
            color: #92400e;
        }

        .priority-low {
            background: #dcfce7;
            color: #166534;
        }

        .status-open {
            background: #f3f4f6;
            color: #374151;
        }

        .status-assigned {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .status-in_progress {
            background: #ede9fe;
            color: #6d28d9;
        }

        .status-resolved {
            background: #dcfce7;
            color: #166534;
        }

        .incident-meta {
            margin-top: 12px;

            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .meta {
            background: #f8fafc;
            border-radius: 9px;
            padding: 9px;
        }

        .meta-label {
            color: #9ca3af;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .meta-value {
            margin-top: 3px;
            font-size: 11px;
            font-weight: 700;
        }

        .sla {
            margin-top: 10px;
            padding: 9px 10px;
            border-radius: 9px;

            background: #fff7ed;
            color: #9a3412;

            font-size: 11px;
            font-weight: 800;
        }

        .sla.breached {
            background: #fee2e2;
            color: #991b1b;
        }

        .incident-actions {
            display: flex;
            gap: 7px;
            margin-top: 11px;
        }

        .action {
            flex: 1;

            border: 0;
            border-radius: 9px;
            padding: 9px 8px;

            background: #111827;
            color: white;

            cursor: pointer;

            font-size: 11px;
            font-weight: 800;
        }

        .action.secondary {
            background: #f3f4f6;
            color: #374151;
        }

        .action:disabled {
            opacity: .45;
            cursor: not-allowed;
        }

        /* --------------------------------------------------
           Empty / Loading / Error
        -------------------------------------------------- */

        .state {
            padding: 50px 20px;
            text-align: center;
            color: #6b7280;
            font-size: 13px;
        }

        .error {
            margin-bottom: 18px;
            padding: 13px 15px;

            border-radius: 12px;

            background: #fee2e2;
            color: #991b1b;

            display: none;

            font-size: 13px;
            font-weight: 700;
        }

        /* --------------------------------------------------
           Responsive
        -------------------------------------------------- */

        @media (max-width: 1100px) {
            .summary {
                grid-template-columns: repeat(3, 1fr);
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            #map {
                height: 520px;
            }

            .incident-list {
                max-height: none;
            }
        }

        @media (max-width: 700px) {
            .topbar {
                padding: 12px 14px;
            }

            .officer-box {
                gap: 5px;
            }

            .officer-info {
                display: none;
            }

            .container {
                padding: 14px;
            }

            .page-heading h1 {
                font-size: 23px;
            }

            .summary {
                grid-template-columns: repeat(2, 1fr);
            }

            .summary-card {
                padding: 14px;
            }

            .summary-value {
                font-size: 24px;
            }

            #map {
                height: 400px;
            }

            .dashboard-grid {
                gap: 14px;
            }
        }
    </style>
</head>

<body>

<div class="app">

    <header class="topbar">

        <div class="brand">
            <div class="brand-icon">SV</div>

            <div>
                <div class="brand-title">
                    Smart Vadodara Connect
                </div>

                <div class="brand-subtitle">
                    Field Operations Command
                </div>
            </div>
        </div>

        <div class="officer-box">

            <div class="officer-avatar" id="officerAvatar">
                --
            </div>

            <div class="officer-info">
                <div class="officer-name" id="officerName">
                    Loading...
                </div>

                <div class="officer-ward" id="officerWard">
                    Loading ward...
                </div>
            </div>

            <button
                class="logout"
                onclick="logout()"
            >
                Logout
            </button>

        </div>

    </header>

    <main class="container">

        <div class="page-heading">

            <h1 id="pageTitle">
                Field Officer Dashboard
            </h1>

            <p>
                Live civic incidents assigned to your ward
            </p>

        </div>

        <div
            class="error"
            id="errorBox"
        ></div>

        <section class="summary">

            <div class="summary-card">
                <div class="summary-label">
                    Total Incidents
                </div>

                <div
                    class="summary-value"
                    id="totalIncidents"
                >
                    —
                </div>
            </div>

            <div class="summary-card summary-danger">
                <div class="summary-label">
                    High Priority
                </div>

                <div
                    class="summary-value"
                    id="highPriority"
                >
                    —
                </div>
            </div>

            <div class="summary-card summary-warning">
                <div class="summary-label">
                    Assigned
                </div>

                <div
                    class="summary-value"
                    id="assigned"
                >
                    —
                </div>
            </div>

            <div class="summary-card">
                <div class="summary-label">
                    In Progress
                </div>

                <div
                    class="summary-value"
                    id="inProgress"
                >
                    —
                </div>
            </div>

            <div class="summary-card summary-success">
                <div class="summary-label">
                    Resolved
                </div>

                <div
                    class="summary-value"
                    id="resolved"
                >
                    —
                </div>
            </div>

        </section>

        <section class="dashboard-grid">

            <!-- MAP -->

            <div class="panel">

                <div class="panel-header">

                    <div>
                        <div class="panel-title">
                            Ward Incident Map
                        </div>

                        <div class="panel-subtitle">
                            Incident locations and ward boundary
                        </div>
                    </div>

                    <button
                        class="refresh"
                        onclick="loadDashboard()"
                    >
                        ↻ Refresh
                    </button>

                </div>

                <div id="map"></div>

                <div class="map-legend">

                    <div class="legend-item">
                        <span class="dot dot-critical"></span>
                        Critical
                    </div>

                    <div class="legend-item">
                        <span class="dot dot-high"></span>
                        High
                    </div>

                    <div class="legend-item">
                        <span class="dot dot-medium"></span>
                        Medium
                    </div>

                    <div class="legend-item">
                        <span class="dot dot-low"></span>
                        Low
                    </div>

                </div>

            </div>

            <!-- INCIDENTS -->

            <div class="panel">

                <div class="panel-header">

                    <div>
                        <div class="panel-title">
                            Assigned Incidents
                        </div>

                        <div class="panel-subtitle">
                            Prioritized by urgency and SLA
                        </div>
                    </div>

                    <div
                        id="incidentCount"
                        class="panel-subtitle"
                    >
                        —
                    </div>

                </div>

                <div
                    class="incident-list"
                    id="incidentList"
                >

                    <div class="state">
                        Loading incidents...
                    </div>

                </div>

            </div>

        </section>

    </main>

</div>

<!-- =========================================================
     INCIDENT DETAIL MODAL
========================================================= -->

<div
    id="incidentModal"
    style="
        display:none;
        position:fixed;
        inset:0;
        z-index:5000;
        background:rgba(15,23,42,.45);
        backdrop-filter:blur(4px);
        padding:24px;
        align-items:center;
        justify-content:center;
    "
    onclick="closeIncidentModal(event)"
>

    <div
        style="
            width:min(560px,100%);
            max-height:90vh;
            overflow:auto;
            background:white;
            border-radius:22px;
            box-shadow:0 25px 70px rgba(15,23,42,.25);
        "
        onclick="event.stopPropagation()"
    >

        <!-- Header -->

        <div
            style="
                padding:20px;
                border-bottom:1px solid #eef0f3;
                display:flex;
                justify-content:space-between;
                align-items:flex-start;
                gap:15px;
            "
        >

            <div>

                <div
                    id="detailIncidentNumber"
                    style="
                        color:#6b7280;
                        font-size:11px;
                        font-weight:800;
                        letter-spacing:.4px;
                    "
                >
                    —
                </div>

                <div
                    id="detailTitle"
                    style="
                        margin-top:5px;
                        font-size:20px;
                        font-weight:800;
                    "
                >
                    —
                </div>

            </div>

            <button
                onclick="closeIncidentModal()"
                style="
                    width:34px;
                    height:34px;
                    border:1px solid #e5e7eb;
                    border-radius:50%;
                    background:white;
                    cursor:pointer;
                    font-size:18px;
                "
            >
                ×
            </button>

        </div>


        <!-- Body -->

        <div style="padding:20px;">

            <div
                style="
                    display:flex;
                    flex-wrap:wrap;
                    gap:8px;
                    margin-bottom:18px;
                "
            >

                <span
                    id="detailPriority"
                    class="badge priority-high"
                >
                    HIGH
                </span>

                <span
                    id="detailStatus"
                    class="badge status-assigned"
                >
                    Assigned
                </span>

                <span
                    id="detailReports"
                    class="badge status-open"
                >
                    1 Report
                </span>

            </div>


            <!-- Information grid -->

            <div
                style="
                    display:grid;
                    grid-template-columns:1fr 1fr;
                    gap:10px;
                "
            >

                <div class="meta">

                    <div class="meta-label">
                        Category
                    </div>

                    <div
                        id="detailCategory"
                        class="meta-value"
                    >
                        —
                    </div>

                </div>

                <div class="meta">

                    <div class="meta-label">
                        Department
                    </div>

                    <div
                        id="detailDepartment"
                        class="meta-value"
                    >
                        —
                    </div>

                </div>

                <div class="meta">

                    <div class="meta-label">
                        Ward
                    </div>

                    <div
                        id="detailWard"
                        class="meta-value"
                    >
                        —
                    </div>

                </div>

                <div class="meta">

                    <div class="meta-label">
                        Complaints
                    </div>

                    <div
                        id="detailComplaintCount"
                        class="meta-value"
                    >
                        —
                    </div>

                </div>

            </div>


            <!-- Location -->

            <div
                style="
                    margin-top:10px;
                    background:#f8fafc;
                    border-radius:12px;
                    padding:13px;
                "
            >

                <div class="meta-label">
                    Location
                </div>

                <div
                    id="detailLocation"
                    class="meta-value"
                >
                    —
                </div>

                <button
                    onclick="openIncidentLocation()"
                    style="
                        margin-top:10px;
                        border:0;
                        background:#111827;
                        color:white;
                        border-radius:9px;
                        padding:9px 12px;
                        cursor:pointer;
                        font-size:11px;
                        font-weight:800;
                    "
                >
                    📍 Open Location
                </button>

            </div>


            <!-- SLA -->

            <div
                id="detailSla"
                class="sla"
                style="margin-top:10px;"
            >
                ⏱ —
            </div>


            <!-- Description -->

            <div style="margin-top:18px;">

                <div
                    style="
                        font-size:11px;
                        color:#6b7280;
                        font-weight:800;
                        text-transform:uppercase;
                    "
                >
                    Complaint Description
                </div>

                <div
                    id="detailDescription"
                    style="
                        margin-top:7px;
                        font-size:14px;
                        line-height:1.55;
                        color:#374151;
                    "
                >
                    —
                </div>

            </div>


            <!-- Complaints -->

            <div
                id="detailComplaints"
                style="margin-top:18px;"
            ></div>


            <!-- Action -->

            <div
                id="detailAction"
                style="margin-top:20px;"
            ></div>

        </div>

    </div>

</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>

    /*
    |--------------------------------------------------------------------------
    | API
    |--------------------------------------------------------------------------
    */

    const API_BASE = '/api';

    let map = null;

    let mapMarkers = [];

    let wardLayer = null;

    let dashboardData = null;


    /*
    |--------------------------------------------------------------------------
    | Token
    |--------------------------------------------------------------------------
    */

    function getToken() {
        return localStorage.getItem('smart_vadodara_token');
    }


    /*
    |--------------------------------------------------------------------------
    | Init
    |--------------------------------------------------------------------------
    */

    document.addEventListener('DOMContentLoaded', () => {

        const token = getToken();

        if (!token) {

            showError(
                'No login token found. Please login first.'
            );

            return;
        }

        initializeMap();

        loadDashboard();

    });


    /*
    |--------------------------------------------------------------------------
    | Map
    |--------------------------------------------------------------------------
    */

    function initializeMap() {

        map = L.map('map', {
            zoomControl: true
        }).setView(
            [22.3072, 73.1812],
            12
        );

        L.tileLayer(
            'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }
        ).addTo(map);

    }


    /*
    |--------------------------------------------------------------------------
    | Dashboard API
    |--------------------------------------------------------------------------
    */

    async function loadDashboard() {

        hideError();

        try {

            const response = await fetch(
                `${API_BASE}/field/dashboard`,
                {
                    method: 'GET',

                    headers: {
                        'Accept': 'application/json',
                        'Authorization':
                            `Bearer ${getToken()}`
                    }
                }
            );

            const data = await response.json();

            if (!response.ok || !data.success) {

                throw new Error(
                    data.message ||
                    'Unable to load dashboard.'
                );

            }

            dashboardData = data;

            renderOfficer(data.officer);

            renderSummary(data.summary);

            renderIncidents(data.incidents);

            renderMap(data);

        } catch (error) {

            console.error(error);

            showError(error.message);

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Officer
    |--------------------------------------------------------------------------
    */

    function renderOfficer(officer) {

        const name =
            officer?.name ||
            'Field Officer';

        document.getElementById(
            'officerName'
        ).textContent = name;

        const ward =
            officer?.ward
                ? `Ward ${officer.ward.ward_no} · ${officer.ward.name}`
                : 'Ward not assigned';

        document.getElementById(
            'officerWard'
        ).textContent = ward;

        const initials =
            name
                .split(' ')
                .map(part => part.charAt(0))
                .join('')
                .substring(0, 2)
                .toUpperCase();

        document.getElementById(
            'officerAvatar'
        ).textContent = initials;

        document.getElementById(
            'pageTitle'
        ).textContent =
            officer?.ward
                ? `Ward ${officer.ward.ward_no} Operations`
                : 'Field Officer Dashboard';
    }


    /*
    |--------------------------------------------------------------------------
    | Summary
    |--------------------------------------------------------------------------
    */

    function renderSummary(summary) {

        document.getElementById(
            'totalIncidents'
        ).textContent =
            summary.total_incidents ?? 0;

        document.getElementById(
            'highPriority'
        ).textContent =
            summary.high_priority ?? 0;

        document.getElementById(
            'assigned'
        ).textContent =
            summary.assigned ?? 0;

        document.getElementById(
            'inProgress'
        ).textContent =
            summary.in_progress ?? 0;

        document.getElementById(
            'resolved'
        ).textContent =
            summary.resolved ?? 0;

    }


    /*
    |--------------------------------------------------------------------------
    | Incidents
    |--------------------------------------------------------------------------
    */

    function renderIncidents(incidents) {

        const list =
            document.getElementById(
                'incidentList'
            );

        document.getElementById(
            'incidentCount'
        ).textContent =
            `${incidents.length} incidents`;

        if (!incidents.length) {

            list.innerHTML = `
                <div class="state">
                    No assigned incidents right now.
                </div>
            `;

            return;
        }

        list.innerHTML =
            incidents
                .map(renderIncident)
                .join('');

    }


    function renderIncident(incident) {

        const priority =
            (incident.priority || 'medium')
                .toLowerCase();

        const status =
            (incident.status || 'open')
                .toLowerCase();

        const dueAt =
            incident.due_at
                ? new Date(incident.due_at)
                : null;

        const slaText =
            dueAt
                ? formatSla(dueAt)
                : 'SLA not available';

        const breached =
            dueAt &&
            dueAt.getTime() < Date.now() &&
            !['resolved', 'closed'].includes(status);

        let actionHtml = '';

        if (status === 'assigned') {

            actionHtml = `
                <button
                    class="action"
                    onclick="updateStatus(
                        ${incident.complaints?.[0]?.id},
                        'in_progress'
                    )"
                >
                    Start Work
                </button>
            `;

        } else if (status === 'in_progress') {

            actionHtml = `
                <button
                    class="action"
                    onclick="updateStatus(
                        ${incident.complaints?.[0]?.id},
                        'resolved'
                    )"
                >
                    Mark Resolved
                </button>
            `;

        } else {

            actionHtml = `
                <button
                    class="action secondary"
                    onclick="focusIncident(${incident.id})"
                >
                    View Location
                </button>
            `;

        }

        return `
            <article
                class="incident"
                id="incident-${incident.id}"
                onclick="focusIncident(${incident.id})"
            >

                <div class="incident-top">

                    <div>
                        <div class="incident-number">
                            ${escapeHtml(
                                incident.incident_number
                            )}
                        </div>

                        <div class="incident-title">
                            ${escapeHtml(
                                incident.title ||
                                incident.description ||
                                'Civic Issue'
                            )}
                        </div>
                    </div>

                </div>

                <div class="badges">

                    <span
                        class="badge priority-${priority}"
                    >
                        ${priority.toUpperCase()}
                    </span>

                    <span
                        class="badge status-${status}"
                    >
                        ${formatStatus(status)}
                    </span>

                    <span class="badge status-open">
                        ${incident.report_count || 1}
                        Report${incident.report_count == 1 ? '' : 's'}
                    </span>

                </div>

                <div class="incident-meta">

                    <div class="meta">

                        <div class="meta-label">
                            Category
                        </div>

                        <div class="meta-value">
                            ${escapeHtml(
                                incident.category?.name ||
                                '—'
                            )}
                        </div>

                    </div>

                    <div class="meta">

                        <div class="meta-label">
                            Department
                        </div>

                        <div class="meta-value">
                            ${escapeHtml(
                                incident.department?.name ||
                                '—'
                            )}
                        </div>

                    </div>

                    <div class="meta">

                        <div class="meta-label">
                            Location
                        </div>

                        <div class="meta-value">
                            ${incident.location?.latitude ?? '—'},
                            ${incident.location?.longitude ?? '—'}
                        </div>

                    </div>

                    <div class="meta">

                        <div class="meta-label">
                            Complaints
                        </div>

                        <div class="meta-value">
                            ${incident.complaints?.length ?? 0}
                        </div>

                    </div>

                </div>

                <div class="sla ${breached ? 'breached' : ''}">

                    ${breached ? '⚠ SLA BREACHED · ' : '⏱ '}

                    ${escapeHtml(slaText)}

                </div>

                <div
                    class="incident-actions"
                    onclick="event.stopPropagation()"
                >
                    ${actionHtml}
                </div>

            </article>
        `;
    }


    /*
    |--------------------------------------------------------------------------
    | Map rendering
    |--------------------------------------------------------------------------
    */

    // function renderMap(data) {

    //     clearMarkers();

    //     const incidents =
    //         data.incidents || [];

    //     const bounds = [];

    //     incidents.forEach(incident => {

    //         const lat =
    //             Number(incident.location?.latitude);

    //         const lng =
    //             Number(incident.location?.longitude);

    //         if (
    //             !Number.isFinite(lat) ||
    //             !Number.isFinite(lng)
    //         ) {
    //             return;
    //         }

    //         const marker =
    //             createMarker(
    //                 incident,
    //                 lat,
    //                 lng
    //             );

    //         marker.addTo(map);

    //         mapMarkers.push({
    //             incidentId: incident.id,
    //             marker
    //         });

    //         bounds.push([lat, lng]);

    //     });

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Ward boundary
    //     |--------------------------------------------------------------------------
    //     |
    //     | The current dashboard API does not yet return
    //     | ward GeoJSON. Once the field API exposes
    //     | boundary_geojson, this renderer will use it.
    //     |
    //     */

    //     if (
    //         data.officer?.ward &&
    //         data.officer.ward.boundary_geojson
    //     ) {

    //         drawWardBoundary(
    //             data.officer.ward.boundary_geojson
    //         );

    //     }

    //     if (bounds.length) {

    //         map.fitBounds(
    //             bounds,
    //             {
    //                 padding: [30, 30],
    //                 maxZoom: 16
    //             }
    //         );

    //     }

    // }

    function renderMap(data) {

        clearMarkers();

        const incidents =
            data.incidents || [];

        const allBounds = [];

        /*
        |--------------------------------------------------------------------------
        | Draw Ward Boundary First
        |--------------------------------------------------------------------------
        */

        if (
            data.officer?.ward &&
            data.officer.ward.boundary_geojson
        ) {

            drawWardBoundary(
                data.officer.ward.boundary_geojson
            );

            /*
            | Add complete ward boundary to map bounds
            */

            if (wardLayer) {

                const wardBounds =
                    wardLayer.getBounds();

                if (wardBounds.isValid()) {

                    allBounds.push(
                        wardBounds.getSouthWest()
                    );

                    allBounds.push(
                        wardBounds.getNorthEast()
                    );

                }

            }

        }

        /*
        |--------------------------------------------------------------------------
        | Incident Markers
        |--------------------------------------------------------------------------
        */

        incidents.forEach(incident => {

            const lat =
                Number(
                    incident.location?.latitude
                );

            const lng =
                Number(
                    incident.location?.longitude
                );

            if (
                !Number.isFinite(lat) ||
                !Number.isFinite(lng)
            ) {
                return;
            }

            const marker =
                createMarker(
                    incident,
                    lat,
                    lng
                );

            marker.addTo(map);

            mapMarkers.push({
                incidentId: incident.id,
                marker
            });

            allBounds.push([lat, lng]);

        });

        /*
        |--------------------------------------------------------------------------
        | Fit Entire Ward + Incidents
        |--------------------------------------------------------------------------
        */

        if (allBounds.length) {

            map.fitBounds(
                allBounds,
                {
                    padding: [35, 35],
                    maxZoom: 14
                }
            );

        }

    }


    function createMarker(
        incident,
        lat,
        lng
    ) {

        const priority =
            (incident.priority || 'medium')
                .toLowerCase();

        const color =
            markerColor(priority);

        const marker =
            L.circleMarker(
                [lat, lng],
                {
                    radius: 9,
                    weight: 3,
                    color: color,
                    fillColor: color,
                    fillOpacity: .78
                }
            );

        marker.bindPopup(`
            <div style="min-width:220px">

                <strong>
                    ${escapeHtml(
                        incident.incident_number
                    )}
                </strong>

                <div style="margin-top:5px;font-weight:700">
                    ${escapeHtml(
                        incident.title || 'Civic Issue'
                    )}
                </div>

                <div style="margin-top:7px">
                    Priority:
                    <strong>
                        ${priority.toUpperCase()}
                    </strong>
                </div>

                <div>
                    Status:
                    ${formatStatus(
                        incident.status
                    )}
                </div>

                <div>
                    Reports:
                    ${incident.report_count || 1}
                </div>

            </div>
        `);

        // marker.on(
        //     'click',
        //     () => highlightIncident(
        //         incident.id
        //     )
        // );
        marker.on(
            'click',
            () => openIncidentDetail(
                incident
            )
        );

        return marker;
    }


    function markerColor(priority) {

        switch (priority) {

            case 'critical':
                return '#dc2626';

            case 'high':
                return '#ea580c';

            case 'medium':
                return '#d97706';

            case 'low':
                return '#16a34a';

            default:
                return '#6b7280';

        }

    }


    function clearMarkers() {

        mapMarkers.forEach(item => {

            map.removeLayer(
                item.marker
            );

        });

        mapMarkers = [];

    }


    // function drawWardBoundary(geojson) {

    //     if (wardLayer) {

    //         map.removeLayer(
    //             wardLayer
    //         );

    //     }

    //     wardLayer =
    //         L.geoJSON(
    //             geojson,
    //             {
    //                 style: {
    //                     weight: 2,
    //                     fillOpacity: .05
    //                 }
    //             }
    //         ).addTo(map);

    // }

    function drawWardBoundary(geojson) {

        if (wardLayer) {

            map.removeLayer(
                wardLayer
            );

        }

        const normalizedGeoJson =
            normalizeWardGeoJson(geojson);

        wardLayer =
            L.geoJSON(
                normalizedGeoJson,
                {
                    style: {
                        weight: 3,
                        fillOpacity: 0.08
                    }
                }
            ).addTo(map);

    }


    function normalizeWardGeoJson(geojson) {

    if (!geojson || !geojson.coordinates) {
        return geojson;
    }

    function swapCoordinate(coordinate) {

        return [
            coordinate[1],
            coordinate[0]
        ];

    }

    function normalizeCoordinates(
            coordinates
        ) {

            if (
                Array.isArray(coordinates) &&
                typeof coordinates[0] === 'number'
            ) {
                return swapCoordinate(
                    coordinates
                );
            }

            return coordinates.map(
                normalizeCoordinates
            );

        }

        return {
            ...geojson,

            coordinates:
                normalizeCoordinates(
                    geojson.coordinates
                )
        };

    }


    /*
    |--------------------------------------------------------------------------
    | Focus incident
    |--------------------------------------------------------------------------
    */

    // function focusIncident(incidentId) {

    //     const incident =
    //         dashboardData?.incidents
    //             ?.find(
    //                 item =>
    //                     item.id === incidentId
    //             );

    //     if (!incident) {
    //         return;
    //     }

    //     const lat =
    //         Number(incident.location?.latitude);

    //     const lng =
    //         Number(incident.location?.longitude);

    //     if (
    //         !Number.isFinite(lat) ||
    //         !Number.isFinite(lng)
    //     ) {
    //         return;
    //     }

    //     map.setView(
    //         [lat, lng],
    //         17,
    //         {
    //             animate: true
    //         }
    //     );

    //     const marker =
    //         mapMarkers.find(
    //             item =>
    //                 item.incidentId === incidentId
    //         );

    //     if (marker) {

    //         marker.marker.openPopup();

    //     }

    //     highlightIncident(
    //         incidentId
    //     );

    // }

    //updated code 
    function focusIncident(incidentId) {

        const incident =
            dashboardData?.incidents
                ?.find(
                    item =>
                        item.id === incidentId
                );

        if (!incident) {
            return;
        }

        const lat =
            Number(
                incident.location?.latitude
            );

        const lng =
            Number(
                incident.location?.longitude
            );

        if (
            Number.isFinite(lat) &&
            Number.isFinite(lng)
        ) {

            map.setView(
                [lat, lng],
                16,
                {
                    animate: true
                }
            );

            const marker =
                mapMarkers.find(
                    item =>
                        item.incidentId === incidentId
                );

            if (marker) {
                marker.marker.openPopup();
            }

        }

        highlightIncident(
            incidentId
        );

        openIncidentDetail(
            incident
        );

    }

    
    /*
    |--------------------------------------------------------------------------
    | Incident Detail
    |--------------------------------------------------------------------------
    */

    let selectedIncident = null;


    function openIncidentDetail(incident) {

        selectedIncident = incident;

        const priority =
            (incident.priority || 'medium')
                .toLowerCase();

        const status =
            (incident.status || 'open')
                .toLowerCase();

        const dueAt =
            incident.due_at
                ? new Date(incident.due_at)
                : null;

        document.getElementById(
            'detailIncidentNumber'
        ).textContent =
            incident.incident_number || '—';


        document.getElementById(
            'detailTitle'
        ).textContent =
            incident.title ||
            incident.description ||
            'Civic Issue';


        const priorityElement =
            document.getElementById(
                'detailPriority'
            );

        priorityElement.className =
            `badge priority-${priority}`;

        priorityElement.textContent =
            priority.toUpperCase();


        const statusElement =
            document.getElementById(
                'detailStatus'
            );

        statusElement.className =
            `badge status-${status}`;

        statusElement.textContent =
            formatStatus(status);


        document.getElementById(
            'detailReports'
        ).textContent =
            `${incident.report_count || 1} Report${
                incident.report_count == 1
                    ? ''
                    : 's'
            }`;


        document.getElementById(
            'detailCategory'
        ).textContent =
            incident.category?.name || '—';


        document.getElementById(
            'detailDepartment'
        ).textContent =
            incident.department?.name || '—';


        document.getElementById(
            'detailWard'
        ).textContent =
            incident.ward
                ? `Ward ${incident.ward.ward_no} · ${incident.ward.name}`
                : '—';


        document.getElementById(
            'detailComplaintCount'
        ).textContent =
            incident.complaints?.length || 0;


        document.getElementById(
            'detailLocation'
        ).textContent =
            incident.location
                ? `${incident.location.latitude}, ${incident.location.longitude}`
                : '—';


        document.getElementById(
            'detailDescription'
        ).textContent =
            incident.description || 'No description available.';


        /*
        |--------------------------------------------------------------------------
        | SLA
        |--------------------------------------------------------------------------
        */

        const slaElement =
            document.getElementById(
                'detailSla'
            );

        if (dueAt) {

            const breached =
                dueAt.getTime() < Date.now() &&
                !['resolved', 'closed'].includes(status);

            slaElement.className =
                `sla ${breached ? 'breached' : ''}`;

            slaElement.textContent =
                breached
                    ? `⚠ SLA BREACHED · ${formatSla(dueAt)}`
                    : `⏱ ${formatSla(dueAt)}`;

        } else {

            slaElement.className =
                'sla';

            slaElement.textContent =
                '⏱ SLA not available';

        }


        /*
        |--------------------------------------------------------------------------
        | Complaint list
        |--------------------------------------------------------------------------
        */

        const complaints =
            incident.complaints || [];

        document.getElementById(
            'detailComplaints'
        ).innerHTML = complaints.length

            ? `
                <div
                    style="
                        font-size:11px;
                        color:#6b7280;
                        font-weight:800;
                        text-transform:uppercase;
                        margin-bottom:8px;
                    "
                >
                    Citizen Reports
                </div>

                ${complaints.map(complaint => `
                    <div
                        style="
                            padding:12px;
                            background:#f8fafc;
                            border-radius:11px;
                            margin-bottom:7px;
                        "
                    >

                        <div
                            style="
                                font-size:11px;
                                font-weight:800;
                            "
                        >
                            ${escapeHtml(
                                complaint.complaint_number
                            )}
                        </div>

                        <div
                            style="
                                margin-top:4px;
                                font-size:12px;
                                color:#4b5563;
                            "
                        >
                            ${escapeHtml(
                                complaint.description ||
                                'No description'
                            )}
                        </div>

                        <div
                            style="
                                margin-top:6px;
                                font-size:10px;
                                color:#6b7280;
                            "
                        >
                            Status:
                            ${formatStatus(
                                complaint.status
                            )}
                        </div>

                    </div>
                `).join('')}

            `

            : '';


        /*
        |--------------------------------------------------------------------------
        | Action button
        |--------------------------------------------------------------------------
        */

        let actionHtml = '';

        const firstComplaint =
            complaints[0];

        if (
            status === 'assigned' &&
            firstComplaint
        ) {

            actionHtml = `
                <button
                    class="action"
                    style="width:100%;"
                    onclick="
                        closeIncidentModal();
                        updateStatus(
                            ${firstComplaint.id},
                            'in_progress'
                        );
                    "
                >
                    ▶ Start Work
                </button>
            `;

        } else if (
            status === 'in_progress' &&
            firstComplaint
        ) {

            actionHtml = `
                <button
                    class="action"
                    style="width:100%;"
                    onclick="
                        closeIncidentModal();
                        updateStatus(
                            ${firstComplaint.id},
                            'resolved'
                        );
                    "
                >
                    ✓ Mark Resolved
                </button>
            `;

        } else {

            actionHtml = `
                <button
                    class="action secondary"
                    style="width:100%;"
                    onclick="openIncidentLocation()"
                >
                    📍 Open Incident Location
                </button>
            `;

        }

        document.getElementById(
            'detailAction'
        ).innerHTML =
            actionHtml;


        /*
        |--------------------------------------------------------------------------
        | Open modal
        |--------------------------------------------------------------------------
        */

        const modal =
            document.getElementById(
                'incidentModal'
            );

        modal.style.display =
            'flex';

        document.body.style.overflow =
            'hidden';

    }


    function closeIncidentModal(event) {

        if (
            event &&
            event.target &&
            event.target.id !==
                'incidentModal'
        ) {
            return;
        }

        const modal =
            document.getElementById(
                'incidentModal'
            );

        modal.style.display =
            'none';

        document.body.style.overflow =
            '';

    }


    function openIncidentLocation() {

        if (!selectedIncident) {
            return;
        }

        const lat =
            Number(
                selectedIncident.location?.latitude
            );

        const lng =
            Number(
                selectedIncident.location?.longitude
            );

        if (
            !Number.isFinite(lat) ||
            !Number.isFinite(lng)
        ) {
            return;
        }

        map.setView(
            [lat, lng],
            18,
            {
                animate: true
            }
        );

        const marker =
            mapMarkers.find(
                item =>
                    item.incidentId ===
                    selectedIncident.id
            );

        if (marker) {
            marker.marker.openPopup();
        }

        closeIncidentModal();

    }

    function highlightIncident(incidentId) {

        document
            .querySelectorAll('.incident')
            .forEach(el => {
                el.style.outline = 'none';
            });

        const element =
            document.getElementById(
                `incident-${incidentId}`
            );

        if (element) {

            element.style.outline =
                '2px solid #111827';

            element.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest'
            });

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Status update
    |--------------------------------------------------------------------------
    */

    async function updateStatus(
        complaintId,
        status
    ) {

        if (!complaintId) {

            showError(
                'No complaint is available for this incident.'
            );

            return;

        }

        const label =
            status === 'in_progress'
                ? 'start work'
                : 'mark this complaint as resolved';

        if (
            !confirm(
                `Are you sure you want to ${label}?`
            )
        ) {
            return;
        }

        try {

            const response =
                await fetch(
                    `${API_BASE}/field/complaints/${complaintId}/status`,
                    {
                        method: 'POST',

                        headers: {
                            'Accept':
                                'application/json',

                            'Content-Type':
                                'application/json',

                            'Authorization':
                                `Bearer ${getToken()}`
                        },

                        body: JSON.stringify({
                            status,
                            remarks:
                                status === 'in_progress'
                                    ? 'Field officer started work from dashboard.'
                                    : 'Field officer marked the complaint as resolved from dashboard.'
                        })
                    }
                );

            const data =
                await response.json();

            if (
                !response.ok ||
                !data.success
            ) {

                throw new Error(
                    data.message ||
                    'Unable to update status.'
                );

            }

            await loadDashboard();

        } catch (error) {

            console.error(error);

            showError(
                error.message
            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */

    async function logout() {

        const token =
            getToken();

        try {

            await fetch(
                `${API_BASE}/auth/logout`,
                {
                    method: 'POST',

                    headers: {
                        'Accept':
                            'application/json',

                        'Authorization':
                            `Bearer ${token}`
                    }
                }
            );

        } catch (error) {

            console.error(error);

        }

        localStorage.removeItem(
            'smart_vadodara_token'
        );

        window.location.href =
            '/citizen';

    }


    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    function formatStatus(status) {

        return String(status)
            .replaceAll('_', ' ')
            .replace(/\b\w/g, char =>
                char.toUpperCase()
            );

    }


    function formatSla(date) {

        const diff =
            date.getTime() -
            Date.now();

        if (diff <= 0) {

            return 'SLA deadline passed';

        }

        const hours =
            Math.floor(
                diff /
                (1000 * 60 * 60)
            );

        const minutes =
            Math.floor(
                (
                    diff %
                    (1000 * 60 * 60)
                ) /
                (1000 * 60)
            );

        if (hours > 24) {

            const days =
                Math.floor(hours / 24);

            const remainingHours =
                hours % 24;

            return `${days}d ${remainingHours}h remaining`;

        }

        return `${hours}h ${minutes}m remaining`;

    }


    function escapeHtml(value) {

        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');

    }


    function showError(message) {

        const box =
            document.getElementById(
                'errorBox'
            );

        box.textContent =
            message;

        box.style.display =
            'block';

    }


    function hideError() {

        const box =
            document.getElementById(
                'errorBox'
            );

        box.style.display =
            'none';

    }

</script>

</body>
</html>