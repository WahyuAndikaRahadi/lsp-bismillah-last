@extends('layouts.app', ['title' => 'FR.AK.05 & FR.AK.06 Laporan & Tinjauan Asesmen'])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('asesor.jadwal.asesi', $schedule->id) }}" class="text-xs text-brand-600 hover:text-brand-700 font-semibold">&larr; Kembali ke Jadwal</a>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">Laporan & Tinjauan Proses Asesmen</h1>
            <p class="text-xs text-slate-500">Kompilasi FR.AK.05 (Laporan Hasil) dan FR.AK.06 (Meninjau Proses Asesmen) sebelum penutupan jadwal uji</p>
        </div>
    </div>

    {{-- Ringkasan Jadwal --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm text-xs">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <span class="text-slate-500 block">Jadwal:</span>
                <span class="font-bold text-slate-900">{{ $schedule->name }}</span>
            </div>
            <div>
                <span class="text-slate-500 block">Skema:</span>
                <span class="font-bold text-slate-900">{{ $schedule->scheme->name }}</span>
            </div>
            <div>
                <span class="text-slate-500 block">TUK:</span>
                <span class="font-bold text-slate-900">{{ $schedule->tuk_name }}</span>
            </div>
            <div>
                <span class="text-slate-500 block">Jumlah Asesi:</span>
                <span class="font-bold text-brand-700">{{ $schedule->assessments->count() }} Orang ({{ $schedule->assessments->where('status', 'COMPLETED')->count() }} Selesai)</span>
            </div>
        </div>
    </div>

    <form action="{{ route('asesor.schedule.reports.submit', $schedule->id) }}" method="POST" class="space-y-6"
        x-data="{
            ak05Positif: '{{ addslashes(old('ak05_aspek_positif', $schedule->ak05_report['aspek_positif'] ?? '')) }}',
            ak05Negatif: '{{ addslashes(old('ak05_aspek_negatif', $schedule->ak05_report['aspek_negatif'] ?? '')) }}',
            ak05Rekomendasi: '{{ addslashes(old('ak05_rekomendasi', $schedule->ak05_report['rekomendasi'] ?? '')) }}',
            ak06ValidNotes: '{{ addslashes(old('ak06_valid_notes', $schedule->ak06_review['valid_notes'] ?? '')) }}',
            ak06ReliabelNotes: '{{ addslashes(old('ak06_reliabel_notes', $schedule->ak06_review['reliabel_notes'] ?? '')) }}',
            ak06FleksibelNotes: '{{ addslashes(old('ak06_fleksibel_notes', $schedule->ak06_review['fleksibel_notes'] ?? '')) }}',
            ak06AdilNotes: '{{ addslashes(old('ak06_adil_notes', $schedule->ak06_review['adil_notes'] ?? '')) }}',
            ak06Catatan: '{{ addslashes(old('ak06_catatan_peninjau', $schedule->ak06_review['catatan_peninjau'] ?? '')) }}',
            useDraft() {
                this.ak05Positif = 'Sarana dan prasarana bengkel TUK lengkap, ketersediaan alat ukur dan APD sesuai standar K3, asesi hadir tepat waktu.';
                this.ak05Negatif = 'Tidak ditemukan kendala signifikan selama proses asesmen berlangsung.';
                this.ak05Rekomendasi = 'Mempertahankan konsistensi kalibrasi peralatan tangan dan SST serta peningkatan durasi praktik mandiri.';
                this.ak06ValidNotes = 'Seluruh bukti dikumpulkan mengacu langsung pada KUK SKKNI.';
                this.ak06ReliabelNotes = 'Rubrik penilaian dan kunci jawaban diterapkan secara seragam dan konsisten.';
                this.ak06FleksibelNotes = 'Formulir penyesuaian wajar (FR.AK.07) telah difasilitasi jika asesi membutuhkan.';
                this.ak06AdilNotes = 'Semua asesi mendapat kesempatan dan hak yang setara tanpa bias ras/gender/latar belakang.';
                this.ak06Catatan = 'Proses sertifikasi kompetensi telah mematuhi SOP BNSP dan standar mutu LSP PrO Indo.';
            }
        }">
        @csrf

        {{-- FR.AK.05 Laporan Asesmen --}}
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm space-y-4">
            <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
                <div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 uppercase">FR.AK.05</span>
                    <h2 class="text-base font-bold text-slate-900 mt-1">Laporan Asesmen</h2>
                    <p class="text-xs text-slate-500">Evaluasi pelaksanaan asesmen kompetensi di Tempat Uji Kompetensi</p>
                </div>
                <button type="button" @click="useDraft()" class="px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-300 rounded text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Gunakan Draf Saran
                </button>
            </div>

            <div class="space-y-3 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">1. Aspek Positif Pelaksanaan Asesmen:</label>
                    <textarea name="ak05_aspek_positif" x-model="ak05Positif" rows="2" required class="w-full text-xs rounded-lg border-slate-300 focus:ring-brand-500" placeholder="Tuliskan aspek positif pelaksanaan asesmen di TUK..."></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">2. Aspek Negatif / Kendala Teknis Lapangan:</label>
                    <textarea name="ak05_aspek_negatif" x-model="ak05Negatif" rows="2" required class="w-full text-xs rounded-lg border-slate-300 focus:ring-brand-500" placeholder="Tuliskan kendala teknis atau catatan khusus..."></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">3. Rekomendasi Peningkatan Mutu Asesmen:</label>
                    <textarea name="ak05_rekomendasi" x-model="ak05Rekomendasi" rows="2" required class="w-full text-xs rounded-lg border-slate-300 focus:ring-brand-500" placeholder="Tuliskan rekomendasi peningkatan mutu..."></textarea>
                </div>
            </div>
        </div>

        {{-- FR.AK.06 Tinjauan Proses Asesmen --}}
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm space-y-4">
            <div class="border-b border-slate-200 pb-3">
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200 uppercase">FR.AK.06</span>
                <h2 class="text-base font-bold text-slate-900 mt-1">Meninjau Proses Asesmen (Quality Review)</h2>
                <p class="text-xs text-slate-500">Verifikasi pemenuhan 4 Prinsip Asesmen BNSP (Valid, Reliabel, Fleksibel, Adil)</p>
            </div>

            <div class="overflow-x-auto border border-slate-200 rounded-lg">
                <table class="w-full text-xs">
                    <thead class="bg-slate-50 text-slate-700 font-bold border-b border-slate-200">
                        <tr>
                            <th class="px-3 py-2 text-left w-28">Prinsip Asesmen</th>
                            <th class="px-3 py-2 text-center w-24">Kesesuaian</th>
                            <th class="px-3 py-2 text-left">Catatan & Komentar Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="px-3 py-2.5 font-bold text-slate-800">Validitas (Valid)</td>
                            <td class="px-3 py-2.5 text-center">
                                <select name="ak06_valid" class="text-xs rounded border-slate-300 font-bold text-emerald-700">
                                    <option value="YA" selected>YA</option>
                                    <option value="TIDAK">TIDAK</option>
                                </select>
                            </td>
                            <td class="px-3 py-2.5">
                                <input type="text" name="ak06_valid_notes" x-model="ak06ValidNotes" required placeholder="Catatan kesesuaian prinsip validitas..." class="w-full text-xs rounded border-slate-300">
                            </td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2.5 font-bold text-slate-800">Reliabilitas (Reliable)</td>
                            <td class="px-3 py-2.5 text-center">
                                <select name="ak06_reliabel" class="text-xs rounded border-slate-300 font-bold text-emerald-700">
                                    <option value="YA" selected>YA</option>
                                    <option value="TIDAK">TIDAK</option>
                                </select>
                            </td>
                            <td class="px-3 py-2.5">
                                <input type="text" name="ak06_reliabel_notes" x-model="ak06ReliabelNotes" required placeholder="Catatan kesesuaian reliabilitas..." class="w-full text-xs rounded border-slate-300">
                            </td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2.5 font-bold text-slate-800">Fleksibilitas (Flexible)</td>
                            <td class="px-3 py-2.5 text-center">
                                <select name="ak06_fleksibel" class="text-xs rounded border-slate-300 font-bold text-emerald-700">
                                    <option value="YA" selected>YA</option>
                                    <option value="TIDAK">TIDAK</option>
                                </select>
                            </td>
                            <td class="px-3 py-2.5">
                                <input type="text" name="ak06_fleksibel_notes" x-model="ak06FleksibelNotes" required placeholder="Catatan fleksibilitas..." class="w-full text-xs rounded border-slate-300">
                            </td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2.5 font-bold text-slate-800">Keadilan (Fair)</td>
                            <td class="px-3 py-2.5 text-center">
                                <select name="ak06_adil" class="text-xs rounded border-slate-300 font-bold text-emerald-700">
                                    <option value="YA" selected>YA</option>
                                    <option value="TIDAK">TIDAK</option>
                                </select>
                            </td>
                            <td class="px-3 py-2.5">
                                <input type="text" name="ak06_adil_notes" x-model="ak06AdilNotes" required placeholder="Catatan keadilan..." class="w-full text-xs rounded border-slate-300">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="pt-2 text-xs">
                <label class="block font-bold text-slate-700 mb-1">Catatan Keseluruhan Peninjau / Lead Asesor:</label>
                <textarea name="ak06_catatan_peninjau" x-model="ak06Catatan" rows="2" required class="w-full text-xs rounded-lg border-slate-300 focus:ring-brand-500" placeholder="Tuliskan catatan keseluruhan peninjauan proses asesmen..."></textarea>
            </div>
        </div>

        {{-- Mandatory Confirmation Checkbox --}}
        <div class="p-4 bg-amber-50/70 border border-amber-200 rounded-xl">
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="confirm_draft_review" value="1" required class="mt-0.5 rounded border-amber-400 text-brand-600 focus:ring-brand-500">
                <div class="text-xs text-amber-900">
                    <span class="font-bold block">Konfirmasi Integritas Peninjauan:</span>
                    Saya menyatakan telah memeriksa, meninjau, dan memastikan seluruh isi laporan evaluasi FR.AK.05 dan FR.AK.06 sesuai dengan fakta aktual pelaksanaan asesmen di Tempat Uji Kompetensi.
                </div>
            </label>
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-slate-200">
            <a href="{{ route('asesor.jadwal.asesi', $schedule->id) }}" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900">
                Kembali
            </a>
            <button type="submit" class="px-6 py-2.5 text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-lg shadow-sm transition"
                    onclick="return confirm('PERINGATAN KONSEKUENSI PENUTUPAN JADWAL:\n\n1. Jadwal asesmen ini akan resmi DITUTUP (status FINISHED).\n2. Seluruh hasil penilaian peserta akan dikunci dan tidak dapat diubah lagi.\n3. Berita Acara dan Dokumen MUK siap diverifikasi untuk audit BNSP.\n\nApakah Anda yakin ingin menyimpan laporan dan MENUTUP jadwal asesmen ini?');">
                Simpan Laporan & Tutup Jadwal Asesmen (Final)
            </button>
        </div>
    </form>
</div>
@endsection
