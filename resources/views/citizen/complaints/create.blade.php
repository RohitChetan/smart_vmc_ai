<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Report an Issue - Smart Vadodara</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 text-slate-900">

<div class="mx-auto min-h-screen max-w-md bg-white shadow-xl">

    {{-- Header --}}
    <header class="flex items-center gap-3 border-b border-slate-100 px-5 py-4">

        <a
            href="{{ route('citizen.home') }}"
            class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-xl"
        >
            ←
        </a>

        <div>
            <p class="text-xs font-medium text-slate-500">
                Smart Vadodara
            </p>

            <h1 class="text-lg font-bold">
                Report an Issue
            </h1>
        </div>

    </header>


    <main class="px-5 pb-28 pt-5">

        {{-- Intro --}}
        <div class="rounded-2xl bg-indigo-50 p-4">

            <div class="flex gap-3">

                <div class="text-xl">
                    🤖
                </div>

                <div>
                    <h2 class="font-semibold text-indigo-900">
                        AI will identify the issue
                    </h2>

                    <p class="mt-1 text-sm leading-5 text-indigo-700">
                        You don't need to select the correct category.
                        Just describe the problem and upload a photo.
                    </p>
                </div>

            </div>

        </div>


        {{-- Description --}}
        <section class="mt-6">

            <label
                for="description"
                class="block text-sm font-semibold"
            >
                What is the problem?
            </label>

            <textarea
                id="description"
                rows="5"
                maxlength="5000"
                placeholder="Example: Road par garbage no moto pile chhe..."
                class="mt-2 w-full resize-none rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50"
            ></textarea>

            <p class="mt-1 text-xs text-slate-400">
                Describe the problem clearly.
            </p>

        </section>


        {{-- Media --}}
        <section class="mt-6">

            <label class="block text-sm font-semibold">
                Add Photo or Video
            </label>

            <div class="mt-3 grid grid-cols-2 gap-3">

                {{-- Camera --}}
                <label
                    class="flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 p-5 text-center transition hover:border-indigo-400 hover:bg-indigo-50"
                >

                    <div class="text-3xl">
                        📷
                    </div>

                    <span class="mt-2 text-sm font-semibold">
                        Take Photo
                    </span>

                    <span class="mt-1 text-xs text-slate-400">
                        Camera
                    </span>

                    <input
                        id="cameraInput"
                        type="file"
                        accept="image/*"
                        capture="environment"
                        class="hidden"
                    >

                </label>


                {{-- Gallery --}}
                <label
                    class="flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 p-5 text-center transition hover:border-indigo-400 hover:bg-indigo-50"
                >

                    <div class="text-3xl">
                        🖼️
                    </div>

                    <span class="mt-2 text-sm font-semibold">
                        Choose Media
                    </span>

                    <span class="mt-1 text-xs text-slate-400">
                        Photo / Video
                    </span>

                    <input
                        id="mediaInput"
                        type="file"
                        accept="image/*,video/mp4,video/quicktime"
                        multiple
                        class="hidden"
                    >

                </label>

            </div>


            {{-- Selected media --}}
            <div
                id="mediaPreview"
                class="mt-3 hidden"
            ></div>

        </section>


        {{-- Location --}}
        <section class="mt-6">

            <div class="flex items-center justify-between">

                <label class="text-sm font-semibold">
                    Your Location
                </label>

                <span
                    id="locationStatus"
                    class="text-xs font-medium text-slate-400"
                >
                    Not detected
                </span>

            </div>


            <div class="mt-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">

                <div class="flex gap-3">

                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-xl">
                        📍
                    </div>

                    <div class="min-w-0">

                        <p
                            id="locationText"
                            class="text-sm font-medium"
                        >
                            Detecting your location...
                        </p>

                        <p
                            id="coordinates"
                            class="mt-1 text-xs text-slate-400"
                        >
                            Please allow location access.
                        </p>

                    </div>

                </div>

            </div>

        </section>


        {{-- Category optional --}}
        <section class="mt-6">

            <label
                for="category"
                class="block text-sm font-semibold"
            >
                Category
                <span class="font-normal text-slate-400">
                    (Optional)
                </span>
            </label>

            <select
                id="category"
                class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50"
            >

                <option value="">
                    Let AI decide
                </option>

                <option value="1">
                    Road & Pothole
                </option>

                <option value="4">
                    Garbage / Waste
                </option>

                <option value="6">
                    Drainage Blockage
                </option>

                <option value="7">
                    Waterlogging
                </option>

                <option value="9">
                    Water Leakage
                </option>

                <option value="11">
                    Street Light
                </option>

                <option value="13">
                    Tree / Fallen Tree
                </option>

                <option value="15">
                    Building / Structure
                </option>

                <option value="16">
                    Other Civic Issue
                </option>

            </select>

            <p class="mt-1 text-xs text-slate-400">
                Even if you choose incorrectly, AI can correct it.
            </p>

        </section>


        {{-- Error --}}
        <div
            id="errorBox"
            class="mt-5 hidden rounded-2xl bg-red-50 p-4 text-sm text-red-700"
        ></div>


        {{-- Success --}}
        <div
            id="successBox"
            class="mt-5 hidden rounded-2xl bg-emerald-50 p-5"
        >

            <div class="text-3xl">
                ✅
            </div>

            <h2 class="mt-2 text-lg font-bold text-emerald-900">
                Complaint Submitted
            </h2>

            <p class="mt-1 text-sm text-emerald-700">
                Your complaint has been received successfully.
            </p>

            <div class="mt-4 rounded-xl bg-white p-3">

                <p class="text-xs text-slate-400">
                    Complaint Number
                </p>

                <p
                    id="complaintNumber"
                    class="mt-1 font-bold text-indigo-700"
                ></p>

            </div>

            <div class="mt-3 rounded-xl bg-white p-3">

                <p class="text-xs text-slate-400">
                    Detected Ward
                </p>

                <p
                    id="complaintWard"
                    class="mt-1 font-semibold"
                ></p>

            </div>

            <button
                onclick="window.location.reload()"
                class="mt-4 w-full rounded-xl bg-indigo-600 py-3 text-sm font-semibold text-white"
            >
                Report Another Issue
            </button>

        </div>


        {{-- Submit --}}
        <button
            type="button"
            id="submitComplaintButton"
            onclick="submitComplaint()"
            class="mt-6 w-full rounded-2xl bg-indigo-600 px-5 py-4 text-sm font-bold text-white shadow-lg transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60"
        >
            Submit Complaint
        </button>


        <p class="mt-3 text-center text-xs leading-5 text-slate-400">
            By submitting, you allow Smart Vadodara to use the
            provided information for civic issue processing.
        </p>

    </main>

