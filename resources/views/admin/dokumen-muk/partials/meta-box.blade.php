{{-- Shared MUK Metadata Box --}}
<table class="w-full text-xs border border-black mb-4">
    <tr>
        <td class="w-1/3 p-2 font-bold bg-slate-100 border-r border-black align-top">
            Skema Sertifikasi<br>
            <span class="font-normal text-[10px] text-slate-600">(KKNI / Okupasi / Klaster)</span>
        </td>
        <td class="w-2/3 p-2 border-black">
            <div class="grid grid-cols-12 gap-1">
                <span class="col-span-2 font-semibold">Judul</span>
                <span class="col-span-10">: <strong>{{ $scheme->name }}</strong></span>
                <span class="col-span-2 font-semibold">Nomor</span>
                <span class="col-span-10 font-mono">: {{ $scheme->code }}</span>
            </div>
        </td>
    </tr>
    <tr class="border-t border-black">
        <td class="p-2 font-bold bg-slate-100 border-r border-black">TUK</td>
        <td class="p-2 font-bold">{{ $schedule->tuk_name ?? 'Sewaktu / Tempat Kerja / Mandiri' }}</td>
    </tr>
    <tr class="border-t border-black">
        <td class="p-2 font-bold bg-slate-100 border-r border-black">{{ $assessorLabel ?? 'Nama Asesor' }}</td>
        <td class="p-2 font-semibold">
            @if(isset($assessor) && $assessor)
                {{ $assessor->full_name ?? '-' }} (No. MET: {{ $assessor->profile?->no_reg_asesor ?? '-' }})
            @elseif(isset($assessors) && $assessors->count() > 0)
                @foreach($assessors as $asr)
                    {{ $asr->full_name }} (No. Reg: {{ $asr->profile?->no_reg_asesor ?? '-' }}){{ !$loop->last ? '; ' : '' }}
                @endforeach
            @else
                -
            @endif
        </td>
    </tr>
    @if(isset($asesi) && $asesi)
        <tr class="border-t border-black">
            <td class="p-2 font-bold bg-slate-100 border-r border-black">Nama Asesi</td>
            <td class="p-2 font-bold">{{ $asesi->full_name }} (NIK: {{ $profile?->nik ?? '-' }})</td>
        </tr>
    @endif
    <tr class="border-t border-black">
        <td class="p-2 font-bold bg-slate-100 border-r border-black">{{ $dateLabel ?? 'Tanggal Pelaksanaan' }}</td>
        <td class="p-2">{{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') }}{{ isset($timeAllocation) ? ' | Alokasi: ' . $timeAllocation : '' }}</td>
    </tr>
</table>
