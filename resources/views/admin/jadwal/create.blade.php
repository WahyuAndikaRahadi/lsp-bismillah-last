@extends('layouts.app', ['title' => 'Buat Jadwal Baru'])

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <div>
        <a href="{{ route('admin.jadwal.index') }}" class="text-xs text-brand-600 hover:text-brand-700 font-semibold">&larr; Kembali ke Kelola Jadwal</a>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">Buat Jadwal Asesmen Baru</h1>
        <p class="text-sm text-slate-500">Lengkapi formulir untuk menyelenggarakan asesmen baru</p>
    </div>

    <form action="{{ route('admin.jadwal.store') }}" method="POST" class="space-y-6" x-data="{
        code: '{{ old('code', '') }}',
        generateCode() {
            const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
            let res = 'LSP-';
            for (let i = 0; i < 6; i++) {
                res += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            this.code = res;
        }
    }">
        @csrf

        {{-- Basic Information --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h2 class="text-base font-bold text-slate-900">Informasi Dasar Jadwal</h2>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Jadwal <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required value="{{ old('name') }}" placeholder="Contoh: Uji Kompetensi Pemeliharaan Kendaraan Ringan Angkatan 1" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                @error('name')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Skema Sertifikasi <span class="text-rose-500">*</span></label>
                    <select name="scheme_id" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none bg-white">
                        <option value="">-- Pilih Skema --</option>
                        @foreach($schemes ?? [] as $scheme)
                            <option value="{{ $scheme->id }}" {{ old('scheme_id') == $scheme->id ? 'selected' : '' }}>
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
                    <input type="text" name="tuk_name" required value="{{ old('tuk_name') }}" placeholder="Contoh: SMKN 1 Jakarta / Bengkel Mitra" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    @error('tuk_name')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Asesor yang Ditugaskan (Multi-select) <span class="text-rose-500">*</span></label>
                <div class="border border-slate-200 rounded-lg p-3 max-h-40 overflow-y-auto space-y-1.5">
                    @forelse($assessors ?? [] as $asesor)
                        <label class="flex items-center space-x-2.5 text-sm text-slate-700 cursor-pointer hover:bg-slate-50 p-1 rounded">
                            <input type="checkbox" name="assessor_ids[]" value="{{ $asesor->id }}" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500" {{ in_array($asesor->id, old('assessor_ids', [])) ? 'checked' : '' }}>
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
                    <input type="date" name="date" required value="{{ old('date') }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    @error('date')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Jam Mulai <span class="text-rose-500">*</span></label>
                    <input type="time" name="start_time" required value="{{ old('start_time', '08:00') }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    @error('start_time')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Jam Selesai <span class="text-rose-500">*</span></label>
                    <input type="time" name="end_time" required value="{{ old('end_time', '16:00') }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    @error('end_time')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>


        {{-- Schedule Code & Notes --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h2 class="text-base font-bold text-slate-900">Kode Jadwal & Catatan</h2>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Kode Jadwal (Untuk Asesi Masuk) <span class="text-rose-500">*</span></label>
                <div class="flex space-x-2">
                    <input type="text" name="code" required x-model="code" placeholder="Contoh: LSP-XYZ123" class="flex-1 px-4 py-2.5 rounded-lg border border-slate-300 text-sm font-mono uppercase focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    <button type="button" @click="generateCode()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition">
                        Generate Kode Acak
                    </button>
                </div>
                @error('code')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Catatan Tambahan</label>
                <textarea name="notes" rows="3" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none" placeholder="Catatan instruksi khusus untuk asesi atau asesor...">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('admin.jadwal.index') }}" class="px-5 py-2.5 text-sm font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition">Batal</a>
            <button type="submit" class="px-6 py-2.5 text-sm font-bold bg-brand-600 hover:bg-brand-700 text-white rounded-lg shadow-sm transition">
                Simpan Jadwal Baru
            </button>
        </div>
    </form>
</div>
@endsection
