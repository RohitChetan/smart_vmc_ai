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

//update code 
    async function loadDashboard() {

        const complaintsList =
            document.getElementById('complaintsList');

        try {

            const response = await fetch('/api/citizen/complaints', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + token
                }
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(
                    data.message || 'Unable to load complaints.'
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

            const complaints = data.complaints || [];

            if (!complaints.length) {

                complaintsList.innerHTML = `
                    <div class="empty">

                        <div class="empty-icon">
                            📋
                        </div>

                        <div style="font-weight:700; margin-bottom:5px;">
                            No complaints yet
                        </div>

                        <div>
                            Report your first civic issue to get started.
                        </div>

                    </div>
                `;

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Show latest 5
            |--------------------------------------------------------------------------
            */

            complaintsList.innerHTML =
                complaints.slice(0, 5).map(complaint => {

                    const statusClass =
                        getStatusClass(complaint.status);

                    const statusLabel =
                        formatStatus(complaint.status);

                    const category =
                        complaint.category || 'Civic Issue';

                    const ward =
                        complaint.ward
                            ? `Ward ${complaint.ward.ward_no}`
                            : 'Ward pending';

                    const date =
                        formatDate(complaint.submitted_at);

                    return `
                        <div
                            class="complaint"
                            onclick="viewComplaint('${complaint.complaint_number}')"
                            style="cursor:pointer;"
                        >

                            <div class="complaint-left">

                                <div class="complaint-number">
                                    ${escapeHtml(complaint.complaint_number)}
                                </div>

                                <div class="complaint-title">
                                    ${escapeHtml(category)}
                                </div>

                                <div class="complaint-meta">
                                    ${escapeHtml(ward)}
                                    ${date ? ' • ' + date : ''}
                                    ${complaint.department
                                        ? ' • ' + escapeHtml(complaint.department)
                                        : ''}
                                </div>

                            </div>

                            <div class="status ${statusClass}">
                                ${statusLabel}
                            </div>

                        </div>
                    `;

                }).join('');


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

                    <div style="font-weight:700; margin-bottom:5px;">
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

function showPoints() {

    alert(
        'Civic Points system will be available soon.'
    );

}


loadDashboard();

</script>

</body>
</html>