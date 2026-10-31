@extends('layouts.print-muk', [
    'title' => 'FR.IA.05.A Lembar Soal Uji Tertulis - ' . $asesi->full_name,
    'docCode' => 'FR.IA.05.A',
    'docTitle' => 'DPT - PERTANYAAN TERTULIS PILIHAN GANDA (LEMBAR SOAL)'
])

@section('print-content')
@include('admin.dokumen-muk.partials.meta-box', [
    'dateLabel' => 'Tanggal & Waktu',
    'timeAllocation' => '60 Menit',
])

{{-- Instructions Box --}}
<div class="border border-black p-3 bg-slate-50 text-[11px] mb-4">
    <span class="font-bold block mb-1">Petunjuk Pengerjaan Soal:</span>
    <ul class="list-disc pl-5 space-y-0.5 text-slate-700">
        <li>Bacalah seluruh soal dengan saksama sebelum menentukan pilihan jawaban Anda.</li>
        <li>Pilihlah satu jawaban yang paling tepat dengan memberi tanda silang (X) atau menghitamkan bulatan pada Lembar Jawaban (FR.IA.05.C).</li>
        <li>Dilarang mencoret-coret, melipat, atau merusak lembar soal ini. Seluruh lembar soal dikumpulkan kembali ke Asesor setelah ujian selesai.</li>
    </ul>
</div>

{{-- Questions List --}}
<div class="space-y-4 text-xs">
    @forelse($questionBanks as $idx => $q)
        <div class="p-3 border border-slate-300 rounded bg-white">
            <div class="flex items-start gap-2 mb-2 font-bold">
                <span class="w-6 shrink-0">{{ $idx + 1 }}.</span>
                <span class="text-slate-900 leading-snug">{{ $q->question_text }}</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 pl-8 text-[11px]">
                <div class="p-1 rounded bg-slate-50"><strong>A.</strong> {{ $q->option_a }}</div>
                <div class="p-1 rounded bg-slate-50"><strong>B.</strong> {{ $q->option_b }}</div>
                <div class="p-1 rounded bg-slate-50"><strong>C.</strong> {{ $q->option_c }}</div>
                <div class="p-1 rounded bg-slate-50"><strong>D.</strong> {{ $q->option_d }}</div>
            </div>
        </div>
    @empty
        <div class="p-6 text-center text-slate-400 border border-dashed border-slate-300 italic">
            Daftar pertanyaan tertulis pilihan ganda untuk skema ini terintegrasi dalam modul Computer Based Test (CBT) online.
        </div>
    @endforelse
</div>
@endsection
