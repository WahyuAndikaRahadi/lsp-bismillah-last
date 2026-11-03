<!DOCTYPE html>
<html lang="id" class="h-full bg-lsp-canvas">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'LSP PrO Indo' }} - Sistem Sertifikasi BNSP</title>

    <!-- Google Fonts: Plus Jakarta Sans & Inter (Modern Sans-Serif) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        lsp: {
                            navy: '#1E2B6E',
                            'navy-dark': '#162052',
                            'navy-light': '#2A3B94',
                            red: '#CE181E',
                            'red-dark': '#B01419',
                            canvas: '#F8FAFC',
                            'input-bg': '#F8FAFC',
                            title: '#0F172A',
                            muted: '#64748B',
                        },
                        brand: {
                            50: '#EEF3FC',
                            100: '#D9E4FA',
                            200: '#B8CFF7',
                            500: '#2A3B94',
                            600: '#1E2B6E',
                            700: '#162052',
                            800: '#11183E',
                            900: '#0B1028',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', '"Inter"', 'system-ui', '-apple-system', 'sans-serif'],
                        serif: ['"Plus Jakarta Sans"', '"Inter"', 'system-ui', '-apple-system', 'sans-serif'],
                        heading: ['"Plus Jakarta Sans"', '"Inter"', 'system-ui', '-apple-system', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }

        body, button, input, select, textarea {
            font-family: 'Plus Jakarta Sans', 'Inter', system-ui, -apple-system, sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Plus Jakarta Sans', 'Inter', system-ui, -apple-system, sans-serif;
            letter-spacing: -0.015em;
        }

        /* Simplified Clean Design System */
        .card-contrast,
        [class*="shadow-[2px_2px"],
        .border-2.border-black,
        .border-2.border-slate-900 {
            border-width: 1px !important;
            border-color: #E2E8F0 !important;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px -1px rgba(0, 0, 0, 0.05) !important;
        }

        /* Form Inputs & Controls */
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"],
        input[type="tel"],
        input[type="date"],
        input[type="time"],
        input[type="file"],
        select,
        textarea {
            border: 1px solid #CBD5E1 !important;
            background-color: #FFFFFF !important;
            color: #0F172A !important;
            font-size: 0.875rem !important;
            border-radius: 0.5rem !important;
            transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: #1E2B6E !important;
            outline: none !important;
            box-shadow: 0 0 0 3px rgba(30, 43, 110, 0.12) !important;
        }

        /* Clean Modern Tables */
        table {
            border-collapse: separate !important;
            border-spacing: 0 !important;
        }

        table thead tr {
            background-color: #F8FAFC !important;
            border-bottom: 1px solid #E2E8F0 !important;
        }

        table thead th {
            color: #475569 !important;
            font-weight: 600 !important;
            font-size: 0.75rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
        }

        table tbody td {
            border-bottom: 1px solid #F1F5F9 !important;
            color: #334155 !important;
        }

        /* Smooth Touch Scrolling */
        .overflow-x-auto {
            -webkit-overflow-scrolling: touch;
        }

        /* Auth Container */
        .max-w-4xl.bg-white {
            border: 1px solid #E2E8F0 !important;
            border-radius: 1rem !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.06), 0 8px 10px -6px rgba(0, 0, 0, 0.04) !important;
        }
    </style>
    @stack('styles')