</div>


<script>

const token = localStorage.getItem('smart_vadodara_token');

let latitude = null;
let longitude = null;
let locationAccuracy = null;

let selectedFiles = [];


/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

if (!token) {
    window.location.href = "{{ route('citizen.login') }}";
}


/*
|--------------------------------------------------------------------------
| Elements
|--------------------------------------------------------------------------
*/

const descriptionInput =
    document.getElementById('description');

const categoryInput =
    document.getElementById('category');

const cameraInput =
    document.getElementById('cameraInput');

const mediaInput =
    document.getElementById('mediaInput');

const mediaPreview =
    document.getElementById('mediaPreview');

const locationStatus =
    document.getElementById('locationStatus');

const locationText =
    document.getElementById('locationText');

const coordinates =
    document.getElementById('coordinates');

const errorBox =
    document.getElementById('errorBox');

const successBox =
    document.getElementById('successBox');


/*
|--------------------------------------------------------------------------
| Location Detection
|--------------------------------------------------------------------------
*/

function detectLocation() {

    if (!navigator.geolocation) {

        locationStatus.textContent =
            'Not supported';

        locationText.textContent =
            'Your browser does not support GPS location.';

        return;
    }

    locationStatus.textContent =
        'Detecting...';

    locationText.textContent =
        'Getting your current location...';

    navigator.geolocation.getCurrentPosition(

        function(position) {

            latitude =
                position.coords.latitude;

            longitude =
                position.coords.longitude;

            locationAccuracy =
                position.coords.accuracy;

            locationStatus.textContent =
                'Detected';

            locationStatus.className =
                'text-xs font-medium text-emerald-600';

            locationText.textContent =
                'Location detected successfully';

            coordinates.textContent =
                `${latitude.toFixed(7)}, ${longitude.toFixed(7)} • Accuracy ±${Math.round(locationAccuracy)}m`;
        },

        function(error) {

            console.error(
                'GPS error:',
                error
            );

            locationStatus.textContent =
                'Failed';

            locationStatus.className =
                'text-xs font-medium text-red-600';

            locationText.textContent =
                'Please allow location access to submit the complaint.';

            coordinates.textContent =
                'Location permission is required.';
        },

        {
            enableHighAccuracy: true,
            timeout: 15000,
            maximumAge: 0
        }
    );
}


/*
|--------------------------------------------------------------------------
| Camera Input
|--------------------------------------------------------------------------
*/

cameraInput?.addEventListener(
    'change',
    function(event) {

        addFiles(event.target.files);

        event.target.value = '';
    }
);


