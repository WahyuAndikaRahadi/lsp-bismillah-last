@extends('layouts.app', ['title' => 'FR.AK.06 - Kaji Ulang Asesmen'])

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-xs">
        <h1 class="text-xl font-bold text-slate-900">FR.AK.06 Kaji Ulang Asesmen</h1>
        <p class="text-xs text-slate-500 mt-1">Jadwal: {{ $schedule->name }} ({{ $schedule->code }}) &bull; Skema: {{ $schedule->scheme?->name }}</p>
    </div>

    <form action="{{ route('asesor.schedule.ak06.store', $schedule->id) }}" method="POST" class="space-y-6">
        @csrf
        <input type="hidden" name="lock_version" value="{{ $report->lock_version ?? 1 }}">

        {{-- Procedure Matrix Table --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                <h2 class="text-sm font-bold text-slate-800">Matriks Evaluasi Prinsip Asesmen terhadap Tahapan Prosedur</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3">Tahapan Prosedur Asesmen</th>
                            <th class="px-3 py-3 text-center w-20">Valid</th>
                            <th class="px-3 py-3 text-center w-20">Reliabel</th>
                            <th class="px-3 py-3 text-center w-20">Fleksibel</th>
                            <th class="px-3 py-3 text-center w-20">Adil</th>
                            <th class="px-4 py-3">Catatan / Alasan N/A</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php
                            $matrix = $report->content_json['procedure_matrix'] ?? $defaultMatrix;
                        @endphp
                        @foreach($matrix as $idx => $row)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-slate-800">
                                    <input type="hidden" name="procedure_matrix[{{ $idx }}][name]" value="{{ $row['name'] }}">
                                    {{ $row['name'] }}
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <input type="text" name="procedure_matrix[{{ $idx }}][valid]" value="{{ old("procedure_matrix.{$idx}.valid", $row['valid'] ?? 'YA') }}" required class="w-16 text-center text-xs rounded border-slate-300">
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <input type="text" name="procedure_matrix[{{ $idx }}][reliabel]" value="{{ old("procedure_matrix.{$idx}.reliabel", $row['reliabel'] ?? 'YA') }}" required class="w-16 text-center text-xs rounded border-slate-300">
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <input type="text" name="procedure_matrix[{{ $idx }}][fleksibel]" value="{{ old("procedure_matrix.{$idx}.fleksibel", $row['fleksibel'] ?? 'YA') }}" required class="w-16 text-center text-xs rounded border-slate-300">
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <input type="text" name="procedure_matrix[{{ $idx }}][adil]" value="{{ old("procedure_matrix.{$idx}.adil", $row['adil'] ?? 'YA') }}" required class="w-16 text-center text-xs rounded border-slate-300">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" name="procedure_matrix[{{ $idx }}][notes]" value="{{ old("procedure_matrix.{$idx}.notes", $row['notes'] ?? '') }}" placeholder="Wajib diisi bila bernilai N/A" class="w-full text-xs rounded border-slate-300">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Dimensions of Competency Review --}}
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-xs space-y-4">
            <h2 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-2">Kaji Ulang 5 Dimensi Kompetensi</h2>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">1. Task Skills *</label>
                <textarea name="dimensions_review[task_skills]" rows="2" required class="w-full text-xs rounded-lg border-slate-300 focus:ring-brand-500">{{ old('dimensions_review.task_skills', $report->content_json['dimensions_review']['task_skills'] ?? '') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">2. Task Management Skills *</label>
                <textarea name="dimensions_review[task_management_skills]" rows="2" required class="w-full text-xs rounded-lg border-slate-300 focus:ring-brand-500">{{ old('dimensions_review.task_management_skills', $report->content_json['dimensions_review']['task_management_skills'] ?? '') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">3. Contingency Management Skills *</label>
                <textarea name="dimensions_review[contingency_management_skills]" rows="2" required class="w-full text-xs rounded-lg border-slate-300 focus:ring-brand-500">{{ old('dimensions_review.contingency_management_skills', $report->content_json['dimensions_review']['contingency_management_skills'] ?? '') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">4. Job Role / Environment Skills *</label>
                <textarea name="dimensions_review[job_role_environment_skills]" rows="2" required class="w-full text-xs rounded-lg border-slate-300 focus:ring-brand-500">{{ old('dimensions_review.job_role_environment_skills', $report->content_json['dimensions_review']['job_role_environment_skills'] ?? '') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">5. Transfer Skills *</label>
                <textarea name="dimensions_review[transfer_skills]" rows="2" required class="w-full text-xs rounded-lg border-slate-300 focus:ring-brand-500">{{ old('dimensions_review.transfer_skills', $report->content_json['dimensions_review']['transfer_skills'] ?? '') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Rujukan Bukti / Instrumen Asesmen</label>
                <textarea name="evidence_references" rows="2" class="w-full text-xs rounded-lg border-slate-300 focus:ring-brand-500">{{ old('evidence_references', $report->content_json['evidence_references'] ?? '') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Komentar / Rekomendasi Lead Asesor</label>
                <textarea name="comments" rows="2" class="w-full text-xs rounded-lg border-slate-300 focus:ring-brand-500">{{ old('comments', $report->content_json['comments'] ?? '') }}</textarea>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-5 py-2.5 bg-brand-900 hover:bg-brand-800 text-white rounded-lg text-xs font-bold shadow-xs transition">
                Simpan &amp; Serahkan Kaji Ulang FR.AK.06
            </button>
        </div>
    </form>
</div>
@endsection
