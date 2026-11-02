@extends('layouts.app', ['title' => 'Bank Soal Asesmen (FR.IA.05 CBT)'])

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Bank Soal Asesmen CBT (FR.IA.05)</h1>
            <p class="text-sm text-slate-500">Kelola paket instrumen uji tertulis pilihan ganda CBT untuk 7 skema sertifikasi LSP</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.question-banks.create', request()->only('scheme_id')) }}" class="px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-lg text-xs font-bold shadow-sm transition flex items-center space-x-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>+ Tambah Butir Soal</span>
            </a>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Soal di Database</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ number_format($totalQuestions) }} <span class="text-xs font-normal text-slate-500">Butir</span></div>
            <div class="text-[11px] text-emerald-600 font-semibold mt-1">✓ Tersebar di 7 Skema</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Paket Uji Skema</div>
            <div class="text-2xl font-black text-brand-700 mt-1">{{ $schemes->count() }} <span class="text-xs font-normal text-slate-500">Paket</span></div>
            <div class="text-[11px] text-slate-500 mt-1">Standar BNSP Terkait</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tipe Format Uji</div>
            <div class="text-2xl font-black text-slate-900 mt-1">FR.IA.05</div>
            <div class="text-[11px] text-slate-500 mt-1">Pilihan Ganda (A, B, C, D)</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Ambang Kelulusan (Passing Grade)</div>
            <div class="text-2xl font-black text-emerald-600 mt-1">75.0%</div>
            <div class="text-[11px] text-slate-500 mt-1">Auto-Grading Otomatis</div>
        </div>
    </div>

    {{-- Section 1: Ringkasan Paket Bank Soal per Skema --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-slate-900">1. Ringkasan Paket Bank Soal per Skema (7 Paket)</h2>
                <p class="text-xs text-slate-500 mt-0.5">Daftar paket instrumen tes CBT untuk masing-masing skema sertifikasi</p>
            </div>
            @if(request()->filled('scheme_id'))
                <a href="{{ route('admin.question-banks.index') }}" class="text-xs text-brand-600 hover:underline font-semibold">
                    Tampilkan Semua Soal &rarr;
                </a>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-slate-50/50 text-left font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3">Kode & Nama Paket Soal</th>
                        <th class="px-6 py-3">Skema Terkait</th>
                        <th class="px-6 py-3">Tipe Instrumen</th>
                        <th class="px-6 py-3 text-center">Jumlah Butir Soal</th>
                        <th class="px-6 py-3">Status CBT</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($questionBanks as $bank)
                        @php
                            $isSelected = (request('scheme_id') == $bank->scheme->id);
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition {{ $isSelected ? 'bg-brand-50/40 font-semibold' : '' }}">
                            <td class="px-6 py-3.5">
                                <div class="font-bold text-slate-900">{{ $bank->title }}</div>
                                <div class="font-mono text-[11px] text-brand-700 mt-0.5">{{ $bank->code }}</div>
                            </td>
                            <td class="px-6 py-3.5 text-slate-600">
                                {{ $bank->scheme->name }}
                            </td>
                            <td class="px-6 py-3.5">
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    {{ $bank->type }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $bank->questions_count > 0 ? 'bg-blue-50 text-blue-800 border border-blue-200' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $bank->questions_count }} Butir
                                </span>
                            </td>
                            <td class="px-6 py-3.5">
                                <span class="px-2 py-0.5 text-[11px] font-bold rounded bg-emerald-100 text-emerald-800">
                                    SIAP UJI
                                </span>
                            </td>
                            <td class="px-6 py-3.5 text-right">
                                <div class="flex items-center justify-end space-x-1.5">
                                    <a
                                        href="{{ route('admin.question-banks.index', ['scheme_id' => $bank->scheme->id]) }}"
                                        class="px-2.5 py-1 text-xs font-semibold rounded transition {{ $isSelected ? 'bg-brand-600 text-white' : 'bg-brand-50 hover:bg-brand-100 text-brand-700' }}"
                                    >
                                        {{ $isSelected ? '✓ Sedang Difilter' : 'Filter Butir Soal' }}
                                    </a>
                                    <a
                                        href="{{ route('admin.question-banks.create', ['scheme_id' => $bank->scheme->id]) }}"
                                        class="p-1 text-slate-500 hover:text-brand-600 hover:bg-slate-100 rounded transition"
                                        title="Tambah Soal ke Skema Ini"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400">Belum ada paket skema terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Section 2: Daftar Butir Soal CBT (Detail & Filter) --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-6 bg-slate-50 border-b border-slate-200 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-sm font-bold text-slate-900">
                    2. Rincian Butir Soal CBT
                    @if($selectedScheme)
                        <span class="text-brand-700 font-semibold">— Skema: {{ $selectedScheme->name }} ({{ $questions->total() }} Soal)</span>
                    @else
                        <span class="text-slate-500 font-normal">({{ $questions->total() }} Soal Ditampilkan)</span>
                    @endif
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Daftar pertanyaan pilihan ganda, kunci jawaban, dan pemetaan unit kompetensi</p>
            </div>

            {{-- Filter Form --}}
            <form method="GET" action="{{ route('admin.question-banks.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                <select name="scheme_id" onchange="this.form.submit()" class="px-3 py-1.5 text-xs bg-white border border-slate-300 rounded-lg focus:ring-1 focus:ring-brand-500 font-medium">
                    <option value="">-- Semua Skema ({{ $totalQuestions }} Soal) --</option>
                    @foreach($schemes as $s)
                        <option value="{{ $s->id }}" {{ request('scheme_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->code }} - {{ $s->name }} ({{ $s->question_banks_count }})
                        </option>
                    @endforeach
                </select>

                <div class="relative">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari teks pertanyaan..."
                        class="px-3 py-1.5 pl-8 text-xs bg-white border border-slate-300 rounded-lg focus:ring-1 focus:ring-brand-500 w-full sm:w-56"
                    />
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <button type="submit" class="px-3 py-1.5 bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold rounded-lg shadow-xs transition">
                    Cari
                </button>

                @if(request()->hasAny(['scheme_id', 'search']))
                    <a href="{{ route('admin.question-banks.index') }}" class="px-3 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-semibold rounded-lg transition text-center">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- Table of Questions --}}
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-slate-50/50 text-left font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3 w-12 text-center">#</th>
                        <th class="px-6 py-3">Pertanyaan & Pilihan Jawaban</th>
                        <th class="px-6 py-3 w-48">Skema & Unit Terkait</th>
                        <th class="px-6 py-3 w-24 text-center">Kunci</th>
                        <th class="px-6 py-3 w-28 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($questions as $index => $q)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-4 align-top text-center font-mono text-slate-400 font-bold">
                                {{ $questions->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4 align-top space-y-1.5">
                                <p class="font-bold text-slate-900 leading-relaxed text-xs">
                                    {{ $q->question_text }}
                                </p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-1 text-[11px] text-slate-600 pt-1">
                                    <div class="{{ $q->correct_answer === 'A' ? 'font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded' : '' }}">
                                        <strong>A.</strong> {{ $q->option_a }}
                                    </div>
                                    <div class="{{ $q->correct_answer === 'B' ? 'font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded' : '' }}">
                                        <strong>B.</strong> {{ $q->option_b }}
                                    </div>
                                    <div class="{{ $q->correct_answer === 'C' ? 'font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded' : '' }}">
                                        <strong>C.</strong> {{ $q->option_c }}
                                    </div>
                                    <div class="{{ $q->correct_answer === 'D' ? 'font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded' : '' }}">
                                        <strong>D.</strong> {{ $q->option_d }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 align-top space-y-1">
                                <div class="font-bold text-slate-800">{{ $q->scheme->name ?? '-' }}</div>
                                <div class="font-mono text-[11px] text-brand-700">{{ $q->scheme->code ?? '' }}</div>
                                @if($q->competencyUnit)
                                    <div class="text-[10px] text-slate-500 font-mono">[{{ $q->competencyUnit->code }}]</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 align-top text-center">
                                <span class="w-7 h-7 rounded-full inline-flex items-center justify-center font-black text-xs bg-emerald-100 text-emerald-800 border border-emerald-300">
                                    {{ $q->correct_answer }}
                                </span>
                            </td>
                            <td class="px-6 py-4 align-top text-right">
                                <div class="flex items-center justify-end space-x-1">
                                    <a
                                        href="{{ route('admin.question-banks.show', $q->id) }}"
                                        class="p-1.5 text-slate-400 hover:text-brand-600 hover:bg-brand-50 rounded transition"
                                        title="Detail Soal"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a
                                        href="{{ route('admin.question-banks.edit', $q->id) }}"
                                        class="p-1.5 text-slate-400 hover:text-slate-800 hover:bg-slate-100 rounded transition"
                                        title="Ubah Soal"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.question-banks.destroy', $q->id) }}" onsubmit="return confirm('Hapus butir pertanyaan ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded transition" title="Hapus Soal">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400">
                                Tidak ada butir soal yang sesuai kriteria pencarian / filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($questions->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                {{ $questions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
