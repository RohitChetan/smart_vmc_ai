<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Citizen Dashboard — Smart Vadodara</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f5f7fb;
            color: #172033;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .app {
            max-width: 1100px;
            margin: auto;
            padding-bottom: 100px;
        }

        /* Header */

        header {
            background: white;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #e9edf3;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: #1769e0;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 18px;
        }

        .brand-title {
            font-size: 17px;
            font-weight: 800;
        }

        .brand-subtitle {
            font-size: 12px;
            color: #7a8393;
            margin-top: 2px;
        }

        .logout {
            border: 0;
            background: #f1f4f8;
            color: #344054;
            padding: 10px 14px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
        }

        /* Main */

        main {
            padding: 25px 20px;
        }

        .welcome {
            margin-bottom: 22px;
        }

        .welcome h1 {
            margin: 0;
            font-size: 28px;
        }

        .welcome p {
            color: #70798a;
            margin: 7px 0 0;
        }

        /* Report Button */

        .report-card {
            background: #1769e0;
            color: white;
            border-radius: 20px;
            padding: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 24px;
        }

        .report-card h2 {
            margin: 0 0 7px;
            font-size: 21px;
        }

        .report-card p {
            margin: 0;
            opacity: .88;
            font-size: 14px;
        }

        .report-button {
            background: white;
            color: #1769e0;
            border: 0;
            padding: 13px 19px;
            border-radius: 12px;
            font-weight: 800;
            cursor: pointer;
            white-space: nowrap;
        }

        /* Stats */

        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 25px;
        }

        .stat {
            background: white;
            border-radius: 16px;
            padding: 18px;
            border: 1px solid #e9edf3;
        }

        .stat-label {
            font-size: 13px;
            color: #7a8393;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 27px;
            font-weight: 800;
        }

        /* Section */

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 13px;
        }

        .section-header h2 {
            margin: 0;
            font-size: 19px;
        }

        .section-header a {
            color: #1769e0;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
        }

        /* Complaint */

        .complaints {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .complaint {
            background: white;
            border: 1px solid #e9edf3;
            border-radius: 16px;
            padding: 17px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
        }

        .complaint-left {
            min-width: 0;
        }

        .complaint-number {
            font-size: 12px;
            color: #7a8393;
            margin-bottom: 5px;
        }

        .complaint-title {
            font-weight: 750;
            margin-bottom: 7px;
        }

        .complaint-meta {
            font-size: 12px;
            color: #7a8393;
        }

        .status {
            padding: 7px 11px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 750;
            white-space: nowrap;
        }

        .status-submitted {
            background: #fff7e6;
            color: #a15c00;
        }

        .status-processing {
            background: #eef4ff;
            color: #2459a6;
        }

        .status-assigned {
            background: #eef4ff;
            color: #2459a6;
        }

        .status-progress {
            background: #fff4e5;
            color: #9a5b00;
        }

        .status-resolved {
            background: #eaf8ef;
            color: #18743a;
        }

        .status-closed {
            background: #eaf8ef;
            color: #18743a;
        }

        .status-reopened {
            background: #fff0f0;
            color: #b42318;
        }

        .empty {
            background: white;
            border: 1px dashed #d9dee8;
            border-radius: 16px;
            padding: 35px 20px;
            text-align: center;
            color: #7a8393;
        }

        .empty-icon {
            font-size: 36px;
            margin-bottom: 8px;
        }

        /* Bottom nav */

        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid #e4e8ef;
            display: flex;
            justify-content: center;
            z-index: 20;
        }

        .bottom-inner {
            width: 100%;
            max-width: 700px;
            display: flex;
            justify-content: space-around;
            padding: 10px 5px;
        }

        .nav-item {
            text-decoration: none;
            color: #70798a;
            font-size: 11px;
            text-align: center;
            font-weight: 600;
        }

        .nav-item.active {
            color: #1769e0;
        }

        .nav-icon {
            font-size: 21px;
            display: block;
            margin-bottom: 2px;
        }

        @media (max-width: 700px) {

            .stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .report-card {
                align-items: flex-start;
                flex-direction: column;
            }

            .report-button {
                width: 100%;
            }
        }

        @media (min-width: 701px) {
            .bottom-nav {
                display: none;
            }
        }
    </style>
</head>

<body>

