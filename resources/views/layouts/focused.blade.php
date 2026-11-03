<!DOCTYPE html>
<html lang="id" class="h-full bg-zinc-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Ruang Uji Terfokus — LSP' }}</title>

    <!-- Local Self-Hosted Fonts & Scripts via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex flex-col font-sans antialiased text-zinc-900 bg-zinc-50 selection:bg-blue-600 selection:text-white">

    <!-- Focused Top Header -->
    <header class="h-16 bg-white border-b border-zinc-200 px-4 sm:px-8 flex items-center justify-between shrink-0">
        <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white font-bold text-sm">
                LSP
            </span>
            <div class="flex flex-col">
                <span class="text-sm font-bold text-zinc-900 tracking-tight leading-tight">
                    {{ $headerTitle ?? 'Ruang Asesmen Terfokus' }}
                </span>
                <span class="text-xs text-zinc-500 font-medium">
                    {{ $headerSubtitle ?? 'Mode Uji Mandiri & Observasi Terstruktur' }}
                </span>
            </div>
        </div>

        <div class="flex items-center gap-4">
            {{ $headerActions ?? '' }}
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 max-w-5xl mx-auto w-full">
        {{ $slot }}
    </main>

</body>
</html>
