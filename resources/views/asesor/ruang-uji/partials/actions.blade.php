@props([
    'assessment',
    'code',
    'isCompleted' => false,
    'nextCandidate' => null,
])

<div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-4 border-t border-slate-200">
    <a href="{{ route('asesor.schedule.instrument', ['schedule' => $assessment->schedule_id, 'code' => strtolower($code)]) }}" class="px-4 py-2.5 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition text-center">
        &larr; Kembali ke Daftar Asesi
    </a>

    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
        <button type="button" x-show="!isEditing" @click="isEditing = true" class="px-5 py-2.5 text-xs font-bold bg-amber-500 hover:bg-amber-600 text-white rounded-lg shadow-xs transition">
            ✎ Edit Penilaian
        </button>

        <div x-show="isEditing" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
            <button type="submit" name="save_action" value="save" class="px-5 py-2.5 text-xs font-bold bg-slate-800 hover:bg-slate-900 text-white rounded-lg shadow-xs transition">
                {{ $isCompleted ? 'Simpan Perubahan' : 'Simpan Saja' }}
            </button>

            <button type="submit" name="save_action" value="next" class="px-6 py-2.5 text-xs font-bold bg-brand-600 hover:bg-brand-700 text-white rounded-lg shadow-xs transition flex items-center justify-center gap-1.5">
                <span>Simpan &amp; Lanjut ke Asesi Berikutnya</span>
                @if($nextCandidate)
                    <span class="text-[11px] opacity-90 font-normal">({{ $nextCandidate->full_name }})</span>
                @endif
                <span>&rarr;</span>
            </button>
        </div>
    </div>
</div>