<div class="app">

    <header>

        <div class="brand">

            <div class="logo">
                SV
            </div>

            <div>
                <div class="brand-title">
                    Smart Vadodara
                </div>

                <div class="brand-subtitle">
                    Citizen Portal
                </div>
            </div>

        </div>

        <button class="logout" onclick="logout()">
            Logout
        </button>

    </header>


    <main>

        <div class="welcome">

            <h1>
                Hello, <span id="userName">Citizen</span> 👋
            </h1>

            <p>
                Help us make Vadodara cleaner, safer and smarter.
            </p>

        </div>


        <!-- Report -->

        <div class="report-card">

            <div>

                <h2>
                    Report a Civic Issue
                </h2>

                <p>
                    Take a photo, share your location and let AI route it automatically.
                </p>

            </div>

            <button
                class="report-button"
                onclick="window.location.href='{{ route('citizen.complaints.create') }}'"
            >
                + Report Issue
            </button>

        </div>


        <!-- Stats -->

        <div class="stats">

            <div class="stat">
                <div class="stat-label">
                    Total Complaints
                </div>

                <div class="stat-value" id="totalCount">
                    0
                </div>
            </div>


            <div class="stat">
                <div class="stat-label">
                    Active
                </div>

                <div class="stat-value" id="activeCount">
                    0
                </div>
            </div>


            <div class="stat">
                <div class="stat-label">
                    Resolved
                </div>

                <div class="stat-value" id="resolvedCount">
                    0
                </div>
            </div>


            <div class="stat">
                <div class="stat-label">
                    Civic Points
                </div>

                <div class="stat-value" id="pointsCount">
                    0
                </div>
            </div>

        </div>


        <!-- Recent complaints -->

        <div class="section-header">

            <h2>
                Recent Complaints
            </h2>

            <a href="{{ route('citizen.track') }}">
                View all
            </a>

        </div>


        <div id="complaintsList" class="complaints">

            <div class="empty">

                <div class="empty-icon">
                    📋
                </div>

                Loading complaints...

            </div>

        </div>

    </main>

</div>


<!-- Bottom navigation -->

<nav class="bottom-nav">

    <div class="bottom-inner">

        <a href="{{ route('citizen.home') }}" class="nav-item active">
            <span class="nav-icon">🏠</span>
            Home
        </a>

        <a href="{{ route('citizen.complaints.create') }}" class="nav-item">
            <span class="nav-icon">➕</span>
            Report
        </a>

        <a href="{{ route('citizen.track') }}" class="nav-item">
            <span class="nav-icon">🔎</span>
            Track
        </a>

        <a href="#" class="nav-item" onclick="showPoints(); return false;">
            <span class="nav-icon">🏆</span>
            Points
        </a>

    </div>

</nav>


<script>

const token =
    localStorage.getItem('smart_vadodara_token');

const storedUser =
    localStorage.getItem('smart_vadodara_user');


/*
|--------------------------------------------------------------------------
| Authentication Check
|--------------------------------------------------------------------------
*/

if (!token) {

    window.location.href =
        "{{ route('citizen.login') }}";

}


/*
|--------------------------------------------------------------------------
| Load User
|--------------------------------------------------------------------------
*/

if (storedUser) {

    try {

        const user =
            JSON.parse(storedUser);

        document.getElementById('userName').textContent =
            user.name || 'Citizen';

    } catch (error) {

        console.error(error);

    }

}


/*
|--------------------------------------------------------------------------
| Load Citizen Complaints
|--------------------------------------------------------------------------
|
| Temporary:
| We currently don't have a dedicated "my complaints" API.
| The page therefore starts with an empty state.
|
| We will connect this to the authenticated complaints API
| in the next step.
|
|--------------------------------------------------------------------------
*/

// async function loadDashboard() {

//     /*
//      * Placeholder until citizen complaint listing API
//      * is added.
//      */

//     document.getElementById('totalCount').textContent = '0';
//     document.getElementById('activeCount').textContent = '0';
//     document.getElementById('resolvedCount').textContent = '0';

//     document.getElementById('complaintsList').innerHTML = `
//         <div class="empty">
//             <div class="empty-icon">📋</div>
//             <div style="font-weight:700; margin-bottom:5px;">
//                 No complaints yet
//             </div>
//             <div>
//                 Report your first civic issue to get started.
//             </div>
//         </div>
//     `;
// }

