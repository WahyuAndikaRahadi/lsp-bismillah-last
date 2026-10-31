@extends('layouts.app', ['title' => 'FR.AK.03 Umpan Balik dan Catatan Asesmen'])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('asesi.assessment.ruang-uji', $assessment->id) }}" class="text-xs text-brand-600 hover:text-brand-700 font-semibold">&larr; Kembali ke Ruang Uji</a>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">FR.AK.03 Umpan Balik dan Catatan Asesmen</h1>
            <p class="text-xs text-slate-500">Evaluasi pengalaman asesi terhadap transparansi dan kenyamanan pelaksanaan uji kompetensi</p>
        </div>
    </div>

    {{-- Info Card --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm text-xs">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div>
                <span class="text-slate-500 block">Skema:</span>
                <span class="font-bold text-slate-900">{{ $assessment->schedule->scheme->name }}</span>
            </div>
            <div>
                <span class="text-slate-500 block">TUK:</span>
                <span class="font-bold text-slate-900">{{ $assessment->schedule->tuk_name }}</span>
            </div>
            <div>
                <span class="text-slate-500 block">Asesi:</span>
                <span class="font-bold text-slate-900">{{ $assessment->asesi->full_name }}</span>
            </div>
            <div>
                <span class="text-slate-500 block">Asesor:</span>
                <span class="font-bold text-slate-900">{{ $assessment->assessor->full_name ?? 'Asesor LSP' }}</span>
            </div>
        </div>
    </div>

    @php
        $items = [
            1 => 'Saya mendapatkan penjelasan yang cukup memadai mengenai proses asesmen / uji kompetensi.',
            2 => 'Saya diberikan kesempatan untuk mempelajari standar kompetensi yang akan diujikan dan menilai diri sendiri terhadap pencapaiannya.',
            3 => 'Asesor memberikan kesempatan untuk mendiskusikan / menegosiasikan metoda, instrumen, dan sumber asesmen serta jadwal uji.',
            4 => 'Asesor berusaha menggali seluruh bukti pendukung yang sesuai dengan latar belakang pelatihan dan pengalaman saya.',
            5 => 'Saya sepenuhnya diberikan kesempatan untuk mendemonstrasikan kompetensi yang saya miliki selama asesmen berlangsung.',
            6 => 'Saya mendapatkan penjelasan yang memadai mengenai keputusan asesmen (K / BK).',
            7 => 'Asesor memberikan umpan balik yang membangun dan mendukung setelah asesmen serta arahan tindak lanjutnya.',
            8 => 'Asesor bersama saya mempelajari semua dokumen asesmen serta menandatanganinya secara transparan.',
            9 => 'Saya mendapatkan jaminan kerahasiaan hasil asesmen serta penjelasan mengenai penanganan berkas portofolio.',
            10 => 'Asesor menggunakan keterampilan komunikasi yang efektif, santun, dan profesional selama asesmen.',
        ];
        $savedRatings = $ak03?->ratings_json ?? [];
    @endphp

    <form action="{{ route('asesi.assessment.ak03.submit', $assessment->id) }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                <h2 class="text-sm font-bold text-slate-900">Komponen Penilaian Pengalaman Asesmen</h2>
                <p class="text-xs text-slate-500">Pilih YA jika pernyataan sesuai dengan apa yang Anda rasakan, atau TIDAK jika tidak terpenuhi</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-center w-12">No</th>
                            <th class="px-4 py-3 text-left">Pernyataan Evaluasi</th>
                            <th class="px-3 py-3 text-center w-28">Pilihan</th>
                            <th class="px-4 py-3 text-left w-64">Catatan / Komentar Asesi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($items as $no => $text)
                            @php
                                $val = $savedRatings[$no]['value'] ?? null;
                                $comment = $savedRatings[$no]['comment'] ?? '';
                            @endphp
                            <tr class="hover:bg-slate-50/60">
                                <td class="px-4 py-3 text-center text-slate-500 font-mono">{{ $no }}</td>
                                <td class="px-4 py-3 text-slate-800 leading-relaxed">{{ $text }}</td>
                                <td class="px-3 py-3 text-center">
                                    <div class="inline-flex items-center space-x-2">
                                        <label class="inline-flex items-center space-x-1 cursor-pointer">
                                            <input type="radio" name="ratings[{{ $no }}][value]" value="YA" {{ $val === 'YA' ? 'checked' : '' }} required class="text-brand-600 focus:ring-brand-500">
                                            <span class="font-bold text-emerald-700">Ya</span>
                                        </label>
                                        <label class="inline-flex items-center space-x-1 cursor-pointer">
                                            <input type="radio" name="ratings[{{ $no }}][value]" value="TIDAK" {{ $val === 'TIDAK' ? 'checked' : '' }} required class="text-rose-600 focus:ring-rose-500">
                                            <span class="font-bold text-rose-700">Tidak</span>
                                        </label>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" name="ratings[{{ $no }}][comment]" value="{{ $comment }}" class="w-full text-xs rounded border-slate-300 focus:ring-brand-500" placeholder="Opsional">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-6 border-t border-slate-200 bg-slate-50/50 space-y-2">
                <label class="block text-xs font-bold text-slate-700">Catatan / Komentar Tambahan Lainnya (apabila ada):</label>
                <textarea name="general_notes" rows="2" class="w-full text-xs rounded-lg border-slate-300 focus:ring-brand-500" placeholder="Kesan, pesan, atau masukan untuk pelaksanaan asesmen...">{{ $ak03?->general_notes ?? '' }}</textarea>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('asesi.jadwal.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900">
                Kembali ke Jadwal
            </a>
            <button type="submit" class="px-6 py-2.5 text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-lg shadow-sm transition">
                Kirim Umpan Balik (FR.AK.03) & Selesai
            </button>
        </div>
    </form>
</div>
@endsection
