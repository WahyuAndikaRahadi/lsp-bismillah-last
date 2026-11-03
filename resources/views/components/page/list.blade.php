@props([
    'title' => '',
    'description' => null,
    'actions' => null,
    'filters' => null,
    'isEmpty' => false,
    'emptyMessage' => 'Belum ada data yang tersedia.',
    'emptyAction' => null,
])

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 border-b border-zinc-200">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-zinc-900 tracking-tight">{{ $title }}</h1>
            @if ($description)
                <p class="mt-1 text-sm text-zinc-600">{{ $description }}</p>
            @endif
        </div>
        @if ($actions)
            <div class="flex items-center gap-3">
                {{ $actions }}
            </div>
        @endif
    </div>

    <!-- Filters & Search Toolbar -->
    @if ($filters)
        <div class="p-4 bg-white rounded-xl border border-zinc-200 shadow-xs">
            {{ $filters }}
        </div>
    @endif

    <!-- Content / Table or Empty State -->
    @if ($isEmpty)
        <div class="p-12 text-center bg-white rounded-xl border border-dashed border-zinc-300">
            <div class="w-12 h-12 mx-auto rounded-full bg-zinc-100 flex items-center justify-center text-zinc-400 mb-3">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
            </div>
            <p class="text-sm font-semibold text-zinc-800">{{ $emptyMessage }}</p>
            @if ($emptyAction)
                <div class="mt-4">
                    {{ $emptyAction }}
                </div>
            @endif
        </div>
    @else
        <div class="bg-white rounded-xl border border-zinc-200 shadow-xs overflow-hidden">
            {{ $slot }}
        </div>
    @endif
</div>