async function loadDashboard() {

    const complaintsList =
        document.getElementById('complaintsList');

    /*
    |--------------------------------------------------------------------------
    | Load Civic Points
    |--------------------------------------------------------------------------
    */

    try {

        const rewardResponse = await fetch(
            '/api/citizen/rewards',
            {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + token
                }
            }
        );

        const rewardData =
            await rewardResponse.json();

        if (
            rewardResponse.ok &&
            rewardData.success
        ) {

            const points =
                Number(
                    rewardData.reward?.total_points || 0
                );

            const pointsCount =
                document.getElementById('pointsCount');

            if (pointsCount) {
                pointsCount.textContent = points;
            }
        }

    } catch (rewardError) {

        console.error(
            'Citizen rewards error:',
            rewardError
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Load Citizen Complaints
    |--------------------------------------------------------------------------
    */

    try {

        const response = await fetch(
            '/api/citizen/complaints',
            {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + token
                }
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
                'Unable to load complaints.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        */

        document.getElementById('totalCount').textContent =
            data.summary.total;

        document.getElementById('activeCount').textContent =
            data.summary.active;

        document.getElementById('resolvedCount').textContent =
            data.summary.resolved;


        /*
        |--------------------------------------------------------------------------
        | Complaints
        |--------------------------------------------------------------------------
        */

        const complaints =
            data.complaints || [];


        if (!complaints.length) {

            complaintsList.innerHTML = `
                <div class="empty">

                    <div class="empty-icon">
                        📋
                    </div>

                    <div style="
                        font-weight:700;
                        margin-bottom:5px;
                    ">
                        No complaints yet
                    </div>

                    <div>
                        Report your first civic issue
                        to get started.
                    </div>

                </div>
            `;

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Show latest 5 complaints
        |--------------------------------------------------------------------------
        */

        complaintsList.innerHTML =
            complaints
                .slice(0, 5)
                .map(complaint => {

                    const statusClass =
                        getStatusClass(
                            complaint.status
                        );

                    const statusLabel =
                        formatStatus(
                            complaint.status
                        );

                    const category =
                        complaint.category ||
                        'Civic Issue';

                    const ward =
                        complaint.ward
                            ? `Ward ${complaint.ward.ward_no}`
                            : 'Ward pending';

                    const date =
                        formatDate(
                            complaint.submitted_at
                        );

                    return `
                        <div
                            class="complaint"
                            onclick="viewComplaint('${complaint.complaint_number}')"
                            style="cursor:pointer;"
                        >

                            <div class="complaint-left">

                                <div class="complaint-number">
                                    ${escapeHtml(
                                        complaint.complaint_number
                                    )}
                                </div>

                                <div class="complaint-title">
                                    ${escapeHtml(category)}
                                </div>

                                <div class="complaint-meta">

                                    ${escapeHtml(ward)}

                                    ${
                                        date
                                            ? ' • ' + date
                                            : ''
                                    }

                                    ${
                                        complaint.department
                                            ? ' • ' +
                                              escapeHtml(
                                                  complaint.department
                                              )
                                            : ''
                                    }

                                </div>

                            </div>

                            <div class="status ${statusClass}">
                                ${statusLabel}
                            </div>

                        </div>
                    `;

                })
                .join('');


    } catch (error) {

        console.error(
            'Citizen dashboard error:',
            error
        );

        complaintsList.innerHTML = `
            <div class="empty">

                <div class="empty-icon">
                    ⚠️
                </div>

                <div style="
                    font-weight:700;
                    margin-bottom:5px;
                ">
                    Unable to load complaints
                </div>

                <div>
                    Please refresh the page and try again.
                </div>

            </div>
        `;
    }
}

    

/*
|--------------------------------------------------------------------------
| Status Helpers
|--------------------------------------------------------------------------
*/

function getStatusClass(status) {

    switch (status) {

        case 'submitted':
            return 'status-submitted';

        case 'ai_processing':
            return 'status-processing';

        case 'assigned':
            return 'status-assigned';

        case 'in_progress':
            return 'status-progress';

        case 'resolved':
        case 'verification_pending':
        case 'closed':
            return 'status-resolved';

        case 'reopened':
            return 'status-reopened';

        default:
            return 'status-processing';
    }
}


function formatStatus(status) {

    const labels = {
        submitted: 'Submitted',
        ai_processing: 'AI Processing',
        assigned: 'Assigned',
        in_progress: 'In Progress',
        verification_pending: 'Verification',
        resolved: 'Resolved',
        closed: 'Closed',
        reopened: 'Reopened',
        rejected: 'Rejected'
    };

    return labels[status] || status;
}


function formatDate(dateString) {

    if (!dateString) {
        return '';
    }

    const date = new Date(dateString);

    if (Number.isNaN(date.getTime())) {
        return '';
    }

    return date.toLocaleDateString('en-IN', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
}


function escapeHtml(value) {

    if (value === null || value === undefined) {
        return '';
    }

    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}


function viewComplaint(complaintNumber) {

    window.location.href =
        "{{ route('citizen.track') }}" +
        "?complaint=" +
        encodeURIComponent(complaintNumber);
}


/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/

async function logout() {

    try {

        await fetch('/api/auth/logout', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token
            }
        });

    } catch (error) {

        console.error(error);

    }

    localStorage.removeItem(
        'smart_vadodara_token'
    );

    localStorage.removeItem(
        'smart_vadodara_user'
    );

    window.location.href =
        "{{ route('citizen.login') }}";
}


/*
|--------------------------------------------------------------------------
| Points
|--------------------------------------------------------------------------
*/

// async function showPoints() {
//     const token = localStorage.getItem('smart_vadodara_token');

//     if (!token) {
//         window.location.href = '/citizen/login';
//         return;
//     }

//     try {
//         // Get reward data
//         const response = await fetch('/api/citizen/rewards', {
//             method: 'GET',
//             headers: {
//                 'Accept': 'application/json',
//                 'Authorization': `Bearer ${token}`,
//             },
//         });

//         const data = await response.json();

//         if (!response.ok || !data.success) {
//             throw new Error(data.message || 'Unable to load civic points.');
//         }

//         const points = Number(data.reward?.total_points || 0);
//         const level = data.reward?.level || 'Citizen';

//         // Update dashboard points number
//         const pointsCount = document.getElementById('pointsCount');

//         if (pointsCount) {
//             pointsCount.textContent = points;
//         }

//         // Get certificates
//         const certificatesResponse = await fetch(
//             '/api/citizen/certificates',
//             {
//                 method: 'GET',
//                 headers: {
//                     'Accept': 'application/json',
//                     'Authorization': `Bearer ${token}`,
//                 },
//             }
//         );

//         const certificates = certificatesData.success
//             ? (certificatesData.certificates || [])
//             : [];

//         window.citizenCertificates = certificates;

//         let certificatesHTML = '';

//         if (certificates.length > 0) {

//             certificatesHTML = certificates.map(certificate => {

//                 const issuedDate = certificate.issued_at
//                     ? new Date(certificate.issued_at)
//                         .toLocaleDateString('en-IN')
//                     : '-';

//                 return `
//                     <div style="
//                         margin-top:16px;
//                         padding:16px;
//                         border:1px solid #e5e7eb;
//                         border-radius:16px;
//                         background:#ffffff;
//                     ">

//                         <div style="
//                             display:flex;
//                             align-items:flex-start;
//                             justify-content:space-between;
//                             gap:12px;
//                         ">

//                             <div>
//                                 <div style="
//                                     font-size:14px;
//                                     font-weight:700;
//                                     color:#111827;
//                                 ">
//                                     🏆 ${certificate.title || 'Jagruk Nagrik Certificate'}
//                                 </div>

//                                 <div style="
//                                     margin-top:5px;
//                                     font-size:12px;
//                                     color:#6b7280;
//                                 ">
//                                     ${certificate.certificate_number || ''}
//                                 </div>
//                             </div>

//                             <span style="
//                                 flex-shrink:0;
//                                 padding:5px 10px;
//                                 border-radius:999px;
//                                 background:#dcfce7;
//                                 color:#15803d;
//                                 font-size:11px;
//                                 font-weight:700;
//                             ">
//                                 Issued
//                             </span>

//                         </div>

//                         <div style="
//                             margin-top:12px;
//                             font-size:12px;
//                             color:#6b7280;
//                         ">
//                             Issued on: ${issuedDate}
//                         </div>

//                         <button
//                             type="button"
//                             onclick="viewCertificate(${certificate.id})"
//                             style="
//                                 margin-top:14px;
//                                 width:100%;
//                                 padding:11px 16px;
//                                 border:1px solid #2563eb;
//                                 border-radius:12px;
//                                 background:#ffffff;
//                                 color:#2563eb;
//                                 font-size:13px;
//                                 font-weight:600;
//                                 cursor:pointer;
//                             "
//                         >
//                             View Certificate
//                         </button>

//                     </div>
//                 `;

//             }).join('');

//         } else {

//             certificatesHTML = `
//                 <div style="
//                     margin-top:16px;
//                     padding:24px 16px;
//                     text-align:center;
//                     border:1px dashed #d1d5db;
//                     border-radius:16px;
//                     background:#ffffff;
//                 ">
//                     <div style="font-size:36px;">
//                         🏆
//                     </div>

//                     <div style="
//                         margin-top:8px;
//                         font-size:14px;
//                         font-weight:700;
//                         color:#1f2937;
//                     ">
//                         No certificates yet
//                     </div>

//                     <div style="
//                         margin-top:5px;
//                         font-size:12px;
//                         line-height:1.5;
//                         color:#6b7280;
//                     ">
//                         Confirm a successfully resolved civic complaint
//                         to earn your first certificate.
//                     </div>
//                 </div>
//             `;
//         }

//         // Remove existing modal
//         const oldModal = document.getElementById('pointsModal');

//         if (oldModal) {
//             oldModal.remove();
//         }

//         // Create modal
//         const modal = document.createElement('div');

//         modal.id = 'pointsModal';

//         modal.style.cssText = `
//             position:fixed;
//             inset:0;
//             z-index:999999;
//             display:flex;
//             align-items:center;
//             justify-content:center;
//             padding:20px;
//             background:rgba(15,23,42,0.55);
//             backdrop-filter:blur(4px);
//         `;

//         modal.innerHTML = `
//             <div
//                 style="
//                     width:100%;
//                     max-width:460px;
//                     max-height:90vh;
//                     overflow-y:auto;
//                     background:#f8fafc;
//                     border-radius:28px;
//                     padding:24px;
//                     box-shadow:0 25px 60px rgba(0,0,0,0.25);
//                 "
//             >

//                 <!-- Header -->
//                 <div style="
//                     display:flex;
//                     align-items:center;
//                     justify-content:space-between;
//                 ">

//                     <div>
//                         <div style="
//                             font-size:11px;
//                             font-weight:700;
//                             letter-spacing:0.08em;
//                             text-transform:uppercase;
//                             color:#94a3b8;
//                         ">
//                             Smart Vadodara
//                         </div>

//                         <div style="
//                             margin-top:3px;
//                             font-size:22px;
//                             line-height:1.2;
//                             font-weight:800;
//                             color:#172033;
//                         ">
//                             Civic Points
//                         </div>
//                     </div>

//                     <button
//                         type="button"
//                         id="closePointsModal"
//                         style="
//                             width:38px;
//                             height:38px;
//                             border:0;
//                             border-radius:50%;
//                             background:#e2e8f0;
//                             color:#475569;
//                             font-size:18px;
//                             cursor:pointer;
//                         "
//                     >
//                         ×
//                     </button>

//                 </div>

//                 <!-- Points Card -->
//                 <div style="
//                     margin-top:20px;
//                     padding:26px;
//                     border-radius:24px;
//                     background:linear-gradient(135deg,#2563eb 0%,#1d4ed8 100%);
//                     color:white;
//                     box-shadow:0 12px 30px rgba(37,99,235,0.25);
//                 ">

//                     <div style="
//                         font-size:13px;
//                         font-weight:500;
//                         opacity:0.85;
//                     ">
//                         Your Civic Points
//                     </div>

//                     <div style="
//                         margin-top:7px;
//                         display:flex;
//                         align-items:flex-end;
//                         gap:8px;
//                     ">

//                         <span style="
//                             font-size:52px;
//                             line-height:1;
//                             font-weight:800;
//                         ">
//                             ${points}
//                         </span>

//                         <span style="
//                             padding-bottom:5px;
//                             font-size:13px;
//                             opacity:0.85;
//                         ">
//                             points
//                         </span>

//                     </div>

//                     <div style="
//                         display:inline-block;
//                         margin-top:18px;
//                         padding:6px 14px;
//                         border-radius:999px;
//                         background:rgba(255,255,255,0.18);
//                         font-size:12px;
//                         font-weight:700;
//                     ">
//                         Level: ${level}
//                     </div>

//                 </div>

//                 <!-- Certificates -->
//                 <div style="
//                     margin-top:24px;
//                     font-size:15px;
//                     font-weight:800;
//                     color:#172033;
//                 ">
//                     🏅 Your Certificates
//                 </div>

//                 ${certificatesHTML}

//             </div>
//         `;

//         document.body.appendChild(modal);

//         // Close button
//         document.getElementById('closePointsModal').onclick = () => {
//             modal.remove();
//         };

//         // Click outside
//         modal.addEventListener('click', function(event) {
//             if (event.target === modal) {
//                 modal.remove();
//             }
//         });

//     } catch (error) {

//         console.error('Civic Points Error:', error);

//         alert(
//             error.message ||
//             'Unable to load Civic Points.'
//         );
//     }
// }

async function showPoints() {
    const token = localStorage.getItem('smart_vadodara_token');

    if (!token) {
        window.location.href = '/citizen/login';
        return;
    }

    try {
        // -----------------------------
        // Load Civic Points
        // -----------------------------
        const rewardResponse = await fetch('/api/citizen/rewards', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`,
            },
        });

        const rewardData = await rewardResponse.json();

        if (!rewardResponse.ok || !rewardData.success) {
            throw new Error(
                rewardData.message || 'Unable to load civic points.'
            );
        }

        const points = Number(
            rewardData.reward?.total_points || 0
        );

        const level =
            rewardData.reward?.level || 'Citizen';

        // Update dashboard card
        const pointsCount =
            document.getElementById('pointsCount');

        if (pointsCount) {
            pointsCount.textContent = points;
        }

        // -----------------------------
        // Load Certificates
        // -----------------------------
        const certificateResponse = await fetch(
            '/api/citizen/certificates',
            {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`,
                },
            }
        );

        const certificateData =
            await certificateResponse.json();

        if (
            !certificateResponse.ok ||
            !certificateData.success
        ) {
            throw new Error(
                certificateData.message ||
                'Unable to load certificates.'
            );
        }

        const certificates =
            certificateData.certificates || [];

        // Store globally for View Certificate
        window.citizenCertificates = certificates;

        // -----------------------------
        // Certificate HTML
        // -----------------------------
        let certificatesHTML = '';

        if (certificates.length > 0) {

            certificatesHTML = certificates.map(
                certificate => {

                    const issuedDate =
                        certificate.issued_at
                            ? new Date(
                                certificate.issued_at
                            ).toLocaleDateString('en-IN')
                            : '-';

                    return `
                        <div style="
                            margin-top:16px;
                            padding:16px;
                            border:1px solid #e5e7eb;
                            border-radius:16px;
                            background:#ffffff;
                        ">

                            <div style="
                                display:flex;
                                align-items:flex-start;
                                justify-content:space-between;
                                gap:12px;
                            ">

                                <div>
                                    <div style="
                                        font-size:14px;
                                        font-weight:700;
                                        color:#111827;
                                    ">
                                        🏆 ${
                                            certificate.title ||
                                            '"Jagruk Nagrik Certificate"'
                                        }
                                    </div>

                                    <div style="
                                        margin-top:5px;
                                        font-size:12px;
                                        color:#6b7280;
                                    ">
                                        ${
                                            certificate.certificate_number ||
                                            ''
                                        }
                                    </div>
                                </div>

                                <span style="
                                    flex-shrink:0;
                                    padding:5px 10px;
                                    border-radius:999px;
                                    background:#dcfce7;
                                    color:#15803d;
                                    font-size:11px;
                                    font-weight:700;
                                ">
                                    Issued
                                </span>

                            </div>

                            <div style="
                                margin-top:12px;
                                font-size:12px;
                                color:#6b7280;
                            ">
                                Issued on: ${issuedDate}
                            </div>

                            <button
                                type="button"
                                onclick="viewCertificate(${certificate.id})"
                                style="
                                    margin-top:14px;
                                    width:100%;
                                    padding:11px 16px;
                                    border:1px solid #2563eb;
                                    border-radius:12px;
                                    background:#ffffff;
                                    color:#2563eb;
                                    font-size:13px;
                                    font-weight:700;
                                    cursor:pointer;
                                "
                            >
                                View Certificate
                            </button>

                        </div>
                    `;
                }
            ).join('');

        } else {

            certificatesHTML = `
                <div style="
                    margin-top:16px;
                    padding:24px 16px;
                    text-align:center;
                    border:1px dashed #d1d5db;
                    border-radius:16px;
                    background:#ffffff;
                ">

                    <div style="font-size:36px;">
                        🏆
                    </div>

                    <div style="
                        margin-top:8px;
                        font-size:14px;
                        font-weight:700;
                        color:#1f2937;
                    ">
                        No certificates yet
                    </div>

                    <div style="
                        margin-top:5px;
                        font-size:12px;
                        line-height:1.5;
                        color:#6b7280;
                    ">
                        Confirm a successfully resolved civic
                        complaint to earn your first certificate.
                    </div>

                </div>
            `;
        }

        // -----------------------------
        // Remove old modal
        // -----------------------------
        document.getElementById(
            'pointsModal'
        )?.remove();

        // -----------------------------
        // Create Modal
        // -----------------------------
        const modal =
            document.createElement('div');

        modal.id = 'pointsModal';

        modal.style.cssText = `
            position:fixed;
            inset:0;
            z-index:999999;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:20px;
            background:rgba(15,23,42,.65);
            backdrop-filter:blur(5px);
        `;

        modal.innerHTML = `
            <div style="
                width:100%;
                max-width:460px;
                max-height:90vh;
                overflow-y:auto;
                background:#f8fafc;
                border-radius:28px;
                padding:24px;
                box-shadow:0 25px 60px rgba(0,0,0,.25);
            ">

                <!-- Header -->
                <div style="
                    display:flex;
                    align-items:center;
                    justify-content:space-between;
                ">

                    <div>
                        <div style="
                            font-size:11px;
                            font-weight:700;
                            letter-spacing:.08em;
                            text-transform:uppercase;
                            color:#94a3b8;
                        ">
                            Smart Vadodara
                        </div>

                        <div style="
                            margin-top:3px;
                            font-size:22px;
                            font-weight:800;
                            color:#172033;
                        ">
                            Civic Points
                        </div>
                    </div>

                    <button
                        type="button"
                        id="closePointsModal"
                        style="
                            width:38px;
                            height:38px;
                            border:0;
                            border-radius:50%;
                            background:#e2e8f0;
                            color:#475569;
                            font-size:20px;
                            font-weight:700;
                            cursor:pointer;
                        "
                    >
                        ×
                    </button>

                </div>

                <!-- Points -->
                <div style="
                    margin-top:20px;
                    padding:26px;
                    border-radius:24px;
                    background:linear-gradient(
                        135deg,
                        #2563eb 0%,
                        #1d4ed8 100%
                    );
                    color:white;
                    box-shadow:
                        0 12px 30px
                        rgba(37,99,235,.25);
                ">

                    <div style="
                        font-size:13px;
                        opacity:.85;
                    ">
                        Your Civic Points
                    </div>

                    <div style="
                        margin-top:7px;
                        display:flex;
                        align-items:flex-end;
                        gap:8px;
                    ">

                        <span style="
                            font-size:52px;
                            line-height:1;
                            font-weight:800;
                        ">
                            ${points}
                        </span>

                        <span style="
                            padding-bottom:5px;
                            font-size:13px;
                            opacity:.85;
                        ">
                            points
                        </span>

                    </div>

                    <div style="
                        display:inline-block;
                        margin-top:18px;
                        padding:6px 14px;
                        border-radius:999px;
                        background:rgba(255,255,255,.18);
                        font-size:12px;
                        font-weight:700;
                    ">
                        Level: ${level}
                    </div>

                </div>

                <!-- Certificates -->
                <div style="
                    margin-top:24px;
                    font-size:15px;
                    font-weight:800;
                    color:#172033;
                ">
                    🏅 Your Certificates
                </div>

                ${certificatesHTML}

            </div>
        `;

        document.body.appendChild(modal);

        // Close
        document.getElementById(
            'closePointsModal'
        ).onclick = () => {
            modal.remove();
        };

        // Click outside
        modal.addEventListener(
            'click',
            function(event) {
                if (event.target === modal) {
                    modal.remove();
                }
            }
        );

    } catch (error) {

        console.error(
            'Civic Points Error:',
            error
        );

        alert(
            error.message ||
            'Unable to load Civic Points.'
        );
    }
}

