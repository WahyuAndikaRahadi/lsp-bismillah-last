@extends('layouts.app', ['title' => 'FR.MAPA.01 - Merencanakan Aktivitas dan Proses Asesmen'])

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200">
                    FR.MAPA.01
                </span>
                <span class="text-xs text-slate-500">Revisi {{ $plan->revision ?? 1 }}</span>
                @if($plan->is_locked)
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                        Terkunci (Locked)
                    </span>
                @else
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                        Draf Asesor
                    </span>
                @endif
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">
                Merencanakan Aktivitas dan Proses Asesmen
            </h1>
            <p class="text-sm text-slate-500">
                Skema: <strong class="text-slate-800">{{ $scheme->name }} ({{ $scheme->code }})</strong> &bull; Jadwal: {{ $schedule->name }}
            </p>
        </div>
        <a href="{{ route('asesor.jadwal.asesi', $schedule->id) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">
            &larr; Kembali ke Roster Asesi
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('warning'))
        <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm">
            {{ session('warning') }}
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm space-y-1">
            <p class="font-bold">Terdapat kesalahan pengisian formulir:</p>
            <ul class="list-disc list-inside text-xs space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $formAction = $plan->exists
            ? route('asesor.mapa01.update', $plan->id)
            : route('asesor.mapa01.store', ['schedule' => $schedule->id]);
        $approach = $plan->approach_data ?? [];
        $modifs = $plan->modifications ?? [];
        $groups = $plan->work_groups ?? [];
        $confirm = $plan->confirmation_data ?? [];
    @endphp

    <form action="{{ $formAction }}" method="POST" class="space-y-6">
        @csrf
        @if($plan->exists)
            @method('PUT')
        @endif

        <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
        @if($plan->assessment_id)
            <input type="hidden" name="assessment_id" value="{{ $plan->assessment_id }}">
        @endif
        <input type="hidden" name="lock_version" value="{{ $plan->lock_version ?? 1 }}">

        {{-- BAGIAN 1: Menentukan Pendekatan Asesmen --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-5">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-slate-900">1. Menentukan Pendekatan Asesmen</h2>
                <p class="text-xs text-slate-500">Karakteristik kandidat asesi, tujuan asesmen, konteks lingkungan, dan acuan pembanding</p>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">1.1. Asesi</label>
                    <div class="space-y-2 text-sm">
                        @php
                            $asesiOptions = [
                                'hasil_pelatihan_telusur' => 'Hasil pelatihan dan / atau pendidikan, dimana Kurikulum dan fasilitas praktek mampu telusur terhadap standar kompetensi',
                                'hasil_pelatihan_belum_kompetensi' => 'Hasil pelatihan dan / atau pendidikan, dimana kurikulum belum berbasis kompetensi',
                                'pekerja_berpengalaman_telusur' => 'Pekerja berpengalaman, dimana berasal dari industri/tempat kerja yang dalam operasionalnya mampu telusur dengan standar kompetensi',
                                'pekerja_berpengalaman_belum_kompetensi' => 'Pekerja berpengalaman, dimana berasal dari industri/tempat kerja yang dalam operasionalnya belum berbasis kompetensi',
                                'otodidak' => 'Pelatihan / belajar mandiri atau otodidak',
                            ];
                        @endphp
                        @foreach($asesiOptions as $val => $label)
                            <label class="flex items-start space-x-2.5 cursor-pointer">
                                <input type="radio" name="approach_data[asesi_type]" value="{{ $val }}"
                                    {{ old('approach_data.asesi_type', $approach['asesi_type'] ?? 'hasil_pelatihan_telusur') === $val ? 'checked' : '' }}
                                    @disabled($plan->is_locked)
                                    class="mt-1 text-brand-600 focus:ring-brand-500">
                                <span class="text-slate-700">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tujuan Asesmen</label>
                        <select name="approach_data[purpose]" @disabled($plan->is_locked) class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 bg-white">
                            <option value="sertifikasi" {{ old('approach_data.purpose', $approach['purpose'] ?? 'sertifikasi') === 'sertifikasi' ? 'selected' : '' }}>Sertifikasi</option>
                            <option value="pkt" {{ old('approach_data.purpose', $approach['purpose'] ?? '') === 'pkt' ? 'selected' : '' }}>Pengakuan Kompetensi Terkini (PKT)</option>
                            <option value="rpl" {{ old('approach_data.purpose', $approach['purpose'] ?? '') === 'rpl' ? 'selected' : '' }}>Rekognisi Pembelajaran Lampau (RPL)</option>
                            <option value="lainnya" {{ old('approach_data.purpose', $approach['purpose'] ?? '') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Lingkungan Konteks</label>
                        <select name="approach_data[context_environment]" @disabled($plan->is_locked) class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 bg-white">
                            <option value="tempat_kerja_simulasi" {{ old('approach_data.context_environment', $approach['context_environment'] ?? 'tempat_kerja_simulasi') === 'tempat_kerja_simulasi' ? 'selected' : '' }}>Tempat kerja disimulasikan (TUK)</option>
                            <option value="tempat_kerja_nyata" {{ old('approach_data.context_environment', $approach['context_environment'] ?? '') === 'tempat_kerja_nyata' ? 'selected' : '' }}>Tempat kerja nyata / Industri</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- BAGIAN 2: Peta Kelompok Pekerjaan & Unit Kompetensi --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-5">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-slate-900">2. Peta Kelompok Pekerjaan dan Unit Kompetensi</h2>
                <p class="text-xs text-slate-500">Penentuan jenis bukti (L/TL/T) dan metode asesmen (CL, DIT, DPL, DPT, VPK, CVP, CRP, PW) untuk setiap unit skema</p>
            </div>

            @foreach($groups as $gIdx => $group)
                <div class="space-y-3 bg-slate-50/70 p-4 rounded-xl border border-slate-200">
                    <input type="hidden" name="work_groups[{{ $gIdx }}][name]" value="{{ $group['name'] }}">
                    <h3 class="text-sm font-bold text-slate-800">{{ $group['name'] }}</h3>

                    <div class="space-y-4">
                        @foreach($group['units'] ?? [] as $uIdx => $u)
                            <div class="bg-white p-3.5 rounded-lg border border-slate-200 space-y-3 text-xs">
                                <input type="hidden" name="work_groups[{{ $gIdx }}][units][{{ $uIdx }}][code]" value="{{ $u['code'] }}">
                                <input type="hidden" name="work_groups[{{ $gIdx }}][units][{{ $uIdx }}][title]" value="{{ $u['title'] }}">

                                <div class="flex items-center justify-between font-bold text-slate-800">
                                    <span class="font-mono text-brand-700">{{ $u['code'] }}</span>
                                    <span>{{ $u['title'] }}</span>
                                </div>

                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Bukti yang Diidentifikasi</label>
                                    <input type="text" name="work_groups[{{ $gIdx }}][units][{{ $uIdx }}][evidence_text]"
                                        value="{{ old("work_groups.{$gIdx}.units.{$uIdx}.evidence_text", $u['evidence_text'] ?? ('Hasil observasi dan tes tertulis tentang ' . $u['title'])) }}"
                                        @disabled($plan->is_locked)
                                        class="w-full px-3 py-1.5 rounded border border-slate-300 text-xs focus:ring-1 focus:ring-brand-500">
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-1">
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Jenis Bukti</label>
                                        <div class="flex items-center space-x-3">
                                            @foreach(['L' => 'L (Langsung)', 'TL' => 'TL (Tidak Langsung)', 'T' => 'T (Tambahan)'] as $btCode => $btLabel)
                                                @php
                                                    $checkedTypes = $u['evidence_types'] ?? ['L', 'T'];
                                                @endphp
                                                <label class="flex items-center space-x-1.5 cursor-pointer">
                                                    <input type="checkbox" name="work_groups[{{ $gIdx }}][units][{{ $uIdx }}][evidence_types][]" value="{{ $btCode }}"
                                                        {{ in_array($btCode, $checkedTypes, true) ? 'checked' : '' }}
                                                        @disabled($plan->is_locked)
                                                        class="rounded border-slate-300 text-brand-600">
                                                    <span>{{ $btCode }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Metode & Perangkat</label>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach(['CL', 'DIT', 'DPL', 'DPT', 'VPK', 'CVP', 'CRP', 'PW'] as $mCode)
                                                @php
                                                    $checkedMethods = $u['methods'] ?? ['CL', 'DPT'];
                                                @endphp
                                                <label class="flex items-center space-x-1 cursor-pointer">
                                                    <input type="checkbox" name="work_groups[{{ $gIdx }}][units][{{ $uIdx }}][methods][]" value="{{ $mCode }}"
                                                        {{ in_array($mCode, $checkedMethods, true) ? 'checked' : '' }}
                                                        @disabled($plan->is_locked)
                                                        class="rounded border-slate-300 text-brand-600">
                                                    <span>{{ $mCode }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        {{-- BAGIAN 3: Mengidentifikasi Persyaratan Modifikasi dan Kontekstualisasi --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-5">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-slate-900">3. Mengidentifikasi Persyaratan Modifikasi & Kontekstualisasi</h2>
                <p class="text-xs text-slate-500">Pilihan 'Ada' wajib melampirkan rincian kebutuhan / penyesuaian yang diterapkan</p>
            </div>

            @php
                $modQuestions = [
                    'special_characteristics' => [
                        'code' => '3.1.a',
                        'title' => 'Karakteristik Khusus Asesi',
                        'key' => 'has_special_characteristics',
                        'notes' => 'special_characteristics_notes',
                        'placeholder' => 'Tuliskan karakteristik khusus asesi jika ada (misal: disabilitas fisik, kendala bahasa)...',
                    ],
                    'workplace_context' => [
                        'code' => '3.1.b',
                        'title' => 'Kebutuhan Kontekstualisasi Terkait Tempat Kerja',
                        'key' => 'has_workplace_context',
                        'notes' => 'workplace_context_notes',
                        'placeholder' => 'Tuliskan kebutuhan kontekstualisasi tempat kerja jika ada...',
                    ],
                    'training_package' => [
                        'code' => '3.2',
                        'title' => 'Saran dari Paket Pelatihan / Pengembang Pelatihan',
                        'key' => 'has_training_package_suggestions',
                        'notes' => 'training_package_suggestions_notes',
                        'placeholder' => 'Tuliskan saran khusus dari paket pelatihan jika ada...',
                    ],
                    'instrument_adjustments' => [
                        'code' => '3.3',
                        'title' => 'Penyesuaian Perangkat Asesmen Terkait Kontekstualisasi',
                        'key' => 'has_instrument_adjustments',
                        'notes' => 'instrument_adjustments_notes',
                        'placeholder' => 'Tuliskan penyesuaian instrumen atau peralatan yang disesuaikan...',
                    ],
                    'integrated_assessment' => [
                        'code' => '3.4',
                        'title' => 'Peluang Kegiatan Asesmen Terintegrasi',
                        'key' => 'has_integrated_assessment_opportunities',
                        'notes' => 'integrated_assessment_notes',
                        'placeholder' => 'Tuliskan kegiatan terintegrasi lintas unit kompetensi...',
                    ],
                ];
            @endphp

            <div class="space-y-4">
                @foreach($modQuestions as $q)
                    @php
                        $val = old("modifications.{$q['key']}", $modifs[$q['key']] ?? null);
                        $noteVal = old("modifications.{$q['notes']}", $modifs[$q['notes']] ?? '');
                    @endphp
                    <div class="border border-slate-200 rounded-lg p-4 space-y-2.5" x-data="{ decision: '{{ $val }}' }">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-800">{{ $q['code'] }} {{ $q['title'] }}</span>
                            <div class="flex items-center space-x-4 text-xs font-semibold">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="modifications[{{ $q['key'] }}]" value="ada" x-model="decision"
                                        {{ $val === 'ada' ? 'checked' : '' }} @disabled($plan->is_locked)
                                        class="text-brand-600 focus:ring-brand-500">
                                    <span class="ml-1.5 text-slate-700">Ada</span>
                                </label>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="modifications[{{ $q['key'] }}]" value="tidak_ada" x-model="decision"
                                        {{ $val === 'tidak_ada' ? 'checked' : '' }} @disabled($plan->is_locked)
                                        class="text-brand-600 focus:ring-brand-500">
                                    <span class="ml-1.5 text-slate-700">Tidak Ada</span>
                                </label>
                            </div>
                        </div>

                        <div x-show="decision === 'ada'" x-cloak>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Rincian Penjelasan (Wajib diisi jika Ada)</label>
                            <textarea name="modifications[{{ $q['notes'] }}]" rows="2" placeholder="{{ $q['placeholder'] }}"
                                @disabled($plan->is_locked)
                                class="w-full px-3 py-2 rounded border border-slate-300 text-xs focus:ring-2 focus:ring-brand-500">{{ $noteVal }}</textarea>
                            @error("modifications.{$q['notes']}")
                                <p class="text-xs text-rose-600 mt-0.5">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- BAGIAN 4: Konfirmasi dan Penyusun/Validator --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-5">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-slate-900">4. Konfirmasi Penyusun & Validator</h2>
                <p class="text-xs text-slate-500">Identitas asesor penyusun rencana dan pengesah validator resmi LSP</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                <div class="bg-slate-50 p-3.5 rounded-lg border border-slate-200 space-y-2">
                    <span class="font-bold text-slate-800 uppercase tracking-wider block">Asesor Penyusun</span>
                    <p class="text-slate-700">Nama: <strong>{{ auth()->user()->full_name }}</strong></p>
                    <p class="text-slate-600">No. MET: {{ auth()->user()->assessor_registration_number ?? auth()->user()->asesorProfile?->no_met ?? 'Tercatat' }}</p>
                    <p class="text-slate-500">Tanggal: {{ now()->translatedFormat('d F Y') }}</p>
                </div>

                <div class="bg-slate-50 p-3.5 rounded-lg border border-slate-200 space-y-2">
                    <span class="font-bold text-slate-800 uppercase tracking-wider block">Status Pengesahan</span>
                    <p class="text-slate-700">Status Dokumen: <strong class="uppercase">{{ $plan->status ?? 'DRAFT' }}</strong></p>
                    @if($plan->validated_by)
                        <p class="text-emerald-700">Tervalidasi oleh: {{ $plan->validator?->full_name }} ({{ $plan->validated_at?->format('d/m/Y') }})</p>
                    @else
                        <p class="text-slate-400">Menunggu review / validasi Manajer Sertifikasi LSP.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-between bg-white p-4 rounded-xl border border-slate-200">
            <div>
                @if($plan->exists && ! $plan->is_locked)
                    <button type="submit" form="lockPlanForm" class="px-4 py-2 text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg transition">
                        Kunci Rencana Asesmen (Lock)
                    </button>
                @endif
            </div>

            <div class="flex items-center space-x-3">
                <a href="{{ route('asesor.jadwal.asesi', $schedule->id) }}" class="px-5 py-2.5 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition">
                    Kembali
                </a>

                @if(! $plan->is_locked)
                    <button type="submit" class="px-6 py-2.5 text-xs font-bold bg-brand-600 hover:bg-brand-700 text-white rounded-lg shadow-sm transition">
                        Simpan Rencana Asesmen (FR.MAPA.01)
                    </button>
                @endif
            </div>
        </div>
    </form>

    @if($plan->exists && ! $plan->is_locked)
        <form id="lockPlanForm" action="{{ route('asesor.mapa01.lock', $plan->id) }}" method="POST" class="hidden">
            @csrf
        </form>
    @endif
</div>
@endsection
