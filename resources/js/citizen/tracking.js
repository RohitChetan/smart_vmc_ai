const API_BASE = '/api';

const $ = (id) => document.getElementById(id);

function escapeHtml(value) {
    if (value === null || value === undefined) {
        return '';
    }

    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

function showError(message) {
    $('loading')?.classList.add('hidden');
    $('result')?.classList.add('hidden');

    const error = $('error');

    if (error) {
        error.textContent = message;
        error.classList.remove('hidden');
    }
}

function clearError() {
    const error = $('error');

    if (error) {
        error.textContent = '';
        error.classList.add('hidden');
    }
}

function formatText(value) {
    if (!value) {
        return '—';
    }

    return String(value)
        .replaceAll('_', ' ')
        .replace(/\b\w/g, char => char.toUpperCase());
}

function formatDate(value) {
    if (!value) {
        return '—';
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return '—';
    }

    return date.toLocaleString('en-IN', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

function statusClasses(status) {

    const classes = {
        submitted: 'bg-blue-100 text-blue-700',
        ai_processing: 'bg-purple-100 text-purple-700',
        assigned: 'bg-orange-100 text-orange-700',
        in_progress: 'bg-blue-100 text-blue-700',
        resolved: 'bg-emerald-100 text-emerald-700',
        verification_pending: 'bg-amber-100 text-amber-700',
        closed: 'bg-emerald-100 text-emerald-700',
        reopened: 'bg-red-100 text-red-700',
        rejected: 'bg-red-100 text-red-700',
    };

    return classes[status]
        ?? 'bg-slate-100 text-slate-700';
}

function renderTimeline(history) {

    const timeline = $('timeline');

    if (!timeline) {
        return;
    }

    if (!Array.isArray(history) || history.length === 0) {

        timeline.innerHTML = `
            <div class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-500">
                No timeline updates available.
            </div>
        `;

        return;
    }

    timeline.innerHTML = history.map((item, index) => {

        const status = item.status ?? 'updated';

        return `
            <div class="relative flex gap-3 ${index < history.length - 1 ? 'pb-6' : ''}">

                <div class="relative flex w-4 shrink-0 justify-center">

                    <div class="mt-1 h-3 w-3 rounded-full bg-indigo-600 ring-4 ring-indigo-50"></div>

                    ${
                        index < history.length - 1
                            ? `
                                <div class="absolute top-4 h-full w-px bg-slate-200"></div>
                            `
                            : ''
                    }

                </div>

                <div class="min-w-0 flex-1">

                    <div class="text-sm font-semibold text-slate-900">
                        ${escapeHtml(formatText(status))}
                    </div>

                    ${
                        item.remarks
                            ? `
                                <div class="mt-1 text-xs leading-5 text-slate-500">
                                    ${escapeHtml(item.remarks)}
                                </div>
                            `
                            : ''
                    }

                    <div class="mt-1 text-xs text-slate-400">
                        ${escapeHtml(formatDate(item.created_at))}
                    </div>

                </div>

            </div>
        `;

    }).join('');
}

function renderComplaint(complaint) {

    /*
    |--------------------------------------------------------------------------
    | Summary
    |--------------------------------------------------------------------------
    */

    $('number').textContent =
        complaint.complaint_number ?? '—';

    $('description').textContent =
        complaint.description ?? '—';

    const status = complaint.status ?? 'submitted';

    $('status').textContent =
        formatText(status);

    $('status').className =
        `mt-3 inline-flex rounded-full px-3 py-1.5 text-xs font-semibold ${statusClasses(status)}`;


    /*
    |--------------------------------------------------------------------------
    | Information
    |--------------------------------------------------------------------------
    */

    $('ward').textContent =
        complaint.ward
            ? `Ward ${complaint.ward.ward_no} • ${complaint.ward.name}`
            : '—';

    $('priority').textContent =
        formatText(complaint.priority);

    $('category').textContent =
        complaint.category?.ai_detected
        ?? 'Processing';

    $('department').textContent =
        complaint.department
        ?? '—';

    $('assigned').textContent =
        complaint.assignment?.assigned_to?.name
        ?? 'Not assigned yet';

    /*
    |--------------------------------------------------------------------------
    | AI Confidence
    |--------------------------------------------------------------------------
    */

    if (
        complaint.ai_confidence !== null &&
        complaint.ai_confidence !== undefined
    ) {

        const confidence =
            Number(complaint.ai_confidence);

        $('confidence').textContent =
            `${Math.round(confidence * 100)}%`;

    } else {

        $('confidence').textContent = '—';

    }


    /*
    |--------------------------------------------------------------------------
    | Dates
    |--------------------------------------------------------------------------
    */

    $('submittedAt').textContent =
        formatDate(complaint.submitted_at);

    $('dueAt').textContent =
        formatDate(complaint.due_at);

    $('resolvedAt').textContent =
        formatDate(complaint.resolved_at);


    /*
    |--------------------------------------------------------------------------
    | Location
    |--------------------------------------------------------------------------
    */

    if (complaint.location) {

        const latitude =
            complaint.location.latitude;

        const longitude =
            complaint.location.longitude;

        $('locationText').textContent =
            `${latitude}, ${longitude}`;

        const mapUrl =
            `https://www.google.com/maps?q=${latitude},${longitude}`;

        $('mapLink').href = mapUrl;

    } else {

        $('locationText').textContent =
            'Location unavailable';

        $('mapLink').classList.add('hidden');

    }


    /*
    |--------------------------------------------------------------------------
    | Timeline
    |--------------------------------------------------------------------------
    */

    renderTimeline(
        complaint.status_history ?? []
    );


    /*
    |--------------------------------------------------------------------------
    | Verification State
    |--------------------------------------------------------------------------
    */

    const verificationCard =
        $('verificationCard');

    const closedCard =
        $('closedCard');

    verificationCard?.classList.add('hidden');
    closedCard?.classList.add('hidden');

    if (status === 'verification_pending') {

        verificationCard?.classList.remove('hidden');

    }

    if (status === 'closed') {

        closedCard?.classList.remove('hidden');

    }


    /*
    |--------------------------------------------------------------------------
    | Show result
    |--------------------------------------------------------------------------
    */

    $('loading')?.classList.add('hidden');
    $('error')?.classList.add('hidden');
    $('result')?.classList.remove('hidden');
}

async function trackComplaint() {

    const input =
        $('complaintNumber');

    if (!input) {
        return;
    }

    const complaintNumber =
        input.value.trim();

    if (!complaintNumber) {

        showError(
            'Please enter complaint number.'
        );

        return;
    }

    clearError();

    $('result')?.classList.add('hidden');
    $('loading')?.classList.remove('hidden');

    try {

        const response = await fetch(
            `${API_BASE}/complaints/${encodeURIComponent(complaintNumber)}`,
            {
                method: 'GET',

                headers: {
                    Accept: 'application/json',
                },
            }
        );

        const data =
            await response.json();

        console.log(
            'Track Complaint API:',
            data
        );

        if (!response.ok) {

            throw new Error(
                data.message
                ?? 'Complaint not found.'
            );

        }

        if (
            !data.success ||
            !data.complaint
        ) {

            throw new Error(
                data.message
                ?? 'Invalid complaint response.'
            );

        }

        renderComplaint(
            data.complaint
        );

    } catch (error) {

        console.error(
            'Track Complaint Error:',
            error
        );

        showError(
            error.message
            ?? 'Unable to track complaint.'
        );

    }
}


/*
|--------------------------------------------------------------------------
| Page Initialization
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'DOMContentLoaded',
    () => {

        const button =
            $('trackButton');

        const input =
            $('complaintNumber');

        button?.addEventListener(
            'click',
            trackComplaint
        );

        input?.addEventListener(
            'keydown',
            (event) => {

                if (event.key === 'Enter') {
                    trackComplaint();
                }

            }
        );

        /*
         * Automatically load demo complaint
         */

        if (
            input &&
            input.value.trim()
        ) {

            trackComplaint();

        }

    }
);