</head>
<body class="h-full font-sans text-slate-800 antialiased bg-lsp-canvas" x-data="{ sidebarOpen: false }">

    <!-- Desktop Left Screen Vertical Dual Bar (Hidden on Mobile) -->
    <div class="hidden md:flex fixed left-0 top-0 bottom-0 w-1.5 z-50 pointer-events-none">
        <div class="w-0.75 bg-lsp-navy h-full"></div>
        <div class="w-0.75 bg-lsp-red h-full"></div>
    </div>

    @guest
        <!-- Guest Content (Login, Register) -->
        <main class="min-h-screen flex flex-col justify-center items-center p-3 sm:p-6 md:pl-8">
            @yield('content')
            {{ $slot ?? '' }}
        </main>
    @else
        <!-- Authenticated Layout with Sidebar -->
        <div class="min-h-screen flex flex-col">

            <!-- Mobile Sidebar Backdrop -->
            <div x-show="sidebarOpen" 
                 x-cloak
                 @click="sidebarOpen = false"
                 class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-xs md:hidden transition-opacity"></div>

            <!-- Sidebar Navigation -->
            <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                   class="fixed inset-y-0 left-0 md:left-1.5 z-50 md:z-30 w-72 md:w-64 bg-white border-r border-slate-200 shadow-xl md:shadow-none flex flex-col h-screen transition-transform duration-200 ease-in-out md:translate-x-0">
                
                <!-- Sidebar Top Section (Fixed Header) -->
                <div class="shrink-0">
                    <!-- Top Horizontal Dual Stripe -->
                    <div class="w-full flex h-1">
                        <div class="w-2/3 bg-lsp-navy"></div>
                        <div class="w-1/3 bg-lsp-red"></div>
                    </div>

                    <!-- Brand / Logo with Mobile Close Button -->
                    <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <img src="{{ asset('images/logo-lsp-wisindo.png') }}" 
                                 alt="Logo LSP" 
                                 class="h-10 sm:h-12 w-auto object-contain flex-shrink-0">
                            <div class="flex flex-col">
                                <span class="font-sans font-bold text-base leading-tight text-lsp-title tracking-tight">
                                    LSP PrO Indo
                                </span>
                                <span class="text-[9px] font-bold text-lsp-red uppercase tracking-wider mt-0.5">
                                    PRAKTISI OTOMOTIF
                                </span>
                                <span class="text-[9px] text-slate-400 font-medium">
                                    Lisensi BNSP
                                </span>
                            </div>
                        </div>

                        <!-- Mobile Close Button -->
                        <button @click="sidebarOpen = false" 
                                type="button" 
                                aria-label="Tutup Menu"
                                class="md:hidden p-1.5 -mr-1 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 focus:outline-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Navigation Links (Scrollable Nav Area) -->
                <nav class="flex-1 overflow-y-auto min-h-0 p-3 space-y-1 text-xs overscroll-contain">
                        
                        @if(auth()->user()->isRole('ASESI'))
                            <div class="px-3 pt-3 pb-1 text-[10px] font-extrabold text-lsp-muted uppercase tracking-wider">
                                Menu Asesi
                            </div>
                            <a href="{{ route('asesi.dashboard') }}" 
                               class="flex items-center space-x-3 px-3 py-2.5 rounded text-xs font-semibold transition {{ request()->routeIs('asesi.dashboard') ? 'bg-lsp-input-bg text-lsp-navy border-l-4 border-lsp-navy font-bold' : 'text-slate-600 hover:text-lsp-navy hover:bg-slate-50' }}">
                                <svg class="w-4 h-4 text-lsp-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                <span>Dashboard Asesi</span>
                            </a>
                            <a href="{{ route('asesi.profile.edit') }}" 
                               class="flex items-center space-x-3 px-3 py-2.5 rounded text-xs font-semibold transition {{ request()->routeIs('asesi.profile.*') ? 'bg-lsp-input-bg text-lsp-navy border-l-4 border-lsp-navy font-bold' : 'text-slate-600 hover:text-lsp-navy hover:bg-slate-50' }}">
                                <svg class="w-4 h-4 text-lsp-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7 7z"/>
                                </svg>
                                <span>Biodata & TTD Digital</span>
                            </a>
                            <a href="{{ route('asesi.jadwal.index') }}" 
                               class="flex items-center space-x-3 px-3 py-2.5 rounded text-xs font-semibold transition {{ request()->routeIs('asesi.jadwal.*') || request()->routeIs('asesi.assessment.*') ? 'bg-lsp-input-bg text-lsp-navy border-l-4 border-lsp-navy font-bold' : 'text-slate-600 hover:text-lsp-navy hover:bg-slate-50' }}">
                                <svg class="w-4 h-4 text-lsp-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>Jadwal Asesmen Uji</span>
                            </a>

                        @elseif(auth()->user()->isRole('ASESOR'))
                            <div class="px-3 pt-3 pb-1 text-[10px] font-extrabold text-lsp-muted uppercase tracking-wider">
                                Menu Asesor
                            </div>
                            <a href="{{ route('asesor.jadwal.index') }}" 
                               class="flex items-center space-x-3 px-3 py-2.5 rounded text-xs font-semibold transition {{ request()->routeIs('asesor.jadwal.*') || request()->routeIs('asesor.schedule.*') || request()->routeIs('asesor.assessment.*') || request()->routeIs('asesor.review-apl*') || request()->routeIs('asesor.ruang-uji*') || request()->routeIs('asesor.ak*') ? 'bg-lsp-input-bg text-lsp-navy border-l-4 border-lsp-navy font-bold' : 'text-slate-600 hover:text-lsp-navy hover:bg-slate-50' }}">
                                <svg class="w-4 h-4 text-lsp-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                                <span>Jadwal Penugasan Uji</span>
                            </a>
                            <a href="{{ route('asesor.profile.edit') }}" 
                               class="flex items-center space-x-3 px-3 py-2.5 rounded text-xs font-semibold transition {{ request()->routeIs('asesor.profile.*') ? 'bg-lsp-input-bg text-lsp-navy border-l-4 border-lsp-navy font-bold' : 'text-slate-600 hover:text-lsp-navy hover:bg-slate-50' }}">
                                <svg class="w-4 h-4 text-lsp-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7 7z"/>
                                </svg>
                                <span>Biodata & TTD Asesor</span>
                            </a>

                        @elseif(auth()->user()->isAdmin())
                            <div class="px-3 pt-3 pb-1 text-[10px] font-extrabold text-lsp-muted uppercase tracking-wider">
                                Manajemen Asesmen
                            </div>
                            <a href="{{ route('admin.dashboard') }}" 
                               class="flex items-center space-x-3 px-3 py-2.5 rounded text-xs font-semibold transition {{ request()->routeIs('admin.dashboard') ? 'bg-lsp-input-bg text-lsp-navy border-l-4 border-lsp-navy font-bold' : 'text-slate-600 hover:text-lsp-navy hover:bg-slate-50' }}">
                                <svg class="w-4 h-4 text-lsp-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                </svg>
                                <span>Dashboard Admin</span>
                            </a>
                            <a href="{{ route('admin.jadwal.index') }}" 
                               class="flex items-center space-x-3 px-3 py-2.5 rounded text-xs font-semibold transition {{ request()->routeIs('admin.jadwal.*') ? 'bg-lsp-input-bg text-lsp-navy border-l-4 border-lsp-navy font-bold' : 'text-slate-600 hover:text-lsp-navy hover:bg-slate-50' }}">
                                <svg class="w-4 h-4 text-lsp-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>Kelola Jadwal Asesmen</span>
                            </a>
                            <a href="{{ route('admin.verifications.index') }}" 
                               class="flex items-center space-x-3 px-3 py-2.5 rounded text-xs font-semibold transition {{ request()->routeIs('admin.verifications.*') ? 'bg-lsp-input-bg text-lsp-navy border-l-4 border-lsp-navy font-bold' : 'text-slate-600 hover:text-lsp-navy hover:bg-slate-50' }}">
                                <svg class="w-4 h-4 text-lsp-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                                <span>Verifikasi Berkas APL</span>
                            </a>
                            <a href="{{ route('admin.pleno.index') }}" 
                               class="flex items-center space-x-3 px-3 py-2.5 rounded text-xs font-semibold transition {{ request()->routeIs('admin.pleno.*') ? 'bg-lsp-input-bg text-lsp-navy border-l-4 border-lsp-navy font-bold' : 'text-slate-600 hover:text-lsp-navy hover:bg-slate-50' }}">
                                <svg class="w-4 h-4 text-lsp-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <span>Rapat Pleno Komite</span>
                            </a>
                            @if(auth()->user()->isRole('SUPER_ADMIN') || auth()->user()->isRole('admin'))
                                <div class="px-3 pt-4 pb-1 text-[10px] font-extrabold text-lsp-muted uppercase tracking-wider">
                                    Master Data & Pengaturan
                                </div>
                                <a href="{{ route('admin.users.index') }}" 
                                   class="flex items-center space-x-3 px-3 py-2 rounded text-xs font-medium transition {{ request()->routeIs('admin.users.*') ? 'bg-lsp-input-bg text-lsp-navy font-bold border-l-4 border-lsp-navy' : 'text-slate-600 hover:text-lsp-navy hover:bg-slate-50' }}">
                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    <span>Master Pengguna</span>
                                </a>
                                <a href="{{ route('admin.schemes.index') }}" 
                                   class="flex items-center space-x-3 px-3 py-2 rounded text-xs font-medium transition {{ request()->routeIs('admin.schemes.*') ? 'bg-lsp-input-bg text-lsp-navy font-bold border-l-4 border-lsp-navy' : 'text-slate-600 hover:text-lsp-navy hover:bg-slate-50' }}">
                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                    <span>Master Skema Sertifikasi</span>
                                </a>
                                <a href="{{ route('admin.question-banks.index') }}" 
                                   class="flex items-center space-x-3 px-3 py-2 rounded text-xs font-medium transition {{ request()->routeIs('admin.question-banks.*') ? 'bg-lsp-input-bg text-lsp-navy font-bold border-l-4 border-lsp-navy' : 'text-slate-600 hover:text-lsp-navy hover:bg-slate-50' }}">
                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span>Bank Soal CBT (IA05)</span>
                                </a>
                                <a href="{{ route('admin.tuks.index') }}" 
                                   class="flex items-center space-x-3 px-3 py-2 rounded text-xs font-medium transition {{ request()->routeIs('admin.tuks.*') ? 'bg-lsp-input-bg text-lsp-navy font-bold border-l-4 border-lsp-navy' : 'text-slate-600 hover:text-lsp-navy hover:bg-slate-50' }}">
                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <span>Tempat Uji Kompetensi (TUK)</span>
                                </a>
                                <a href="{{ route('admin.lsp-profile.edit') }}" 
                                   class="flex items-center space-x-3 px-3 py-2 rounded text-xs font-medium transition {{ request()->routeIs('admin.lsp-profile.*') ? 'bg-lsp-input-bg text-lsp-navy font-bold border-l-4 border-lsp-navy' : 'text-slate-600 hover:text-lsp-navy hover:bg-slate-50' }}">
                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span>Profil Lembaga LSP</span>
                                </a>

                            @endif
                        @endif

                </nav>

                <!-- Sidebar Bottom: User Profile & Logout (Fixed Bottom) -->
                <div class="shrink-0 p-3 border-t border-slate-200 bg-slate-50/70">
                    <div class="flex items-center justify-between p-1.5">
                        <a href="{{ route('profile.edit') }}" 
                           title="Buka Pengaturan Profil & Akun" 
                           class="flex items-center space-x-2.5 overflow-hidden group flex-1 hover:bg-slate-100/80 p-1.5 rounded-lg transition">
                            <div class="w-8 h-8 rounded-lg bg-lsp-navy text-white font-bold text-xs flex items-center justify-center flex-shrink-0 shadow-xs group-hover:bg-lsp-navy-dark transition">
                                {{ strtoupper(substr(auth()->user()->full_name, 0, 2)) }}
                            </div>
                            <div class="truncate text-left">
                                <p class="text-xs font-bold text-slate-800 truncate leading-tight group-hover:text-lsp-navy transition">
                                    {{ auth()->user()->full_name }}
                                </p>
                                <span class="inline-block px-1.5 py-0.5 text-[9px] font-semibold rounded bg-slate-200/80 text-slate-600 uppercase tracking-wider mt-0.5">
                                    {{ auth()->user()->role_label }}
                                </span>
                            </div>
                        </a>

                        <div class="flex items-center space-x-1 shrink-0 ml-1">
                            <!-- Link Pengaturan Profil -->
                            <a href="{{ route('profile.edit') }}" 
                               title="Pengaturan Profil" 
                               class="p-1.5 text-slate-400 hover:text-lsp-navy hover:bg-slate-100 rounded-lg transition {{ request()->routeIs('profile.edit') ? 'text-lsp-navy bg-slate-100' : '' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </a>

                            <!-- Logout Form -->
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        title="Keluar (Logout)" 
                                        class="p-1.5 text-slate-400 hover:text-lsp-red hover:bg-red-50 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </aside>

            <!-- Main Content Area (Shifted for sidebar) -->
            <div class="md:pl-64 flex flex-col flex-1 min-w-0">
                
                <!-- Top Horizontal Header Bar -->
                <header class="bg-white/95 backdrop-blur-sm border-b border-slate-200 sticky top-0 z-30">
                    <!-- Top dual stripe above the header bar -->
                    <div class="w-full flex h-1">
                        <div class="w-2/3 bg-lsp-navy"></div>
                        <div class="w-1/3 bg-lsp-red"></div>
                    </div>

                    <div class="px-3.5 sm:px-6 lg:px-8 py-3 flex items-center justify-between gap-3">
                        <!-- Left: Mobile Toggle & Page Title -->
                        <div class="flex items-center space-x-2.5 sm:space-x-3 min-w-0">
                            <button @click="sidebarOpen = !sidebarOpen" 
                                    type="button" 
                                    aria-label="Buka Menu"
                                    class="p-2 -ml-1 rounded-lg text-slate-600 hover:text-lsp-navy hover:bg-slate-100 md:hidden focus:outline-none shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                            </button>
                            <h1 class="font-sans font-bold text-base sm:text-lg text-lsp-title tracking-tight truncate">
                                {{ $title ?? 'Sistem Asesmen Digital' }}
                            </h1>
                        </div>

                        <!-- Right: Portal & Impersonation Badge -->
                        <div class="flex items-center space-x-2.5 sm:space-x-3 shrink-0">
                            <div class="hidden sm:flex items-center space-x-2 text-xs text-slate-500 font-medium">
                                <svg class="w-3.5 h-3.5 text-lsp-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>{{ now()->isoFormat('D MMMM Y') }}</span>
                            </div>

                            <div class="h-4 w-px bg-slate-200 hidden sm:block"></div>

                            <!-- User Profile Shortcut in Header -->
                            <a href="{{ route('profile.edit') }}" 
                               class="flex items-center space-x-2 p-1.5 sm:px-2.5 sm:py-1 rounded-lg border border-slate-200 hover:border-slate-300 hover:bg-slate-50 transition text-xs font-semibold text-slate-700 shadow-xs"
                               title="Buka Pengaturan Profil & Kata Sandi">
                                <div class="w-6 h-6 rounded bg-lsp-navy text-white text-[11px] font-bold flex items-center justify-center">
                                    {{ strtoupper(substr(auth()->user()->full_name, 0, 1)) }}
                                </div>
                                <span class="hidden md:inline truncate max-w-[130px]">{{ auth()->user()->name }}</span>
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </header>

                <!-- Page Content Body -->
                <main class="flex-1 p-3.5 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
                    
                    {{-- Flash Messages --}}
                    @if(session('success'))
                        <div class="mb-6 rounded bg-emerald-50 border-l-4 border-emerald-500 p-4 text-xs text-emerald-800 flex items-start space-x-3 shadow-sm">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="flex-1 font-medium">{{ session('success') }}</div>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="mb-6 rounded bg-amber-50 border-l-4 border-amber-500 p-4 text-xs text-amber-900 flex items-start space-x-3 shadow-sm">
                            <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <div class="flex-1 font-medium">{{ session('warning') }}</div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-6 rounded bg-rose-50 border-l-4 border-lsp-red p-4 text-xs text-rose-800 flex items-start space-x-3 shadow-sm">
                            <svg class="w-4 h-4 text-lsp-red shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="flex-1 font-medium">{{ session('error') }}</div>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-6 rounded bg-rose-50 border-l-4 border-lsp-red p-4 text-xs text-rose-800 shadow-sm">
                            <div class="flex items-center space-x-2 font-bold mb-1">
                                <svg class="w-4 h-4 text-lsp-red shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Terjadi kesalahan validasi:</span>
                            </div>
                            <ul class="list-disc list-inside space-y-0.5 text-rose-700 ml-5 text-xs">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('content')
            {{ $slot ?? '' }}
                </main>

                <!-- Footer -->
                <footer class="bg-white border-t border-slate-200 py-4 mt-auto">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-[11px] text-slate-500">
                        <p>&copy; {{ date('Y') }} <strong>LSP PrO Indo</strong> (Lembaga Sertifikasi Profesi Praktisi Otomotif Indonesia). Lisensi Resmi BNSP.</p>
                    </div>
                </footer>

            </div>
        </div>
    @endguest

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Strict numeric-only input restriction for integer fields (kode pos, nik, phone, etc.)
            document.addEventListener('keydown', function (e) {
                const el = e.target;
                if (!el || el.tagName !== 'INPUT') return;
                if (el.matches('.numeric-only, [data-numeric-only], [inputmode="numeric"]')) {
                    if (e.ctrlKey || e.metaKey || (e.key && e.key.length > 1)) {
                        return;
                    }
                    if (!/^[0-9]$/.test(e.key)) {
                        e.preventDefault();
                    }
                }
            });

            document.addEventListener('input', function (e) {
                const el = e.target;
                if (!el || el.tagName !== 'INPUT') return;
                if (el.matches('.numeric-only, [data-numeric-only], [inputmode="numeric"]')) {
                    el.value = el.value.replace(/\D/g, '');
                }
            });

            // Anti-double-click & loading spinner protection for forms
            document.addEventListener('submit', function (e) {
                const form = e.target;
                if (!form || form.tagName !== 'FORM') return;

                if (form.hasAttribute('data-no-disable')) return;
                if (typeof form.checkValidity === 'function' && !form.checkValidity()) return;

                if (form.dataset.submitting === 'true') {
                    e.preventDefault();
                    return false;
                }

                form.dataset.submitting = 'true';

                const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
                submitButtons.forEach(function (btn) {
                    if (btn.hasAttribute('data-no-disable')) return;

                    if (!btn.dataset.originalHtml) {
                        btn.dataset.originalHtml = btn.innerHTML;
                    }

                    setTimeout(function () {
                        btn.disabled = true;
                        btn.classList.add('opacity-75', 'cursor-not-allowed');

                        if (btn.tagName === 'BUTTON') {
                            btn.innerHTML = `
                                <svg class="animate-spin -ml-1 mr-2 h-3.5 w-3.5 text-current inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Memproses...</span>
                            `;
                        } else if (btn.tagName === 'INPUT') {
                            btn.value = 'Memproses...';
                        }
                    }, 10);
                });
            });

            // Restore buttons on pageshow (e.g. browser back button from bfcache)
            window.addEventListener('pageshow', function () {
                document.querySelectorAll('form').forEach(function (form) {
                    delete form.dataset.submitting;
                    const buttons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
                    buttons.forEach(function (btn) {
                        btn.disabled = false;
                        btn.classList.remove('opacity-75', 'cursor-not-allowed');
                        if (btn.dataset.originalHtml && btn.tagName === 'BUTTON') {
                            btn.innerHTML = btn.dataset.originalHtml;
                        }
                    });
                });
            });
        });
    </script>

    @stack('scripts')
</body>
</html>