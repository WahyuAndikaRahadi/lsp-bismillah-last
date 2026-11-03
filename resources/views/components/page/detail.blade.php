@props([
    'title' => '',
    'subtitle' => null,
    'badge' => null,
    'backUrl' => null,
    'backLabel' => 'Kembali',
    'actions' => null,
])

<div class="space-y-6">
    <!-- Back Link -->
    @if ($backUrl)
        <div>
            <a href="{{ $backUrl }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-500 hover:text-zinc-800 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                {{ $backLabel }}
            </a>
        </div>
    @endif

    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 border-b border-zinc-200">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-xl sm:text-2xl font-bold text-zinc-900 tracking-tight">{{ $title }}</h1>
                @if ($badge)
                    {{ $badge }}
                @endif
            </div>
            @if ($subtitle)
                <p class="mt-1 text-sm text-zinc-600">{{ $subtitle }}</p>
            @endif
        </div>
        @if ($actions)
            <div class="flex items-center gap-3">
                {{ $actions }}
            </div>
        @endif
    </div>

    <!-- Main Detail Content Card -->
    <div class="bg-white rounded-xl border border-zinc-200 shadow-xs p-6 space-y-6">
        {{ $slot }}
    </div>
</div>
