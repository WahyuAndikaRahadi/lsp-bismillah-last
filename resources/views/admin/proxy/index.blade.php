@extends('layouts.app', ['title' => 'Proxy Role / Masuk Sebagai Pengguna'])

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Proxy Role / Masuk Sebagai Pengguna</h1>
        <p class="text-sm text-slate-500">Beralih peran secara langsung untuk menguji alur sistem atau membantu pengguna</p>
    </div>

    {{-- Filter / Search Tabs --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 flex flex-col sm:flex-row items-center justify-between gap-4" x-data="{ roleFilter: 'ALL' }">
        <div class="flex items-center space-x-2">
            <button type="button" @click="roleFilter = 'ALL'" :class="roleFilter === 'ALL' ? 'bg-brand-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3 py-1.5 rounded-lg text-xs font-bold transition">
                Semua Pengguna
            </button>
            <button type="button" @click="roleFilter = 'ASESOR'" :class="roleFilter === 'ASESOR' ? 'bg-brand-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3 py-1.5 rounded-lg text-xs font-bold transition">
                Asesor Saja
            </button>
            <button type="button" @click="roleFilter = 'ASESI'" :class="roleFilter === 'ASESI' ? 'bg-brand-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3 py-1.5 rounded-lg text-xs font-bold transition">
                Asesi Saja
            </button>
        </div>

        <div class="w-full sm:w-72">
            <input type="text" placeholder="Cari nama atau email..." class="w-full px-3 py-1.5 rounded-lg border border-slate-300 text-xs focus:ring-2 focus:ring-brand-500 focus:outline-none">
        </div>
    </div>

    {{-- Users Table --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3">Nama Lengkap</th>
                        <th class="px-6 py-3">Email</th>
                        <th class="px-6 py-3">Role</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Aksi Proxy</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users ?? [] as $user)
                        @php
                            $roleBadge = match($user->role) {
                                'SUPER_ADMIN' => 'bg-purple-100 text-purple-700 border-purple-200',
                                'ADMIN_LSP' => 'bg-blue-100 text-blue-700 border-blue-200',
                                'ASESOR' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                'ASESI' => 'bg-amber-100 text-amber-700 border-amber-200',
                                default => 'bg-slate-100 text-slate-700 border-slate-200',
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $user->full_name }}</div>
                            </td>
                            <td class="px-6 py-4 text-xs font-mono text-slate-600">{{ $user->email }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full border {{ $roleBadge }}">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 text-xs font-semibold rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Aktif
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('proxy.switch', $user->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3.5 py-1.5 text-xs font-bold bg-amber-500 hover:bg-amber-600 text-white rounded-lg shadow-sm transition inline-flex items-center space-x-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                            </svg>
                                            <span>Masuk Sebagai Pengguna Ini</span>
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-400 italic">Akun Anda Saat Ini</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-500 text-xs">Tidak ada data pengguna untuk proxy.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($users) && method_exists($users, 'links'))
            <div class="p-4 border-t border-slate-200">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
