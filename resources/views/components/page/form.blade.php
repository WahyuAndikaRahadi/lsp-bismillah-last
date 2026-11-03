@props([
    'title' => '',
    'description' => null,
    'action' => '',
    'method' => 'POST',
    'enctype' => null,
    'backUrl' => null,
    'submitLabel' => 'Simpan Data',
])

<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="pb-4 border-b border-zinc-200">
        <h1 class="text-xl sm:text-2xl font-bold text-zinc-900 tracking-tight">{{ $title }}</h1>
        @if ($description)
            <p class="mt-1 text-sm text-zinc-600">{{ $description }}</p>
        @endif
    </div>

    <!-- Form Container -->
    <form action="{{ $action }}" method="{{ strtolower($method) === 'get' ? 'GET' : 'POST' }}" @if ($enctype) enctype="{{ $enctype }}" @endif class="space-y-6">
        @if (strtolower($method) !== 'get')
            @csrf
            @if (!in_array(strtoupper($method), ['GET', 'POST']))
                @method($method)
            @endif
        @endif

        <div class="bg-white rounded-xl border border-zinc-200 shadow-xs p-6 space-y-5">
            {{ $slot }}
        </div>

        <!-- Footer Actions -->
        <div class="flex items-center justify-end gap-3 pt-2">
            @if ($backUrl)
                <a href="{{ $backUrl }}" class="min-h-[44px] px-5 py-2.5 rounded-xl border border-zinc-300 text-sm font-semibold text-zinc-700 bg-white hover:bg-zinc-50 transition inline-flex items-center justify-center">
                    Batal
                </a>
            @endif
            <button type="submit" class="min-h-[44px] px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-xs transition inline-flex items-center justify-center focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
                {{ $submitLabel }}
            </button>
        </div>
    </form>
</div>