/*
|--------------------------------------------------------------------------
| Gallery / Media Input
|--------------------------------------------------------------------------
*/

mediaInput?.addEventListener(
    'change',
    function(event) {

        addFiles(event.target.files);

        event.target.value = '';
    }
);


/*
|--------------------------------------------------------------------------
| Add Files
|--------------------------------------------------------------------------
*/

function addFiles(files) {

    for (const file of files) {

        const isImage =
            file.type.startsWith('image/');

        const isVideo =
            file.type.startsWith('video/');

        if (!isImage && !isVideo) {
            continue;
        }

        if (file.size > 50 * 1024 * 1024) {

            showError(
                `${file.name} is larger than 50 MB.`
            );

            continue;
        }

        selectedFiles.push(file);
    }

    renderMediaPreview();
}


/*
|--------------------------------------------------------------------------
| Media Preview
|--------------------------------------------------------------------------
*/

function renderMediaPreview() {

    if (!selectedFiles.length) {

        mediaPreview.classList.add('hidden');

        mediaPreview.innerHTML = '';

        return;
    }

    mediaPreview.classList.remove('hidden');

    mediaPreview.innerHTML = `
        <div class="space-y-2">
            ${selectedFiles.map((file, index) => {

                const url =
                    URL.createObjectURL(file);

                if (file.type.startsWith('image/')) {

                    return `
                        <div class="flex items-center gap-3 rounded-xl border border-slate-200 p-2">

                            <img
                                src="${url}"
                                class="h-16 w-16 rounded-lg object-cover"
                            >

                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium">
                                    ${escapeHtml(file.name)}
                                </p>

                                <p class="text-xs text-slate-400">
                                    ${formatFileSize(file.size)}
                                </p>
                            </div>

                            <button
                                type="button"
                                onclick="removeFile(${index})"
                                class="px-2 text-red-500"
                            >
                                ✕
                            </button>

                        </div>
                    `;
                }

                return `
                    <div class="flex items-center gap-3 rounded-xl border border-slate-200 p-3">

                        <div class="text-2xl">
                            🎥
                        </div>

                        <div class="min-w-0 flex-1">

                            <p class="truncate text-sm font-medium">
                                ${escapeHtml(file.name)}
                            </p>

                            <p class="text-xs text-slate-400">
                                ${formatFileSize(file.size)}
                            </p>

                        </div>

                        <button
                            type="button"
                            onclick="removeFile(${index})"
                            class="px-2 text-red-500"
                        >
                            ✕
                        </button>

                    </div>
                `;

            }).join('')}
        </div>
    `;
}


/*
|--------------------------------------------------------------------------
| Remove File
|--------------------------------------------------------------------------
*/

function removeFile(index) {

    selectedFiles.splice(index, 1);

    renderMediaPreview();
}


/*
|--------------------------------------------------------------------------
| Submit Complaint
|--------------------------------------------------------------------------
*/

async function submitComplaint() {

    hideMessages();

    const description =
        descriptionInput.value.trim();

    if (!description) {

        showError(
            'Please describe the civic issue.'
        );

        descriptionInput.focus();

        return;
    }

    if (latitude === null || longitude === null) {

        showError(
            'Please allow location access before submitting.'
        );

        detectLocation();

        return;
    }

    if (!selectedFiles.length) {

        showError(
            'Please upload at least one photo or video.'
        );

        return;
    }


    const submitButton =
        document.getElementById('submitComplaintButton');

    if (submitButton) {

        submitButton.disabled = true;

        submitButton.textContent =
            'Submitting...';
    }


    try {

        const formData =
            new FormData();

        formData.append(
            'description',
            description
        );

        formData.append(
            'latitude',
            latitude
        );

        formData.append(
            'longitude',
            longitude
        );

        formData.append(
            'location_accuracy',
            locationAccuracy ?? ''
        );


        if (categoryInput?.value) {

            formData.append(
                'category_id',
                categoryInput.value
            );
        }


        selectedFiles.forEach(
            function(file) {

                formData.append(
                    'media[]',
                    file
                );
            }
        );


        const response =
            await fetch('/api/complaints', {

                method: 'POST',

                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + token
                },

                body: formData
            });


        const data =
            await response.json();


        if (!response.ok || !data.success) {

            let message =
                data.message ||
                'Unable to submit complaint.';

            if (data.errors) {

                const validationMessages =
                    Object.values(data.errors)
                        .flat();

                if (validationMessages.length) {

                    message =
                        validationMessages.join(' ');
                }
            }

            throw new Error(message);
        }


        /*
        |--------------------------------------------------------------------------
        | Success
        |--------------------------------------------------------------------------
        */

        document.getElementById(
            'complaintNumber'
        ).textContent =
            data.complaint.complaint_number;


        // Show the ward detected by backend GIS
        const wardElement =
            document.getElementById('complaintWard');

        if (wardElement) {
            if (data.complaint && data.complaint.ward) {
                wardElement.textContent =
                    `Ward ${data.complaint.ward.ward_no} – ${data.complaint.ward.name}`;
            } else {
                wardElement.textContent =
                    'Ward will be determined by GIS';
            }
        }


        successBox.classList.remove(
            'hidden'
        );


        /*
        |--------------------------------------------------------------------------
        | Hide Form Sections
        |--------------------------------------------------------------------------
        */

        descriptionInput.disabled = true;

        if (categoryInput) {
            categoryInput.disabled = true;
        }

        cameraInput.disabled = true;
        mediaInput.disabled = true;


        if (submitButton) {

            submitButton.disabled = true;

            submitButton.textContent =
                'Complaint Submitted';
        }


        /*
        |--------------------------------------------------------------------------
        | Scroll to success
        |--------------------------------------------------------------------------
        */

        successBox.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });


    } catch (error) {

        console.error(
            'Complaint submission error:',
            error
        );

        showError(
            error.message ||
            'Something went wrong while submitting your complaint.'
        );


        if (submitButton) {

            submitButton.disabled = false;

            submitButton.textContent =
                'Submit Complaint';
        }
    }
}


