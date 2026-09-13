@extends('layouts.citizen')

@section('title', 'Track Complaint - Smart Vadodara')

@section('header')
    <header class="border-b border-slate-100 px-5 py-5">
        <p class="text-xs font-medium text-slate-500">
            {{ settings('organization_name', 'Vadodara Municipal Corporation') }}
        </p>

        <h1 class="mt-1 text-2xl font-bold">
            Track Complaint
        </h1>
    </header>
@endsection

@section('content')

<main class="px-5 pb-8 pt-5">

    {{-- Search --}}
    <div class="flex items-center gap-2">

        <input
            id="complaintNumber"
            type="text"
            placeholder="Enter complaint number"
            value="SVC-2026-4R0JV7YD"
            class="min-w-0 flex-1 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50"
        >

        <button
            type="button"
            id="trackButton"
            class="shrink-0 rounded-2xl bg-indigo-600 px-5 py-3 font-semibold text-white shadow-lg shadow-indigo-200 transition hover:bg-indigo-700"
        >
            Track
        </button>

    </div>


    {{-- Error --}}
    <div
        id="error"
        class="mt-4 hidden rounded-2xl bg-red-50 p-4 text-sm text-red-700"
    ></div>


    {{-- Loading --}}
    <div
        id="loading"
        class="hidden py-10 text-center text-sm text-slate-500"
    >
        Loading complaint...
    </div>


    {{-- Result --}}
    <div id="result" class="mt-5 hidden">

        {{-- Complaint Summary --}}
        <x-citizen.card>

            <div
                id="number"
                class="text-xs font-medium text-slate-500"
            ></div>

            <div
                id="description"
                class="mt-1 text-lg font-semibold"
            ></div>

            <div
                id="status"
                class="mt-3 inline-flex rounded-full px-3 py-1.5 text-xs font-semibold"
            ></div>

        </x-citizen.card>


        {{-- Information --}}
        <div class="mt-4 grid grid-cols-2 gap-3">

            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                <div class="text-xs text-slate-500">Ward</div>
                <div id="ward" class="mt-1 text-sm font-semibold"></div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                <div class="text-xs text-slate-500">Priority</div>
                <div id="priority" class="mt-1 text-sm font-semibold"></div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                <div class="text-xs text-slate-500">AI Category</div>
                <div id="category" class="mt-1 text-sm font-semibold"></div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                <div class="text-xs text-slate-500">Department</div>
                <div id="department" class="mt-1 text-sm font-semibold"></div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                <div class="text-xs text-slate-500">Assigned To</div>
                <div id="assigned" class="mt-1 text-sm font-semibold"></div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                <div class="text-xs text-slate-500">AI Confidence</div>
                <div id="confidence" class="mt-1 text-sm font-semibold"></div>
            </div>

        </div>


        {{-- Dates --}}
        <div class="mt-4 grid grid-cols-2 gap-3">

            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                <div class="text-xs text-slate-500">
                    Submitted
                </div>

                <div
                    id="submittedAt"
                    class="mt-1 text-sm font-semibold"
                ></div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                <div class="text-xs text-slate-500">
                    SLA Due
                </div>

                <div
                    id="dueAt"
                    class="mt-1 text-sm font-semibold"
                ></div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-4 col-span-2">

                <div class="text-xs text-slate-500">
                    Resolved
                </div>

                <div
                    id="resolvedAt"
                    class="mt-1 text-sm font-semibold"
                ></div>

            </div>

        </div>


        {{-- Location --}}
        <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-5">

            <div class="flex items-center justify-between">

                <div>
                    <h2 class="text-base font-bold">
                        Complaint Location
                    </h2>

                    <p
                        id="locationText"
                        class="mt-1 text-xs text-slate-500"
                    ></p>
                </div>

                <div class="rounded-xl bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700">
                    GPS
                </div>

            </div>

            <a
                id="mapLink"
                href="#"
                target="_blank"
                rel="noopener noreferrer"
                class="mt-4 inline-flex w-full items-center justify-center rounded-2xl bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-800 transition hover:bg-slate-200"
            >
                View Location
            </a>

        </div>


        {{-- Timeline --}}
        <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-5">

            <h2 class="text-base font-bold">
                Complaint Timeline
            </h2>

            <div
                id="timeline"
                class="mt-4"
            ></div>

        </div>


        {{-- Verification --}}
        <div
            id="verificationCard"
            class="mt-4 hidden rounded-2xl border border-amber-200 bg-amber-50 p-5"
        >

            <div class="flex gap-3">

                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-lg">
                    ✓
                </div>

                <div>
                    <h2 class="font-bold text-slate-900">
                        Is the issue resolved?
                    </h2>

                    <p class="mt-1 text-sm leading-5 text-slate-600">
                        The field officer has marked this complaint as resolved.
                        Please verify the work before we close the complaint.
                    </p>
                </div>

            </div>

            <button
                type="button"
                id="verifyButton"
                class="mt-4 w-full rounded-2xl bg-indigo-600 px-5 py-3.5 text-sm font-semibold text-white shadow-lg shadow-indigo-200"
            >
                Verify Resolution
            </button>

        </div>


        {{-- Closed Message --}}
        <div
            id="closedCard"
            class="mt-4 hidden rounded-2xl bg-emerald-50 p-5 text-center"
        >

            <div class="text-2xl">
                🎉
            </div>

            <h2 class="mt-2 font-bold text-emerald-900">
                Complaint Closed
            </h2>

            <p class="mt-1 text-sm text-emerald-700">
                Thank you for helping keep Vadodara clean and better.
            </p>

            <button
                type="button"
                id="reopenButton"
                class="mt-4 w-full rounded-2xl border border-red-200 bg-white px-5 py-3.5 text-sm font-semibold text-red-600 transition hover:bg-red-50"
            >
                Issue Still Not Resolved? Reopen Complaint
            </button>

        </div>

    </div>

</main>

@endsection

@push('scripts')
    @vite('resources/js/citizen/tracking.js')
@endpush