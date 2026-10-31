@props([
    'assessment',
    'savedGeneralNotes' => null,
    'savedSignature' => null,
])

<div class="space-y-4">
    {{-- Catatan Umum Asesor --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-5 space-y-2">
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 font-mono">
            Umpan Balik &amp; Catatan Asesor
        </label>
        <textarea name="general_notes" rows="3" :disabled="!isEditing" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-xs focus:ring-2 focus:ring-brand-500 focus:outline-none" placeholder="Catatan unjuk kerja, rekomendasi, atau klarifikasi observasi...">{{ $savedGeneralNotes ?? old('general_notes') }}</textarea>
    </div>

    {{-- Tanda Tangan Dua Pihak (Asesi & Asesor) --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-5">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 font-mono mb-4">Pengesahan Asesmen</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Tanda Tangan Asesi (Otomatis dari Persetujuan AK.01) --}}
            <div class="bg-slate-50 border {{ $assessment->esign_consent_at ? 'border-slate-200' : 'border-amber-300 bg-amber-50/50' }} rounded-lg p-4 flex flex-col justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-800 block">Tanda Tangan Asesi</span>
                    <span class="text-[11px] text-slate-500 block mt-0.5">{{ $assessment->asesi->full_name }}</span>
                    @if($assessment->esign_consent_at)
                        <div class="mt-2 inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded text-[11px] font-semibold">
                            <span>✓</span> E-Sign Disetujui (FR.AK.01)
                        </div>
                    @else
                        <div class="mt-2 inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-100 text-amber-900 rounded text-[11px] font-bold border border-amber-300">
                            <span>⚠️</span> Belum Menyetujui Klausul E-Sign
                        </div>
                        <p class="text-[10px] text-amber-800 mt-1.5 leading-tight">
                            Asesi harus menyetujui klausul tanda tangan elektronik pada akunnya sebelum penilaian ini dapat disimpan.
                        </p>
                    @endif
                </div>

                <div class="mt-3 pt-3 border-t border-slate-200 flex items-center justify-between">
                    @php
                        $hasConsent = (bool) $assessment->esign_consent_at;
                        $asesiSig = $hasConsent ? ($assessment->asesi_signature ?? $assessment->asesi?->profile?->signature_data) : null;
                    @endphp
                    @if($hasConsent && $asesiSig)
                        <img src="{{ $asesiSig }}" alt="Tanda Tangan Asesi" class="h-12 max-w-[140px] object-contain">
                    @else
                        <span class="text-[11px] text-slate-400 italic">
                            {{ $hasConsent ? 'Tanda tangan digital tersinkron' : 'Menunggu persetujuan asesi' }}
                        </span>
                    @endif
                    <span class="text-[10px] text-slate-400 font-mono">
                        {{ $assessment->ak01_agreed_at ? \Carbon\Carbon::parse($assessment->ak01_agreed_at)->format('d/m/Y') : '-' }}
                    </span>
                </div>
            </div>

            {{-- Tanda Tangan Asesor --}}
            <div>
                <x-signature-pad 
                    name="assessor_signature" 
                    :value="$savedSignature ?? auth()->user()->profile?->signature_data" 
                    label="Tanda Tangan Asesor Penguji" 
                    :required="true"
                    height="120"
                />
            </div>
        </div>
    </div>
</div>
