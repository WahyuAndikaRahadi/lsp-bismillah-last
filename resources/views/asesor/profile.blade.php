@extends('layouts.app', ['title' => 'Biodata & Tanda Tangan Asesor'])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Biodata & Tanda Tangan Asesor</h1>
            <p class="text-sm text-slate-500">
                Lengkapi biodata dan atur tanda tangan digital Anda. Tanda tangan ini akan otomatis digunakan pada form AK.01, instrumen IA, dan dokumen MUK lainnya.
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-900 flex items-center gap-3 shadow-sm">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <p class="text-sm font-semibold">{{ session('success') }}</p>
        </div>
    @endif

    @php
        $p = $profile ?? new \App\Models\UserProfile();
    @endphp

    <form action="{{ route('asesor.profile.update') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Section 1: Data Asesor & Akun --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-5">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-slate-800">1. Data Registrasi Asesor</h2>
                <p class="text-xs text-slate-400">Informasi utama identitas dan sertifikasi asesor kompetensi BNSP</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Nama Lengkap Beserta Gelar <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="full_name" required value="{{ old('full_name', $user->full_name) }}"
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    @error('full_name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Nomor Registrasi Asesor (No. Reg MET)
                    </label>
                    <input type="text" name="assessor_registration_number" value="{{ old('assessor_registration_number', $user->assessor_registration_number ?? $user->asesorProfile?->no_met ?? $p->met_number) }}"
                        placeholder="Contoh: MET.000.002498 2013"
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm font-mono focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    @error('assessor_registration_number') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    <p class="text-[11px] text-slate-400 mt-1">Dicantumkan pada form verifikasi & dokumen MUK resmi</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Nomor Induk Kependudukan (NIK)
                    </label>
                    <input type="text" name="nik" maxlength="16" inputmode="numeric" value="{{ old('nik', $p->nik) }}"
                        placeholder="16 digit NIK KTP (angka)"
                        class="numeric-only w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    @error('nik') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Email Asesor
                    </label>
                    <input type="email" disabled value="{{ $user->email }}"
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-200 bg-slate-100 text-slate-500 text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Nomor Handphone / WhatsApp
                    </label>
                    <input type="text" name="phone" inputmode="numeric" maxlength="15" value="{{ old('phone', $user->phone) }}"
                        placeholder="Contoh: 081234567890"
                        class="numeric-only w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    @error('phone') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Jenis Kelamin
                    </label>
                    <div class="flex items-center space-x-6 h-[42px] px-3 bg-slate-50 border border-slate-300 rounded-lg text-sm">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="jenis_kelamin" value="L" {{ old('jenis_kelamin', $p->jenis_kelamin) === 'L' ? 'checked' : '' }}
                                class="text-brand-600 focus:ring-brand-500">
                            <span class="ml-2 text-slate-700 font-medium">Laki-laki</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="jenis_kelamin" value="P" {{ old('jenis_kelamin', $p->jenis_kelamin) === 'P' ? 'checked' : '' }}
                                class="text-brand-600 focus:ring-brand-500">
                            <span class="ml-2 text-slate-700 font-medium">Perempuan</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Tempat Lahir
                    </label>
                    <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $p->tempat_lahir) }}"
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Tanggal Lahir
                    </label>
                    <input type="date" name="tanggal_lahir" 
                        value="{{ old('tanggal_lahir', $p->tanggal_lahir ? (\Carbon\Carbon::parse($p->tanggal_lahir)->format('Y-m-d')) : '') }}"
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Kebangsaan
                    </label>
                    <input type="text" name="kebangsaan" value="{{ old('kebangsaan', $p->kebangsaan ?? 'Indonesia') }}"
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Pendidikan Terakhir
                    </label>
                    <select name="pendidikan_terakhir" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                        <option value="">-- Pilih Pendidikan --</option>
                        @foreach(['D3', 'D4', 'S1', 'S2', 'S3', 'SMK', 'Lainnya'] as $opt)
                            <option value="{{ $opt }}" {{ old('pendidikan_terakhir', $p->pendidikan_terakhir) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Alamat Rumah
                    </label>
                    <textarea name="alamat_rumah" rows="2"
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">{{ old('alamat_rumah', $p->alamat_rumah) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Kode Pos
                    </label>
                    <input type="text" name="kode_pos" maxlength="10" inputmode="numeric" value="{{ old('kode_pos', $p->kode_pos) }}"
                        placeholder="Contoh: 17530"
                        class="numeric-only w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    @error('kode_pos') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        No. Telepon Rumah
                    </label>
                    <input type="text" name="no_telepon_rumah" inputmode="numeric" maxlength="15" value="{{ old('no_telepon_rumah', $p->no_telepon_rumah) }}"
                        placeholder="Contoh: 0218901234"
                        class="numeric-only w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    @error('no_telepon_rumah') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Section 2: Pekerjaan & Institusi Asal Asesor --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-5">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-slate-800">2. Data Pekerjaan & Institusi</h2>
                <p class="text-xs text-slate-400">Informasi instansi asal, industri, atau lembaga tempat asesor bernaung</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Institusi / Perusahaan / Lembaga
                    </label>
                    <input type="text" name="nama_institusi_perusahaan" value="{{ old('nama_institusi_perusahaan', $p->nama_institusi_perusahaan) }}"
                        placeholder="Contoh: LSP PrO Indo / PT. Otomotif Indonesia"
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Jabatan / Profesi
                    </label>
                    <input type="text" name="jabatan" value="{{ old('jabatan', $p->jabatan) }}"
                        placeholder="Contoh: Asesor Kompetensi Otomotif"
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Alamat Kantor / Institusi
                    </label>
                    <textarea name="alamat_kantor" rows="2"
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">{{ old('alamat_kantor', $p->alamat_kantor) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        No. Telepon Kantor
                    </label>
                    <input type="text" name="no_telepon_kantor" inputmode="numeric" maxlength="15" value="{{ old('no_telepon_kantor', $p->no_telepon_kantor) }}"
                        placeholder="Contoh: 0218905678"
                        class="numeric-only w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    @error('no_telepon_kantor') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Section 3: Master Tanda Tangan Digital Asesor --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-800">3. Master Tanda Tangan Digital Asesor</h2>
                    <p class="text-xs text-slate-400">
                        Tanda tangan ini akan otomatis digunakan saat menandatangani persetujuan AK.01, rubrik instrumen uji (IA.01, IA.01B, IA.02, IA.03), serta dokumen ekspor MUK.
                    </p>
                </div>
                @if($p->signature_data)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        TTD Tersimpan
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                        Belum Diatur
                    </span>
                @endif
            </div>

            @if($p->signature_data)
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg flex flex-col sm:flex-row items-center gap-4">
                    <div class="bg-white p-3 rounded-lg border border-slate-300 shadow-xs">
                        <img src="{{ $p->signature_data }}" alt="Pratinjau Tanda Tangan Asesor" class="max-h-20 max-w-[200px] object-contain">
                    </div>
                    <div class="text-xs text-slate-600 space-y-1">
                        <p class="font-bold text-slate-800">Tanda Tangan Aktif Saat Ini</p>
                        <p>Tanda tangan di atas tersimpan sebagai tanda tangan master Anda dan siap digunakan otomatis. Anda dapat menggambar ulang pada area di bawah jika ingin memperbaruinya.</p>
                    </div>
                </div>
            @endif

            <x-signature-pad 
                name="signature_data" 
                :value="old('signature_data', $p->signature_data)"
                label="Goreskan Tanda Tangan Digital Baru / Perbarui"
                height="180"
            />
        </div>

        {{-- Submit Button --}}
        <div class="flex items-center justify-end space-x-3 pt-2">
            <a href="{{ route('asesor.jadwal.index') }}" class="px-5 py-2.5 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-lsp-navy hover:bg-lsp-navy-dark text-white font-bold text-xs uppercase tracking-wider rounded-lg shadow-sm transition">
                Simpan Biodata & Tanda Tangan
            </button>
        </div>
    </form>
</div>
@endsection
