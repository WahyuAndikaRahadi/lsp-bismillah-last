@extends('layouts.app', ['title' => 'Biodata & Tanda Tangan Asesi'])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Data Diri & Tanda Tangan</h1>
            <p class="text-sm text-slate-500">
                Informasi ini digunakan otomatis untuk pengisian formulir resmi FR.APL.01 Permohonan Sertifikasi Kompetensi
            </p>
        </div>
        <a href="{{ route('asesi.dashboard') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 flex items-center">
            &larr; Kembali ke Dashboard
        </a>
    </div>

    @php
        $user = auth()->user();
        $p = $user->profile ?? new \App\Models\UserProfile();
    @endphp

    <form action="{{ route('asesi.profile.update') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Section 1: Data Pribadi --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-5">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-slate-800">1. Data Pribadi Asesi</h2>
                <p class="text-xs text-slate-400">Pastikan data sesuai dengan kartu identitas (KTP) yang sah</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Nama Lengkap (Sesuai KTP) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="full_name" required value="{{ old('full_name', $user->full_name) }}"
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    @error('full_name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Nomor Induk Kependudukan (NIK) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nik" maxlength="16" inputmode="numeric" required value="{{ old('nik', $p->nik) }}"
                        placeholder="16 digit NIK KTP (angka)"
                        class="numeric-only w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    @error('nik') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Jenis Kelamin <span class="text-rose-500">*</span>
                    </label>
                    <div class="flex items-center space-x-6 h-[42px] px-3 bg-slate-50 border border-slate-300 rounded-lg text-sm">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="jenis_kelamin" value="L" {{ old('jenis_kelamin', $p->jenis_kelamin) === 'L' ? 'checked' : '' }} required
                                class="text-brand-600 focus:ring-brand-500">
                            <span class="ml-2 text-slate-700 font-medium">Laki-laki</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="jenis_kelamin" value="P" {{ old('jenis_kelamin', $p->jenis_kelamin) === 'P' ? 'checked' : '' }} required
                                class="text-brand-600 focus:ring-brand-500">
                            <span class="ml-2 text-slate-700 font-medium">Perempuan</span>
                        </label>
                    </div>
                    @error('jenis_kelamin') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Tempat Lahir: Terintegrasi API Wilayah Indonesia (cahyadsn/wilayah Kepmendagri 2025) --}}
                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-50/70 p-3.5 rounded-xl border border-slate-200" 
                     x-data="wilayahSelector('{{ addslashes(old('tempat_lahir', $p->tempat_lahir ?? '')) }}')">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Provinsi Tempat Lahir <span class="text-rose-500">*</span>
                        </label>
                        <select x-model="selectedProvince" 
                                @change="onProvinceChange()" 
                                class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none bg-white">
                            <option value="">-- Pilih Provinsi Asal --</option>
                            <template x-for="prov in provinces" :key="prov.code">
                                <option :value="prov.code" :selected="prov.code === selectedProvince" x-text="prov.name"></option>
                            </template>
                        </select>
                        <p class="text-[11px] text-slate-400 mt-1">Pilih provinsi untuk memfilter daftar kota/kabupaten</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Kota / Kabupaten Tempat Lahir <span class="text-rose-500">*</span>
                        </label>
                        <select name="tempat_lahir" 
                                x-model="selectedCity" 
                                required 
                                class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none bg-white"
                                :disabled="!selectedProvince && regencies.length === 0">
                            <option value="">-- Pilih Kota / Kabupaten --</option>
                            <template x-for="reg in regencies" :key="reg.code">
                                <option :value="reg.name" :selected="reg.name === selectedCity" x-text="reg.name"></option>
                            </template>
                            <template x-if="selectedCity && !regencies.some(r => r.name === selectedCity)">
                                <option :value="selectedCity" selected x-text="selectedCity"></option>
                            </template>
                        </select>
                        @error('tempat_lahir') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        <p class="text-[11px] text-slate-400 mt-1">Standar resmi Kepmendagri No. 300.2.2-2430 (cahyadsn/wilayah)</p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Tanggal Lahir <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="tanggal_lahir" required 
                        value="{{ old('tanggal_lahir', $p->tanggal_lahir ? (\Carbon\Carbon::parse($p->tanggal_lahir)->format('Y-m-d')) : '') }}"
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    @error('tanggal_lahir') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Kebangsaan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="kebangsaan" required value="{{ old('kebangsaan', $p->kebangsaan ?? 'Indonesia') }}"
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    @error('kebangsaan') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Pendidikan Terakhir <span class="text-rose-500">*</span>
                    </label>
                    <select name="pendidikan_terakhir" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                        <option value="">-- Pilih Pendidikan --</option>
                        @foreach(['SD', 'SMP', 'SMA/Sederajat', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3'] as $opt)
                            <option value="{{ $opt }}" {{ old('pendidikan_terakhir', $p->pendidikan_terakhir) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                    @error('pendidikan_terakhir') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Alamat Rumah Tinggal <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="alamat_rumah" rows="2" required
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">{{ old('alamat_rumah', $p->alamat_rumah) }}</textarea>
                    @error('alamat_rumah') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Kode Pos <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="kode_pos" maxlength="10" inputmode="numeric" required value="{{ old('kode_pos', $p->kode_pos) }}"
                        placeholder="Contoh: 17530"
                        class="numeric-only w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    @error('kode_pos') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Nomor HP / WhatsApp <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="phone" required inputmode="numeric" maxlength="15" value="{{ old('phone', $user->phone) }}"
                        placeholder="Contoh: 081234567890"
                        class="numeric-only w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    @error('phone') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Telepon Rumah (Opsional)
                    </label>
                    <input type="text" name="no_telepon_rumah" inputmode="numeric" maxlength="15" value="{{ old('no_telepon_rumah', $p->no_telepon_rumah) }}"
                        placeholder="Contoh: 0218901234"
                        class="numeric-only w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    @error('no_telepon_rumah') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Email Asesi
                    </label>
                    <input type="email" disabled value="{{ $user->email }}"
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-200 bg-slate-100 text-slate-500 text-sm">
                </div>
            </div>
        </div>

        {{-- Section 2: Data Pekerjaan / Institusi --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-5">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-slate-800">2. Data Pekerjaan / Sekolah / Institusi</h2>
                <p class="text-xs text-slate-400">Isi data sekolah SMK tempat belajar atau bengkel/perusahaan tempat bekerja</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Nama Institusi / Sekolah / Perusahaan (Opsional)
                    </label>
                    <input type="text" name="nama_institusi_perusahaan" value="{{ old('nama_institusi_perusahaan', $p->nama_institusi_perusahaan) }}"
                        placeholder="Contoh: SMK Negeri 1 Cikarang Barat / Bengkel Auto"
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Jabatan / Posisi / Status <span class="text-rose-500">*</span>
                    </label>
                    @php
                        $jobOptions = [
                            'Pelajar / Siswa',
                            'Mahasiswa',
                            'Karyawan Swasta / Teknisi / Mekanik',
                            'Pegawai Negeri / BUMN',
                            'Wirausaha / Pemilik Usaha',
                            'Belum Bekerja / Fresh Graduate',
                            'Lainnya',
                        ];
                    @endphp
                    <select name="jabatan" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                        <option value="">-- Pilih Jabatan / Posisi / Status --</option>
                        @foreach($jobOptions as $opt)
                            <option value="{{ $opt }}" {{ old('jabatan', $p->jabatan) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                        @if($p->jabatan && !in_array($p->jabatan, $jobOptions))
                            <option value="{{ $p->jabatan }}" selected>{{ $p->jabatan }}</option>
                        @endif
                    </select>
                    @error('jabatan') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Alamat Kantor / Sekolah (Opsional)
                    </label>
                    <textarea name="alamat_kantor" rows="2"
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">{{ old('alamat_kantor', $p->alamat_kantor) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        No. Telepon Kantor / Sekolah (Opsional)
                    </label>
                    <input type="text" name="no_telepon_kantor" inputmode="numeric" maxlength="15" value="{{ old('no_telepon_kantor', $p->no_telepon_kantor) }}"
                        placeholder="Contoh: 0218905678"
                        class="numeric-only w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                </div>
            </div>
        </div>

        {{-- Section 3: Tanda Tangan Digital --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-slate-800">3. Tanda Tangan Digital Asesi</h2>
                <p class="text-xs text-slate-400">
                    Tanda tangan ini akan dicantumkan secara otomatis pada dokumen MUK (APL.01, APL.02, AK.01, dll)
                </p>
            </div>

            <x-signature-pad 
                name="signature_data" 
                :value="old('signature_data', $p->signature_data)"
                :profileSignature="''"
                label="Goreskan Tanda Tangan Anda"
                :required="true"
                height="180"
            />
        </div>

        <div class="flex items-center justify-end space-x-3 pt-2">
            <button type="submit" class="px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm rounded-lg shadow-sm transition">
                Simpan Perubahan Biodata & TTD
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function wilayahSelector(initialCity) {
        return {
            provinces: [],
            regencies: [],
            allRegenciesMap: {},
            selectedProvince: '',
            selectedCity: initialCity || '',

            async init() {
                try {
                    const res = await fetch('/api/wilayah/all');
                    if (res.ok) {
                        const data = await res.json();
                        this.provinces = data.provinces || [];
                        this.allRegenciesMap = data.regencies || {};

                        if (this.selectedCity) {
                            const target = this.selectedCity.trim().toLowerCase();
                            const cleanTarget = target.replace(/^(kabupaten|kota)\s+/i, '');

                            for (const provCode in this.allRegenciesMap) {
                                const list = this.allRegenciesMap[provCode];
                                const match = list.find(r => {
                                    const rName = r.name.toLowerCase();
                                    const rClean = rName.replace(/^(kabupaten|kota)\s+/i, '');
                                    return rName === target || rClean === cleanTarget;
                                });

                                if (match) {
                                    this.selectedProvince = provCode;
                                    this.regencies = list;
                                    this.$nextTick(() => {
                                        this.selectedCity = match.name;
                                    });
                                    break;
                                }
                            }
                        }
                    }
                } catch (e) {
                    console.error('Gagal memuat API wilayah:', e);
                }
            },

            onProvinceChange() {
                if (!this.selectedProvince) {
                    this.regencies = [];
                    this.selectedCity = '';
                    return;
                }
                this.regencies = this.allRegenciesMap[this.selectedProvince] || [];
                if (!this.regencies.some(r => r.name === this.selectedCity)) {
                    this.selectedCity = '';
                }
            }
        };
    }
</script>
@endpush
