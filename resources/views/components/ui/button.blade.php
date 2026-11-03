@props([
    'variant' => 'primary',
    'type' => 'button',
])

@php
    $classes = match ($variant) {
        'secondary' => 'bg-white hover:bg-zinc-50 text-zinc-800 border border-zinc-300 shadow-xs focus-visible:ring-zinc-400',
        'danger' => 'bg-red-600 hover:bg-red-700 text-white shadow-xs focus-visible:ring-red-600',
        default => 'bg-blue-600 hover:bg-blue-700 text-white shadow-xs focus-visible:ring-blue-600',
    };
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => "min-h-[44px] px-5 py-2.5 rounded-xl text-sm font-semibold transition inline-flex items-center justify-center gap-2 focus-visible:ring-2 focus-visible:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed {$classes}"]) }}>
    {{ $slot }}
</button>
