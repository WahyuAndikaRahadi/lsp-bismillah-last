@extends('layouts.app', ['title' => 'FR.AK.04 Pengajuan Banding Asesmen'])

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('asesi.jadwal.index') }}" class="text-xs text-brand-600 hover:text-brand-700 font-semibold">&larr; Kembali ke Jadwal</a>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">FR.AK.04 Pengajuan Banding Asesmen</h1>
            <p class="text-xs text-slate-500">Formulir resmi sanggahan hasil keputusan asesmen kompetensi</p>
        </div>
        @if($appeal)
            <span class="px-3 py-1 text-xs font-bold rounded-lg border {{ $appeal->status === 'ACCEPTED' ? 'bg-emerald-50 text-emerald-700 border-emerald-300' : ($appeal->status === 'REJECTED' ? 'bg-rose-50 text-rose-700 border-rose-300' : 'bg-purple-50 text-purple-700 border-purple-300') }}">
                Status Banding: {{ $appeal->status }}
            </span>
        @endif
    </div>

    {{-- Info Card --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm text-xs">
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
            <div>
                <span class="text-slate-500 block">Skema Sertifikasi:</span>
                <span class="font-bold text-slate-900">{{ $assessment->schedule->scheme->name }}</span>
            </div>
            <div>
                <span class="text-slate-500 block">Asesor Penilai:</span>
                <span class="font-bold text-slate-900">{{ $assessment->assessor->full_name ?? '-' }}</span>
            </div>
            <div>
                <span class="text-slate-500 block">Keputusan Asesmen Saat Ini:</span>
                <span class="font-bold {{ $assessment->final_result === 'KOMPETEN' ? 'text-emerald-700' : 'text-rose-700' }}">
                    {{ $assessment->final_result ?? 'BELUM SELESAI' }}
                </span>
            </div>
        </div>
    </div>

    {{-- Appeal Deadline Information Banner --}}
    <div class="p-4 rounded-xl border {{ $isWithinDeadline ? 'bg-amber-50 border-amber-200 text-amber-900' : 'bg-rose-50 border-rose-200 text-rose-900' }} text-xs space-y-1">
        <div class="flex items-center justify-between">
            <span class="font-bold flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Batas Pengajuan Banding: {{ $appealWindowDays }} Hari Kerja (hingga {{ $deadline->isoFormat('dddd, D MMMM Y') }})
            </span>
            <span class="text-[10px] uppercase font-extrabold px-2 py-0.5 rounded-full {{ $isWithinDeadline ? 'bg-amber-200 text-amber-800' : 'bg-rose-200 text-rose-800' }}">
                {{ $isWithinDeadline ? 'Tenggat Terbuka' : 'Tenggat Berakhir' }}
            </span>
        </div>
        <p class="text-[11px] {{ $isWithinDeadline ? 'text-amber-800' : 'text-rose-800' }}">
            * Catatan Regulasi: Batas {{ $appealWindowDays }} hari kerja dihitung hari kerja resmi (Senin-Jumat, mengabaikan Sabtu dan Minggu). Batas 3 hari kerja diambil dari praktik umum BNSP, belum dikonfirmasi dengan SOP LSP PrO Indo.
        </p>
    </div>

    @if($appeal && $appeal->status !== 'SUBMITTED')
        {{-- Decision Result Box --}}
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm space-y-3 text-xs">
            <h3 class="text-sm font-bold text-slate-900">Keputusan Banding dari Admin LSP</h3>
            <div class="p-4 rounded-lg {{ $appeal->status === 'ACCEPTED' ? 'bg-emerald-50 border border-emerald-200' : 'bg-rose-50 border border-rose-200' }}">
                <p class="font-bold {{ $appeal->status === 'ACCEPTED' ? 'text-emerald-900' : 'text-rose-900' }}">
                    Status Keputusan: {{ $appeal->status === 'ACCEPTED' ? 'BANDING DITERIMA' : 'BANDING DITOLAK' }}
                </p>
                <p class="mt-1 text-slate-700">{{ $appeal->decision_notes ?? 'Tidak ada catatan tambahan.' }}</p>
                <p class="text-[11px] text-slate-500 mt-2">Ditetapkan tanggal: {{ \Carbon\Carbon::parse($appeal->decision_date)->isoFormat('D MMMM Y') }}</p>
            </div>
        </div>
    @endif

    <form action="{{ route('asesi.assessment.appeal.submit', $assessment->id) }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm space-y-4 text-xs">
            <h2 class="text-sm font-bold text-slate-900">Alasan dan Rincian Banding</h2>

            <div>
                <label class="block font-bold text-slate-700 mb-1">1. Alasan Pengajuan Banding (*):</label>
                <textarea name="appeal_reasons" rows="4" required class="w-full text-xs rounded-lg border-slate-300 focus:ring-brand-500" placeholder="Jelaskan secara rinci alasan ketidaksetujuan terhadap keputusan asesmen, bukti yang diabaikan, atau ketidaksesuaian prosedur...">{{ $appeal?->appeal_reasons }}</textarea>
                @error('appeal_reasons')
                    <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-2">2. Unit Kompetensi yang Dipersoalkan (Pilih yang relevan):</label>
                <div class="space-y-2 max-h-48 overflow-y-auto border border-slate-200 rounded-lg p-3 bg-slate-50">
                    @php $contested = $appeal?->contested_units ?? []; @endphp
                    @foreach($units as $u)
                        <label class="flex items-start space-x-2 cursor-pointer hover:bg-white p-1 rounded transition">
                            <input type="checkbox" name="contested_units[]" value="{{ $u->id }}" {{ in_array($u->id, $contested) ? 'checked' : '' }} class="mt-0.5 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                            <span class="text-slate-800">
                                <strong class="font-mono text-slate-900">{{ $u->code }}</strong> - {{ $u->title }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="pt-4 border-t border-slate-200 flex items-center justify-between">
                <a href="{{ route('asesi.jadwal.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900">
                    Batal
                </a>
                @if(! $isWithinDeadline && ! $appeal)
                    <div class="text-right">
                        <span class="text-xs text-rose-600 font-bold block">Tenggat pengajuan banding telah berakhir.</span>
                        <button type="button" disabled class="mt-1 px-6 py-2.5 text-xs font-bold text-white bg-slate-400 rounded-lg cursor-not-allowed">
                            Batas Waktu Habis
                        </button>
                    </div>
                @else
                    <button type="submit" class="px-6 py-2.5 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-lg shadow-sm transition">
                        {{ $appeal ? 'Perbarui Pengajuan Banding' : 'Kirim Pengajuan Banding (FR.AK.04)' }}
                    </button>
                @endif
            </div>
        </div>
    </form>
</div>
@endsection
