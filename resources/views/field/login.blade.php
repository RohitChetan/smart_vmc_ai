<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Field Officer Login — Smart Vadodara Connect</title>

    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-slate-50">

    <main class="flex min-h-screen items-center justify-center px-4 py-8">

        <div class="w-full max-w-md">

            {{-- Header --}}
            <div class="mb-8 text-center">

                <div
                    class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-600 text-2xl shadow-lg shadow-indigo-200"
                >
                    🏙️
                </div>

                <h1 class="mt-5 text-2xl font-bold text-slate-900">
                    Smart Vadodara Connect
                </h1>

                <p class="mt-2 text-sm text-slate-500">
                    Field Officer Portal
                </p>

            </div>


            {{-- Login Card --}}
            <div
                class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-200/50"
            >

                <div class="mb-6">

                    <h2 class="text-xl font-bold text-slate-900">
                        Field Officer Login
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Sign in to manage your assigned civic incidents.
                    </p>

                </div>


                {{-- Error --}}
                <div
                    id="errorBox"
                    class="mb-4 hidden rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700"
                ></div>


                {{-- Email --}}
                <div>

                    <label
                        for="email"
                        class="text-sm font-semibold text-slate-700"
                    >
                        Email
                    </label>

                    <input
                        id="email"
                        type="email"
                        autocomplete="email"
                        placeholder="ward11.officer@smartvadodara.test"
                        class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                    >

                </div>


                {{-- Password --}}
                <div class="mt-4">

                    <label
                        for="password"
                        class="text-sm font-semibold text-slate-700"
                    >
                        Password
                    </label>

                    <input
                        id="password"
                        type="password"
                        autocomplete="current-password"
                        placeholder="Enter your password"
                        class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                    >

                </div>


                {{-- Login --}}
                <button
                    type="button"
                    id="loginButton"
                    class="mt-6 w-full rounded-2xl bg-indigo-600 px-5 py-3.5 text-sm font-semibold text-white shadow-lg shadow-indigo-200 transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60"
                >
                    Sign In
                </button>


                <div class="mt-6 text-center">

                    <a
                        href="{{ route('citizen.home') }}"
                        class="text-sm font-semibold text-slate-500 hover:text-indigo-600"
                    >
                        ← Back to Citizen Portal
                    </a>

                </div>

            </div>


            <p class="mt-6 text-center text-xs text-slate-400">
                Authorized VMC Field Officers only
            </p>

        </div>

    </main>


<script>

const emailInput =
    document.getElementById('email');

const passwordInput =
    document.getElementById('password');

const loginButton =
    document.getElementById('loginButton');

const errorBox =
    document.getElementById('errorBox');


function showError(message) {

    errorBox.textContent =
        message;

    errorBox.classList.remove('hidden');
}


function hideError() {

    errorBox.textContent =
        '';

    errorBox.classList.add('hidden');
}


async function login() {

    hideError();

    const email =
        emailInput.value.trim();

    const password =
        passwordInput.value;

    if (!email) {

        showError('Please enter your email.');

        emailInput.focus();

        return;
    }

    if (!password) {

        showError('Please enter your password.');

        passwordInput.focus();

        return;
    }


    loginButton.disabled = true;

    loginButton.textContent =
        'Signing in...';


    try {

        const response =
            await fetch('/api/auth/login', {

                method: 'POST',

                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },

                body: JSON.stringify({
                    email,
                    password,
                }),

            });


        const data =
            await response.json();


        if (!response.ok || !data.success) {

            let message =
                data.message ||
                'Unable to sign in.';

            if (data.errors) {

                const validationMessages =
                    Object.values(data.errors).flat();

                if (validationMessages.length) {
                    message =
                        validationMessages.join(' ');
                }
            }

            throw new Error(message);
        }


        /*
         * Only Ward Officers can access
         * the Field Officer portal.
         */
        if (
            !data.user ||
            data.user.role !== 'ward_officer'
        ) {

            throw new Error(
                'This account is not authorized for the Field Officer portal.'
            );
        }


        if (!data.token) {

            throw new Error(
                'Login succeeded but no authentication token was returned.'
            );
        }


        /*
         * Keep citizen/admin sessions separate.
         */
        localStorage.removeItem(
            'smart_vadodara_token'
        );

        localStorage.removeItem(
            'smart_vadodara_admin_token'
        );


        /*
         * Store Field Officer token separately.
         */
        localStorage.setItem(
            'smart_vadodara_field_token',
            data.token
        );


        localStorage.setItem(
            'smart_vadodara_field_user',
            JSON.stringify(data.user)
        );


        window.location.href =
            "{{ route('field.dashboard') }}";


    } catch (error) {

        console.error(
            'Field Login Error:',
            error
        );

        showError(
            error.message ||
            'Unable to sign in.'
        );

        loginButton.disabled = false;

        loginButton.textContent =
            'Sign In';
    }
}


loginButton?.addEventListener(
    'click',
    login
);


passwordInput?.addEventListener(
    'keydown',
    (event) => {

        if (event.key === 'Enter') {
            login();
        }

    }
);

</script>

</body>
</html>