/*
|--------------------------------------------------------------------------
| Location Detection on Page Load
|--------------------------------------------------------------------------
*/

detectLocation();


/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

function showError(message) {

    errorBox.textContent =
        message;

    errorBox.classList.remove(
        'hidden'
    );

    errorBox.scrollIntoView({
        behavior: 'smooth',
        block: 'center'
    });
}


function hideMessages() {

    errorBox.classList.add(
        'hidden'
    );

    successBox.classList.add(
        'hidden'
    );
}


function formatFileSize(bytes) {

    if (bytes < 1024) {
        return bytes + ' B';
    }

    if (bytes < 1024 * 1024) {
        return (bytes / 1024).toFixed(1) + ' KB';
    }

    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
}


function escapeHtml(value) {

    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

</script>


<!-- Bottom Navigation -->
<!-- Bottom Navigation -->
<nav
    style="
        position: fixed;
        left: 50%;
        bottom: 0;
        transform: translateX(-50%);
        width: 100%;
        max-width: 448px;
        z-index: 99999;
        background: #ffffff;
        border-top: 1px solid #e2e8f0;
        box-shadow: 0 -4px 12px rgba(15, 23, 42, 0.08);
    "
>
    <div
        style="
            display: flex;
            align-items: center;
            justify-content: space-around;
            padding: 8px 8px calc(8px + env(safe-area-inset-bottom));
        "
    >

        <a
            href="{{ route('citizen.home') }}"
            style="
                display:flex;
                flex-direction:column;
                align-items:center;
                justify-content:center;
                min-width:70px;
                padding:4px 8px;
                text-decoration:none;
                color:#64748b;
                font-size:12px;
                font-weight:600;
            "
        >
            <span style="font-size:21px; line-height:24px;">🏠</span>
            <span>Home</span>
        </a>

        <a
            href="{{ route('citizen.complaints.create') }}"
            style="
                display:flex;
                flex-direction:column;
                align-items:center;
                justify-content:center;
                min-width:70px;
                padding:4px 8px;
                text-decoration:none;
                color:#4f46e5;
                font-size:12px;
                font-weight:700;
            "
        >
            <span style="font-size:21px; line-height:24px;">➕</span>
            <span>Report</span>
        </a>

        <a
            href="{{ route('citizen.track') }}"
            style="
                display:flex;
                flex-direction:column;
                align-items:center;
                justify-content:center;
                min-width:70px;
                padding:4px 8px;
                text-decoration:none;
                color:#64748b;
                font-size:12px;
                font-weight:600;
            "
        >
            <span style="font-size:21px; line-height:24px;">🔎</span>
            <span>Track</span>
        </a>

        <a
            href="#"
            onclick="alert('Civic Points system will be available soon.'); return false;"
            style="
                display:flex;
                flex-direction:column;
                align-items:center;
                justify-content:center;
                min-width:70px;
                padding:4px 8px;
                text-decoration:none;
                color:#64748b;
                font-size:12px;
                font-weight:600;
            "
        >
            <span style="font-size:21px; line-height:24px;">🏆</span>
            <span>Points</span>
        </a>

    </div>
</nav>

</body>
</html>
