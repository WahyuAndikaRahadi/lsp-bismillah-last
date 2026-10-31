@php
    $isCompleted = (bool) $response;
@endphp
@extends('layouts.app', ['title' => 'FR.IA.02 - Tugas Praktik Demonstrasi'])

@section('content')
<div class="space-y-6" x-data="{ isEditing: {{ $isCompleted ? 'false' : 'true' }} }">
    @include('asesor.ruang-uji.partials.header', [
        'assessment' => $assessment,
        'code' => 'ia02',
        'title' => 'FR.IA.02 – Tugas Praktik Demonstrasi (TPD)',
        'subtitle' => 'Penilaian pelaksanaan tugas unjuk kerja praktik asesi di TUK.',
        'isCompleted' => $isCompleted,
    ])

    <form action="{{ route('asesor.assessment.instrument.store', [$assessment->id, 'ia02']) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Skenario & Waktu Box --}}
        <div class="bg-gradient-to-r from-slate-900 to-lsp-navy text-white rounded-xl p-6 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-white/10 pb-4 mb-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-lsp-red bg-white/10 px-2.5 py-1 rounded">Skenario Praktik Demonstrasi</span>
                    <h2 class="text-lg font-bold text-white mt-1">{{ $scheme->name }}</h2>
                </div>
                <div class="bg-white/10 px-4 py-2 rounded-lg border border-white/20 text-center">
                    <div class="text-[10px] text-slate-300 uppercase tracking-wider font-semibold">Alokasi Waktu</div>
                    <div class="text-base font-black text-amber-300">{{ $tpdData['time_limit'] ?? '30 Menit' }}</div>
                </div>
            </div>
            <p class="text-sm text-slate-200 leading-relaxed">{{ $tpdData['scenario'] ?? 'Laksanakan instruksi kerja praktik unjuk kerja sesuai standar operasional prosedur bengkel resmi dan manual service.' }}</p>

            @if(!empty($tpdData['equipment']))
                <div class="mt-4 pt-4 border-t border-white/10">
                    <span class="text-xs font-bold text-amber-300">Peralatan dan Bahan Uji:</span>
                    <ul class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2 text-xs text-slate-300">
                        @foreach($tpdData['equipment'] as $eq)
                            <li class="flex items-center gap-2">
                                <span class="text-lsp-red font-bold">•</span> {{ $eq }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        {{-- Pemeriksaan Bukti Lembar Fisik / Kertas --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Pemeriksaan Bukti Lembar Fisik / Kertas — Tugas Praktik Demonstrasi (FR.IA.02)</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Asesi menyelesaikan demonstrasi pada lembar fisik dan mengunggah foto hasilnya ke sistem.</p>
                </div>
                <div>
                    @if(!empty($response?->data_json['evidence_image_path']))
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                            ✓ Lembar Jawaban Terunggah
                        </span>
                    @else
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                            Belum Ada Unggahan Foto
                        </span>
                    @endif
                </div>
            </div>

            @if(!empty($response?->data_json['evidence_image_path']))
                @php
                    $isPdf = str_ends_with(strtolower($response->data_json['evidence_image_path']), '.pdf');
                    $evidenceUrl = route('secure-file.evidence', $response);
                @endphp
                <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 flex flex-col md:flex-row items-center gap-6">
                    <div class="w-full md:w-48 h-36 bg-white border border-slate-200 rounded-lg overflow-hidden flex items-center justify-center shrink-0">
                        @if($isPdf)
                            <div class="text-center p-4">
                                <span class="text-3xl">📄</span>
                                <span class="block text-xs font-bold text-slate-700 mt-1">Dokumen PDF</span>
                            </div>
                        @else
                            <img src="{{ $evidenceUrl }}" alt="Bukti Lembar Jawaban Asesi" class="w-full h-full object-cover">
                        @endif
                    </div>
                    <div class="space-y-2 flex-1">
                        <div class="text-xs text-slate-500">Nama File: <strong class="text-slate-800">{{ $response->data_json['evidence_original_name'] ?? basename($response->data_json['evidence_image_path']) }}</strong></div>
                        <div class="text-xs text-slate-500">Diunggah: <strong class="text-slate-800">{{ !empty($response->data_json['evidence_uploaded_at']) ? \Carbon\Carbon::parse($response->data_json['evidence_uploaded_at'])->translatedFormat('d F Y, H:i') : '-' }}</strong></div>
                        <div class="pt-2 flex items-center gap-2">
                            <a href="{{ $evidenceUrl }}" target="_blank" class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-lg shadow-sm transition">
                                Buka Lembar Fisik Penuh &nearr;
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-amber-50/70 border border-amber-200 rounded-lg p-4 text-xs text-amber-900 leading-relaxed">
                    Asesi belum mengunggah lembar kerja fisik. Asesor tetap dapat menilai langsung berdasarkan observasi fisik di TUK.
                </div>
            @endif
        </div>

        {{-- Task List Table --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                <h2 class="text-base font-bold text-slate-900">Daftar Langkah Kerja Demonstrasi</h2>
                <p class="text-xs text-slate-500 mt-0.5">Beri tanda centang pada capaian unjuk kerja asesi untuk setiap instruksi tugas</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-100 text-xs uppercase font-mono text-slate-700 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3 w-16">No</th>
                            <th class="px-6 py-3">Instruksi Tugas Praktik / Standar Kinerja</th>
                            <th class="px-6 py-3 w-36 text-center">Capaian (Ya / Tidak)</th>
                            <th class="px-6 py-3 w-48">Catatan Asesor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($tasks ?? [] as $i => $task)
                            @php
                                $val = $savedAnswers[$task->id]['result'] ?? old("tasks.{$task->id}.result");
                                $note = $savedAnswers[$task->id]['note'] ?? old("tasks.{$task->id}.note");
                            @endphp
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-6 py-3 text-slate-500 font-mono text-xs">{{ $i + 1 }}</td>
                                <td class="px-6 py-3 text-slate-800">
                                    <div class="font-medium text-sm">{{ $task->title ?? $task->instruction }}</div>
                                    @if(isset($task->standard))
                                        <div class="text-xs text-slate-500 mt-0.5">Standar Kinerja: {{ $task->standard }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <div class="flex items-center justify-center space-x-4">
                                        <label class="p-2 inline-flex items-center cursor-pointer">
                                            <input type="radio" name="tasks[{{ $task->id }}][result]" value="YA" required :disabled="!isEditing" class="task-radio task-radio-ya text-emerald-600 focus:ring-emerald-500 w-5 h-5 cursor-pointer" {{ $val === 'YA' ? 'checked' : '' }}>
                                            <span class="ml-1.5 text-xs font-bold text-emerald-700">Ya</span>
                                        </label>
                                        <label class="p-2 inline-flex items-center cursor-pointer">
                                            <input type="radio" name="tasks[{{ $task->id }}][result]" value="TIDAK" required :disabled="!isEditing" class="task-radio task-radio-tidak text-rose-600 focus:ring-rose-500 w-5 h-5 cursor-pointer" {{ $val === 'TIDAK' ? 'checked' : '' }}>
                                            <span class="ml-1.5 text-xs font-bold text-rose-700">Tidak</span>
                                        </label>
                                    </div>
                                </td>
                                <td class="px-6 py-3">
                                    <input type="text" name="tasks[{{ $task->id }}][note]" value="{{ $note }}" :disabled="!isEditing" placeholder="Catatan..." class="w-full px-2.5 py-1.5 text-xs rounded border border-slate-300 focus:ring-1 focus:ring-brand-500 focus:outline-none">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-slate-500 text-xs">Belum ada tugas praktik yang dikonfigurasi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @include('asesor.ruang-uji.partials.signatures', [
            'assessment' => $assessment,
            'savedGeneralNotes' => $savedGeneralNotes,
            'savedSignature' => $savedSignature,
        ])

        @include('asesor.ruang-uji.partials.actions', [
            'assessment' => $assessment,
            'code' => 'ia02',
            'isCompleted' => $isCompleted,
            'nextCandidate' => $nextCandidate,
        ])
    </form>
</div>
@endsection
