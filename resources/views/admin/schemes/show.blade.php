@extends('layouts.app', ['title' => 'Detail Skema - ' . $scheme->name])

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('admin.schemes.index') }}" class="text-xs text-brand-600 hover:text-brand-700 font-semibold">&larr; Kembali ke Daftar Skema</a>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">{{ $scheme->name }}</h1>
            <div class="flex items-center space-x-3 text-xs text-slate-500 mt-1">
                <span class="font-mono font-bold text-brand-700 bg-brand-50 px-2 py-0.5 rounded">{{ $scheme->code }}</span>
                <span>Jenis: {{ $scheme->scheme_type ?? 'Klaster' }}</span>
                <span>Passing Grade: {{ $scheme->passing_grade_theory ?? 70 }}%</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $scheme->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                    {{ $scheme->is_active ? 'AKTIF' : 'NON-AKTIF' }}
                </span>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.schemes.edit', $scheme->id) }}" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg text-xs font-bold shadow-sm transition">
                Edit Skema
            </a>
        </div>
    </div>

    {{-- Scheme Description --}}
    @if($scheme->description)
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm text-xs text-slate-700 leading-relaxed">
            <span class="font-bold text-slate-900 block mb-1">Deskripsi Skema:</span>
            {{ $scheme->description }}
        </div>
    @endif

    {{-- Competency Units & Elements List --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900">
                Daftar Unit Kompetensi Standar SKKNI ({{ $scheme->competencyUnits->count() }} Unit)
            </h2>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($scheme->competencyUnits as $index => $unit)
                <div class="p-5 hover:bg-slate-50/50 transition" x-data="{ open: false }">
                    <div class="flex items-start justify-between gap-4 cursor-pointer" @click="open = !open">
                        <div class="space-y-1">
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-0.5 text-xs font-mono font-bold bg-slate-100 text-slate-800 rounded">{{ $unit->code }}</span>
                                <h3 class="text-sm font-bold text-slate-900">{{ $unit->title }}</h3>
                            </div>
                            <div class="text-xs text-slate-500">
                                <span>{{ $unit->elements->count() }} Elemen Kompetensi</span>
                            </div>
                        </div>
                        <button type="button" class="text-xs text-brand-600 hover:underline font-semibold shrink-0">
                            <span x-show="!open">Lihat Elemen & KUK &darr;</span>
                            <span x-show="open">Tutup &uarr;</span>
                        </button>
                    </div>

                    {{-- Elements & KUK Accordion --}}
                    <div x-show="open" x-cloak class="mt-4 pt-4 border-t border-slate-100 space-y-3">
                        @forelse($unit->elements as $elIndex => $element)
                            <div class="bg-slate-50 rounded-lg p-3 text-xs">
                                <h4 class="font-bold text-slate-800 mb-2">
                                    Elemen {{ $elIndex + 1 }}: {{ $element->name }}
                                </h4>
                                <ul class="list-disc pl-5 space-y-1 text-slate-600">
                                    @forelse($element->kuks as $kuk)
                                        <li>
                                            <span class="font-mono text-[11px] font-semibold text-slate-500">{{ $kuk->criteria_number }}</span>
                                            {{ $kuk->statement }}
                                        </li>
                                    @empty
                                        <li class="italic text-slate-400">Tidak ada butir KUK terdaftar.</li>
                                    @endforelse
                                </ul>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 italic">Belum ada elemen kompetensi.</p>
                        @endforelse
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-500 text-xs">
                    Belum ada unit kompetensi yang terhubung ke skema ini.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
