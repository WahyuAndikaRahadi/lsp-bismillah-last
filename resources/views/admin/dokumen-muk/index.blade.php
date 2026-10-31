@extends('layouts.app', ['title' => 'Dokumen MUK & Berita Acara'])

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Dokumen MUK & Berita Acara</h1>
        <p class="text-sm text-slate-500">Cetak dokumen rekaman MUK, sertifikat, dan berita acara pelaksanaan asesmen</p>
    </div>

    {{-- Schedules List with Accordion --}}
    <div class="space-y-4">
        @forelse($schedules ?? [] as $sch)
            <div x-data="{ open: false }" class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                {{-- Schedule Header / Toolbar --}}
                <div class="p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-100">
                    <div class="space-y-1">
                        <div class="flex items-center space-x-2">
                            <span class="font-mono text-xs font-bold px-2 py-0.5 rounded bg-brand-50 text-brand-700">{{ $sch->code }}</span>
                            <h2 class="text-base font-bold text-slate-900">{{ $sch->name ?? $sch->scheme->name }}</h2>
                        </div>
                        <div class="text-xs text-slate-500 flex items-center space-x-3">
                            <span>TUK: {{ $sch->tuk_name }}</span>
                            <span>&bull;</span>
                            <span>{{ \Carbon\Carbon::parse($sch->date)->isoFormat('D MMMM Y') }}</span>
                            <span>&bull;</span>
                            <span>{{ $sch->assessments->count() }} Asesi Terdaftar</span>
                        </div>
                    </div>

                    {{-- Schedule-level Exports --}}
                    <div class="flex items-center space-x-1.5 flex-wrap gap-y-1">
                        <a href="{{ route('admin.dokumen-muk.export-ba', $sch->id) }}" target="_blank" class="px-2.5 py-1 text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white rounded shadow-sm transition">
                            Berita Acara (BA)
                        </a>
                        <a href="{{ route('admin.dokumen-muk.export-hadir-asesor', $sch->id) }}" target="_blank" class="px-2.5 py-1 text-xs font-semibold bg-purple-50 text-purple-700 hover:bg-purple-100 rounded border border-purple-200 transition">
                            Hadir Asesor
                        </a>
                        <a href="{{ route('admin.dokumen-muk.export-hadir-asesi', $sch->id) }}" target="_blank" class="px-2.5 py-1 text-xs font-semibold bg-amber-50 text-amber-700 hover:bg-amber-100 rounded border border-amber-200 transition">
                            Hadir Asesi
                        </a>
                        <a href="{{ route('admin.dokumen-muk.export', ['schedule' => $sch->id, 'type' => 'ak05']) }}" target="_blank" class="px-2.5 py-1 text-xs font-semibold bg-rose-50 text-rose-700 hover:bg-rose-100 rounded border border-rose-200 transition">
                            FR.AK.05 (Laporan)
                        </a>
                        <a href="{{ route('admin.dokumen-muk.export', ['schedule' => $sch->id, 'type' => 'ak06']) }}" target="_blank" class="px-2.5 py-1 text-xs font-semibold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded border border-indigo-200 transition">
                            FR.AK.06 (Tinjauan)
                        </a>
                        <a href="{{ route('admin.dokumen-muk.export', ['schedule' => $sch->id, 'type' => 'cover']) }}" target="_blank" class="px-2 py-1 text-xs font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 rounded border border-slate-300 transition">
                            Cover
                        </a>
                        <a href="{{ route('admin.dokumen-muk.export', ['schedule' => $sch->id, 'type' => 'daftar-periksa']) }}" target="_blank" class="px-2 py-1 text-xs font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 rounded border border-slate-300 transition">
                            Periksa
                        </a>
                        <a href="{{ route('admin.dokumen-muk.export', ['schedule' => $sch->id, 'type' => 'mapa']) }}" target="_blank" class="px-2 py-1 text-xs font-semibold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded border border-emerald-200 transition">
                            MAPA.01
                        </a>
                        <a href="{{ route('admin.dokumen-muk.export', ['schedule' => $sch->id, 'type' => 'mapa02']) }}" target="_blank" class="px-2 py-1 text-xs font-semibold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded border border-emerald-200 transition">
                            MAPA.02
                        </a>
                        <a href="{{ route('admin.dokumen-muk.export', ['schedule' => $sch->id, 'type' => 'ia05b']) }}" target="_blank" class="px-2 py-1 text-xs font-semibold bg-cyan-50 text-cyan-700 hover:bg-cyan-100 rounded border border-cyan-200 transition">
                            IA.05.B Kunci
                        </a>
                        <a href="{{ route('admin.dokumen-muk.export-sertifikat', $sch->id) }}" target="_blank" class="px-2.5 py-1 text-xs font-semibold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded border border-emerald-200 transition">
                            Sertifikat
                        </a>
                        <a href="{{ route('admin.dokumen-muk.export-excel-biodata', $sch->id) }}" class="px-2 py-1 text-xs font-semibold bg-teal-50 text-teal-700 hover:bg-teal-100 rounded border border-teal-200 transition" title="Unduh Excel Biodata Peserta">
                            Excel Biodata
                        </a>
                        <a href="{{ route('admin.dokumen-muk.export-excel-hadir-asesi', $sch->id) }}" class="px-2 py-1 text-xs font-semibold bg-teal-50 text-teal-700 hover:bg-teal-100 rounded border border-teal-200 transition" title="Unduh Excel Daftar Hadir Peserta">
                            Excel Hadir Peserta
                        </a>
                        <a href="{{ route('admin.dokumen-muk.export-excel-hadir-asesor', $sch->id) }}" class="px-2 py-1 text-xs font-semibold bg-teal-50 text-teal-700 hover:bg-teal-100 rounded border border-teal-200 transition" title="Unduh Excel Daftar Hadir Asesor">
                            Excel Hadir Asesor
                        </a>

                        <button type="button" @click="open = !open" class="px-3 py-1 text-xs font-bold bg-slate-800 hover:bg-slate-900 text-white rounded transition flex items-center space-x-1 ml-1">
                            <span x-text="open ? 'Tutup Asesi' : 'Daftar Asesi'"></span>
                            <svg class="w-3.5 h-3.5 transform transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Accordion: Asesi List & Individual PDF Exports --}}
                <div x-show="open" x-cloak class="p-5 bg-slate-50/50">
                    <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wider mb-3">Daftar Dokumen MUK Individual Asesi</h3>

                    @if($sch->assessments->isEmpty())
                        <p class="text-xs text-slate-400">Belum ada asesi pada jadwal ini.</p>
                    @else
                        <div class="overflow-x-auto bg-white rounded-lg border border-slate-200">
                            <table class="w-full text-xs">
                                <thead class="bg-slate-50 text-left font-semibold text-slate-600 border-b border-slate-200">
                                    <tr>
                                        <th class="px-3 py-2.5">No</th>
                                        <th class="px-3 py-2.5">Nama Asesi</th>
                                        <th class="px-3 py-2.5">Hasil Akhir</th>
                                        <th class="px-3 py-2.5 text-right">Cetak Dokumen MUK Individual</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($sch->assessments as $idx => $assessment)
                                        <tr class="hover:bg-slate-50/70">
                                            <td class="px-3 py-2.5 text-slate-500 font-mono">{{ $idx + 1 }}</td>
                                            <td class="px-3 py-2.5">
                                                <div class="font-bold text-slate-800">{{ $assessment->asesi->full_name }}</div>
                                                <div class="text-[11px] text-slate-400">{{ $assessment->asesi->email }} &bull; NIK: {{ $assessment->asesi->profile?->nik ?? '-' }}</div>
                                            </td>
                                            <td class="px-3 py-2.5">
                                                @if(in_array($assessment->final_result, ['K', 'KOMPETEN'], true))
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">KOMPETEN (K)</span>
                                                @elseif(in_array($assessment->final_result, ['BK', 'BELUM_KOMPETEN'], true))
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-300">BELUM KOMPETEN (BK)</span>
                                                @elseif($assessment->final_result === 'TIDAK_HADIR')
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-700 border border-slate-300">TIDAK HADIR</span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700">{{ $assessment->status ?? 'PENDING' }}</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2.5 text-right">
                                                <div class="flex items-center justify-end space-x-1 flex-wrap gap-y-1">
                                                    <a href="{{ route('admin.dokumen-muk.export-individual', ['assessment' => $assessment->id, 'type' => 'apl01']) }}" target="_blank" class="px-1.5 py-0.5 text-[10px] font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 rounded border border-slate-300" title="FR.APL.01">
                                                        APL.01
                                                    </a>
                                                    <a href="{{ route('admin.dokumen-muk.export-individual', ['assessment' => $assessment->id, 'type' => 'apl02']) }}" target="_blank" class="px-1.5 py-0.5 text-[10px] font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 rounded border border-slate-300" title="FR.APL.02">
                                                        APL.02
                                                    </a>
                                                    <a href="{{ route('admin.dokumen-muk.export-individual', ['assessment' => $assessment->id, 'type' => 'ak01']) }}" target="_blank" class="px-1.5 py-0.5 text-[10px] font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 rounded border border-slate-300" title="FR.AK.01">
                                                        AK.01
                                                    </a>
                                                    <a href="{{ route('admin.dokumen-muk.export-individual', ['assessment' => $assessment->id, 'type' => 'ak07']) }}" target="_blank" class="px-1.5 py-0.5 text-[10px] font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 rounded border border-slate-300" title="FR.AK.07">
                                                        AK.07
                                                    </a>
                                                    <a href="{{ route('admin.dokumen-muk.export-individual', ['assessment' => $assessment->id, 'type' => 'ia01']) }}" target="_blank" class="px-1.5 py-0.5 text-[10px] font-medium bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded border border-indigo-200" title="FR.IA.01 Ceklis Observasi">
                                                        IA.01
                                                    </a>
                                                    <a href="{{ route('admin.dokumen-muk.export-individual', ['assessment' => $assessment->id, 'type' => 'ia01b']) }}" target="_blank" class="px-1.5 py-0.5 text-[10px] font-medium bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded border border-indigo-200" title="FR.IA.01B Report Sheet">
                                                        IA.01B
                                                    </a>
                                                    <a href="{{ route('admin.dokumen-muk.export-individual', ['assessment' => $assessment->id, 'type' => 'ia02']) }}" target="_blank" class="px-1.5 py-0.5 text-[10px] font-medium bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded border border-indigo-200" title="FR.IA.02 Praktik Demonstrasi">
                                                        IA.02
                                                    </a>
                                                    <a href="{{ route('admin.dokumen-muk.export-individual', ['assessment' => $assessment->id, 'type' => 'ia03']) }}" target="_blank" class="px-1.5 py-0.5 text-[10px] font-medium bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded border border-indigo-200" title="FR.IA.03 Pertanyaan Lisan">
                                                        IA.03
                                                    </a>
                                                    <a href="{{ route('admin.dokumen-muk.export-individual', ['assessment' => $assessment->id, 'type' => 'ia05a']) }}" target="_blank" class="px-1.5 py-0.5 text-[10px] font-medium bg-cyan-50 hover:bg-cyan-100 text-cyan-700 rounded border border-cyan-200" title="FR.IA.05.A Lembar Soal">
                                                        IA.05A
                                                    </a>
                                                    <a href="{{ route('admin.dokumen-muk.export-individual', ['assessment' => $assessment->id, 'type' => 'ia05c']) }}" target="_blank" class="px-1.5 py-0.5 text-[10px] font-medium bg-cyan-50 hover:bg-cyan-100 text-cyan-700 rounded border border-cyan-200" title="FR.IA.05.C LJK Asesi">
                                                        IA.05C
                                                    </a>
                                                    <a href="{{ route('admin.dokumen-muk.export-individual', ['assessment' => $assessment->id, 'type' => 'ak02']) }}" target="_blank" class="px-2 py-0.5 text-[10px] font-bold bg-brand-50 hover:bg-brand-100 text-brand-700 rounded border border-brand-200" title="FR.AK.02 Rekaman Asesmen">
                                                        AK.02
                                                    </a>
                                                    <a href="{{ route('admin.dokumen-muk.export-individual', ['assessment' => $assessment->id, 'type' => 'ak03']) }}" target="_blank" class="px-2 py-0.5 text-[10px] font-medium bg-amber-50 hover:bg-amber-100 text-amber-700 rounded border border-amber-200" title="FR.AK.03 Umpan Balik">
                                                        AK.03
                                                    </a>
                                                    <a href="{{ route('admin.dokumen-muk.export-individual', ['assessment' => $assessment->id, 'type' => 'ak04']) }}" target="_blank" class="px-2 py-0.5 text-[10px] font-medium bg-purple-50 hover:bg-purple-100 text-purple-700 rounded border border-purple-200" title="FR.AK.04 Banding">
                                                        AK.04
                                                    </a>
                                                    <a href="{{ route('admin.dokumen-muk.export-individual', ['assessment' => $assessment->id, 'type' => 'pernyataan']) }}" target="_blank" class="px-2 py-0.5 text-[10px] font-medium bg-teal-50 hover:bg-teal-100 text-teal-700 rounded border border-teal-200" title="Surat Pernyataan">
                                                        Pernyataan
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-slate-200 p-8 text-center text-slate-500 text-sm">
                Belum ada jadwal asesmen tersedia untuk ekspor dokumen MUK.
            </div>
        @endforelse
    </div>
</div>
@endsection
