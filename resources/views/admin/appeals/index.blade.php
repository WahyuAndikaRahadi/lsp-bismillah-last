@extends('layouts.app', ['title' => 'Daftar Pengajuan Banding Asesmen (FR.AK.04)'])

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Pengajuan Banding Asesmen (FR.AK.04)</h1>
            <p class="text-xs text-slate-500">Kelola dan putuskan sanggahan hasil uji kompetensi dari asesi</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900">Daftar Banding Masuk ({{ $appeals->total() }})</h2>
        </div>

        @if($appeals->isEmpty())
            <div class="p-8 text-center text-slate-400 italic text-xs">
                Belum ada permohonan banding yang diajukan oleh asesi.
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach($appeals as $appeal)
                    <div class="p-6 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <div>
                                <span class="font-bold text-slate-900 text-sm">{{ $appeal->assessment->asesi->full_name }}</span>
                                <span class="text-xs text-slate-500 ml-2">({{ $appeal->assessment->schedule->scheme->name }})</span>
                                <div class="text-[11px] text-slate-400 mt-0.5">
                                    Diajukan: {{ \Carbon\Carbon::parse($appeal->appeal_date)->isoFormat('D MMMM Y') }} | Jadwal: {{ $appeal->assessment->schedule->name }}
                                </div>
                            </div>
                            <div>
                                <span class="px-3 py-1 text-xs font-bold rounded-lg border {{ $appeal->status === 'ACCEPTED' ? 'bg-emerald-50 text-emerald-700 border-emerald-300' : ($appeal->status === 'REJECTED' ? 'bg-rose-50 text-rose-700 border-rose-300' : 'bg-purple-50 text-purple-700 border-purple-300') }}">
                                    {{ $appeal->status }}
                                </span>
                            </div>
                        </div>

                        <div class="bg-slate-50 rounded-lg p-3 text-xs space-y-1 border border-slate-200">
                            <span class="font-bold text-slate-700 block">Alasan Banding:</span>
                            <p class="text-slate-800 leading-relaxed">{{ $appeal->appeal_reasons }}</p>
                        </div>

                        @if($appeal->status === 'SUBMITTED' || $appeal->status === 'UNDER_REVIEW')
                            <form action="{{ route('admin.appeals.decide', $appeal->id) }}" method="POST" class="pt-2 flex flex-col md:flex-row items-center gap-3 text-xs">
                                @csrf
                                <input type="text" name="appeals_committee_chair" class="w-full md:w-56 text-xs rounded-lg border-slate-300 focus:ring-brand-500" placeholder="Nama Ketua Tim Banding (opsional)..." value="{{ old('appeals_committee_chair', $appeal->appeals_committee_chair) }}">
                                <input type="text" name="decision_notes" class="flex-1 text-xs rounded-lg border-slate-300 focus:ring-brand-500" placeholder="Catatan keputusan banding (tindak lanjut/asesmen ulang)..." required>
                                <div class="flex items-center space-x-2">
                                    <button type="submit" name="decision" value="ACCEPTED" class="px-4 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-sm transition">
                                        Terima Banding
                                    </button>
                                    <button type="submit" name="decision" value="REJECTED" class="px-4 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-lg shadow-sm transition">
                                        Tolak Banding
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="text-xs text-slate-600 bg-slate-100 p-3 rounded-lg border border-slate-200">
                                <strong>Catatan Keputusan:</strong> {{ $appeal->decision_notes ?? '-' }}
                                @if($appeal->appeals_committee_chair)
                                    <div class="text-slate-800 mt-1"><strong>Ketua Tim Banding:</strong> {{ $appeal->appeals_committee_chair }}</div>
                                @endif
                                <span class="text-slate-400 block mt-1">Diputuskan oleh: {{ $appeal->decider->full_name ?? 'Admin' }} pada {{ $appeal->decision_date ? \Carbon\Carbon::parse($appeal->decision_date)->isoFormat('D MMMM Y') : '-' }}</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="p-4 border-t border-slate-200">
                {{ $appeals->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
