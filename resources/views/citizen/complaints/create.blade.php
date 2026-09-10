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


    <main class="px-5 pb-8 pt-5">

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
            id="submitButton"
            type="button"
            class="mt-7 w-full rounded-2xl bg-indigo-600 px-5 py-4 text-sm font-bold text-white shadow-lg shadow-indigo-200 transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50"
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

let latitude = null;
let longitude = null;
let locationAccuracy = null;

let selectedFiles = [];


/*
|--------------------------------------------------------------------------
| Location
|--------------------------------------------------------------------------
*/

function detectLocation() {

    const status = document.getElementById('locationStatus');
    const text = document.getElementById('locationText');
    const coordinates = document.getElementById('coordinates');

    if (!navigator.geolocation) {

        status.textContent = 'Unavailable';
        text.textContent = 'Location is not supported by this browser.';
        return;
    }

    status.textContent = 'Detecting...';
    text.textContent = 'Getting your current location...';

    navigator.geolocation.getCurrentPosition(

        async function(position) {

            latitude = position.coords.latitude;
            longitude = position.coords.longitude;
            locationAccuracy = position.coords.accuracy;

            coordinates.textContent =
                `${latitude.toFixed(6)}, ${longitude.toFixed(6)} • ±${Math.round(locationAccuracy)}m`;

            status.textContent = 'Detected ✓';
            status.className =
                'text-xs font-semibold text-emerald-600';

            text.textContent =
                'Checking VMC ward...';

            try {

                const response = await fetch(
                    '/api/location/detect-ward',
                    {
                        method: 'POST',

                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },

                        body: JSON.stringify({
                            latitude: latitude,
                            longitude: longitude
                        })
                    }
                );

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(
                        data.message ||
                        'Location is outside VMC boundaries.'
                    );
                }

                text.textContent =
                    `Ward ${data.ward.ward_no} • ${data.ward.name}`;

                text.className =
                    'text-sm font-semibold text-emerald-700';

            } catch (error) {

                text.textContent =
                    error.message;

                text.className =
                    'text-sm font-semibold text-red-600';

            }

        },

        function(error) {

            status.textContent = 'Permission required';

            text.textContent =
                'Please allow location access to submit a complaint.';

            coordinates.textContent =
                error.message || 'Unable to detect location.';

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
| Media
|--------------------------------------------------------------------------
*/

function updateMediaPreview() {

    const preview =
        document.getElementById('mediaPreview');

    if (selectedFiles.length === 0) {

        preview.classList.add('hidden');
        preview.innerHTML = '';

        return;
    }

    preview.classList.remove('hidden');

    preview.innerHTML = '';

    selectedFiles.forEach((file, index) => {

        const wrapper =
            document.createElement('div');

        wrapper.className =
            'mb-2 flex items-center justify-between rounded-xl bg-slate-50 p-3';

        wrapper.innerHTML = `
            <div class="flex min-w-0 items-center gap-3">
                <span class="text-xl">
                    ${file.type.startsWith('video/') ? '🎥' : '🖼️'}
                </span>

                <div class="min-w-0">
                    <p class="truncate text-sm font-medium">
                        ${file.name}
                    </p>

                    <p class="text-xs text-slate-400">
                        ${(file.size / 1024 / 1024).toFixed(1)} MB
                    </p>
                </div>
            </div>

            <button
                type="button"
                data-index="${index}"
                class="remove-media ml-3 text-sm font-semibold text-red-500"
            >
                Remove
            </button>
        `;

        preview.appendChild(wrapper);
    });

    document.querySelectorAll('.remove-media')
        .forEach(button => {

            button.addEventListener(
                'click',
                function() {

                    const index =
                        Number(this.dataset.index);

                    selectedFiles.splice(index, 1);

                    updateMediaPreview();
                }
            );

        });
}


function addFiles(files) {

    const incoming =
        Array.from(files);

    selectedFiles =
        [...selectedFiles, ...incoming];

    if (selectedFiles.length > 5) {

        selectedFiles =
            selectedFiles.slice(0, 5);

        showError(
            'You can upload maximum 5 files.'
        );
    }

    updateMediaPreview();
}


document
    .getElementById('cameraInput')
    .addEventListener(
        'change',
        function() {

            addFiles(this.files);

            this.value = '';

        }
    );


document
    .getElementById('mediaInput')
    .addEventListener(
        'change',
        function() {

            addFiles(this.files);

            this.value = '';

        }
    );


/*
|--------------------------------------------------------------------------
| Error
|--------------------------------------------------------------------------
*/

function showError(message) {

    const box =
        document.getElementById('errorBox');

    box.textContent = message;

    box.classList.remove('hidden');

    box.scrollIntoView({
        behavior: 'smooth',
        block: 'center'
    });
}


function clearError() {

    document
        .getElementById('errorBox')
        .classList.add('hidden');
}


/*
|--------------------------------------------------------------------------
| Submit
|--------------------------------------------------------------------------
*/

document
    .getElementById('submitButton')
    .addEventListener(
        'click',
        async function() {

            clearError();

            const description =
                document
                    .getElementById('description')
                    .value
                    .trim();

            const category =
                document
                    .getElementById('category')
                    .value;

            if (description.length < 5) {

                showError(
                    'Please describe the problem in at least 5 characters.'
                );

                return;
            }

            if (latitude === null || longitude === null) {

                showError(
                    'Please allow location access before submitting.'
                );

                detectLocation();

                return;
            }

            if (selectedFiles.length > 5) {

                showError(
                    'Maximum 5 files are allowed.'
                );

                return;
            }


            const button = this;

            button.disabled = true;

            button.textContent =
                'Submitting...';


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

            if (category) {

                formData.append(
                    'category_id',
                    category
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


            try {

                const response =
                    await fetch(
                        '/api/complaints',
                        {
                            method: 'POST',

                            headers: {
                                'Accept':
                                    'application/json'
                            },

                            body: formData
                        }
                    );


                const data =
                    await response.json();


                if (!response.ok) {

                    if (data.errors) {

                        const firstError =
                            Object.values(data.errors)
                                .flat()[0];

                        throw new Error(
                            firstError ||
                            data.message ||
                            'Unable to submit complaint.'
                        );
                    }

                    throw new Error(
                        data.message ||
                        'Unable to submit complaint.'
                    );
                }


                document
                    .getElementById('submitButton')
                    .classList.add('hidden');


                document
                    .getElementById('successBox')
                    .classList.remove('hidden');


                document
                    .getElementById('complaintNumber')
                    .textContent =
                    data.complaint.complaint_number;


                document
                    .getElementById('complaintWard')
                    .textContent =
                    `Ward ${data.complaint.ward.ward_no} • ${data.complaint.ward.name}`;


                document
                    .getElementById('successBox')
                    .scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });


            } catch (error) {

                showError(
                    error.message
                );

                button.disabled = false;

                button.textContent =
                    'Submit Complaint';
            }

        }
    );


/*
|--------------------------------------------------------------------------
| Start
|--------------------------------------------------------------------------
*/

detectLocation();

</script>

</body>
</html>
