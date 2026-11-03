@extends('layouts.app', ['title' => 'FR.MAPA.02 - Peta Instrumen Asesmen'])

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200">
                    FR.MAPA.02
                </span>
                <span class="text-xs text-slate-500">Revisi {{ $plan->revision ?? 1 }}</span>
                @if($plan->is_locked)
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                        Terkunci (Locked)
                    </span>
                @else
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                        Draf Asesor
                    </span>
                @endif
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">
                Peta Instrumen Asesmen
            </h1>
            <p class="text-sm text-slate-500">
                Skema: <strong class="text-slate-800">{{ $scheme->name }} ({{ $scheme->code }})</strong> &bull; Jadwal: {{ $schedule->name }}
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('asesor.mapa01.edit', $schedule->id) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">
                &larr; Ke FR.MAPA.01
            </a>
            <a href="{{ route('asesor.jadwal.asesi', $schedule->id) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">
                Ke Roster Asesi &rarr;
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('warning'))
        <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm">
            {{ session('warning') }}
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm space-y-1">
            <p class="font-bold">Terdapat kesalahan pengisian peta instrumen:</p>
            <ul class="list-disc list-inside text-xs space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('asesor.mapa02.update', $plan->id) }}" class="space-y-6">
        @csrf
        @method('PUT')
        <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
        <input type="hidden" name="lock_version" value="{{ $plan->lock_version ?? 1 }}">

        @php
            $groups = $plan->mapa02_data ?? [];
            $blockedCodes = ['FR.IA.04A', 'FR.IA.04B', 'FR.IA.06', 'FR.IA.07', 'FR.IA.08', 'FR.IA.09', 'FR.IA.10', 'FR.IA.11'];
        @endphp

        @foreach($groups as $gIdx => $group)
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">
                            Kelompok Kerja: {{ $group['group_name'] ?? "Kelompok #{$gIdx}" }}
                        </h2>
                        <input type="hidden" name="mapa02_data[{{ $gIdx }}][group_name]" value="{{ $group['group_name'] ?? "Kelompok #{$gIdx}" }}">
                    </div>
                    <span class="text-xs text-slate-500 font-mono">MAPA-02 Item Group</span>
                </div>

                <div class="p-6 overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
                                <th class="pb-3 w-28">Kode Instrumen</th>
                                <th class="pb-3">Nama Instrumen Asesmen</th>
                                <th class="pb-3 text-center w-36">Potensi Asesi (1–5)</th>
                                <th class="pb-3 text-center w-28">Aktif / Siap</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($group['instruments'] ?? [] as $iIdx => $inst)
                                @php
                                    $code = $inst['code'] ?? '';
                                    $isBlocked = in_array(strtoupper($code), $blockedCodes) || str_contains($code, '04') || str_contains($code, '06') || str_contains($code, '07') || str_contains($code, '08') || str_contains($code, '09') || str_contains($code, '10') || str_contains($code, '11');
                                    $isActive = ! empty($inst['is_active']) && ! $isBlocked;
                                @endphp
                                <tr class="hover:bg-slate-50/60 {{ $isBlocked ? 'opacity-60 bg-slate-50/40' : '' }}">
                                    <td class="py-3 font-mono font-bold text-slate-800">
                                        {{ $code }}
                                        <input type="hidden" name="mapa02_data[{{ $gIdx }}][instruments][{{ $iIdx }}][code]" value="{{ $code }}">
                                    </td>
                                    <td class="py-3 text-slate-800 font-medium">
                                        {{ $inst['name'] ?? '' }}
                                        <input type="hidden" name="mapa02_data[{{ $gIdx }}][instruments][{{ $iIdx }}][name]" value="{{ $inst['name'] ?? '' }}">
                                        @if($isBlocked)
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">
                                                Blocked Source (Dokumen Original Belum Ada)
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-center">
                                        <div class="flex items-center justify-center space-x-1.5">
                                            @for($p = 1; $p <= 5; $p++)
                                                @php
                                                    $potensi = $inst['potensi'] ?? [];
                                                    $checked = in_array($p, $potensi);
                                                @endphp
                                                <label class="inline-flex items-center text-[10px] text-slate-600 font-mono">
                                                    <input type="checkbox"
                                                        name="mapa02_data[{{ $gIdx }}][instruments][{{ $iIdx }}][potensi][]"
                                                        value="{{ $p }}"
                                                        {{ $checked ? 'checked' : '' }}
                                                        {{ $isBlocked ? 'disabled' : '' }}
                                                        class="rounded border-slate-300 text-lsp-navy focus:ring-lsp-navy h-3 w-3">
                                                    <span class="ml-0.5">{{ $p }}</span>
                                                </label>
                                            @endfor
                                        </div>
                                    </td>
                                    <td class="py-3 text-center">
                                        @if($isBlocked)
                                            <input type="hidden" name="mapa02_data[{{ $gIdx }}][instruments][{{ $iIdx }}][is_active]" value="0">
                                            <span class="text-xs text-rose-600 font-semibold">Terkunci</span>
                                        @else
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="checkbox"
                                                    name="mapa02_data[{{ $gIdx }}][instruments][{{ $iIdx }}][is_active]"
                                                    value="1"
                                                    {{ $isActive ? 'checked' : '' }}
                                                    class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                                            </label>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach

        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-200">
            <a href="{{ route('asesor.jadwal.asesi', $schedule->id) }}" class="px-5 py-2.5 rounded-lg border border-slate-300 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-lg bg-lsp-navy hover:bg-lsp-navy-dark text-white text-xs font-bold uppercase tracking-wider shadow-xs transition">
                Simpan FR.MAPA.02
            </button>
        </div>
    </form>
</div>
@endsection
