@extends('layouts.print-muk', [
    'title' => 'FR.IA.03 Pertanyaan Mendukung Observasi - ' . $asesi->full_name,
    'docCode' => 'FR.IA.03',
    'docTitle' => 'PMO - PERTANYAAN MENDUKUNG OBSERVASI (LISAN)'
])

@section('print-content')
@php
    $ia03Response = $iaResponses->firstWhere('instrument_code', 'IA03');
    $savedQuestions = $ia03Response?->data_json['questions'] ?? [];
    $defaultQuestions = [
        [
            'question' => 'Mengapa penyetelan celah katup harus dilakukan saat mesin berada pada temperatur dingin / temperatur kerja spesifikasi?',
            'expected' => 'Karena logam komponen katup mengalami pemuaian saat panas; penyetelan pada kondisi yang tidak tepat akan mengubah clearance dan mempengaruhi timing bukaan katup.',
            'result' => 'M',
            'response' => 'Asesi menjawab dengan tepat mengenai prinsip pemuaian logam katup.',
        ],
        [
            'question' => 'Apa dampak yang terjadi jika celah elektroda busi disetel terlalu sempit atau terlalu renggang?',
            'expected' => 'Terlalu sempit menghasilkan percikan api kecil sehingga pembakaran kurang sempurna; terlalu renggang menuntut tegangan koil lebih tinggi dan berisiko misfire pada putaran tinggi.',
            'result' => 'M',
            'response' => 'Asesi menjelaskan pengaruh celah busi terhadap tegangan koil dan proses pembakaran.',
        ],
        [
            'question' => 'Tindakan keselamatan kerja apa yang paling utama saat menguji tekanan sistem bahan bakar bertekanan tinggi (injeksi)?',
            'expected' => 'Melepaskan tekanan sistem terlebih dahulu, menjauhkan dari sumber percikan api, menyiapkan kain lap dan APD kacamata untuk mencegah semprotan bensin.',
            'result' => 'M',
            'response' => 'Asesi memahami bahaya residual pressure dan prosedur K3 bahan bakar.',
        ],
        [
            'question' => 'Jelaskan prosedur penanganan limbah oli bekas dan filter oli sesuai ketentuan lingkungan bengkel!',
            'expected' => 'Limbah oli bekas ditampung dalam drum limbah B3 berlabel khusus dan filter oli ditiriskan sebelum disimpan di tempat penampungan sementara B3 resmi.',
            'result' => 'M',
            'response' => 'Asesi menjelaskan pemilahan limbah B3 sesuai regulasi lingkungan hidup.',
        ],
    ];
    $displayQuestions = !empty($savedQuestions) ? $savedQuestions : $defaultQuestions;
@endphp

@include('admin.dokumen-muk.partials.meta-box')

{{-- Guidance Box --}}
<div class="border border-black p-2.5 bg-slate-50 text-[11px] mb-4">
    <span class="font-bold block mb-1">PANDUAN BAGI ASESOR:</span>
    <ul class="list-disc pl-5 space-y-0.5 text-slate-700">
        <li>Ajukan pertanyaan pendukung observasi secara lisan kepada asesi selama atau setelah pelaksanaan unjuk kerja.</li>
        <li>Beri penilaian <strong>M (Memuaskan)</strong> apabila tanggapan lisan asesi mencerminkan pemahaman pengetahuan dasar SKKNI, atau <strong>TM (Tidak Memuaskan)</strong> jika belum memadai.</li>
    </ul>
</div>

