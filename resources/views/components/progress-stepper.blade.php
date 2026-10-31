@props([
    'currentStep' => 'apl',
    'assessment' => null,
    'selectedInstruments' => null,
])

@php
    $instruments = $selectedInstruments ?? ($assessment?->schedule?->selected_instruments ?? ['AK01', 'AK07', 'IA01', 'IA02', 'IA03', 'IA05']);
    if (is_string($instruments)) {
        $instruments = json_decode($instruments, true) ?: [];
    }

    // Build ordered list of steps
    $allSteps = [
        [
            'code' => 'apl',
            'title' => 'FR.APL.01 & 02',
            'subtitle' => 'Pendaftaran & Asesmen Mandiri',
            'badge' => 'Syarat Utama',
        ],
        [
            'code' => 'AK01',
            'title' => 'FR.AK.01',
            'subtitle' => 'Persetujuan Asesmen & Kerahasiaan',
            'badge' => 'Persetujuan',
        ],
    ];

    if (in_array('AK07', $instruments)) {
        $allSteps[] = [
            'code' => 'AK07',
            'title' => 'FR.AK.07',
            'subtitle' => 'Penyesuaian Wajar',
            'badge' => 'Kondisi Khusus',
        ];
    }

    if (in_array('IA01', $instruments)) {
        $allSteps[] = [
            'code' => 'IA01',
            'title' => 'FR.IA.01',
            'subtitle' => 'Ceklis Observasi Praktik',
            'badge' => 'Praktik Langsung',
        ];
    }

    if (in_array('IA02', $instruments)) {
        $allSteps[] = [
            'code' => 'IA02',
            'title' => 'FR.IA.02',
            'subtitle' => 'Tugas Praktik Demonstrasi',
            'badge' => 'Instruksi Kerja',
        ];
    }

    if (in_array('IA03', $instruments)) {
        $allSteps[] = [
            'code' => 'IA03',
            'title' => 'FR.IA.03',
            'subtitle' => 'Pertanyaan PMO',
            'badge' => 'Tanya Jawab Lisan',
        ];
    }

    if (in_array('IA05', $instruments)) {
        $allSteps[] = [
            'code' => 'IA05',
            'title' => 'FR.IA.05',
            'subtitle' => 'CBT Ujian Teori (PG)',
            'badge' => 'Ujian Mandiri',
        ];
    }

    $allSteps[] = [
        'code' => 'finish',
        'title' => 'FINISH',
        'subtitle' => 'Selesai & Rekomendasi',
        'badge' => 'Hasil Akhir',
    ];

    // Determine current index and completed steps
    $stepCodes = array_column($allSteps, 'code');
    $normalizedCurrent = strtolower($currentStep);
    
    // Find matching index
    $currentIndex = 0;
    foreach ($stepCodes as $idx => $code) {
        if (strtolower($code) === $normalizedCurrent) {
            $currentIndex = $idx;
            break;
        }
    }

    // Check if APL is approved
    $aplApproved = $assessment ? ($assessment->apl_status === 'APPROVED') : true;
    $isAssessmentCompleted = $assessment ? ($assessment->status === 'COMPLETED') : false;
@endphp

<div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
        <div>
            <h3 class="font-bold text-slate-800 text-sm">Alur Asesmen Kompetensi</h3>
            <p class="text-xs text-slate-500">Urutan instrumen uji LSP PrO Indo</p>
        </div>
        <span class="text-xs font-semibold px-2.5 py-1 bg-brand-50 text-brand-700 rounded-full">
            Step {{ $currentIndex + 1 }} / {{ count($allSteps) }}
        </span>
    </div>

    <div class="relative pl-6 space-y-6 before:absolute before:left-3 before:top-2 before:bottom-3 before:w-0.5 before:bg-slate-200">
        @foreach($allSteps as $idx => $step)
            @php
                $isCurrent = ($idx === $currentIndex) && !$isAssessmentCompleted;
                $isCompleted = ($idx < $currentIndex) || $isAssessmentCompleted;
                $isLocked = ($idx > $currentIndex) && !$isAssessmentCompleted;

                // Special lock: if APL not approved, all subsequent steps are locked
                if (!$aplApproved && $idx > 0) {
                    $isLocked = true;
                    if ($idx === $currentIndex) {
                        $isCurrent = false;
                    }
                }
            @endphp

            <div class="relative flex items-start group">
                {{-- Step Indicator Circle --}}
                <div class="absolute -left-6 mt-0.5 flex items-center justify-center">
                    @if($isCompleted)
                        <div class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center ring-4 ring-emerald-50 shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    @elseif($isCurrent)
                        <div class="w-6 h-6 rounded-full bg-brand-600 text-white flex items-center justify-center ring-4 ring-brand-100 shadow-sm animate-pulse">
                            <span class="w-2 h-2 rounded-full bg-white"></span>
                        </div>
                    @else
                        <div class="w-6 h-6 rounded-full bg-slate-100 text-slate-400 border border-slate-300 flex items-center justify-center text-[10px] font-bold">
                            {{ $idx + 1 }}
                        </div>
                    @endif
                </div>

                {{-- Step Text Content --}}
                <div class="ml-3 flex-1">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold {{ $isCurrent ? 'text-brand-700 font-extrabold' : ($isCompleted ? 'text-slate-900' : 'text-slate-400') }}">
                            {{ $step['title'] }}
                        </span>
                        <span class="text-[10px] px-1.5 py-0.5 rounded {{ $isCurrent ? 'bg-brand-100 text-brand-800 font-semibold' : ($isCompleted ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-400') }}">
                            {{ $step['badge'] }}
                        </span>
                    </div>
                    <p class="text-[11px] mt-0.5 leading-snug {{ $isCurrent ? 'text-slate-700 font-medium' : ($isCompleted ? 'text-slate-600' : 'text-slate-400') }}">
                        {{ $step['subtitle'] }}
                    </p>

                    @if($isCurrent)
                        <div class="mt-1 flex items-center text-[10px] font-semibold text-brand-600">
                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-brand-600 mr-1.5"></span>
                            Sedang Berlangsung
                        </div>
                    @elseif($isLocked)
                        <div class="mt-0.5 flex items-center text-[10px] text-slate-400">
                            <svg class="w-3 h-3 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Terkunci
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
