@extends('layouts.app')

@section('title', 'FR.IA.05 - Pertanyaan Tertulis Pilihan Ganda')

@section('content')
<div class="max-w-4xl mx-auto py-6 space-y-6">
    {{-- Header Dokumen Resmi MUK --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-300 overflow-hidden">
        <div class="border-b border-slate-300 p-5 bg-slate-50 text-slate-900">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200 pb-3 mb-3">
                <div class="font-extrabold text-sm tracking-wider uppercase text-lsp-navy">
                    Lembaga Sertifikasi Profesi Praktisi Otomotif LSP-PrO Indo
                </div>
                <div class="text-[11px] font-mono text-slate-500">
                    Sistem Sertifikasi BNSP
                </div>
            </div>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight">
                        FR.IA.05. PERTANYAAN TERTULIS PILIHAN GANDA
                    </h1>
                    <p class="text-xs text-slate-600 mt-1 italic">
                        Perangkat asesmen tertulis untuk mengukur penguasaan pengetahuan kompetensi profesi
                    </p>
                </div>
                <a href="{{ route('asesi.assessment.ruang-uji', $assessment) }}" class="text-xs text-lsp-navy hover:underline font-semibold flex items-center gap-1 self-start md:self-center">
                    <span>&larr;</span>
                    <span>Kembali ke Ruang Uji</span>
                </a>
            </div>
        </div>

        {{-- Metadata Table Sesuai MUK --}}
        <div class="p-5 bg-white border-b border-slate-300">
            <table class="w-full text-xs border border-slate-300 border-collapse">
                <tbody>
                    <tr>
                        <td class="w-1/4 bg-slate-100 font-bold px-3 py-2 border border-slate-300 text-slate-800">
                            Skema Sertifikasi
                        </td>
                        <td class="px-3 py-2 border border-slate-300 text-slate-900 font-semibold" colspan="3">
                            Judul : <strong class="text-lsp-navy">{{ $assessment->schedule->scheme->name }}</strong> &nbsp;|&nbsp;
                            Nomor : <span class="font-mono text-slate-700">{{ $assessment->schedule->scheme->code }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="w-1/4 bg-slate-100 font-bold px-3 py-2 border border-slate-300 text-slate-800">
                            Nama Asesi
                        </td>
                        <td class="w-1/4 px-3 py-2 border border-slate-300 text-slate-900 font-semibold">
                            {{ auth()->user()->full_name }}
                        </td>
                        <td class="w-1/4 bg-slate-100 font-bold px-3 py-2 border border-slate-300 text-slate-800">
                            Nama Asesor
                        </td>
                        <td class="w-1/4 px-3 py-2 border border-slate-300 text-slate-900 font-semibold">
                            {{ $assessment->assessor?->full_name ?? 'Tim Asesor LSP' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="bg-slate-100 font-bold px-3 py-2 border border-slate-300 text-slate-800">
                            TUK / Waktu
                        </td>
                        <td class="px-3 py-2 border border-slate-300 text-slate-900" colspan="3">
                            {{ $assessment->schedule->tuk_name }} &nbsp;|&nbsp; {{ \Carbon\Carbon::parse($assessment->schedule->date)->translatedFormat('d F Y') }} (Durasi: {{ $assessment->schedule->duration_minutes ?? 120 }} menit)
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        @php
            $existingResp = $assessment->iaResponses->firstWhere('instrument_code', 'IA05');
            $isSubmitted = $existingResp && !empty($existingResp->asesi_signed_at);
            $savedAnswers = is_array($existingResp?->data_json) ? $existingResp->data_json : (json_decode($existingResp?->data_json ?? '[]', true) ?: []);
        @endphp

        {{-- Skor Banner jika sudah selesai --}}
        @if($isSubmitted)
            <div class="p-6 bg-slate-50 border-b border-slate-300 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Ujian CBT Telah Selesai Dikerjakan</h3>
                    <p class="text-xs text-slate-600 mt-0.5">
                        Diserahkan pada {{ \Carbon\Carbon::parse($existingResp->asesi_signed_at)->translatedFormat('d F Y, H:i') }} WIB.
                    </p>
                </div>
                <div class="text-right flex items-center gap-3">
                    <span class="text-xs font-semibold text-slate-600">Hasil Penilaian:</span>
                    <span class="px-3 py-1 rounded text-sm font-black {{ $existingResp->score >= 75 ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-rose-100 text-rose-800 border border-rose-300' }}">
                        Skor: {{ $existingResp->score }}% ({{ $existingResp->score >= 75 ? 'KOMPETEN' : 'BELUM KOMPETEN' }})
                    </span>
                </div>
            </div>
        @endif
    </div>

    @if($questions->isEmpty())
        <div class="bg-amber-50 border border-amber-300 rounded-xl p-8 text-center text-amber-900 text-xs font-semibold">
            Belum ada bank soal pertanyaan tertulis (FR.IA.05) yang ditetapkan untuk skema ini. Silakan hubungi asesor penguji atau admin LSP.
        </div>
    @else
        <form action="{{ route('asesi.assessment.instrument.ia05.submit', $assessment) }}" method="POST" id="cbtForm" class="space-y-6">
            @csrf

            {{-- Auto-save status indicator --}}
            @if(! $isSubmitted)
                <div class="sticky top-2 z-20 bg-white/95 backdrop-blur-sm border border-slate-300 rounded-lg p-3 shadow-sm flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2">
                        <span id="saveStatusIndicator" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                            <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                            <span>Auto-save Aktif</span>
                        </span>
                        <span id="saveStatusText" class="text-slate-600 text-[11px]">Jawaban tersimpan otomatis di perangkat & server</span>
                    </div>
                    <div class="text-[11px] font-bold text-slate-700">
                        Terjawab: <span id="answeredCount" class="font-mono text-lsp-navy">0</span> / {{ $questions->count() }} Soal
                    </div>
                </div>
            @endif

            {{-- Daftar Soal Pilihan Ganda --}}
            <div class="space-y-4">
                @foreach($questions as $index => $q)
                    @php
                        $savedAns = $savedAnswers[$q->id] ?? null;
                    @endphp
                    <div class="bg-white rounded-xl border border-slate-300 p-5 shadow-xs question-card" data-question-id="{{ $q->id }}">
                        <div class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-7 h-7 rounded bg-slate-100 border border-slate-300 text-slate-800 font-bold text-xs flex items-center justify-center font-mono">
                                {{ $loop->iteration }}
                            </span>
                            <div class="flex-grow space-y-3">
                                <p class="text-xs sm:text-sm font-semibold text-slate-900 leading-relaxed">
                                    {{ $q->question_text }}
                                </p>

                                @if($q->question_image)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $q->question_image) }}" alt="Lampiran Soal" class="max-h-60 rounded border border-slate-300">
                                    </div>
                                @endif

                                <div class="grid grid-cols-1 gap-2 pt-1">
                                    @foreach(['A' => $q->option_a, 'B' => $q->option_b, 'C' => $q->option_c, 'D' => $q->option_d] as $optKey => $optText)
                                        @if($optText)
                                            <label class="flex items-center gap-3 p-2.5 rounded border border-slate-300 hover:bg-slate-50 cursor-pointer transition text-xs option-label" data-opt="{{ $optKey }}">
                                                <input type="radio" 
                                                       name="answers[{{ $q->id }}]" 
                                                       value="{{ $optKey }}" 
                                                       class="cbt-radio text-lsp-navy focus:ring-lsp-navy w-4 h-4"
                                                       data-qid="{{ $q->id }}"
                                                       @checked($savedAns === $optKey) 
                                                       @disabled($isSubmitted)>
                                                <span class="font-bold text-slate-800 w-4 font-mono">{{ $optKey }}.</span>
                                                <span class="text-slate-800 leading-normal">{{ $optText }}</span>
                                            </label>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Submit Footer --}}
            <div class="p-5 bg-white rounded-xl border border-slate-300 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm">
                <span class="text-xs text-slate-600">
                    @if(! $isSubmitted)
                        Periksa kembali seluruh jawaban Anda. Sistem akan langsung mengoreksi dan merekam hasil ujian secara objektif.
                    @else
                        Ujian telah diselesaikan. Anda dapat melihat kembali jawaban dan rekapitulasi penilaian.
                    @endif
                </span>

                @if(! $isSubmitted)
                    <button type="submit" 
                            id="submitCbtBtn" 
                            class="w-full sm:w-auto px-8 py-3 bg-lsp-navy hover:bg-lsp-navy-dark text-white font-bold rounded-lg shadow-sm transition flex items-center justify-center gap-2"
                            onclick="return confirm('Apakah Anda yakin ingin menyelesaikan dan mengirim ujian CBT ini?')">
                        <span>Kirim Jawaban CBT</span>
                        <span>&check;</span>
                    </button>
                @endif
            </div>
        </form>

        {{-- Client-Side Auto-Save & Draft Persistence Engine --}}
        @if(! $isSubmitted)
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const assessmentId = {{ $assessment->id }};
                    const storageKey = 'cbt_draft_' + assessmentId;
                    const saveDraftUrl = '{{ route("asesi.assessment.ia05.save-draft", $assessment) }}';
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const answeredCountEl = document.getElementById('answeredCount');
                    const saveStatusTextEl = document.getElementById('saveStatusText');
                    const radios = document.querySelectorAll('.cbt-radio');
                    let debounceTimer = null;

                    // 1. Restore answers from localStorage or backend
                    function restoreAnswers() {
                        let localData = {};
                        try {
                            const raw = localStorage.getItem(storageKey);
                            if (raw) localData = JSON.parse(raw);
                        } catch (e) {
                            console.warn('LocalStorage unavailable', e);
                        }

                        // Apply localStorage values if checked isn't already set
                        radios.forEach(radio => {
                            const qid = radio.dataset.qid;
                            if (localData[qid] && localData[qid] === radio.value) {
                                radio.checked = true;
                            }
                        });

                        updateAnsweredCounter();
                    }

                    // 2. Count answered questions
                    function updateAnsweredCounter() {
                        const checkedQids = new Set();
                        radios.forEach(r => {
                            if (r.checked) checkedQids.add(r.dataset.qid);
                        });
                        if (answeredCountEl) answeredCountEl.textContent = checkedQids.size;
                    }

                    // 3. Collect current state
                    function getCurrentAnswers() {
                        const answers = {};
                        radios.forEach(r => {
                            if (r.checked) answers[r.dataset.qid] = r.value;
                        });
                        return answers;
                    }

                    // 4. Save to localStorage + AJAX debounce
                    function saveAnswers() {
                        const answers = getCurrentAnswers();
                        try {
                            localStorage.setItem(storageKey, JSON.stringify(answers));
                        } catch (e) {}

                        updateAnsweredCounter();

                        if (saveStatusTextEl) {
                            saveStatusTextEl.textContent = 'Menyimpan ke server...';
                        }

                        clearTimeout(debounceTimer);
                        debounceTimer = setTimeout(() => {
                            fetch(saveDraftUrl, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({ answers: answers })
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (saveStatusTextEl) {
                                    const now = new Date();
                                    const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                                    saveStatusTextEl.textContent = 'Tersimpan otomatis (' + timeStr + ')';
                                }
                            })
                            .catch(err => {
                                if (saveStatusTextEl) {
                                    saveStatusTextEl.textContent = 'Tersimpan lokal di perangkat';
                                }
                            });
                        }, 500);
                    }

                    // 5. Attach change listeners
                    radios.forEach(radio => {
                        radio.addEventListener('change', saveAnswers);
                    });

                    // 6. On submit, clear localStorage
                    const form = document.getElementById('cbtForm');
                    if (form) {
                        form.addEventListener('submit', function () {
                            try {
                                localStorage.removeItem(storageKey);
                            } catch (e) {}
                        });
                    }

                    // Initial run
                    restoreAnswers();
                });
            </script>
        @endif
    @endif
</div>
@endsection
