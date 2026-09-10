<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Field Officer Dashboard</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    <style>

        body {
            margin: 0;
            background: #f4f6f8;
            font-family: Arial, sans-serif;
            color: #111827;
        }

        .app {
            max-width: 1100px;
            margin: auto;
            min-height: 100vh;
        }

        .header {
            background: white;
            padding: 24px;
            border-bottom: 1px solid #e5e7eb;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .brand {
            color: #6b7280;
            font-size: 13px;
            font-weight: 600;
        }

        h1 {
            margin: 5px 0;
            font-size: 26px;
        }

        .officer {
            color: #6b7280;
            font-size: 14px;
        }

        .content {
            padding: 24px;
        }

        .summary {
            display: grid;
            grid-template-columns:
                repeat(4, 1fr);

            gap: 15px;
            margin-bottom: 25px;
        }

        .summary-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            border: 1px solid #e5e7eb;
        }

        .summary-label {
            color: #6b7280;
            font-size: 13px;
        }

        .summary-value {
            font-size: 30px;
            font-weight: 700;
            margin-top: 8px;
        }

        .complaint-list {
            display: grid;
            gap: 18px;
        }

        .complaint {
            background: white;
            border-radius: 18px;
            border: 1px solid #e5e7eb;
            padding: 22px;
        }

        .complaint-header {
            display: flex;
            justify-content: space-between;
            gap: 15px;
        }

        .number {
            color: #6b7280;
            font-size: 13px;
        }

        .description {
            font-size: 18px;
            font-weight: 700;
            margin-top: 6px;
        }

        .badges {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 14px;
        }

        .badge {
            padding: 7px 11px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .priority-high {
            background: #fee2e2;
            color: #991b1b;
        }

        .priority-medium {
            background: #fef3c7;
            color: #92400e;
        }

        .priority-low {
            background: #dcfce7;
            color: #166534;
        }

        .status {
            background: #e0e7ff;
            color: #3730a3;
        }

        .info-grid {
            display: grid;
            grid-template-columns:
                repeat(4, 1fr);

            gap: 12px;
            margin-top: 18px;
        }

        .info {
            background: #f8fafc;
            border-radius: 12px;
            padding: 13px;
        }

        .label {
            font-size: 11px;
            color: #6b7280;
        }

        .value {
            margin-top: 5px;
            font-size: 14px;
            font-weight: 600;
        }

        .sla {
            margin-top: 18px;
            padding: 13px;
            border-radius: 12px;
            background: #fff7ed;
            color: #9a3412;
            font-weight: 600;
            font-size: 13px;
        }

        .actions {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }

        button {
            border: 0;
            padding: 12px 18px;
            border-radius: 11px;
            background: #111827;
            color: white;
            font-weight: 700;
            cursor: pointer;
        }

        button.secondary {
            background: #e5e7eb;
            color: #111827;
        }

        button:disabled {
            opacity: .5;
            cursor: not-allowed;
        }

        .empty {
            background: white;
            padding: 50px;
            border-radius: 18px;
            text-align: center;
            color: #6b7280;
        }

        @media (max-width: 700px) {

            .summary {
                grid-template-columns:
                    repeat(2, 1fr);
            }

            .info-grid {
                grid-template-columns:
                    repeat(2, 1fr);
            }

            .header-top {
                display: block;
            }

            .content {
                padding: 15px;
            }
        }

    </style>

</head>

<body>

<div class="app">

    <div class="header">

        <div class="header-top">

            <div>

                <div class="brand">
                    VADODARA MUNICIPAL CORPORATION
                </div>

                <h1>
                    Field Officer Dashboard
                </h1>

                <div
                    id="officerName"
                    class="officer"
                >
                    Loading officer...
                </div>

            </div>

            <div>
                <button
                    onclick="loadDashboard()"
                >
                    Refresh
                </button>
            </div>

        </div>

    </div>


    <div class="content">

        <div
            id="summary"
            class="summary"
        ></div>

        <div
            id="complaints"
            class="complaint-list"
        ></div>

    </div>

</div>


<script>

async function loadDashboard() {

    const complaintsBox =
        document.getElementById(
            'complaints'
        );

    complaintsBox.innerHTML =
        '<div class="empty">Loading complaints...</div>';

    try {

        const response =
            await fetch(
                '/api/field/dashboard'
            );

        const data =
            await response.json();

        if (!response.ok || !data.success) {

            throw new Error(
                data.message
                || 'Unable to load dashboard.'
            );
        }

        document.getElementById(
            'officerName'
        ).innerText =
            `${data.officer.name} • Ward 11`;

        renderSummary(
            data.summary
        );

        renderComplaints(
            data.complaints
        );

    } catch (error) {

        complaintsBox.innerHTML = `
            <div class="empty">
                ${error.message}
            </div>
        `;
    }
}


function renderSummary(summary) {

    const box =
        document.getElementById(
            'summary'
        );

    box.innerHTML = `

        <div class="summary-card">
            <div class="summary-label">
                Total
            </div>

            <div class="summary-value">
                ${summary.total}
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-label">
                Assigned
            </div>

            <div class="summary-value">
                ${summary.assigned}
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-label">
                In Progress
            </div>

            <div class="summary-value">
                ${summary.in_progress}
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-label">
                High Priority
            </div>

            <div class="summary-value">
                ${summary.high_priority}
            </div>
        </div>
    `;
}


function renderComplaints(complaints) {

    const box =
        document.getElementById(
            'complaints'
        );

    if (!complaints.length) {

        box.innerHTML = `
            <div class="empty">
                No complaints assigned.
            </div>
        `;

        return;
    }

    box.innerHTML =
        complaints.map(
            complaint => {

                const priorityClass =
                    `priority-${complaint.priority}`;

                const status =
                    formatStatus(
                        complaint.status
                    );

                const canStart =
                    complaint.status ===
                    'assigned';

                const canResolve =
                    complaint.status ===
                    'in_progress';

                return `

                    <div class="complaint">

                        <div class="complaint-header">

                            <div>

                                <div class="number">
                                    ${complaint.complaint_number}
                                </div>

                                <div class="description">
                                    ${complaint.description}
                                </div>

                            </div>

                        </div>


                        <div class="badges">

                            <span
                                class="badge ${priorityClass}"
                            >
                                ${formatStatus(
                                    complaint.priority
                                )}
                                Priority
                            </span>

                            <span class="badge status">
                                ${status}
                            </span>

                        </div>


                        <div class="info-grid">

                            <div class="info">

                                <div class="label">
                                    Ward
                                </div>

                                <div class="value">
                                    Ward
                                    ${complaint.ward?.ward_no}
                                    •
                                    ${complaint.ward?.name}
                                </div>

                            </div>


                            <div class="info">

                                <div class="label">
                                    AI Category
                                </div>

                                <div class="value">
                                    ${complaint.ai_category
                                        || 'Processing'}
                                </div>

                            </div>


                            <div class="info">

                                <div class="label">
                                    Department
                                </div>

                                <div class="value">
                                    ${complaint.department
                                        || '—'}
                                </div>

                            </div>


                            <div class="info">

                                <div class="label">
                                    AI Confidence
                                </div>

                                <div class="value">
                                    ${complaint.ai_confidence
                                        ? Math.round(
                                            complaint.ai_confidence
                                            * 100
                                        ) + '%'
                                        : '—'}
                                </div>

                            </div>

                        </div>


                        ${
                            complaint.due_at
                            ? `
                                <div class="sla">
                                    SLA Due:
                                    ${formatDate(
                                        complaint.due_at
                                    )}
                                </div>
                            `
                            : ''
                        }


                        <div class="actions">

                            ${
                                canStart
                                ? `
                                    <button
                                        onclick="
                                            updateStatus(
                                                ${complaint.id},
                                                'in_progress'
                                            )
                                        "
                                    >
                                        Start Work
                                    </button>
                                `
                                : ''
                            }


                            ${
                                canResolve
                                ? `
                                    <button
                                        class="secondary"
                                        onclick="
                                            updateStatus(
                                                ${complaint.id},
                                                'resolved'
                                            )
                                        "
                                    >
                                        Mark Resolved
                                    </button>
                                `
                                : ''
                            }

                        </div>

                    </div>

                `;
            }
        ).join('');
}


async function updateStatus(
    complaintId,
    status
) {

    let remarks = null;

    if (status === 'in_progress') {

        remarks =
            'Field officer started work.';
    }

    if (status === 'resolved') {

        remarks =
            'Field officer marked complaint as resolved.';
    }

    try {

        const response =
            await fetch(
                `/api/field/complaints/${complaintId}/status`,
                {
                    method: 'POST',

                    headers: {
                        'Content-Type':
                            'application/json',

                        'Accept':
                            'application/json'
                    },

                    body: JSON.stringify({
                        status,
                        remarks
                    })
                }
            );

        const data =
            await response.json();

        if (!response.ok || !data.success) {

            throw new Error(
                data.message
                || 'Unable to update status.'
            );
        }

        await loadDashboard();

    } catch (error) {

        alert(
            error.message
        );
    }
}


function formatStatus(value) {

    if (!value) {
        return 'Unknown';
    }

    return value
        .replaceAll('_', ' ')
        .replace(
            /\b\w/g,
            c => c.toUpperCase()
        );
}


function formatDate(value) {

    if (!value) {
        return '—';
    }

    return new Date(value)
        .toLocaleString();
}


loadDashboard();

</script>

</body>
</html>
