@props([
    'assessment',
    'code',
    'title',
    'subtitle' => null,
    'isCompleted' => false,
])

<div class="space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('asesor.schedule.instrument', ['schedule' => $assessment->schedule_id, 'code' => strtolower($code)]) }}" class="inline-flex items-center gap-1.5 text-xs text-brand-600 hover:text-brand-700 font-bold transition">
                &larr; Kembali ke Daftar Asesi (FR.{{ strtoupper($code) }})
            </a>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-1 font-sans">{{ $title }}</h1>
            @if($subtitle)
                <p class="text-xs text-slate-500 mt-0.5">{{ $subtitle }}</p>
            @endif
        </div>

        @if($isCompleted)
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" x-show="!isEditing" @click="isEditing = true" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-lg shadow-xs transition flex items-center gap-1.5">
                    <span>✎ Edit Penilaian</span>
                </button>
                <button type="button" x-show="isEditing" @click="isEditing = false" style="display: none;" class="px-3.5 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 text-xs font-bold rounded-lg transition">
                    Batal Edit
                </button>
            </div>
        @endif
    </div>

    {{-- Meta Grid Standar BNSP --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-4 text-xs grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div>
            <span class="text-slate-400 block text-[11px]">Asesi</span>
            <strong class="text-slate-900">{{ $assessment->asesi->full_name }}</strong>
            <span class="text-slate-500 block text-[10px] font-mono">NIK: {{ $assessment->asesi->profile?->nik ?? '-' }}</span>
        </div>
        <div>
            <span class="text-slate-400 block text-[11px]">Skema</span>
            <strong class="text-slate-900 line-clamp-1" title="{{ $assessment->schedule->scheme->name }}">{{ $assessment->schedule->scheme->name }}</strong>
            <span class="text-slate-500 block text-[10px] font-mono">{{ $assessment->schedule->scheme->code }}</span>
        </div>
        <div>
            <span class="text-slate-400 block text-[11px]">TUK &amp; Tanggal</span>
            <strong class="text-slate-900">{{ $assessment->schedule->tuk_name }}</strong>
            <span class="text-slate-500 block text-[10px]">{{ \Carbon\Carbon::parse($assessment->schedule->date)->translatedFormat('d F Y') }}</span>
        </div>
        <div>
            <span class="text-slate-400 block text-[11px]">Asesor Penguji</span>
            <strong class="text-slate-900">{{ auth()->user()->full_name }}</strong>
            <span class="text-slate-500 block text-[10px] font-mono">No. Reg: {{ auth()->user()->assessor_registration_number ?? '-' }}</span>
        </div>
    </div>

    @if($isCompleted)
        <div class="bg-emerald-50 border-2 border-emerald-300 rounded-xl p-3.5 flex flex-col sm:flex-row items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-2.5">
                <span class="w-7 h-7 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shrink-0">✓</span>
                <div>
                    <div class="text-xs font-bold text-emerald-950">Penilaian FR.{{ strtoupper($code) }} Telah Selesai &amp; Tersimpan</div>
                    <p class="text-[11px] text-emerald-800" x-show="!isEditing">Form dalam mode terkunci (read-only). Klik tombol 'Edit Penilaian' untuk memperbarui data.</p>
                    <p class="text-[11px] text-amber-800 font-semibold" x-show="isEditing" style="display: none;">Mode Pengeditan Aktif. Ubah penilaian lalu klik Simpan.</p>
                </div>
            </div>
            @if(!empty($response?->assessor_signed_at))
                <div class="text-right text-[11px] text-emerald-700 font-mono shrink-0">
                    Disimpan: {{ \Carbon\Carbon::parse($response->assessor_signed_at)->isoFormat('D MMM Y, HH:mm') }} WIB
                </div>
            @endif
        </div>
    @endif
</div>