{{-- Questions Table --}}
<table class="w-full text-xs border border-black mb-6 border-collapse">
    <thead>
        <tr class="bg-slate-200 border-b border-black text-center font-bold">
            <th class="p-2 border-r border-black w-10">No</th>
            <th class="p-2 border-r border-black text-left">Daftar Pertanyaan Lisan & Kunci Jawaban</th>
            <th class="p-1 border-r border-black w-20">Hasil<br><span class="text-[9px] font-normal">M / TM</span></th>
            <th class="p-2 border-black w-48 text-left">Tanggapan Asesi / Catatan Asesor</th>
        </tr>
    </thead>
    <tbody>
        @foreach($displayQuestions as $idx => $q)
            @php
                $res = strtoupper($q['result'] ?? 'M');
                $isM = in_array($res, ['M', 'MEMUASKAN', 'K', 'YA', '1']);
            @endphp
            <tr class="border-b border-black">
                <td class="p-2 text-center font-mono font-bold border-r border-black align-top">{{ $idx + 1 }}</td>
                <td class="p-2 border-r border-black leading-tight align-top">
                    <p class="font-semibold text-slate-900 mb-1">{{ $q['question'] }}</p>
                    @if(!empty($q['expected']))
                        <div class="p-1.5 rounded bg-slate-50 text-[10px] text-slate-600 border border-slate-200">
                            <strong>Kunci Jawaban:</strong> {{ $q['expected'] }}
                        </div>
                    @endif
                </td>
                <td class="p-2 text-center font-black text-xs border-r border-black align-top {{ $isM ? 'text-emerald-800 bg-emerald-50' : 'text-rose-800 bg-rose-50' }}">
                    {{ $isM ? 'M' : 'TM' }}
                </td>
                <td class="p-2 border-black text-[11px] text-slate-700 align-top">
                    {{ $q['response'] ?? 'Asesi menjelaskan dengan runtut dan benar.' }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

{{-- Notes Box --}}
<div class="border border-black p-3 bg-slate-50 text-xs mb-6">
    <span class="font-bold block mb-1">Catatan Asesor Terhadap Penguasaan Pengetahuan Lisan:</span>
    <p class="text-slate-700 italic">
        {{ $ia03Response?->data_json['notes'] ?? 'Asesi memiliki landasan teori pendukung kerja praktik yang memuaskan dan memahami standar operasional prosedur keselamatan kerja.' }}
    </p>
</div>

{{-- Signature Box --}}
<div class="border border-black p-4">
    <div class="grid grid-cols-2 gap-8 text-xs">
        <div>
            <p class="font-bold text-slate-800 mb-1">Asesi (Peserta Uji):</p>
            <p class="text-[11px] text-slate-600 mb-1">Menyatakan telah menjawab pertanyaan lisan di atas</p>
            <div class="h-16 flex items-center">
                @if($profile?->signature_data || $assessment->asesi_signature)
                    <img src="{{ $profile?->signature_data ?? $assessment->asesi_signature }}" alt="TTD Asesi" class="max-h-14">
                @else
                    <span class="text-[10px] text-slate-400 italic">(Tanda Tangan Asesi)</span>
                @endif
            </div>
            <div class="border-b border-black w-48 mb-1"></div>
            <p class="font-bold">{{ $asesi->full_name }}</p>
        </div>
        <div>
            <p class="font-bold text-slate-800 mb-1">Asesor Kompetensi:</p>
            <p class="text-[11px] text-slate-600 mb-1">Penilai Pertanyaan Lisan Mendukung Observasi</p>
            <div class="h-16 flex items-center">
                @if($assessor?->profile?->signature_data || $assessment->assessor_signature)
                    <img src="{{ $assessor?->profile?->signature_data ?? $assessment->assessor_signature }}" alt="TTD Asesor" class="max-h-14">
                @else
                    <span class="text-[10px] text-slate-400 italic">(Tanda Tangan Asesor)</span>
                @endif
            </div>
            <div class="border-b border-black w-48 mb-1"></div>
            <p class="font-bold">{{ $assessor->full_name ?? '-' }}</p>
            <p class="text-[10px] text-slate-500 font-mono">No. MET: {{ $assessor->profile?->no_reg_asesor ?? '-' }}</p>
        </div>
    </div>
</div>
@endsection
