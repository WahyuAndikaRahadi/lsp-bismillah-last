@extends('layouts.app', ['title' => 'Edit Pengguna - ' . $user->full_name])

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <div>
        <a href="{{ route('admin.users.index') }}" class="text-xs text-brand-600 hover:text-brand-700 font-semibold">&larr; Kembali ke Kelola Pengguna</a>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">Edit Pengguna</h1>
        <p class="text-sm text-slate-500">Perbarui profil dan wewenang akun <span class="font-bold text-slate-800">{{ $user->full_name }}</span></p>
    </div>

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4" x-data="{ role: '{{ old('role', $user->role) }}' }">
            <h2 class="text-base font-bold text-slate-900">Data Akun Pengguna</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" name="full_name" required value="{{ old('full_name', $user->full_name) }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    @error('full_name')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Email <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" required value="{{ old('email', $user->email) }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    @error('email')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nomor Telepon / WhatsApp</label>
                    <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" inputmode="numeric" maxlength="15" placeholder="Contoh: 081234567890" class="numeric-only w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    @error('phone')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Password Baru (Opsional)</label>
                    <input type="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah password" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    @error('password')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Peran / Role <span class="text-rose-500">*</span></label>
                    <select name="role" x-model="role" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none bg-white">
                        <option value="ASESI">Asesi (Peserta Uji)</option>
                        <option value="ASESOR">Asesor Kompetensi</option>
                        <option value="ADMIN_LSP">Admin LSP</option>
                        <option value="SUPER_ADMIN">Super Admin</option>
                        <option value="KOMITE_TEKNIS">Komite Teknis</option>
                        <option value="TUK_ADMIN">Admin TUK</option>
                    </select>
                    @error('role')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Status Akun <span class="text-rose-500">*</span></label>
                    <select name="status" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none bg-white">
                        <option value="ACTIVE" {{ old('status', $user->status) == 'ACTIVE' ? 'selected' : '' }}>Aktif</option>
                        <option value="INACTIVE" {{ old('status', $user->status) == 'INACTIVE' ? 'selected' : '' }}>Non-Aktif</option>
                        <option value="SUSPENDED" {{ old('status', $user->status) == 'SUSPENDED' ? 'selected' : '' }}>Ditangguhkan (Suspended)</option>
                    </select>
                    @error('status')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Hidden by default, only shown when Role is set to Asesor Kompetensi --}}
            <div x-show="role === 'ASESOR'" x-cloak x-transition class="pt-2 border-t border-slate-100">
                <label class="block text-sm font-semibold text-slate-700 mb-1">No. Registrasi Asesor (No. MET BNSP)</label>
                <input type="text" name="assessor_registration_number" value="{{ old('assessor_registration_number', $user->profile?->no_reg_asesor ?? $user->assessor_registration_number) }}" placeholder="Contoh: MET.000.002498 2013" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm font-mono focus:ring-2 focus:ring-brand-500 focus:outline-none">
                <p class="text-xs text-slate-400 mt-1">Dicantumkan pada form verifikasi & dokumen MUK resmi Asesor.</p>
                @error('assessor_registration_number')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 text-sm font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition">Batal</a>
            <button type="submit" class="px-6 py-2.5 text-sm font-bold bg-brand-600 hover:bg-brand-700 text-white rounded-lg shadow-sm transition">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