function viewCertificate(certificateId) {

    const certificates = window.citizenCertificates || [];

    const certificate = certificates.find(
        item => Number(item.id) === Number(certificateId)
    );

    if (!certificate) {
        alert('Certificate not found.');
        return;
    }

    const user = JSON.parse(
        localStorage.getItem('smart_vadodara_user') || '{}'
    );

    const citizenName = user.name || 'Citizen';

    const issuedDate = certificate.issued_at
        ? new Date(certificate.issued_at).toLocaleDateString('en-IN', {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        })
        : '-';

    const complaintNumber =
        certificate.complaint_number ||
        certificate.complaint?.complaint_number ||
        '-';

    const category =
        certificate.category ||
        certificate.complaint?.ai_category?.name ||
        certificate.complaint?.category?.name ||
        'Civic Issue';

    const ward =
        certificate.ward_name ||
        certificate.complaint?.ward?.name ||
        (certificate.ward_id
            ? `Ward ${certificate.ward_id}`
            : 'Vadodara');

    // Remove points modal
    document.getElementById('pointsModal')?.remove();

    // Remove existing certificate modal
    document.getElementById('certificateModal')?.remove();

    const modal = document.createElement('div');

    modal.id = 'certificateModal';

    modal.style.cssText = `
        position:fixed;
        inset:0;
        z-index:999999;
        display:flex;
        align-items:center;
        justify-content:center;
        padding:20px;
        background:rgba(15,23,42,0.65);
        backdrop-filter:blur(5px);
        overflow-y:auto;
    `;

    modal.innerHTML = `
        <div
            id="certificatePrintable"
            style="
                width:100%;
                max-width:850px;
                max-height:92vh;
                overflow-y:auto;
                background:#ffffff;
                border-radius:20px;
                box-shadow:0 30px 80px rgba(0,0,0,.3);
            "
        >

            <!-- Certificate -->
            <div
                style="
                    margin:18px;
                    padding:48px 45px;
                    min-height:600px;
                    border:10px solid #2563eb;
                    border-radius:12px;
                    position:relative;
                    background:
                        radial-gradient(
                            circle at top right,
                            rgba(37,99,235,.08),
                            transparent 35%
                        ),
                        #ffffff;
                    text-align:center;
                    box-sizing:border-box;
                "
            >

                <!-- Inner Border -->
                <div
                    style="
                        position:absolute;
                        inset:12px;
                        border:2px solid #bfdbfe;
                        border-radius:6px;
                        pointer-events:none;
                    "
                ></div>

                <!-- Content -->
                <div style="position:relative;z-index:2;">

                    <!-- Logo -->
                    <div
                        style="
                            width:64px;
                            height:64px;
                            margin:0 auto;
                            border-radius:18px;
                            background:#2563eb;
                            color:white;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            font-size:22px;
                            font-weight:800;
                            box-shadow:0 8px 20px rgba(37,99,235,.25);
                        "
                    >
                        SV
                    </div>

                    <div
                        style="
                            margin-top:12px;
                            font-size:12px;
                            font-weight:800;
                            letter-spacing:.18em;
                            color:#64748b;
                        "
                    >
                        SMART VADODARA
                    </div>

                    <div
                        style="
                            margin-top:30px;
                            font-size:14px;
                            font-weight:700;
                            letter-spacing:.22em;
                            color:#2563eb;
                        "
                    >
                        CERTIFICATE OF APPRECIATION
                    </div>

                    <div
                        style="
                            margin-top:12px;
                            font-size:42px;
                            line-height:1.15;
                            font-family:Georgia,serif;
                            font-weight:700;
                            color:#172033;
                        "
                    >
                        🏆
                    </div>

                    <div
                        style="
                            margin-top:8px;
                            font-size:30px;
                            font-family:Georgia,serif;
                            font-weight:700;
                            color:#172033;
                        "
                    >
                        Jagruk Nagrik
                    </div>

                    <div
                        style="
                            margin-top:8px;
                            font-size:13px;
                            color:#64748b;
                        "
                    >
                        This certificate is proudly presented to
                    </div>

                    <!-- Citizen Name -->
                    <div
                        style="
                            margin-top:18px;
                            font-size:30px;
                            font-family:Georgia,serif;
                            font-weight:700;
                            color:#2563eb;
                        "
                    >
                        ${escapeHtml(citizenName)}
                    </div>

                    <div
                        style="
                            width:280px;
                            height:1px;
                            margin:10px auto 0;
                            background:#cbd5e1;
                        "
                    ></div>

                    <div
                        style="
                            max-width:580px;
                            margin:24px auto 0;
                            font-size:14px;
                            line-height:1.7;
                            color:#475569;
                        "
                    >
                        In recognition of your active participation
                        in reporting civic issues and contributing
                        towards a cleaner, safer and smarter Vadodara.
                    </div>

                    <!-- Complaint Details -->
                    <div
                        style="
                            max-width:580px;
                            margin:28px auto 0;
                            padding:18px;
                            border-radius:12px;
                            background:#f8fafc;
                            border:1px solid #e2e8f0;
                            text-align:left;
                        "
                    >

                        <div
                            style="
                                display:grid;
                                grid-template-columns:1fr 1fr;
                                gap:16px;
                            "
                        >

                            <div>
                                <div style="
                                    font-size:10px;
                                    font-weight:700;
                                    text-transform:uppercase;
                                    color:#94a3b8;
                                ">
                                    Certificate Number
                                </div>

                                <div style="
                                    margin-top:4px;
                                    font-size:12px;
                                    font-weight:700;
                                    color:#172033;
                                ">
                                    ${escapeHtml(
                                        certificate.certificate_number || '-'
                                    )}
                                </div>
                            </div>

                            <div>
                                <div style="
                                    font-size:10px;
                                    font-weight:700;
                                    text-transform:uppercase;
                                    color:#94a3b8;
                                ">
                                    Issue Date
                                </div>

                                <div style="
                                    margin-top:4px;
                                    font-size:12px;
                                    font-weight:700;
                                    color:#172033;
                                ">
                                    ${issuedDate}
                                </div>
                            </div>

                            <div>
                                <div style="
                                    font-size:10px;
                                    font-weight:700;
                                    text-transform:uppercase;
                                    color:#94a3b8;
                                ">
                                    Complaint
                                </div>

                                <div style="
                                    margin-top:4px;
                                    font-size:12px;
                                    font-weight:700;
                                    color:#172033;
                                ">
                                    ${escapeHtml(complaintNumber)}
                                </div>
                            </div>

                            <div>
                                <div style="
                                    font-size:10px;
                                    font-weight:700;
                                    text-transform:uppercase;
                                    color:#94a3b8;
                                ">
                                    Ward
                                </div>

                                <div style="
                                    margin-top:4px;
                                    font-size:12px;
                                    font-weight:700;
                                    color:#172033;
                                ">
                                    ${escapeHtml(ward)}
                                </div>
                            </div>

                        </div>

                    </div>

                    <!-- Footer -->
                    <div
                        style="
                            margin-top:30px;
                            display:flex;
                            justify-content:space-between;
                            align-items:flex-end;
                            text-align:left;
                        "
                    >

                        <div>
                            <div style="
                                width:130px;
                                border-top:1px solid #94a3b8;
                            "></div>

                            <div style="
                                margin-top:6px;
                                font-size:10px;
                                color:#64748b;
                            ">
                                Smart Vadodara
                            </div>
                        </div>

                        <div style="
                            text-align:right;
                        ">
                            <div style="
                                font-size:10px;
                                color:#94a3b8;
                            ">
                                Verified Civic Contribution
                            </div>

                            <div style="
                                margin-top:4px;
                                font-size:12px;
                                font-weight:700;
                                color:#2563eb;
                            ">
                                Smart Vadodara Connect
                            </div>
                        </div>

                    </div>

                </div>

            </div>

            <!-- Actions -->
            <div
                class="certificate-actions"
                style="
                    display:flex;
                    gap:12px;
                    padding:0 24px 24px;
                "
            >

                <button
                    type="button"
                    id="closeCertificate"
                    style="
                        flex:1;
                        padding:13px 18px;
                        border:1px solid #d1d5db;
                        border-radius:12px;
                        background:#ffffff;
                        color:#475569;
                        font-size:14px;
                        font-weight:700;
                        cursor:pointer;
                    "
                >
                    Close
                </button>

                <button
                    type="button"
                    id="printCertificate"
                    style="
                        flex:1;
                        padding:13px 18px;
                        border:0;
                        border-radius:12px;
                        background:#2563eb;
                        color:#ffffff;
                        font-size:14px;
                        font-weight:700;
                        cursor:pointer;
                    "
                >
                    🖨️ Print Certificate
                </button>

            </div>

        </div>
    `;

    document.body.appendChild(modal);

    document.getElementById('closeCertificate').onclick = () => {
        modal.remove();
    };

    modal.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.remove();
        }
    });

    document.getElementById('printCertificate').onclick = () => {
        printCertificate();
    };
}

