@extends('layouts.app', ['title' => 'Detail Skema - ' . $scheme->name])

@section('content')
<div class="space-y-6 max-w-5xl mx-auto" x-data="{
    showAddUnitModal: false,
    showEditUnitModal: false,
    editUnitData: { id: null, code: '', title: '', standard: 'SKKNI' },

    showAddElementModal: false,
    activeUnitId: null,

    showEditElementModal: false,
    editElementData: { id: null, title: '' },

    showAddKukModal: false,
    activeElementId: null,

    showEditKukModal: false,
    editKukData: { id: null, code: '', statement: '' },

    openUnits: {}
}">
    <!-- Header & Scheme Details -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('admin.schemes.index') }}" class="text-xs text-brand-600 hover:text-brand-700 font-semibold inline-flex items-center gap-1">
                <span>&larr;</span> Kembali ke Daftar Skema
            </a>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">{{ $scheme->name }}</h1>
            <div class="flex items-center flex-wrap gap-2 text-xs text-slate-500 mt-1">
                <span class="font-mono font-bold text-brand-700 bg-brand-50 px-2 py-0.5 rounded">{{ $scheme->code }}</span>
                <span>•</span>
                <span>Jenis: <strong class="text-slate-700">{{ $scheme->scheme_type ?? 'Klaster' }}</strong></span>
                <span>•</span>
                <span>Passing Grade: <strong class="text-slate-700">{{ $scheme->passing_grade_theory ?? 70 }}%</strong></span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $scheme->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                    {{ $scheme->is_active ? 'AKTIF' : 'NON-AKTIF' }}
                </span>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.schemes.edit', $scheme->id) }}" class="px-4 py-2 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 rounded-lg text-xs font-bold shadow-xs transition">
                Edit Skema
            </a>
            <button
                type="button"
                @click="showAddUnitModal = true"
                class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg text-xs font-bold shadow-sm transition inline-flex items-center gap-1.5 cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>+ Tambah Unit Kompetensi</span>
            </button>
        </div>
    </div>

    {{-- Scheme Description --}}
    @if($scheme->description)
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs text-xs text-slate-700 leading-relaxed">
            <span class="font-bold text-slate-900 block mb-1">Deskripsi Skema:</span>
            {{ $scheme->description }}
        </div>
    @endif

    {{-- Competency Units & Elements List --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-sm font-bold text-slate-900">
                    Daftar Unit Kompetensi Standar SKKNI ({{ $scheme->competencyUnits->count() }} Unit)
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Kelola unit kompetensi, elemen kompetensi, dan kriteria unjuk kerja (KUK).</p>
            </div>
            <button
                type="button"
                @click="showAddUnitModal = true"
                class="px-3 py-1.5 bg-brand-50 hover:bg-brand-100 text-brand-700 rounded-lg text-xs font-semibold transition inline-flex items-center gap-1 shrink-0 self-start sm:self-auto"
            >
                <span>+ Tambah Unit</span>
            </button>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($scheme->competencyUnits as $index => $unit)
                <div class="p-5 hover:bg-slate-50/40 transition">
                    {{-- Unit Header Bar --}}
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                        <div class="space-y-1.5 flex-1">
                            <div class="flex items-center flex-wrap gap-2">
                                <span class="px-2 py-0.5 text-xs font-mono font-bold bg-slate-100 text-slate-800 rounded border border-slate-200/60">{{ $unit->code }}</span>
                                <span class="text-[10px] uppercase font-bold text-slate-400 bg-slate-50 px-1.5 py-0.5 rounded border border-slate-200/40">{{ $unit->standard_reference ?? 'SKKNI' }}</span>
                                <span class="text-[10px] text-slate-400 font-medium">Urutan: #{{ $unit->sequence ?? ($index + 1) }}</span>
                            </div>
                            <h3 class="text-sm font-bold text-slate-900 leading-snug">{{ $unit->title }}</h3>
                            <div class="text-xs text-slate-500 flex items-center gap-3">
                                <span>{{ $unit->elements->count() }} Elemen Kompetensi</span>
                                <span>•</span>
                                <span>{{ $unit->kuks->count() }} Butir KUK</span>
                            </div>
                        </div>

                        {{-- Action Buttons for Unit --}}
                        <div class="flex items-center gap-1.5 shrink-0">
                            <button
                                type="button"
                                @click="activeUnitId = {{ $unit->id }}; showAddElementModal = true"
                                class="px-2.5 py-1 text-xs font-semibold text-brand-700 bg-brand-50 hover:bg-brand-100 rounded-lg transition"
                                title="Tambah Elemen ke Unit Ini"
                            >
                                + Elemen
                            </button>

                            <button
                                type="button"
                                @click="editUnitData = { id: {{ $unit->id }}, code: '{{ addslashes($unit->code) }}', title: '{{ addslashes($unit->title) }}', standard: '{{ addslashes($unit->standard_reference ?? 'SKKNI') }}' }; showEditUnitModal = true"
                                class="p-1.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition"
                                title="Ubah Unit Kompetensi"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>

                            <form method="POST" action="{{ route('admin.schemes.units.destroy', $unit->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus unit kompetensi ini beserta seluruh elemen dan KUK-nya?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Hapus Unit Kompetensi">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>

                            <button
                                type="button"
                                @click="openUnits[{{ $unit->id }}] = !openUnits[{{ $unit->id }}]"
                                class="text-xs text-brand-600 hover:text-brand-700 font-semibold px-2 py-1 hover:bg-brand-50/50 rounded-lg transition"
                            >
                                <span x-show="!openUnits[{{ $unit->id }}]">Lihat Elemen & KUK &darr;</span>
                                <span x-show="openUnits[{{ $unit->id }}]">Tutup &uarr;</span>
                            </button>
                        </div>
                    </div>

                    {{-- Elements & KUK Accordion --}}
                    <div x-show="openUnits[{{ $unit->id }}]" x-cloak class="mt-4 pt-4 border-t border-slate-100 space-y-4">
                        <div class="flex items-center justify-between text-xs text-slate-500 font-semibold px-1">
                            <span>Daftar Elemen Kompetensi:</span>
                            <button
                                type="button"
                                @click="activeUnitId = {{ $unit->id }}; showAddElementModal = true"
                                class="text-brand-600 hover:underline font-bold"
                            >
                                + Tambah Elemen Baru
                            </button>
                        </div>

                        @forelse($unit->elements as $elIndex => $element)
                            <div class="bg-slate-50/70 border border-slate-200/80 rounded-xl p-4 text-xs">
                                <div class="flex items-start justify-between gap-3 pb-2.5 border-b border-slate-200/60">
                                    <div>
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Elemen {{ $elIndex + 1 }}</span>
                                        <h4 class="font-bold text-slate-800 text-xs mt-0.5">
                                            {{ $element->title ?? $element->name }}
                                        </h4>
                                    </div>
                                    <div class="flex items-center gap-1.5 shrink-0">
                                        <button
                                            type="button"
                                            @click="activeElementId = {{ $element->id }}; showAddKukModal = true"
                                            class="text-[11px] font-semibold text-brand-700 bg-brand-50 hover:bg-brand-100 px-2 py-0.5 rounded transition"
                                            title="Tambah KUK ke elemen ini"
                                        >
                                            + Tambah KUK
                                        </button>
                                        <button
                                            type="button"
                                            @click="editElementData = { id: {{ $element->id }}, title: '{{ addslashes($element->title) }}' }; showEditElementModal = true"
                                            class="p-1 text-slate-400 hover:text-slate-700 hover:bg-slate-200/50 rounded transition"
                                            title="Ubah Elemen"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <form method="POST" action="{{ route('admin.schemes.elements.destroy', $element->id) }}" onsubmit="return confirm('Hapus elemen kompetensi ini beserta seluruh KUK di dalamnya?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded transition" title="Hapus Elemen">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                {{-- KUK List --}}
                                <div class="mt-3">
                                    <table class="w-full text-left text-xs">
                                        <thead class="text-slate-400 uppercase font-semibold text-[10px]">
                                            <tr>
                                                <th class="py-1 w-16">No KUK</th>
                                                <th class="py-1">Pernyataan Kriteria Unjuk Kerja (KUK)</th>
                                                <th class="py-1 w-16 text-right">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-200/40">
                                            @forelse($element->kuks as $kuk)
                                                <tr class="hover:bg-white/80 transition">
                                                    <td class="py-2 align-top font-mono text-[11px] font-bold text-brand-700">
                                                        {{ $kuk->code_number ?? $kuk->criteria_number }}
                                                    </td>
                                                    <td class="py-2 align-top text-slate-700 leading-relaxed pr-3">
                                                        {{ $kuk->statement }}
                                                    </td>
                                                    <td class="py-2 align-top text-right shrink-0">
                                                        <div class="flex items-center justify-end gap-1">
                                                            <button
                                                                type="button"
                                                                @click="editKukData = { id: {{ $kuk->id }}, code: '{{ addslashes($kuk->code_number) }}', statement: '{{ addslashes($kuk->statement) }}' }; showEditKukModal = true"
                                                                class="p-0.5 text-slate-400 hover:text-slate-700 rounded transition"
                                                                title="Ubah KUK"
                                                            >
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                                </svg>
                                                            </button>
                                                            <form method="POST" action="{{ route('admin.schemes.criterias.destroy', $kuk->id) }}" onsubmit="return confirm('Hapus butir KUK ini?');" class="inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="p-0.5 text-slate-300 hover:text-rose-600 rounded transition" title="Hapus KUK">
                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                    </svg>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="py-2.5 text-center text-slate-400 italic">
                                                        Belum ada butir KUK terdaftar. Klik "+ Tambah KUK" di atas untuk menambahkan.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 bg-slate-50 rounded-xl text-center text-xs text-slate-400">
                                Belum ada elemen kompetensi pada unit ini. Klik "+ Elemen" di atas untuk menambahkan.
                            </div>
                        @endforelse
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-500 text-xs">
                    <p>Belum ada unit kompetensi yang terhubung ke skema ini.</p>
                    <button
                        type="button"
                        @click="showAddUnitModal = true"
                        class="mt-3 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg text-xs font-bold shadow-sm transition"
                    >
                        + Tambah Unit Kompetensi Pertama
                    </button>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ==================== MODALS ==================== --}}

    {{-- MODAL 1: Tambah Unit Kompetensi --}}
    <div x-show="showAddUnitModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs" style="display: none;" x-cloak>
        <div @click.away="showAddUnitModal = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-sm font-bold text-slate-900">Tambah Unit Kompetensi Baru</h3>
                <button type="button" @click="showAddUnitModal = false" class="text-slate-400 hover:text-slate-600 text-sm">✕</button>
            </div>
            <form method="POST" action="{{ route('admin.schemes.units.store', $scheme->id) }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700">Kode Unit Kompetensi <span class="text-rose-500">*</span></label>
                    <input type="text" name="kode_unit" required placeholder="Contoh: G.45TSM01.034.2" class="mt-1 w-full px-3 py-2 text-xs bg-white border border-slate-300 rounded-lg font-mono focus:ring-1 focus:ring-brand-500" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700">Judul Unit Kompetensi <span class="text-rose-500">*</span></label>
                    <input type="text" name="judul_unit" required placeholder="Contoh: Melakukan bongkar pasang cover body" class="mt-1 w-full px-3 py-2 text-xs bg-white border border-slate-300 rounded-lg focus:ring-1 focus:ring-brand-500" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700">Jenis Standar Acuan</label>
                    <input type="text" name="jenis_standar" value="SKKNI" placeholder="Contoh: SKKNI No. 147 Tahun 2019" class="mt-1 w-full px-3 py-2 text-xs bg-white border border-slate-300 rounded-lg focus:ring-1 focus:ring-brand-500" />
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" @click="showAddUnitModal = false" class="px-3 py-1.5 text-xs text-slate-600 hover:bg-slate-100 rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">Simpan Unit</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL 2: Edit Unit Kompetensi --}}
    <div x-show="showEditUnitModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs" style="display: none;" x-cloak>
        <div @click.away="showEditUnitModal = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-sm font-bold text-slate-900">Ubah Unit Kompetensi</h3>
                <button type="button" @click="showEditUnitModal = false" class="text-slate-400 hover:text-slate-600 text-sm">✕</button>
            </div>
            <form :action="'/admin/units/' + editUnitData.id" method="POST" class="space-y-3">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-semibold text-slate-700">Kode Unit Kompetensi <span class="text-rose-500">*</span></label>
                    <input type="text" name="kode_unit" x-model="editUnitData.code" required class="mt-1 w-full px-3 py-2 text-xs bg-white border border-slate-300 rounded-lg font-mono focus:ring-1 focus:ring-brand-500" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700">Judul Unit Kompetensi <span class="text-rose-500">*</span></label>
                    <input type="text" name="judul_unit" x-model="editUnitData.title" required class="mt-1 w-full px-3 py-2 text-xs bg-white border border-slate-300 rounded-lg focus:ring-1 focus:ring-brand-500" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700">Jenis Standar Acuan</label>
                    <input type="text" name="jenis_standar" x-model="editUnitData.standard" class="mt-1 w-full px-3 py-2 text-xs bg-white border border-slate-300 rounded-lg focus:ring-1 focus:ring-brand-500" />
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" @click="showEditUnitModal = false" class="px-3 py-1.5 text-xs text-slate-600 hover:bg-slate-100 rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">Perbarui Unit</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL 3: Tambah Elemen Kompetensi --}}
    <div x-show="showAddElementModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs" style="display: none;" x-cloak>
        <div @click.away="showAddElementModal = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-sm font-bold text-slate-900">Tambah Elemen Kompetensi</h3>
                <button type="button" @click="showAddElementModal = false" class="text-slate-400 hover:text-slate-600 text-sm">✕</button>
            </div>
            <form :action="'/admin/units/' + activeUnitId + '/elements'" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700">Judul / Pernyataan Elemen Kompetensi <span class="text-rose-500">*</span></label>
                    <textarea name="nama_elemen" required rows="3" placeholder="Contoh: Menyiapkan bongkar-pasang cover body sesuai standar" class="mt-1 w-full px-3 py-2 text-xs bg-white border border-slate-300 rounded-lg focus:ring-1 focus:ring-brand-500"></textarea>
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" @click="showAddElementModal = false" class="px-3 py-1.5 text-xs text-slate-600 hover:bg-slate-100 rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">Simpan Elemen</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL 4: Edit Elemen Kompetensi --}}
    <div x-show="showEditElementModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs" style="display: none;" x-cloak>
        <div @click.away="showEditElementModal = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-sm font-bold text-slate-900">Ubah Elemen Kompetensi</h3>
                <button type="button" @click="showEditElementModal = false" class="text-slate-400 hover:text-slate-600 text-sm">✕</button>
            </div>
            <form :action="'/admin/elements/' + editElementData.id" method="POST" class="space-y-3">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-semibold text-slate-700">Judul / Pernyataan Elemen Kompetensi <span class="text-rose-500">*</span></label>
                    <textarea name="nama_elemen" x-model="editElementData.title" required rows="3" class="mt-1 w-full px-3 py-2 text-xs bg-white border border-slate-300 rounded-lg focus:ring-1 focus:ring-brand-500"></textarea>
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" @click="showEditElementModal = false" class="px-3 py-1.5 text-xs text-slate-600 hover:bg-slate-100 rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">Perbarui Elemen</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL 5: Tambah KUK --}}
    <div x-show="showAddKukModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs" style="display: none;" x-cloak>
        <div @click.away="showAddKukModal = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-sm font-bold text-slate-900">Tambah Kriteria Unjuk Kerja (KUK)</h3>
                <button type="button" @click="showAddKukModal = false" class="text-slate-400 hover:text-slate-600 text-sm">✕</button>
            </div>
            <form :action="'/admin/elements/' + activeElementId + '/criterias'" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700">Nomor KUK <span class="text-rose-500">*</span></label>
                    <input type="text" name="nomor_kuk" required placeholder="Contoh: 1.1" class="mt-1 w-full px-3 py-2 text-xs bg-white border border-slate-300 rounded-lg font-mono focus:ring-1 focus:ring-brand-500" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700">Pernyataan Kriteria Unjuk Kerja <span class="text-rose-500">*</span></label>
                    <textarea name="pernyataan" required rows="3" placeholder="Contoh: Komponen cover body diidentifikasi fungsinya..." class="mt-1 w-full px-3 py-2 text-xs bg-white border border-slate-300 rounded-lg focus:ring-1 focus:ring-brand-500"></textarea>
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" @click="showAddKukModal = false" class="px-3 py-1.5 text-xs text-slate-600 hover:bg-slate-100 rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">Simpan KUK</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL 6: Edit KUK --}}
    <div x-show="showEditKukModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs" style="display: none;" x-cloak>
        <div @click.away="showEditKukModal = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-sm font-bold text-slate-900">Ubah Kriteria Unjuk Kerja (KUK)</h3>
                <button type="button" @click="showEditKukModal = false" class="text-slate-400 hover:text-slate-600 text-sm">✕</button>
            </div>
            <form :action="'/admin/criterias/' + editKukData.id" method="POST" class="space-y-3">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-semibold text-slate-700">Nomor KUK <span class="text-rose-500">*</span></label>
                    <input type="text" name="nomor_kuk" x-model="editKukData.code" required class="mt-1 w-full px-3 py-2 text-xs bg-white border border-slate-300 rounded-lg font-mono focus:ring-1 focus:ring-brand-500" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700">Pernyataan Kriteria Unjuk Kerja <span class="text-rose-500">*</span></label>
                    <textarea name="pernyataan" x-model="editKukData.statement" required rows="3" class="mt-1 w-full px-3 py-2 text-xs bg-white border border-slate-300 rounded-lg focus:ring-1 focus:ring-brand-500"></textarea>
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" @click="showEditKukModal = false" class="px-3 py-1.5 text-xs text-slate-600 hover:bg-slate-100 rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">Perbarui KUK</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
