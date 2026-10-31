<!DOCTYPE html>
<html lang="id" class="h-full bg-zinc-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard' }} — {{ config('app.name', 'Sistem Sertifikasi LSP') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Scripts & Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-zinc-800 bg-zinc-100 selection:bg-blue-600 selection:text-white" x-data="{ sidebarOpen: false }">

    <div class="min-h-full flex">

        <!-- Mobile Sidebar Backdrop -->
        <div
            x-show="sidebarOpen"
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-40 bg-zinc-900/50 backdrop-blur-sm lg:hidden"
            @click="sidebarOpen = false"
        ></div>

        <!-- Sidebar Container -->
        <aside
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-zinc-900 text-zinc-300 flex flex-col transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 shrink-0"
        >
            <!-- Sidebar Header / Logo -->
            <div class="h-16 flex items-center justify-between px-6 bg-zinc-950 border-b border-zinc-800">
                <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white font-bold text-sm shadow">
                        LSP
                    </div>
                    <div class="flex flex-col">
                        <span class="font-bold text-white text-xs tracking-wider uppercase">LSP SYSTEM</span>
                        <span class="text-[10px] text-zinc-400 font-medium tracking-tight">Sertifikasi Profesi</span>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-zinc-400 hover:text-white p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Role Indicator Banner -->
            <div class="px-6 py-3 bg-zinc-800/40 border-b border-zinc-800/80 flex items-center justify-between text-xs">
                <span class="text-zinc-400 font-medium">Peran Aktif:</span>
                <span class="font-semibold text-xs px-2 py-0.5 rounded-full {{ auth()->user()->role->badgeClasses() }}">
                    {{ auth()->user()->role->label() }}
                </span>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">

                {{-- ADMIN NAVIGATION --}}
                @if (auth()->user()->isAdmin())
                    <div class="px-3 pb-2 text-[10px] font-semibold tracking-wider text-zinc-400 uppercase">Menu Operasional</div>
                    
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 text-xs font-medium rounded-lg transition {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white' : 'hover:bg-zinc-800 text-zinc-300 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>Dashboard LSP</span>
                    </a>

                    <div class="pt-4 px-3 pb-2 text-[10px] font-semibold tracking-wider text-zinc-400 uppercase">Master Data Engine</div>
                    
                    <a href="{{ route('admin.lsp-profile.edit') }}" class="flex items-center gap-3 px-3 py-2 text-xs font-medium rounded-lg transition {{ request()->routeIs('admin.lsp-profile.*') ? 'bg-blue-600 text-white' : 'hover:bg-zinc-800 text-zinc-300 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span>Profil Lembaga LSP</span>
                    </a>

                    <a href="{{ route('admin.standards.index') }}" class="flex items-center gap-3 px-3 py-2 text-xs font-medium rounded-lg transition {{ request()->routeIs('admin.standards.*') ? 'bg-blue-600 text-white' : 'hover:bg-zinc-800 text-zinc-300 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Standar Acuan (SKKNI)</span>
                    </a>

                    <a href="{{ route('admin.schemes.index') }}" class="flex items-center gap-3 px-3 py-2 text-xs font-medium rounded-lg transition {{ request()->routeIs('admin.schemes.*') ? 'bg-blue-600 text-white' : 'hover:bg-zinc-800 text-zinc-300 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span>Skema & Unit Kompetensi</span>
                    </a>

                    <a href="{{ route('admin.tuks.index') }}" class="flex items-center gap-3 px-3 py-2 text-xs font-medium rounded-lg transition {{ request()->routeIs('admin.tuks.*') ? 'bg-blue-600 text-white' : 'hover:bg-zinc-800 text-zinc-300 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Master TUK (Lokasi Uji)</span>
                    </a>

                    <a href="{{ route('admin.asesors.index') }}" class="flex items-center gap-3 px-3 py-2 text-xs font-medium rounded-lg transition {{ request()->routeIs('admin.asesors.*') ? 'bg-blue-600 text-white' : 'hover:bg-zinc-800 text-zinc-300 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span>Master Asesor Kompetensi</span>
                    </a>

                    <div class="pt-4 px-3 pb-2 text-[10px] font-semibold tracking-wider text-zinc-400 uppercase">Jadwal & Asesmen (Fase 3)</div>
                    
                    <a href="{{ route('admin.events.index') }}" class="flex items-center gap-3 px-3 py-2 text-xs font-medium rounded-lg transition {{ request()->routeIs('admin.events.*') ? 'bg-blue-600 text-white' : 'hover:bg-zinc-800 text-zinc-300 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Event Sertifikasi</span>
                    </a>

                    <a href="{{ route('admin.schedules.index') }}" class="flex items-center gap-3 px-3 py-2 text-xs font-medium rounded-lg transition {{ request()->routeIs('admin.schedules.*') ? 'bg-blue-600 text-white' : 'hover:bg-zinc-800 text-zinc-300 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Jadwal Uji Kompetensi</span>
                    </a>

                    <div class="pt-4 px-3 pb-2 text-[10px] font-semibold tracking-wider text-zinc-400 uppercase">Verifikasi & Pendaftaran (Fase 4)</div>
                    
                    <a href="{{ route('admin.verifications.index') }}" class="flex items-center justify-between px-3 py-2 text-xs font-medium rounded-lg transition {{ request()->routeIs('admin.verifications.*') ? 'bg-blue-600 text-white' : 'hover:bg-zinc-800 text-zinc-300 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Verifikasi APL-01</span>
                        </div>
                        @php
                            $pendingVerificationCount = \App\Models\AssessmentApplication::where('status', \App\Enums\ApplicationStatus::Diajukan)->count();
                        @endphp
                        @if ($pendingVerificationCount > 0)
                            <span class="px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-amber-500 text-white">
                                {{ $pendingVerificationCount }}
                            </span>
                        @endif
                    </a>

                    <div class="pt-4 px-3 pb-2 text-[10px] font-semibold tracking-wider text-zinc-400 uppercase">Kelulusan & Pleno (Fase 7)</div>
                    
                    <a href="{{ route('admin.pleno.index') }}" class="flex items-center gap-3 px-3 py-2 text-xs font-medium rounded-lg transition {{ request()->routeIs('admin.pleno.*') ? 'bg-blue-600 text-white' : 'hover:bg-zinc-800 text-zinc-300 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span>Sidang Pleno & Kelulusan</span>
                    </a>

                    <div class="pt-4 px-3 pb-2 text-[10px] font-semibold tracking-wider text-zinc-400 uppercase">Pelaporan & Layanan (Fase 8)</div>
                    
                    <a href="{{ route('admin.exports.bnsp.index') }}" class="flex items-center gap-3 px-3 py-2 text-xs font-medium rounded-lg transition {{ request()->routeIs('admin.exports.*') ? 'bg-blue-600 text-white' : 'hover:bg-zinc-800 text-zinc-300 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Ekspor Sisfo BNSP</span>
                    </a>

                    <a href="{{ route('admin.complaints.index') }}" class="flex items-center justify-between px-3 py-2 text-xs font-medium rounded-lg transition {{ request()->routeIs('admin.complaints.*') ? 'bg-blue-600 text-white' : 'hover:bg-zinc-800 text-zinc-300 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                            <span>Layanan Pengaduan</span>
                        </div>
                        @php $pendingComplaint = \App\Models\Complaint::where('status', 'diproses')->count(); @endphp
                        @if ($pendingComplaint > 0)
                            <span class="px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-blue-500 text-white">
                                {{ $pendingComplaint }}
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('admin.surveillance.index') }}" class="flex items-center gap-3 px-3 py-2 text-xs font-medium rounded-lg transition {{ request()->routeIs('admin.surveillance.*') ? 'bg-blue-600 text-white' : 'hover:bg-zinc-800 text-zinc-300 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        <span>Surveilans Pemegang</span>
                    </a>
                @endif

                {{-- ASESOR NAVIGATION --}}
                @if (auth()->user()->isAsesor())
                    <div class="px-3 pb-2 text-[10px] font-semibold tracking-wider text-zinc-400 uppercase">Portal Asesor</div>

                    <a href="{{ route('asesor.dashboard') }}" class="flex items-center gap-3 px-3 py-2 text-xs font-medium rounded-lg transition {{ request()->routeIs('asesor.dashboard') ? 'bg-blue-600 text-white' : 'hover:bg-zinc-800 text-zinc-300 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>Dashboard Asesor</span>
                    </a>

                    <a href="{{ route('asesor.apl02.index') }}" class="flex items-center justify-between px-3 py-2 text-xs font-medium rounded-lg transition {{ request()->routeIs('asesor.apl02.*') ? 'bg-blue-600 text-white' : 'hover:bg-zinc-800 text-zinc-300 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <span>Peninjauan APL-02</span>
                        </div>
                    </a>
                @endif

                {{-- ASESI NAVIGATION --}}
                @if (auth()->user()->isAsesi())
                    <div class="px-3 pb-2 text-[10px] font-semibold tracking-wider text-zinc-400 uppercase">Portal Mandiri Asesi</div>

                    <a href="{{ route('asesi.dashboard') }}" class="flex items-center gap-3 px-3 py-2 text-xs font-medium rounded-lg transition {{ request()->routeIs('asesi.dashboard') ? 'bg-blue-600 text-white' : 'hover:bg-zinc-800 text-zinc-300 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>Dashboard Asesi</span>
                    </a>

                    <a href="{{ route('asesi.applications.create') }}" class="flex items-center gap-3 px-3 py-2 text-xs font-medium rounded-lg transition {{ request()->routeIs('asesi.applications.create') ? 'bg-blue-600 text-white' : 'hover:bg-zinc-800 text-zinc-300 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Daftar Sertifikasi Baru</span>
                    </a>

                    <a href="{{ route('asesi.applications.index') }}" class="flex items-center gap-3 px-3 py-2 text-xs font-medium rounded-lg transition {{ request()->routeIs('asesi.applications.index') || request()->routeIs('asesi.applications.show') || request()->routeIs('asesi.applications.edit') ? 'bg-blue-600 text-white' : 'hover:bg-zinc-800 text-zinc-300 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Permohonan Saya (APL-01)</span>
                    </a>
                @endif

                <div class="pt-4 px-3 pb-2 text-[10px] font-semibold tracking-wider text-zinc-400 uppercase">Akun Pengguna</div>
                
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2 text-xs font-medium rounded-lg transition {{ request()->routeIs('profile.edit') ? 'bg-zinc-800 text-white' : 'hover:bg-zinc-800 text-zinc-300 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Profil & Kata Sandi</span>
                </a>

            </nav>

            <!-- Sidebar Footer / Logout -->
            <div class="p-4 border-t border-zinc-800 bg-zinc-950 flex items-center justify-between">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-semibold text-xs shrink-0">
                        {{ auth()->user()->initials }}
                    </div>
                    <div class="flex flex-col truncate">
                        <span class="text-xs font-semibold text-white truncate">{{ auth()->user()->name }}</span>
                        <span class="text-[11px] text-zinc-400 truncate">{{ auth()->user()->email }}</span>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Keluar" class="text-zinc-400 hover:text-rose-400 p-1.5 rounded-lg hover:bg-zinc-800 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Workspace Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">

            <!-- Top Header Bar -->
            <header class="h-16 bg-white border-b border-zinc-200 flex items-center justify-between px-4 sm:px-6 lg:px-8 sticky top-0 z-30">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="lg:hidden text-zinc-600 hover:text-zinc-900 p-1.5 rounded-lg hover:bg-zinc-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h1 class="text-base sm:text-lg font-bold text-zinc-900">{{ $header ?? $title ?? 'Sistem Sertifikasi' }}</h1>
                </div>

                <div class="flex items-center gap-4">
                    <a href="{{ url('/') }}" target="_blank" class="hidden sm:inline-flex items-center gap-1.5 text-xs font-medium text-zinc-500 hover:text-zinc-800 transition">
                        <span>Lihat Website</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>

                    <!-- Profile Dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center gap-2.5 p-1 rounded-lg hover:bg-zinc-50 transition">
                            <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-semibold text-xs shadow-sm">
                                {{ auth()->user()->initials }}
                            </div>
                            <span class="hidden md:inline-block text-xs font-semibold text-zinc-800">{{ auth()->user()->name }}</span>
                            <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div
                            x-show="open"
                            @click.away="open = false"
                            x-transition
                            class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-zinc-200 py-1.5 z-50 text-xs"
                            style="display: none;"
                        >
                            <div class="px-4 py-2 border-b border-zinc-100">
                                <p class="font-semibold text-zinc-900 truncate">{{ auth()->user()->name }}</p>
                                <p class="text-zinc-500 text-[11px] truncate">{{ auth()->user()->email }}</p>
                            </div>

                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-zinc-700 hover:bg-zinc-50 hover:text-zinc-900 transition">
                                <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span>Pengaturan Profil</span>
                            </a>

                            <div class="border-t border-zinc-100 my-1"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-rose-600 hover:bg-rose-50 transition text-left">
                                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    <span>Keluar dari Akun</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content Slot -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                {{ $slot }}
            </main>

            <!-- System Flash Toast Component -->
            <x-toast />

        </div>
    </div>

</body>
</html>