function escapeHtml(value) {

    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function printCertificate() {

    const certificate = document.getElementById(
        'certificatePrintable'
    );

    if (!certificate) {
        return;
    }

    const printWindow = window.open(
        '',
        '_blank',
        'width=1100,height=800'
    );

    if (!printWindow) {
        alert('Please allow popups to print the certificate.');
        return;
    }

    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>

            <title>Smart Vadodara Certificate</title>

            <style>

                * {
                    box-sizing:border-box;
                }

                html,
                body {
                    margin:0;
                    padding:0;
                    background:#ffffff;
                }

                body {
                    font-family:
                        Arial,
                        Helvetica,
                        sans-serif;
                }

                #certificatePrintable {
                    width:100%;
                    max-width:100%;
                    background:#ffffff;
                }

                .certificate-actions {
                    display:none !important;
                }

                @page {
                    size:A4 landscape;
                    margin:8mm;
                }

                @media print {

                    body {
                        width:100%;
                    }

                    #certificatePrintable {
                        width:100%;
                    }

                }

            </style>

        </head>

        <body>

            ${certificate.outerHTML}

            <script>
                window.onload = function() {
                    setTimeout(function() {
                        window.print();
                    }, 300);
                };

                window.onafterprint = function() {
                    window.close();
                };
            <\/script>

        </body>
        </html>
    `);

    printWindow.document.close();
}

loadDashboard();

</script>

</body>
</html>