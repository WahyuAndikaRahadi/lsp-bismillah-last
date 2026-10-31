<x-app-layout title="Dashboard Admin LSP">
    <div class="space-y-6">

        <!-- Welcome Banner -->
        <div class="bg-gradient-to-r from-blue-900 to-indigo-900 rounded-2xl p-6 sm:p-8 text-white shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-500/30 text-blue-200 border border-blue-400/30 mb-2">
                    Panel Operasional Admin
                </span>
                <h2 class="text-xl sm:text-2xl font-bold tracking-tight">Selamat Datang, {{ auth()->user()->name }}</h2>
                <p class="text-xs sm:text-sm text-blue-200 mt-1 max-w-xl">
                    Sistem Sertifikasi Profesi siap dikonfigurasi. Seluruh alur sertifikasi dan data master dikontrol melalui dashboard ini.
                </p>
            </div>
            <div class="shrink-0 flex items-center gap-3">
                <span class="px-3 py-1.5 bg-white/10 rounded-xl text-xs font-medium text-white border border-white/20 backdrop-blur-sm">
                    Status Sistem: Aktif
                </span>
            </div>
        </div>

        <!-- Metric Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Card 1 -->
            <div class="bg-white p-5 rounded-2xl border border-zinc-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-medium text-zinc-500">Total Pengguna</span>
                    <p class="text-2xl font-bold text-zinc-900 mt-1">{{ $stats['total_users'] }}</p>
                    <span class="text-[11px] text-zinc-400 mt-1 block">Akun terdaftar</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="bg-white p-5 rounded-2xl border border-zinc-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-medium text-zinc-500">Asesor Terdaftar</span>
                    <p class="text-2xl font-bold text-zinc-900 mt-1">{{ $stats['total_asesor'] }}</p>
                    <span class="text-[11px] text-blue-600 font-medium mt-1 block">Memiliki lisensi MET</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="bg-white p-5 rounded-2xl border border-zinc-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-medium text-zinc-500">Asesi / Pendaftar</span>
                    <p class="text-2xl font-bold text-zinc-900 mt-1">{{ $stats['total_asesi'] }}</p>
                    <span class="text-[11px] text-emerald-600 font-medium mt-1 block">Calon & peserta uji</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="bg-white p-5 rounded-2xl border border-zinc-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-medium text-zinc-500">Staf Admin</span>
                    <p class="text-2xl font-bold text-zinc-900 mt-1">{{ $stats['total_admin'] }}</p>
                    <span class="text-[11px] text-purple-600 font-medium mt-1 block">Pengelola LSP</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Recent Registered Users Table -->
        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-zinc-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-zinc-900">Pengguna Terakhir Terdaftar</h3>
                    <p class="text-xs text-zinc-500 mt-0.5">Daftar akun yang baru dibuat dalam sistem.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-zinc-600">
                    <thead class="bg-zinc-50 text-zinc-500 uppercase font-semibold border-b border-zinc-200/80">
                        <tr>
                            <th class="px-6 py-3">Nama Pengguna</th>
                            <th class="px-6 py-3">Email & Kontak</th>
                            <th class="px-6 py-3">NIK</th>
                            <th class="px-6 py-3">Peran (Role)</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Terdaftar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse ($recentUsers as $user)
                            <tr class="hover:bg-zinc-50/80 transition">
                                <td class="px-6 py-3.5 font-medium text-zinc-900 flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-[10px]">
                                        {{ $user->initials }}
                                    </div>
                                    <span>{{ $user->name }}</span>
                                </td>
                                <td class="px-6 py-3.5">
                                    <div>{{ $user->email }}</div>
                                    <div class="text-[11px] text-zinc-400 font-mono">{{ $user->phone ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-3.5 font-mono text-[11px]">
                                    {{ $user->nik ?? '-' }}
                                </td>
                                <td class="px-6 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $user->role->badgeClasses() }}">
                                        {{ $user->role->label() }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5">
                                    @if ($user->is_active)
                                        <span class="inline-flex items-center gap-1 text-[11px] font-medium text-emerald-600">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[11px] font-medium text-rose-600">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            Non-aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-3.5 text-zinc-400 text-[11px]">
                                    {{ $user->created_at->format('d M Y H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-zinc-400">
                                    Belum ada pengguna terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
