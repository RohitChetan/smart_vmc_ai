<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Track Complaint - Smart Vadodara</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            background: #f5f7fb;
            font-family: Arial, sans-serif;
        }

        .app {
            max-width: 520px;
            margin: auto;
            min-height: 100vh;
            background: white;
        }

        .header {
            padding: 20px;
            border-bottom: 1px solid #eee;
        }

        .header h1 {
            margin: 0;
            font-size: 22px;
        }

        .content {
            padding: 20px;
        }

        .search-box {
            display: flex;
            gap: 10px;
            margin-bottom: 24px;
        }

        input {
            flex: 1;
            padding: 14px;
            border: 1px solid #ddd;
            border-radius: 12px;
            font-size: 15px;
        }

        button {
            border: 0;
            padding: 14px 18px;
            border-radius: 12px;
            background: #111827;
            color: white;
            font-weight: 600;
            cursor: pointer;
        }

        .card {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            padding: 18px;
            margin-bottom: 16px;
        }

        .number {
            font-size: 13px;
            color: #6b7280;
        }

        .description {
            font-size: 17px;
            font-weight: 600;
            margin-top: 6px;
        }

        .badge {
            display: inline-block;
            margin-top: 10px;
            padding: 7px 12px;
            border-radius: 999px;
            background: #dcfce7;
            color: #166534;
            font-size: 13px;
            font-weight: 600;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .info {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 14px;
        }

        .label {
            color: #6b7280;
            font-size: 12px;
            margin-bottom: 5px;
        }

        .value {
            font-weight: 600;
            font-size: 14px;
        }

        .timeline {
            margin-top: 10px;
        }

        .step {
            display: flex;
            gap: 12px;
            position: relative;
            padding-bottom: 20px;
        }

        .dot {
            width: 12px;
            height: 12px;
            background: #22c55e;
            border-radius: 50%;
            margin-top: 4px;
            flex-shrink: 0;
        }

        .line {
            position: absolute;
            left: 5px;
            top: 16px;
            width: 2px;
            height: calc(100% - 8px);
            background: #d1d5db;
        }

        .step:last-child {
            padding-bottom: 0;
        }

        .step:last-child .line {
            display: none;
        }

        .step-title {
            font-weight: 600;
            font-size: 14px;
        }

        .step-time {
            color: #6b7280;
            font-size: 12px;
            margin-top: 3px;
        }

        .error {
            display: none;
            padding: 14px;
            border-radius: 12px;
            background: #fee2e2;
            color: #991b1b;
            margin-bottom: 16px;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 30px;
            color: #6b7280;
        }
    </style>
</head>

<body>

<div class="app">

    <div class="header">
        <div style="font-size:13px;color:#6b7280;">
            VADODARA MUNICIPAL CORPORATION
        </div>

        <h1>Track Complaint</h1>
    </div>

    <div class="content">

        <div class="search-box">
            <input
                id="complaintNumber"
                type="text"
                placeholder="Enter complaint number"
                value="SVC-2026-4R0JV7YD"
            >

            <button onclick="trackComplaint()">
                Track
            </button>
        </div>

        <div id="error" class="error"></div>

        <div id="loading" class="loading">
            Loading complaint...
        </div>

        <div id="result" style="display:none;">

            <div class="card">

                <div class="number" id="number"></div>

                <div class="description" id="description"></div>

                <div class="badge" id="status"></div>

            </div>

            <div class="grid">

                <div class="info">
                    <div class="label">Ward</div>
                    <div class="value" id="ward"></div>
                </div>

                <div class="info">
                    <div class="label">Priority</div>
                    <div class="value" id="priority"></div>
                </div>

                <div class="info">
                    <div class="label">AI Category</div>
                    <div class="value" id="category"></div>
                </div>

                <div class="info">
                    <div class="label">Department</div>
                    <div class="value" id="department"></div>
                </div>

                <div class="info">
                    <div class="label">Assigned To</div>
                    <div class="value" id="assigned"></div>
                </div>

                <div class="info">
                    <div class="label">AI Confidence</div>
                    <div class="value" id="confidence"></div>
                </div>

            </div>

            <div class="card" style="margin-top:16px;">

                <h3 style="margin-top:0;">
                    Complaint Timeline
                </h3>

                <div id="timeline" class="timeline"></div>

            </div>

        </div>

    </div>

</div>

<script>

async function trackComplaint() {

    const number =
        document.getElementById('complaintNumber')
            .value
            .trim();

    const error =
        document.getElementById('error');

    const result =
        document.getElementById('result');

    const loading =
        document.getElementById('loading');

    if (!number) {

        error.innerText =
            'Please enter complaint number.';

        error.style.display = 'block';

        return;
    }

    error.style.display = 'none';
    result.style.display = 'none';
    loading.style.display = 'block';

    try {

        const response =
            await fetch(
                `/api/complaints/${encodeURIComponent(number)}`
            );

        const data =
            await response.json();

        if (!response.ok || !data.success) {

            throw new Error(
                data.message || 'Complaint not found.'
            );
        }

        const complaint =
            data.complaint;

        document.getElementById('number').innerText =
            complaint.complaint_number;

        document.getElementById('description').innerText =
            complaint.description;

        document.getElementById('status').innerText =
            formatStatus(complaint.status);

        document.getElementById('ward').innerText =
            complaint.ward?.ward_no
                ? `Ward ${complaint.ward.ward_no} • ${complaint.ward.name}`
                : 'Not assigned';

        document.getElementById('priority').innerText =
            formatStatus(complaint.priority);

        document.getElementById('category').innerText =
            complaint.category?.ai_detected
                || 'AI processing';

        document.getElementById('department').innerText =
            complaint.department || 'AI processing';

        document.getElementById('confidence').innerText =
            complaint.ai_confidence
                ? `${Math.round(complaint.ai_confidence * 100)}%`
                : 'Processing';

        document.getElementById('assigned').innerText =
            complaint.assignment?.assigned_to?.name
                || complaint.assignment?.assigned_to
                || 'Not assigned';

        renderTimeline(complaint);

        loading.style.display = 'none';
        result.style.display = 'block';

    } catch (errorObject) {

        loading.style.display = 'none';

        error.innerText =
            errorObject.message;

        error.style.display = 'block';
    }
}


function formatStatus(value) {

    if (!value) {
        return 'Unknown';
    }

    return value
        .replaceAll('_', ' ')
        .replace(/\b\w/g, c => c.toUpperCase());
}


function renderTimeline(complaint) {

    const timeline =
        document.getElementById('timeline');

    const steps = [];

    steps.push({
        title: 'Complaint Submitted',
        time: complaint.submitted_at
    });

    if (
        complaint.ai_analysis &&
        complaint.ai_analysis.status === 'completed'
    ) {

        steps.push({
            title: 'AI Analysis Completed',
            time: complaint.ai_analysis.updated_at
        });

    }

    if (complaint.ward?.ward_no) {

        steps.push({
            title:
                `Ward ${complaint.ward.ward_no} • ${complaint.ward.name}`,
            time: null
        });

    }

    if (complaint.category?.ai_detected) {

        steps.push({
            title:
                `AI Detected: ${complaint.category.ai_detected}`,
            time: null
        });

    }

    if (complaint.department) {

        steps.push({
            title:
                `Department: ${complaint.department}`,
            time: null
        });

    }

    if (complaint.assignment) {

        const assignedName =
            complaint.assignment.assigned_to?.name
            || complaint.assignment.assigned_to
            || 'Field Officer';

        steps.push({
            title:
                `Assigned to ${assignedName}`,
            time:
                complaint.assignment.assigned_at
        });

    }

    if (complaint.status === 'in_progress') {

        steps.push({
            title: 'Work In Progress',
            time: null
        });

    }

    if (complaint.status === 'verification_pending') {

        steps.push({
            title: 'Waiting for Citizen Verification',
            time: null
        });

    }

    if (complaint.status === 'resolved') {

        steps.push({
            title: 'Work Resolved',
            time: complaint.resolved_at
        });

    }

    if (complaint.status === 'closed') {

        steps.push({
            title: 'Complaint Closed',
            time: complaint.closed_at
        });

    }

    timeline.innerHTML =
        steps.map((step, index) => {

            return `
                <div class="step">

                    <div class="dot"></div>

                    ${index < steps.length - 1
                        ? '<div class="line"></div>'
                        : ''
                    }

                    <div>
                        <div class="step-title">
                            ${step.title}
                        </div>

                        <div class="step-time">
                            ${formatDate(step.time)}
                        </div>
                    </div>

                </div>
            `;

        }).join('');
}


function formatDate(value) {

    if (!value) {
        return '';
    }

    try {

        return new Date(value)
            .toLocaleString();

    } catch {

        return value;
    }
}


// Automatically load demo complaint
trackComplaint();

</script>

</body>
</html>
