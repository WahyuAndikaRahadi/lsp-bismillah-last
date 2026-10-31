@extends('layouts.app', ['title' => 'Edit Skema Sertifikasi - ' . $scheme->code])

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <div>
        <a href="{{ route('admin.schemes.show', $scheme->id) }}" class="text-xs text-brand-600 hover:text-brand-700 font-semibold">&larr; Kembali ke Detail Skema</a>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">Edit Skema Sertifikasi</h1>
        <p class="text-sm text-slate-500">Perbarui spesifikasi skema sertifikasi <span class="font-mono font-bold text-brand-700">{{ $scheme->code }}</span></p>
    </div>

    <form action="{{ route('admin.schemes.update', $scheme->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h2 class="text-base font-bold text-slate-900">Informasi Skema Sertifikasi</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Kode Skema <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" required value="{{ old('code', $scheme->code) }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm font-mono uppercase focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    @error('code')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Jenis / Kategori Skema</label>
                    <select name="scheme_type" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none bg-white">
                        <option value="KLASTER" {{ old('scheme_type', $scheme->scheme_type) == 'KLASTER' ? 'selected' : '' }}>Klaster</option>
                        <option value="OKUPASI" {{ old('scheme_type', $scheme->scheme_type) == 'OKUPASI' ? 'selected' : '' }}>Okupasi</option>
                        <option value="KKNI" {{ old('scheme_type', $scheme->scheme_type) == 'KKNI' ? 'selected' : '' }}>KKNI</option>
                    </select>
                    @error('scheme_type')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Skema Sertifikasi <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required value="{{ old('name', $scheme->name) }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                @error('name')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Versi / Edisi Dokumen</label>
                    <input type="text" name="version" value="{{ old('version', $scheme->version) }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    @error('version')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Passing Grade Teori (CBT %) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" min="0" max="100" name="passing_grade_theory" value="{{ old('passing_grade_theory', $scheme->passing_grade_theory) }}" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    @error('passing_grade_theory')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Deskripsi / Ruang Lingkup</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">{{ old('description', $scheme->description) }}</textarea>
                @error('description')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="flex items-center space-x-2.5 text-sm font-semibold text-slate-700 cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500" {{ old('is_active', $scheme->is_active) ? 'checked' : '' }}>
                    <span>Skema Aktif dan Siap Digunakan dalam Jadwal Asesmen</span>
                </label>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('admin.schemes.show', $scheme->id) }}" class="px-5 py-2.5 text-sm font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition">Batal</a>
            <button type="submit" class="px-6 py-2.5 text-sm font-bold bg-brand-600 hover:bg-brand-700 text-white rounded-lg shadow-sm transition">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
