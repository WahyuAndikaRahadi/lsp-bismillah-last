@extends('layouts.app', ['title' => 'FR.AK.02 Rekaman Asesmen Kompetensi'])

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('asesor.jadwal.asesi', $assessment->schedule_id) }}" class="text-xs text-brand-600 hover:text-brand-700 font-semibold">&larr; Kembali ke Daftar Asesi</a>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">FR.AK.02 Rekaman Asesmen Kompetensi</h1>
            <p class="text-xs text-slate-500">Rekapitulasi bukti dan penetapan rekomendasi hasil asesmen kompetensi</p>
        </div>
        <div class="flex items-center space-x-2">
            <span class="px-3 py-1 text-xs font-bold rounded-lg border {{ $assessment->final_result === 'KOMPETEN' ? 'bg-emerald-50 text-emerald-700 border-emerald-300' : 'bg-amber-50 text-amber-700 border-amber-300' }}">
                Status: {{ $assessment->final_result ?? 'BELUM SELESAI' }}
            </span>
        </div>
    </div>

    {{-- Info Card --}}
    <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <div>
                <span class="text-slate-500 font-medium">Skema Sertifikasi:</span>
                <span class="font-bold text-slate-900 ml-2">{{ $assessment->schedule->scheme->name }} ({{ $assessment->schedule->scheme->code }})</span>
            </div>
            <div>
                <span class="text-slate-500 font-medium">TUK:</span>
                <span class="font-bold text-slate-900 ml-2">{{ $assessment->schedule->tuk_name }}</span>
            </div>
            <div>
                <span class="text-slate-500 font-medium">Nama Asesi:</span>
                <span class="font-bold text-slate-900 ml-2">{{ $assessment->asesi->full_name }} ({{ $assessment->asesi->profile?->nik ?? '-' }})</span>
            </div>
            <div>
                <span class="text-slate-500 font-medium">Nama Asesor:</span>
                <span class="font-bold text-slate-900 ml-2">{{ auth()->user()->full_name }} (No. MET: {{ auth()->user()->assessor_registration_number ?? '-' }})</span>
            </div>
        </div>
    </div>

    {{-- Form AK.02 --}}
    <form action="{{ route('asesor.assessment.ak02.update', $assessment->id) }}" method="POST" class="space-y-6">
        @csrf

        {{-- Units Table with Methods Checkboxes --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden" x-data="{
            setAllUnits(val) {
                document.querySelectorAll('.unit-result-select').forEach(el => el.value = val);
                const finalSel = document.getElementById('finalResultSelect');
                if (finalSel) {
                    finalSel.value = (val == 1) ? 'KOMPETEN' : 'BELUM_KOMPETEN';
                }
            },
            toggleMethod(name) {
                const boxes = document.querySelectorAll(`input[name*='[${name}]']`);
                const allChecked = Array.from(boxes).every(b => b.checked);
                boxes.forEach(b => b.checked = !allChecked);
            }
        }">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Unit Kompetensi & Metode Asesmen yang Digunakan</h2>
                    <p class="text-xs text-slate-500">Beri tanda centang pada metode pengumpulan bukti yang diperoleh untuk menentukan kompetensi asesi</p>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <button type="button" @click="setAllUnits(1)" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white rounded text-xs font-bold shadow-xs transition flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        Semua K
                    </button>
                    <button type="button" @click="setAllUnits(0)" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white rounded text-xs font-bold shadow-xs transition flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                        Semua BK
                    </button>
                    <button type="button" @click="toggleMethod('observasi_demonstrasi')" class="px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded text-xs font-medium transition">
                        + Observasi
                    </button>
                    <button type="button" @click="toggleMethod('tulis')" class="px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded text-xs font-medium transition">
                        + Tulis (CBT)
                    </button>
                    <button type="button" @click="toggleMethod('lisan')" class="px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded text-xs font-medium transition">
                        + Lisan (PMO)
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-center w-10">No</th>
                            <th class="px-4 py-3 text-left min-w-[200px]">Kode & Judul Unit Kompetensi</th>
                            <th class="px-2 py-3 text-center">Observasi Praktik</th>
                            <th class="px-2 py-3 text-center">Portofolio</th>
                            <th class="px-2 py-3 text-center">Lisan (PMO)</th>
                            <th class="px-2 py-3 text-center">Tulis (CBT)</th>
                            <th class="px-2 py-3 text-center">Pihak Ketiga</th>
                            <th class="px-3 py-3 text-center w-32">Hasil Unit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($units as $idx => $unit)
                            @php
                                $item = $ak02Items->get($unit->id);
                                $methods = $item?->methods_json ?? [];
                                $isCompetent = $item ? $item->is_competent : true;
                            @endphp
                            <tr class="hover:bg-slate-50/60">
                                <td class="px-4 py-3 text-center text-slate-500 font-mono">{{ $idx + 1 }}</td>
                                <td class="px-4 py-3">
                                    <span class="font-mono font-bold text-slate-800 text-[11px] block">{{ $unit->code }}</span>
                                    <span class="text-slate-700">{{ $unit->title }}</span>
                                </td>
                                <td class="px-2 py-3 text-center">
                                    <label class="p-2 inline-flex items-center justify-center cursor-pointer">
                                        <input type="checkbox" name="units[{{ $unit->id }}][observasi_demonstrasi]" value="1" {{ !empty($methods['observasi_demonstrasi']) ? 'checked' : '' }} class="w-5 h-5 rounded border-slate-300 text-brand-600 focus:ring-brand-500 cursor-pointer">
                                    </label>
                                </td>
                                <td class="px-2 py-3 text-center">
                                    <label class="p-2 inline-flex items-center justify-center cursor-pointer">
                                        <input type="checkbox" name="units[{{ $unit->id }}][portofolio]" value="1" {{ !empty($methods['portofolio']) ? 'checked' : '' }} class="w-5 h-5 rounded border-slate-300 text-brand-600 focus:ring-brand-500 cursor-pointer">
                                    </label>
                                </td>
                                <td class="px-2 py-3 text-center">
                                    <label class="p-2 inline-flex items-center justify-center cursor-pointer">
                                        <input type="checkbox" name="units[{{ $unit->id }}][lisan]" value="1" {{ !empty($methods['lisan']) ? 'checked' : '' }} class="w-5 h-5 rounded border-slate-300 text-brand-600 focus:ring-brand-500 cursor-pointer">
                                    </label>
                                </td>
                                <td class="px-2 py-3 text-center">
                                    <label class="p-2 inline-flex items-center justify-center cursor-pointer">
                                        <input type="checkbox" name="units[{{ $unit->id }}][tulis]" value="1" {{ !empty($methods['tulis']) ? 'checked' : '' }} class="w-5 h-5 rounded border-slate-300 text-brand-600 focus:ring-brand-500 cursor-pointer">
                                    </label>
                                </td>
                                <td class="px-2 py-3 text-center">
                                    <label class="p-2 inline-flex items-center justify-center cursor-pointer">
                                        <input type="checkbox" name="units[{{ $unit->id }}][pihak_ketiga]" value="1" {{ !empty($methods['pihak_ketiga']) ? 'checked' : '' }} class="w-5 h-5 rounded border-slate-300 text-brand-600 focus:ring-brand-500 cursor-pointer">
                                    </label>
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <select name="units[{{ $unit->id }}][is_competent]" class="unit-result-select text-xs font-bold rounded border-slate-300 focus:ring-brand-500 py-1.5 px-2 w-full {{ $isCompetent ? 'text-emerald-700' : 'text-rose-700' }}">
                                        <option value="1" {{ $isCompetent ? 'selected' : '' }}>Kompeten (K)</option>
                                        <option value="0" {{ !$isCompetent ? 'selected' : '' }}>Belum Kompeten (BK)</option>
                                    </select>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Final Recommendation & Notes --}}
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm space-y-4 text-xs">
            <h3 class="text-sm font-bold text-slate-900">Rekomendasi Hasil Asesmen Akhir</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Rekomendasi Hasil Asesmen (*):</label>
                    <select name="final_result" id="finalResultSelect" required class="w-full text-xs font-bold rounded-lg border-slate-300 focus:ring-brand-500">
                        <option value="" disabled {{ empty($assessment->final_result) ? 'selected' : '' }}>-- Pilih Rekomendasi Akhir (Wajib) --</option>
                        <option value="KOMPETEN" {{ in_array($assessment->final_result, ['KOMPETEN', 'K']) ? 'selected' : '' }}>KOMPETEN (K)</option>
                        <option value="BELUM_KOMPETEN" {{ in_array($assessment->final_result, ['BELUM_KOMPETEN', 'BK']) ? 'selected' : '' }}>BELUM KOMPETEN (BK)</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tindak Lanjut yang Dibutuhkan:</label>
                    <input type="text" name="follow_up_action" value="{{ old('follow_up_action', $assessment->follow_up_action ?? '') }}" class="w-full text-xs rounded-lg border-slate-300 focus:ring-brand-500" placeholder="Pekerjaan tambahan jika BK atau usulan sertifikat jika K">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Komentar / Catatan Asesor:</label>
                <textarea name="final_notes" rows="3" class="w-full text-xs rounded-lg border-slate-300 focus:ring-brand-500" placeholder="Catatan objektif mengenai unjuk kerja asesi...">{{ old('final_notes', $assessment->final_notes ?? '') }}</textarea>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-200">
                <a href="{{ route('asesor.jadwal.asesi', $assessment->schedule_id) }}" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-lg shadow-sm transition">
                    Simpan FR.AK.02 Rekaman Asesmen
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
