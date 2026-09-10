@props([
    'type' => 'button',
    'variant' => 'primary',
    'href' => null,
    'disabled' => false,
])

@php
    $baseClasses = 'inline-flex w-full items-center justify-center rounded-2xl px-5 py-3.5 text-sm font-semibold transition disabled:cursor-not-allowed disabled:opacity-50';

    $variants = [
        'primary' => 'bg-indigo-600 text-white shadow-lg shadow-indigo-200 hover:bg-indigo-700',
        'secondary' => 'bg-slate-100 text-slate-800 hover:bg-slate-200',
        'white' => 'bg-white text-indigo-700 shadow-sm hover:bg-indigo-50',
        'danger' => 'bg-red-600 text-white hover:bg-red-700',
    ];

    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

@if($href)
    <a
        href="{{ $href }}"
        {{ $attributes->merge(['class' => $classes]) }}
    >
        {{ $slot }}
    </a>
@else
    <button
        type="{{ $type }}"
        @disabled($disabled)
        {{ $attributes->merge(['class' => $classes]) }}
    >
        {{ $slot }}
    </button>
@endif
