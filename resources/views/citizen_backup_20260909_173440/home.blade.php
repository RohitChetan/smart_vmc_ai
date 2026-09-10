<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Smart Vadodara Connect</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 text-slate-900">

    <div class="mx-auto min-h-screen max-w-md bg-white shadow-xl">

        {{-- Header --}}
        <header class="px-5 pb-5 pt-6">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm font-medium text-slate-500">
                        Vadodara Municipal Corporation
                    </p>

                    <h1 class="mt-1 text-2xl font-bold tracking-tight">
                        Smart Vadodara
                    </h1>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-indigo-600 text-lg font-bold text-white">
                    SV
                </div>

            </div>

        </header>


        {{-- Hero --}}
        <section class="px-5">

            <div class="overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 via-indigo-600 to-violet-700 p-6 text-white">

                <p class="text-sm font-medium text-indigo-100">
                    AI-powered civic services
                </p>

                <h2 class="mt-2 text-3xl font-bold leading-tight">
                    Report a civic issue.
                    <br>
                    We'll route it automatically.
                </h2>

                <p class="mt-3 text-sm leading-6 text-indigo-100">
                    Upload a photo, describe the problem and share your location.
                    AI will identify the issue and automatically route it to the correct department.
                </p>

                <a
                    href="{{ route('citizen.complaints.create') }}"
                    class="mt-6 flex w-full items-center justify-center rounded-2xl bg-white px-5 py-3.5 font-semibold text-indigo-700 shadow-sm transition hover:bg-indigo-50"
                >
                    Report a Problem
                </a>

            </div>

        </section>


        {{-- Quick actions --}}
        <section class="px-5 pt-6">

            <div class="grid grid-cols-2 gap-3">

                <a
                    href="{{ route('citizen.complaints.create') }}"
                    class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"
                >
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-xl">
                        📷
                    </div>

                    <h3 class="mt-3 font-semibold">
                        Report Issue
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Photo + GPS + AI
                    </p>
                </a>


                <a
                    href="#tracking"
                    class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"
                >
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-xl">
                        📍
                    </div>

                    <h3 class="mt-3 font-semibold">
                        Track Complaint
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Check live status
                    </p>
                </a>

            </div>

        </section>


        {{-- AI features --}}
        <section class="px-5 pb-8 pt-6">

            <h2 class="text-lg font-bold">
                How Smart Vadodara works
            </h2>

            <div class="mt-4 space-y-3">

                <div class="flex gap-3 rounded-2xl bg-slate-50 p-4">
                    <span class="text-xl">📍</span>

                    <div>
                        <h3 class="font-semibold">
                            Automatic Ward Detection
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            GPS coordinates automatically identify the VMC ward.
                        </p>
                    </div>
                </div>


                <div class="flex gap-3 rounded-2xl bg-slate-50 p-4">
                    <span class="text-xl">🤖</span>

                    <div>
                        <h3 class="font-semibold">
                            AI Issue Detection
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            AI analyzes your description and uploaded image.
                        </p>
                    </div>
                </div>


                <div class="flex gap-3 rounded-2xl bg-slate-50 p-4">
                    <span class="text-xl">🚀</span>

                    <div>
                        <h3 class="font-semibold">
                            Automatic Routing
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Complaints are routed to the appropriate civic department.
                        </p>
                    </div>
                </div>

            </div>

        </section>


        {{-- Footer --}}
        <footer class="border-t border-slate-100 px-5 py-6 text-center">

            <p class="text-xs text-slate-400">
                Smart Vadodara Connect
            </p>

            <p class="mt-1 text-xs text-slate-400">
                AI-powered civic complaint management
            </p>

        </footer>

    </div>

</body>
</html>
