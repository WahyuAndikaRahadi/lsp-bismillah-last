@extends('layouts.app', ['title' => 'Detail Pengguna - ' . $user->full_name])

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('admin.users.index') }}" class="text-xs text-brand-600 hover:text-brand-700 font-semibold">&larr; Kembali ke Kelola Pengguna</a>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">{{ $user->full_name }}</h1>
            <div class="flex items-center space-x-3 text-xs text-slate-500 mt-1">
                <span class="font-mono">{{ $user->email }}</span>
                <span>&bull;</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">{{ $user->role }}</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $user->status === 'ACTIVE' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">{{ $user->status }}</span>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.users.edit', $user->id) }}" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg text-xs font-bold shadow-sm transition">
                Edit Akun
            </a>
        </div>
    </div>

    {{-- Details Card --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4 text-xs">
        <h2 class="text-base font-bold text-slate-900 mb-3">Informasi Profil & Identitas</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <span class="text-slate-500 block">NIK:</span>
                <span class="font-semibold text-slate-900 font-mono text-sm">{{ $user->profile?->nik ?? '-' }}</span>
            </div>
            <div>
                <span class="text-slate-500 block">No. Telepon / HP:</span>
                <span class="font-semibold text-slate-900 text-sm">{{ $user->phone ?? $user->profile?->phone ?? '-' }}</span>
            </div>
            <div>
                <span class="text-slate-500 block">Tempat, Tanggal Lahir:</span>
                <span class="font-semibold text-slate-900">
                    {{ $user->profile?->birth_place ?? '-' }}, 
                    {{ $user->profile?->birth_date ? \Carbon\Carbon::parse($user->profile->birth_date)->translatedFormat('d F Y') : '-' }}
                </span>
            </div>
            <div>
                <span class="text-slate-500 block">Jenis Kelamin:</span>
                <span class="font-semibold text-slate-900">{{ $user->profile?->gender === 'L' ? 'Laki-laki' : ($user->profile?->gender === 'P' ? 'Perempuan' : '-') }}</span>
            </div>
            @if($user->isRole('ASESOR'))
                <div>
                    <span class="text-slate-500 block">No. Registrasi Asesor (MET):</span>
                    <span class="font-bold text-brand-700 font-mono text-sm">{{ $user->profile?->no_reg_asesor ?? $user->assessor_registration_number ?? '-' }}</span>
                </div>
            @endif
            <div>
                <span class="text-slate-500 block">Terdaftar Sejak:</span>
                <span class="font-semibold text-slate-900">{{ $user->created_at?->translatedFormat('d F Y, H:i') ?? '-' }}</span>
            </div>
        </div>

        @if($user->profile?->address)
            <div class="pt-3 border-t border-slate-100">
                <span class="text-slate-500 block">Alamat Domisili:</span>
                <p class="font-medium text-slate-800 mt-0.5 leading-relaxed">{{ $user->profile->address }}</p>
            </div>
        @endif
    </div>
</div>
@endsection
