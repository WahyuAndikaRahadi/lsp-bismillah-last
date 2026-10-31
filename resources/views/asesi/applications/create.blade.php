<x-app-layout title="Formulir Permohonan Sertifikasi Kompetensi (FR-APL-01)">
    <div class="space-y-6" x-data="apl01Form()">

        <!-- Breadcrumb & Title -->
        <div>
            <nav class="flex items-center text-xs text-zinc-500 mb-1 space-x-1">
                <span>Portal Asesi</span>
                <span>&rsaquo;</span>
                <a href="{{ route('asesi.applications.index') }}" class="hover:underline">Permohonan</a>
                <span>&rsaquo;</span>
                <span class="text-zinc-900 font-medium">Pendaftaran Baru (FR-APL-01)</span>
            </nav>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-900">FR-APL-01: Formulir Permohonan Sertifikasi</h1>
                    <p class="text-xs sm:text-sm text-zinc-500 mt-0.5">
                        Isi rincian data pemohon, pilih skema sertifikasi, dan unggah dokumen bukti kelengkapan persyaratan.
                    </p>
                </div>
                <div class="shrink-0 flex items-center gap-2">
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-md bg-zinc-100 text-zinc-700 border border-zinc-200">
                        Standar BNSP 2026
                    </span>
                </div>
            </div>
        </div>

        <!-- Form Step Navigation -->
        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-3">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                <button type="button" @click="currentStep = 1" :class="currentStep === 1 ? 'bg-blue-600 text-white font-bold' : 'hover:bg-zinc-100 text-zinc-600 font-medium'" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs transition text-left">
                    <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold shrink-0" :class="currentStep === 1 ? 'bg-white text-blue-600' : 'bg-zinc-200 text-zinc-700'">1</span>
                    <span class="truncate">1. Data Pemohon</span>
                </button>
                <button type="button" @click="currentStep = 2" :class="currentStep === 2 ? 'bg-blue-600 text-white font-bold' : 'hover:bg-zinc-100 text-zinc-600 font-medium'" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs transition text-left">
                    <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold shrink-0" :class="currentStep === 2 ? 'bg-white text-blue-600' : 'bg-zinc-200 text-zinc-700'">2</span>
                    <span class="truncate">2. Skema & Jadwal</span>
                </button>
                <button type="button" @click="currentStep = 3" :class="currentStep === 3 ? 'bg-blue-600 text-white font-bold' : 'hover:bg-zinc-100 text-zinc-600 font-medium'" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs transition text-left">
                    <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold shrink-0" :class="currentStep === 3 ? 'bg-white text-blue-600' : 'bg-zinc-200 text-zinc-700'">3</span>
                    <span class="truncate">3. Unggah Berkas</span>
                </button>
                <button type="button" @click="currentStep = 4" :class="currentStep === 4 ? 'bg-blue-600 text-white font-bold' : 'hover:bg-zinc-100 text-zinc-600 font-medium'" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs transition text-left">
                    <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold shrink-0" :class="currentStep === 4 ? 'bg-white text-blue-600' : 'bg-zinc-200 text-zinc-700'">4</span>
                    <span class="truncate">4. Tanda Tangan & Kirim</span>
                </button>
            </div>
        </div>

        @if ($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-800 space-y-1">
                <div class="font-bold flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>Terdapat kesalahan pada isian formulir:</span>
                </div>
                <ul class="list-disc list-inside ml-6 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('asesi.applications.store') }}" method="POST" enctype="multipart/form-data" id="apl01MainForm">
            @csrf

            {{-- ========================================================================= --}}
            {{-- LANGKAH 1: DATA PEMOHON SERTIFIKASI (BAGIAN 1 FR-APL-01)                 --}}
            {{-- ========================================================================= --}}
            <div x-show="currentStep === 1" class="space-y-6">
                <!-- A. Data Pribadi -->
                <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 sm:p-8 space-y-6">
                    <div class="border-b border-zinc-100 pb-4">
                        <span class="text-[11px] font-bold text-blue-600 uppercase tracking-wider">Bagian 1 &bull; A</span>
                        <h2 class="text-base font-bold text-zinc-900">Rincian Data Pribadi Pemohon</h2>
                        <p class="text-xs text-zinc-500 mt-0.5">Pastikan nama dan NIK sesuai kartu identitas (KTP) resmi Anda.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-zinc-800 mb-1">Nama Lengkap (Sesuai KTP) <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $profile->nama_lengkap ?: $user->name) }}" required class="w-full text-xs rounded-xl border-zinc-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-zinc-800 mb-1">No. KTP / NIK (16 Digit) <span class="text-rose-500">*</span></label>
                            <input type="text" name="nik" maxlength="16" value="{{ old('nik', $profile->nik ?: $user->nik) }}" required class="w-full text-xs font-mono rounded-xl border-zinc-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-zinc-800 mb-1">Tempat Lahir <span class="text-rose-500">*</span></label>
                            <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $profile->tempat_lahir) }}" required placeholder="Contoh: Jakarta" class="w-full text-xs rounded-xl border-zinc-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-zinc-800 mb-1">Tanggal Lahir <span class="text-rose-500">*</span></label>
                            <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $profile->tanggal_lahir?->format('Y-m-d')) }}" required class="w-full text-xs rounded-xl border-zinc-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-zinc-800 mb-1">Jenis Kelamin <span class="text-rose-500">*</span></label>
                            <select name="jenis_kelamin" required class="w-full text-xs rounded-xl border-zinc-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="L" {{ old('jenis_kelamin', $profile->jenis_kelamin) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin', $profile->jenis_kelamin) === 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-zinc-800 mb-1">Kebangsaan <span class="text-rose-500">*</span></label>
                            <input type="text" name="kebangsaan" value="{{ old('kebangsaan', $profile->kebangsaan ?: 'Indonesia') }}" required class="w-full text-xs rounded-xl border-zinc-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-zinc-800 mb-1">No. Telepon / HP (WhatsApp) <span class="text-rose-500">*</span></label>
                            <input type="text" name="telepon" value="{{ old('telepon', $profile->telepon ?: $user->phone) }}" required placeholder="08xxxxxxxxxx" class="w-full text-xs rounded-xl border-zinc-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-zinc-800 mb-1">Kualifikasi Pendidikan Terakhir <span class="text-rose-500">*</span></label>
                            <select name="pendidikan_terakhir" required class="w-full text-xs rounded-xl border-zinc-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @foreach (['SMA / SMK', 'D1', 'D2', 'D3', 'D4 / S1', 'S2', 'S3', 'Lainnya'] as $pdd)
                                    <option value="{{ $pdd }}" {{ old('pendidikan_terakhir', $profile->pendidikan_terakhir) === $pdd ? 'selected' : '' }}>{{ $pdd }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Cascading Dropdown Wilayah (Provinsi -> Kota -> Kecamatan -> Kelurahan) -->
                    <div class="border-t border-zinc-100 pt-5 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Alamat Domisili Bertingkat</h3>
                            <span class="text-[11px] text-zinc-500 flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                                Data Wilayah Terintegrasi
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-zinc-800 mb-1">Alamat Lengkap (Jalan, No. Rumah, RT/RW) <span class="text-rose-500">*</span></label>
                                <textarea name="alamat" rows="2" required placeholder="Jl. Sudirman No. 12, RT 01 / RW 02" class="w-full text-xs rounded-xl border-zinc-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('alamat', $profile->alamat) }}</textarea>
                            </div>

                            <!-- Dropdown 1: Provinsi -->
                            <div>
                                <label class="block text-xs font-bold text-zinc-800 mb-1 flex items-center justify-between">
                                    <span>Provinsi <span class="text-rose-500">*</span></span>
                                    <span x-show="loadingProv" class="text-[10px] text-blue-600 font-normal">Memuat...</span>
                                </label>
                                <select name="provinsi_kode" x-model="selectedProv" @change="onProvChange()" class="w-full text-xs rounded-xl border-zinc-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">-- Pilih Provinsi --</option>
                                    @foreach ($provinces as $prov)
                                        <option value="{{ $prov['id'] }}" data-nama="{{ $prov['nama'] }}" {{ old('provinsi_kode', $profile->provinsi_kode) == $prov['id'] ? 'selected' : '' }}>
                                            {{ $prov['nama'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="provinsi_nama" x-model="selectedProvNama">
                            </div>

                            <!-- Dropdown 2: Kota / Kabupaten -->
                            <div>
                                <label class="block text-xs font-bold text-zinc-800 mb-1 flex items-center justify-between">
                                    <span>Kota / Kabupaten <span class="text-rose-500">*</span></span>
                                    <span x-show="loadingKota" class="text-[10px] text-blue-600 font-normal flex items-center gap-1">
                                        <svg class="animate-spin h-3 w-3 text-blue-600" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        Memuat kota...
                                    </span>
                                </label>
                                <select name="kota_kode" x-model="selectedKota" @change="onKotaChange()" :disabled="loadingKota || regencies.length === 0" class="w-full text-xs rounded-xl border-zinc-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 disabled:bg-zinc-100 disabled:text-zinc-400">
                                    <option value="">-- Pilih Kota / Kabupaten --</option>
                                    <template x-for="reg in regencies" :key="reg.id">
                                        <option :value="reg.id" :data-nama="reg.nama" x-text="reg.nama"></option>
                                    </template>
                                </select>
                                <input type="hidden" name="kota_nama" x-model="selectedKotaNama">
                            </div>

                            <!-- Dropdown 3: Kecamatan -->
                            <div>
                                <label class="block text-xs font-bold text-zinc-800 mb-1 flex items-center justify-between">
                                    <span>Kecamatan</span>
                                    <span x-show="loadingKec" class="text-[10px] text-blue-600 font-normal flex items-center gap-1">
                                        <svg class="animate-spin h-3 w-3 text-blue-600" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        Memuat kec...
                                    </span>
                                </label>
                                <select name="kecamatan_kode" x-model="selectedKec" @change="onKecChange()" :disabled="loadingKec || districts.length === 0" class="w-full text-xs rounded-xl border-zinc-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 disabled:bg-zinc-100 disabled:text-zinc-400">
                                    <option value="">-- Pilih Kecamatan --</option>
                                    <template x-for="dist in districts" :key="dist.id">
                                        <option :value="dist.id" :data-nama="dist.nama" x-text="dist.nama"></option>
                                    </template>
                                </select>
                                <input type="hidden" name="kecamatan_nama" x-model="selectedKecNama">
                            </div>

                            <!-- Dropdown 4: Kelurahan & Kode Pos -->
                            <div class="grid grid-cols-3 gap-2">
                                <div class="col-span-2">
                                    <label class="block text-xs font-bold text-zinc-800 mb-1 flex items-center justify-between">
                                        <span>Kelurahan / Desa</span>
                                        <span x-show="loadingKel" class="text-[10px] text-blue-600 font-normal">Memuat...</span>
                                    </label>
                                    <select name="kelurahan_kode" x-model="selectedKel" @change="onKelChange()" :disabled="loadingKel || villages.length === 0" class="w-full text-xs rounded-xl border-zinc-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 disabled:bg-zinc-100 disabled:text-zinc-400">
                                        <option value="">-- Pilih Kelurahan --</option>
                                        <template x-for="vil in villages" :key="vil.id">
                                            <option :value="vil.id" :data-nama="vil.nama" :data-kodepos="vil.kode_pos || ''" x-text="vil.nama"></option>
                                        </template>
                                    </select>
                                    <input type="hidden" name="kelurahan_nama" x-model="selectedKelNama">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-zinc-800 mb-1">Kode Pos</label>
                                    <input type="text" name="kode_pos" x-model="kodePos" maxlength="6" placeholder="12345" class="w-full text-xs font-mono rounded-xl border-zinc-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- B. Data Pekerjaan Sekarang -->
                <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 sm:p-8 space-y-6">
                    <div class="border-b border-zinc-100 pb-4">
                        <span class="text-[11px] font-bold text-blue-600 uppercase tracking-wider">Bagian 1 &bull; B</span>
                        <h2 class="text-base font-bold text-zinc-900">Rincian Data Pekerjaan Sekarang</h2>
                        <p class="text-xs text-zinc-500 mt-0.5">Isi data instansi, kantor, atau kampus tempat Anda beraktivitas saat ini (opsional jika belum bekerja).</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-zinc-800 mb-1">Profesi / Status Pekerjaan</label>
                            <input type="text" name="pekerjaan" value="{{ old('pekerjaan', $profile->pekerjaan) }}" placeholder="Karyawan Swasta / Mahasiswa / Freelancer" class="w-full text-xs rounded-xl border-zinc-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-zinc-800 mb-1">Nama Perusahaan / Lembaga</label>
                            <input type="text" name="nama_perusahaan" value="{{ old('nama_perusahaan', $profile->nama_perusahaan) }}" placeholder="PT Contoh Teknologi" class="w-full text-xs rounded-xl border-zinc-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-zinc-800 mb-1">Jabatan / Posisi</label>
                            <input type="text" name="jabatan" value="{{ old('jabatan', $profile->jabatan) }}" placeholder="Staff IT / Junior Programmer" class="w-full text-xs rounded-xl border-zinc-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-zinc-800 mb-1">Alamat Kantor / Perusahaan</label>
                            <input type="text" name="alamat_kantor" value="{{ old('alamat_kantor', $profile->alamat_kantor) }}" placeholder="Gedung Cyber Lt. 3, Jl. Kuningan Barat" class="w-full text-xs rounded-xl border-zinc-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-zinc-800 mb-1">No. Telp / Email Kantor</label>
                            <input type="text" name="telp_kantor" value="{{ old('telp_kantor', $profile->telp_kantor) }}" placeholder="021-xxxxxxx" class="w-full text-xs rounded-xl border-zinc-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Next Button -->
                <div class="flex justify-end">
                    <button type="button" @click="currentStep = 2" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition">
                        <span>Lanjut ke Langkah 2: Skema & Jadwal</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- ========================================================================= --}}
            {{-- LANGKAH 2: DATA SKEMA & JADWAL ASESMEN (BAGIAN 2 FR-APL-01)               --}}
            {{-- ========================================================================= --}}
            <div x-show="currentStep === 2" class="space-y-6" style="display: none;">
                <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 sm:p-8 space-y-6">
                    <div class="border-b border-zinc-100 pb-4">
                        <span class="text-[11px] font-bold text-blue-600 uppercase tracking-wider">Bagian 2</span>
                        <h2 class="text-base font-bold text-zinc-900">Data Sertifikasi & Tujuan Asesmen</h2>
                        <p class="text-xs text-zinc-500 mt-0.5">Tentukan skema sertifikasi yang diajukan beserta tujuan asesmen kompetensi.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Pilihan Skema Sertifikasi -->
                        <div>
                            <label class="block text-xs font-bold text-zinc-800 mb-1">Skema Sertifikasi <span class="text-rose-500">*</span></label>
                            <select name="scheme_id" x-model="selectedSchemeId" @change="onSchemeChange()" required class="w-full text-xs rounded-xl border-zinc-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @foreach ($schemes as $scheme)
                                    <option value="{{ $scheme->id }}" {{ (string)$selectedSchemeId === (string)$scheme->id ? 'selected' : '' }}>
                                        [{{ $scheme->kode_skema }}] {{ $scheme->nama_skema }} ({{ $scheme->jenis_skema }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Pilihan Tujuan Asesmen -->
                        <div>
                            <label class="block text-xs font-bold text-zinc-800 mb-1">Tujuan Asesmen <span class="text-rose-500">*</span></label>
                            <select name="tujuan_asesmen" required class="w-full text-xs rounded-xl border-zinc-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="Sertifikasi Baru" {{ old('tujuan_asesmen') === 'Sertifikasi Baru' ? 'selected' : '' }}>Sertifikasi Baru (Pemula / Baru Pertama Kali)</option>
                                <option value="Sertifikasi Ulang (RCC)" {{ old('tujuan_asesmen') === 'Sertifikasi Ulang (RCC)' ? 'selected' : '' }}>Sertifikasi Ulang (RCC / Perpanjangan Sertifikat)</option>
                                <option value="Pengakuan Kompetensi Terkini (PKT)" {{ old('tujuan_asesmen') === 'Pengakuan Kompetensi Terkini (PKT)' ? 'selected' : '' }}>Pengakuan Kompetensi Terkini (PKT)</option>
                                <option value="Rekognisi Pembelajaran Lampau (RPL)" {{ old('tujuan_asesmen') === 'Rekognisi Pembelajaran Lampau (RPL)' ? 'selected' : '' }}>Rekognisi Pembelajaran Lampau (RPL)</option>
                                <option value="Lainnya" {{ old('tujuan_asesmen') === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>

                        <!-- Pilihan Jadwal Asesmen (Opsional / Langsung daftar jadwal) -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-zinc-800 mb-1 flex items-center justify-between">
                                <span>Pilihan Jadwal Uji Kompetensi (TUK)</span>
                                <span class="text-[11px] text-zinc-400 font-normal">Dapat dikosongkan jika jadwal fleksibel (di-plot admin)</span>
                            </label>
                            <select name="schedule_id" class="w-full text-xs rounded-xl border-zinc-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">-- Belum memilih jadwal (Jadwal akan di-plot oleh Admin LSP) --</option>
                                @foreach ($schedules as $sched)
                                    <option value="{{ $sched->id }}" {{ (string)$selectedScheduleId === (string)$sched->id ? 'selected' : '' }}>
                                        [{{ $sched->kode_jadwal }}] {{ $sched->nama_jadwal }} &bull; Tgl: {{ $sched->tgl_asesmen->format('d M Y') }} di {{ $sched->tuk->nama_tuk }} (Sisa Kuota: {{ $sched->sisa_kuota }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Daftar Unit Kompetensi Kemasan Skema (Preview) -->
                    <div class="border-t border-zinc-100 pt-5">
                        <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider mb-3">Daftar Unit Kompetensi Kemasan Skema Ini</h3>
                        <div class="bg-zinc-50 rounded-xl border border-zinc-200 overflow-hidden">
                            <table class="min-w-full divide-y divide-zinc-200 text-left text-xs">
                                <thead class="bg-zinc-100 text-zinc-600 font-bold uppercase text-[10px]">
                                    <tr>
                                        <th class="px-4 py-2.5 w-12 text-center">No</th>
                                        <th class="px-4 py-2.5 w-48">Kode Unit</th>
                                        <th class="px-4 py-2.5">Judul Unit Kompetensi</th>
                                        <th class="px-4 py-2.5 w-28 text-center">Jenis Standar</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-200" id="schemeUnitsTableBody">
                                    @php
                                        $currentScheme = $schemes->firstWhere('id', $selectedSchemeId) ?? $schemes->first();
                                    @endphp
                                    @if($currentScheme && $currentScheme->competencyUnits->isNotEmpty())
                                        @foreach ($currentScheme->competencyUnits as $idx => $unit)
                                            <tr>
                                                <td class="px-4 py-2 text-center text-zinc-500">{{ $idx + 1 }}</td>
                                                <td class="px-4 py-2 font-mono font-medium text-zinc-900">{{ $unit->kode_unit }}</td>
                                                <td class="px-4 py-2 text-zinc-800">{{ $unit->judul_unit }}</td>
                                                <td class="px-4 py-2 text-center">
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-zinc-200 text-zinc-700">
                                                        {{ $unit->jenis_standar }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4" class="px-4 py-6 text-center text-zinc-500 italic">
                                                Belum ada unit kompetensi yang terdaftar pada skema ini.
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Back / Next Buttons -->
                <div class="flex items-center justify-between">
                    <button type="button" @click="currentStep = 1" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-zinc-300 hover:bg-zinc-100 text-zinc-700 text-xs font-semibold transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        <span>Kembali ke Data Pemohon</span>
                    </button>
                    <button type="button" @click="currentStep = 3" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition">
                        <span>Lanjut ke Langkah 3: Unggah Berkas</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- ========================================================================= --}}
            {{-- LANGKAH 3: UNGGAH BUKTI KELENGKAPAN PERSYARATAN (BAGIAN 3 FR-APL-01)      --}}
            {{-- ========================================================================= --}}
            <div x-show="currentStep === 3" class="space-y-6" style="display: none;">
                <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 sm:p-8 space-y-6">
                    <div class="border-b border-zinc-100 pb-4">
                        <span class="text-[11px] font-bold text-blue-600 uppercase tracking-wider">Bagian 3</span>
                        <h2 class="text-base font-bold text-zinc-900">Bukti Kelengkapan Pemohon (Dokumen Persyaratan)</h2>
                        <p class="text-xs text-zinc-500 mt-0.5">
                            Unggah berkas bukti persyaratan dasar sesuai skema yang dipilih. Format: <strong>PDF, JPG, PNG</strong> (Maksimal 5 MB per file).
                        </p>
                    </div>

                    <div class="space-y-4">
                        @if($currentScheme && $currentScheme->requirements->isNotEmpty())
                            @foreach ($currentScheme->requirements as $req)
                                <div class="p-4 rounded-xl border border-zinc-200 bg-zinc-50/50 hover:bg-zinc-50 transition flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold text-zinc-900">{{ $req->nama_dokumen }}</span>
                                            @if($req->is_required)
                                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">Wajib</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-zinc-200 text-zinc-600">Opsional</span>
                                            @endif
                                        </div>
                                        <p class="text-[11px] text-zinc-500">{{ $req->keterangan ?: 'Lampirkan dokumen sah yang jelas terbaca.' }}</p>
                                    </div>
                                    <div class="shrink-0 w-full sm:w-72">
                                        <input type="file" name="documents[{{ $req->id }}]" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 text-zinc-600">
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <!-- Default Fallback Requirements jika skema belum punya list -->
                            @php
                                $defaultReqs = [
                                    'Kartu Tanda Penduduk (KTP)',
                                    'Ijazah Pendidikan Terakhir',
                                    'Curriculum Vitae (CV) / Portofolio Pengalaman Kerja',
                                    'Pas Foto Resmi (Background Merah/Biru)',
                                ];
                            @endphp
                            @foreach ($defaultReqs as $idx => $dReq)
                                <div class="p-4 rounded-xl border border-zinc-200 bg-zinc-50/50 hover:bg-zinc-50 transition flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold text-zinc-900">{{ $dReq }}</span>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">Wajib</span>
                                        </div>
                                        <p class="text-[11px] text-zinc-500">Format PDF / Gambar, maks 5 MB.</p>
                                    </div>
                                    <div class="shrink-0 w-full sm:w-72">
                                        <input type="hidden" name="custom_names[{{ $idx }}]" value="{{ $dReq }}">
                                        <input type="file" name="documents[custom][{{ $idx }}]" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 text-zinc-600">
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- Back / Next Buttons -->
                <div class="flex items-center justify-between">
                    <button type="button" @click="currentStep = 2" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-zinc-300 hover:bg-zinc-100 text-zinc-700 text-xs font-semibold transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        <span>Kembali ke Skema</span>
                    </button>
                    <button type="button" @click="currentStep = 4" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition">
                        <span>Lanjut ke Langkah 4: Tanda Tangan & Konfirmasi</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- ========================================================================= --}}
            {{-- LANGKAH 4: TANDA TANGAN DIGITAL & PERNYATAAN (BAGIAN PENGESAHAN)          --}}
            {{-- ========================================================================= --}}
            <div x-show="currentStep === 4" class="space-y-6" style="display: none;">
                <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 sm:p-8 space-y-6">
                    <div class="border-b border-zinc-100 pb-4">
                        <span class="text-[11px] font-bold text-blue-600 uppercase tracking-wider">Bagian 4</span>
                        <h2 class="text-base font-bold text-zinc-900">Pernyataan Pemohon & Tanda Tangan Digital</h2>
                        <p class="text-xs text-zinc-500 mt-0.5">Bubuhkan tanda tangan digital Anda sebagai pengesahan formulir FR-APL-01.</p>
                    </div>

                    <!-- Digital Signature Canvas -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-zinc-800">
                            Tanda Tangan Digital Calon Asesi <span class="text-rose-500">*</span>
                        </label>
                        <p class="text-xs text-zinc-500">Gunakan mouse, stylus, atau sentuhan jari Anda pada kanvas di bawah ini:</p>

                        <div class="border-2 border-dashed border-zinc-300 rounded-xl bg-zinc-50 p-2 inline-block">
                            <canvas id="signatureCanvas" width="450" height="180" class="bg-white rounded-lg border border-zinc-200 cursor-crosshair touch-none"></canvas>
                            <div class="flex items-center justify-between mt-2 px-1">
                                <span class="text-[11px] text-zinc-400">Tanda Tangan Elektronik Sah LSP</span>
                                <button type="button" id="clearSignatureBtn" class="text-xs text-rose-600 hover:text-rose-700 font-semibold px-2 py-1 rounded hover:bg-rose-50 transition">
                                    Bersihkan (Clear)
                                </button>
                            </div>
                        </div>
                        <input type="hidden" name="ttd_asesi" id="ttd_asesi_input">
                    </div>

                    <!-- Klausul Pernyataan Kebenaran Data (Sesuai BNSP) -->
                    <div class="p-4 bg-zinc-50 border border-zinc-200 rounded-xl space-y-3">
                        <div class="flex items-start gap-3">
                            <input type="checkbox" name="setuju_ketentuan" id="setuju_ketentuan" value="1" required class="mt-1 rounded text-blue-600 focus:ring-blue-500 border-zinc-300">
                            <label for="setuju_ketentuan" class="text-xs text-zinc-700 leading-relaxed cursor-pointer">
                                <strong>Pernyataan Pemohon:</strong><br>
                                Saya menyatakan bahwa seluruh data dan dokumen yang saya berikan dalam formulir FR-APL-01 ini adalah benar, sah, dan dapat dipertanggungjawabkan secara hukum. Apabila di kemudian hari ditemukan ketidakbenaran atau pemalsuan data, saya bersedia menerima sanksi pembatalan sertifikasi sesuai ketentuan yang berlaku di LSP dan BNSP.
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons: Save Draft or Submit -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <button type="button" @click="currentStep = 3" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-zinc-300 hover:bg-zinc-100 text-zinc-700 text-xs font-semibold transition order-2 sm:order-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        <span>Kembali ke Berkas</span>
                    </button>

                    <div class="flex items-center gap-3 w-full sm:w-auto order-1 sm:order-2">
                        <button type="submit" name="action_draft" value="1" class="flex-1 sm:flex-none px-4 py-2.5 rounded-xl border border-zinc-300 hover:bg-zinc-100 text-zinc-700 text-xs font-semibold transition">
                            Simpan Sebagai Draft
                        </button>
                        <button type="submit" id="submitApl01Btn" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-bold shadow-sm transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Ajukan Permohonan (FR-APL-01)</span>
                        </button>
                    </div>
                </div>
            </div>

        </form>

    </div>

    <!-- Script Cascading Wilayah & Signature Canvas -->
    <script>
        function apl01Form() {
            return {
                currentStep: 1,
                selectedProv: '{{ old('provinsi_kode', $profile->provinsi_kode) }}',
                selectedProvNama: '{{ old('provinsi_nama', $profile->provinsi_nama) }}',
                selectedKota: '{{ old('kota_kode', $profile->kota_kode) }}',
                selectedKotaNama: '{{ old('kota_nama', $profile->kota_nama) }}',
                selectedKec: '{{ old('kecamatan_kode', $profile->kecamatan_kode) }}',
                selectedKecNama: '{{ old('kecamatan_nama', $profile->kecamatan_nama) }}',
                selectedKel: '{{ old('kelurahan_kode', $profile->kelurahan_kode) }}',
                selectedKelNama: '{{ old('kelurahan_nama', $profile->kelurahan_nama) }}',
                kodePos: '{{ old('kode_pos', $profile->kode_pos) }}',
                selectedSchemeId: '{{ $selectedSchemeId }}',

                regencies: [],
                districts: [],
                villages: [],

                loadingProv: false,
                loadingKota: false,
                loadingKec: false,
                loadingKel: false,

                init() {
                    if (this.selectedProv) {
                        this.loadRegencies(this.selectedProv, this.selectedKota);
                    }
                },

                async onProvChange() {
                    const select = event.target;
                    const opt = select.options[select.selectedIndex];
                    this.selectedProvNama = opt ? opt.getAttribute('data-nama') || '' : '';
                    this.selectedKota = '';
                    this.selectedKotaNama = '';
                    this.selectedKec = '';
                    this.selectedKecNama = '';
                    this.selectedKel = '';
                    this.selectedKelNama = '';
                    this.districts = [];
                    this.villages = [];

                    if (this.selectedProv) {
                        await this.loadRegencies(this.selectedProv);
                    } else {
                        this.regencies = [];
                    }
                },

                async loadRegencies(provId, preselectKota = null) {
                    this.loadingKota = true;
                    try {
                        const res = await fetch(`/api/wilayah/regencies?province_id=${provId}`);
                        this.regencies = await res.json();
                        if (preselectKota) {
                            this.selectedKota = preselectKota;
                            await this.loadDistricts(preselectKota, this.selectedKec);
                        }
                    } catch (e) {
                        console.error('Gagal memuat kota:', e);
                    } finally {
                        this.loadingKota = false;
                    }
                },

                async onKotaChange() {
                    const select = event.target;
                    const opt = select.options[select.selectedIndex];
                    this.selectedKotaNama = opt ? opt.getAttribute('data-nama') || '' : '';
                    this.selectedKec = '';
                    this.selectedKecNama = '';
                    this.selectedKel = '';
                    this.selectedKelNama = '';
                    this.villages = [];

                    if (this.selectedKota) {
                        await this.loadDistricts(this.selectedKota);
                    } else {
                        this.districts = [];
                    }
                },

                async loadDistricts(kotaId, preselectKec = null) {
                    this.loadingKec = true;
                    try {
                        const res = await fetch(`/api/wilayah/districts?regency_id=${kotaId}`);
                        this.districts = await res.json();
                        if (preselectKec) {
                            this.selectedKec = preselectKec;
                            await this.loadVillages(preselectKec, this.selectedKel);
                        }
                    } catch (e) {
                        console.error('Gagal memuat kecamatan:', e);
                    } finally {
                        this.loadingKec = false;
                    }
                },

                async onKecChange() {
                    const select = event.target;
                    const opt = select.options[select.selectedIndex];
                    this.selectedKecNama = opt ? opt.getAttribute('data-nama') || '' : '';
                    this.selectedKel = '';
                    this.selectedKelNama = '';

                    if (this.selectedKec) {
                        await this.loadVillages(this.selectedKec);
                    } else {
                        this.villages = [];
                    }
                },

                async loadVillages(kecId, preselectKel = null) {
                    this.loadingKel = true;
                    try {
                        const res = await fetch(`/api/wilayah/villages?district_id=${kecId}`);
                        this.villages = await res.json();
                        if (preselectKel) {
                            this.selectedKel = preselectKel;
                        }
                    } catch (e) {
                        console.error('Gagal memuat kelurahan:', e);
                    } finally {
                        this.loadingKel = false;
                    }
                },

                onKelChange() {
                    const select = event.target;
                    const opt = select.options[select.selectedIndex];
                    this.selectedKelNama = opt ? opt.getAttribute('data-nama') || '' : '';
                    const kp = opt ? opt.getAttribute('data-kodepos') : '';
                    if (kp) {
                        this.kodePos = kp;
                    }
                },

                onSchemeChange() {
                    // Update table when scheme changed
                    window.location.href = `{{ route('asesi.applications.create') }}?scheme_id=${this.selectedSchemeId}`;
                }
            };
        }

        // Signature Canvas setup
        document.addEventListener('DOMContentLoaded', () => {
            const canvas = document.getElementById('signatureCanvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            const clearBtn = document.getElementById('clearSignatureBtn');
            const hiddenInput = document.getElementById('ttd_asesi_input');
            const form = document.getElementById('apl01MainForm');

            let isDrawing = false;
            let lastX = 0;
            let lastY = 0;

            function getPos(e) {
                const rect = canvas.getBoundingClientRect();
                const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                const clientY = e.touches ? e.touches[0].clientY : e.clientY;
                return {
                    x: clientX - rect.left,
                    y: clientY - rect.top
                };
            }

            function startDrawing(e) {
                isDrawing = true;
                const pos = getPos(e);
                lastX = pos.x;
                lastY = pos.y;
            }

            function draw(e) {
                if (!isDrawing) return;
                e.preventDefault();
                const pos = getPos(e);

                ctx.beginPath();
                ctx.moveTo(lastX, lastY);
                ctx.lineTo(pos.x, pos.y);
                ctx.strokeStyle = '#0f172a';
                ctx.lineWidth = 2.5;
                ctx.lineCap = 'round';
                ctx.lineJoin = 'round';
                ctx.stroke();

                lastX = pos.x;
                lastY = pos.y;
            }

            function stopDrawing() {
                if (isDrawing) {
                    isDrawing = false;
                    hiddenInput.value = canvas.toDataURL('image/png');
                }
            }

            canvas.addEventListener('mousedown', startDrawing);
            canvas.addEventListener('mousemove', draw);
            canvas.addEventListener('mouseup', stopDrawing);
            canvas.addEventListener('mouseleave', stopDrawing);

            canvas.addEventListener('touchstart', startDrawing, { passive: false });
            canvas.addEventListener('touchmove', draw, { passive: false });
            canvas.addEventListener('touchend', stopDrawing);

            clearBtn.addEventListener('click', () => {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                hiddenInput.value = '';
            });

            form.addEventListener('submit', () => {
                if (!hiddenInput.value && !isCanvasBlank(canvas)) {
                    hiddenInput.value = canvas.toDataURL('image/png');
                }
            });

            function isCanvasBlank(c) {
                const blank = document.createElement('canvas');
                blank.width = c.width;
                blank.height = c.height;
                return c.toDataURL() === blank.toDataURL();
            }
        });
    </script>
</x-app-layout>
