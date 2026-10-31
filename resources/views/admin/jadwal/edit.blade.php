@extends('layouts.app', ['title' => 'Edit Jadwal Asesmen - ' . $jadwal->code])

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <div>
        <a href="{{ route('admin.jadwal.show', $jadwal->id) }}" class="text-xs text-brand-600 hover:text-brand-700 font-semibold">&larr; Kembali ke Detail Jadwal</a>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">Edit Jadwal Asesmen</h1>
        <p class="text-sm text-slate-500">Perbarui data pelaksanaan asesmen untuk kode <span class="font-mono font-bold text-brand-700">{{ $jadwal->code }}</span></p>
    </div>

    <form action="{{ route('admin.jadwal.update', $jadwal->id) }}" method="POST" class="space-y-6" x-data="{
        code: '{{ old('code', $jadwal->code) }}'
    }">
        @csrf
        @method('PUT')

        {{-- Basic Information --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h2 class="text-base font-bold text-slate-900">Informasi Dasar Jadwal</h2>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Jadwal <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required value="{{ old('name', $jadwal->name) }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                @error('name')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Skema Sertifikasi <span class="text-rose-500">*</span></label>
                    <select name="scheme_id" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none bg-white">
                        @foreach($schemes ?? [] as $scheme)
                            <option value="{{ $scheme->id }}" {{ old('scheme_id', $jadwal->scheme_id) == $scheme->id ? 'selected' : '' }}>
                                {{ $scheme->name }} ({{ $scheme->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('scheme_id')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Tempat Uji Kompetensi (TUK) <span class="text-rose-500">*</span></label>
                    <input type="text" name="tuk_name" required value="{{ old('tuk_name', $jadwal->tuk_name) }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    @error('tuk_name')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Asesor yang Ditugaskan (Multi-select) <span class="text-rose-500">*</span></label>
                @php
                    $selectedAssessorIds = old('assessor_ids', $jadwal->assessors->pluck('id')->toArray());
                @endphp
                <div class="border border-slate-200 rounded-lg p-3 max-h-40 overflow-y-auto space-y-1.5">
                    @forelse($assessors ?? [] as $asesor)
                        <label class="flex items-center space-x-2.5 text-sm text-slate-700 cursor-pointer hover:bg-slate-50 p-1 rounded">
                            <input type="checkbox" name="assessor_ids[]" value="{{ $asesor->id }}" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500" {{ in_array($asesor->id, $selectedAssessorIds) ? 'checked' : '' }}>
                            <span>{{ $asesor->full_name }}</span>
                            <span class="text-xs text-slate-400">({{ $asesor->email }})</span>
                        </label>
                    @empty
                        <p class="text-xs text-slate-400">Tidak ada data asesor terdaftar.</p>
                    @endforelse
                </div>
                @error('assessor_ids')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Date and Time --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h2 class="text-base font-bold text-slate-900">Jadwal Pelaksanaan</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Tanggal Pelaksanaan <span class="text-rose-500">*</span></label>
                    <input type="date" name="date" required value="{{ old('date', \Carbon\Carbon::parse($jadwal->date)->format('Y-m-d')) }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    @error('date')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Jam Mulai <span class="text-rose-500">*</span></label>
                    <input type="time" name="start_time" required value="{{ old('start_time', substr($jadwal->start_time ?? '08:00', 0, 5)) }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    @error('start_time')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Jam Selesai <span class="text-rose-500">*</span></label>
                    <input type="time" name="end_time" required value="{{ old('end_time', substr($jadwal->end_time ?? '16:00', 0, 5)) }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    @error('end_time')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Instruments Checkboxes --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h2 class="text-base font-bold text-slate-900">Instrumen Asesmen yang Digunakan</h2>
            <p class="text-xs text-slate-500">Pilih instrumen yang diaktifkan untuk jadwal asesmen ini</p>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                @php
                    $instruments = [
                        'AK01' => 'FR.AK.01 - Persetujuan Asesmen',
                        'AK07' => 'FR.AK.07 - Penyesuaian',
                        'IA01' => 'FR.IA.01 - Ceklis Observasi',
                        'IA02' => 'FR.IA.02 - Tugas Praktik Demonstrasi',
                        'IA03' => 'FR.IA.03 - Pertanyaan PMO (Lisan)',
                        'IA05' => 'FR.IA.05 - Ujian Tertulis Pilihan Ganda (CBT)',
                    ];
                    $selectedInstruments = old('selected_instruments', old('instruments', $jadwal->selected_instruments ?? ['AK01', 'IA01', 'IA02', 'IA03', 'IA05']));
                @endphp
                @foreach($instruments as $key => $label)
                    <label class="flex items-center space-x-2.5 p-3 rounded-lg border border-slate-200 hover:bg-slate-50 cursor-pointer">
                        <input type="checkbox" name="selected_instruments[]" value="{{ $key }}" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500"
                            {{ in_array($key, $selectedInstruments) ? 'checked' : '' }}>
                        <span class="text-xs font-semibold text-slate-800">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Schedule Code & Notes --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h2 class="text-base font-bold text-slate-900">Kode Jadwal & Catatan</h2>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Kode Jadwal</label>
                <input type="text" readonly value="{{ $jadwal->code }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm font-mono uppercase bg-slate-100 text-slate-600 cursor-not-allowed">
                <p class="text-xs text-slate-400 mt-1">Kode jadwal bersifat unik dan tidak dapat diubah setelah dibuat.</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Catatan Tambahan</label>
                <textarea name="notes" rows="3" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">{{ old('notes', $jadwal->notes) }}</textarea>
                @error('notes')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('admin.jadwal.show', $jadwal->id) }}" class="px-5 py-2.5 text-sm font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition">Batal</a>
            <button type="submit" class="px-6 py-2.5 text-sm font-bold bg-brand-600 hover:bg-brand-700 text-white rounded-lg shadow-sm transition">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
