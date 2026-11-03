<!DOCTYPE html>
<html lang="id" class="h-full bg-zinc-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Sistem Sertifikasi LSP') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Scripts & Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex flex-col font-sans antialiased text-zinc-800 bg-zinc-50 selection:bg-blue-600 selection:text-white">

    <!-- Top Public Navbar -->
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-zinc-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand / Logo -->
                <div class="flex items-center gap-3">
                    <a href="{{ url('/') }}" class="flex items-center gap-2.5 group">
                        <div class="w-9 h-9 rounded-lg bg-blue-600 flex items-center justify-center text-white font-bold text-lg shadow-sm group-hover:bg-blue-700 transition">
                            LSP
                        </div>
                        <div class="flex flex-col">
                            <span class="font-bold text-zinc-900 text-sm tracking-tight leading-none">LSP SERTIFIKASI</span>
                            <span class="text-[11px] text-zinc-500 font-medium leading-tight mt-0.5">Sistem Sertifikasi Profesi Terlisensi BNSP</span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-zinc-600">
                    <a href="{{ url('/') }}" class="hover:text-blue-600 transition {{ request()->is('/') ? 'text-blue-600 font-semibold' : '' }}">Beranda</a>
                    <a href="{{ url('/#skema') }}" class="hover:text-blue-600 transition">Skema Sertifikasi</a>
                    <a href="{{ url('/#jadwal') }}" class="hover:text-blue-600 transition">Jadwal Asesmen</a>
                    <a href="{{ url('/#tuk') }}" class="hover:text-blue-600 transition">Tempat Uji (TUK)</a>
                </nav>

                <!-- Auth Buttons -->
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route(auth()->user()->role->dashboardRoute()) }}" class="inline-flex items-center gap-2 px-3.5 py-1.5 text-xs font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition shadow-sm">
                            <span>Dashboard ({{ auth()->user()->role->label() }})</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-3.5 py-1.5 text-xs font-semibold text-zinc-700 hover:text-zinc-900 transition">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center px-3.5 py-1.5 text-xs font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition shadow-sm">
                            Daftar Asesi
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-1">
        {{ $slot }}
    </main>

    <!-- Toast Notification Component -->
    <x-toast />

    <!-- Public Footer -->
    <footer class="bg-white border-t border-zinc-200 mt-auto py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-zinc-500">
            <p>&copy; {{ date('Y') }} Sistem Sertifikasi LSP. Mengikuti standar operasional Badan Nasional Sertifikasi Profesi (BNSP).</p>
            <div class="flex items-center gap-6">
                <span>Versi 2.0 (Rebuild)</span>
                <span>Standar ISO 17024</span>
            </div>
        </div>
    </footer>

</body>
</html>
