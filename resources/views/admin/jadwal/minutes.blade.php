@extends('layouts.app', ['title' => 'Berita Acara Pelaksanaan Uji Kompetensi'])

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-xs">
        <h1 class="text-xl font-bold text-slate-900">Berita Acara Pelaksanaan Uji Kompetensi</h1>
        <p class="text-xs text-slate-500 mt-1">Jadwal: {{ $schedule->name }} ({{ $schedule->code }}) &bull; Skema: {{ $schedule->scheme?->name }}</p>
    </div>

    {{-- Facts Summary Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-6 gap-3">
        <div class="bg-white p-4 rounded-xl border border-slate-200 text-center shadow-xs">
            <span class="text-xs text-slate-500 font-semibold block">Terdaftar</span>
            <span class="text-xl font-bold text-slate-900">{{ $facts['enrolled_count'] }}</span>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 text-center shadow-xs">
            <span class="text-xs text-slate-500 font-semibold block">Hadir</span>
            <span class="text-xl font-bold text-emerald-600">{{ $facts['present_count'] }}</span>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 text-center shadow-xs">
            <span class="text-xs text-slate-500 font-semibold block">Tidak Hadir</span>
            <span class="text-xl font-bold text-rose-600">{{ $facts['absent_count'] }}</span>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 text-center shadow-xs">
            <span class="text-xs text-slate-500 font-semibold block">Kompeten (K)</span>
            <span class="text-xl font-bold text-emerald-700">{{ $facts['competent_count'] }}</span>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 text-center shadow-xs">
            <span class="text-xs text-slate-500 font-semibold block">Belum Komp (BK)</span>
            <span class="text-xl font-bold text-amber-700">{{ $facts['not_competent_count'] }}</span>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 text-center shadow-xs">
            <span class="text-xs text-slate-500 font-semibold block">Pending</span>
            <span class="text-xl font-bold text-slate-500">{{ $facts['pending_count'] }}</span>
        </div>
    </div>

    <form action="{{ route('admin.schedules.minutes.store', $schedule->id) }}" method="POST" class="space-y-6">
        @csrf
        <input type="hidden" name="lock_version" value="{{ $latestMinutes?->lock_version ?? 1 }}">

        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-xs space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kode Dokumen</label>
                    <input type="text" name="document_code" value="{{ old('document_code', $latestMinutes?->document_code ?? 'FR-SOP-04.4') }}" class="w-full text-xs rounded-lg border-slate-300">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Pelaksanaan Aktual *</label>
                    <input type="date" name="actual_date" value="{{ old('actual_date', $latestMinutes?->actual_date?->toDateString() ?? $schedule->date) }}" required class="w-full text-xs rounded-lg border-slate-300">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Waktu Mulai Aktual (WIB) *</label>
                    <input type="time" name="actual_start_time" value="{{ old('actual_start_time', $latestMinutes?->actual_start_time ?? $schedule->start_time) }}" required class="w-full text-xs rounded-lg border-slate-300">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Waktu Selesai Aktual (WIB) *</label>
                    <input type="time" name="actual_end_time" value="{{ old('actual_end_time', $latestMinutes?->actual_end_time ?? $schedule->end_time) }}" required class="w-full text-xs rounded-lg border-slate-300">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Koordinator TUK / Uji Kompetensi *</label>
                    <select name="coordinator_id" required class="w-full text-xs rounded-lg border-slate-300">
                        <option value="">-- Pilih Koordinator --</option>
                        @foreach($coordinators as $coord)
                            <option value="{{ $coord->id }}" {{ (old('coordinator_id', $latestMinutes?->coordinator_id ?? '') == $coord->id) ? 'selected' : '' }}>
                                {{ $coord->name }} ({{ $coord->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Status Berita Acara</label>
                    <select name="status" class="w-full text-xs rounded-lg border-slate-300">
                        <option value="DRAFT" {{ (old('status', $latestMinutes?->status ?? 'DRAFT') === 'DRAFT') ? 'selected' : '' }}>DRAFT</option>
                        <option value="FINAL" {{ (old('status', $latestMinutes?->status ?? '') === 'FINAL') ? 'selected' : '' }}>FINAL (Tutup Rekaman)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Tambahan Pelaksanaan</label>
                <textarea name="notes" rows="3" class="w-full text-xs rounded-lg border-slate-300">{{ old('notes', $latestMinutes?->notes ?? '') }}</textarea>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-5 py-2.5 bg-brand-900 hover:bg-brand-800 text-white rounded-lg text-xs font-bold shadow-xs transition">
                Simpan Berita Acara
            </button>
        </div>
    </form>
</div>
@endsection
