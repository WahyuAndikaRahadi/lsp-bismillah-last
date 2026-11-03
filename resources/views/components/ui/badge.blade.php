@props([
    'variant' => 'neutral',
])

@php
    $classes = match ($variant) {
        'success' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'danger' => 'bg-red-50 text-red-700 ring-red-600/20',
        'warning' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        'info' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
        default => 'bg-zinc-100 text-zinc-700 ring-zinc-500/10',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold ring-1 ring-inset {$classes}"]) }}>
    {{ $slot }}
</span>
