@extends('layouts.app', ['title' => 'Tambah Butir Soal CBT'])

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <div>
        <a href="{{ route('admin.question-banks.index') }}" class="text-xs text-brand-600 hover:text-brand-700 font-semibold">&larr; Kembali ke Bank Soal</a>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">Tambah Soal CBT (FR.IA.05)</h1>
        <p class="text-sm text-slate-500">Tambahkan butir pertanyaan tertulis pilihan ganda untuk ujian asesi</p>
    </div>

    <form action="{{ route('admin.question-banks.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h2 class="text-base font-bold text-slate-900">Skema & Unit Kompetensi</h2>

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
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Unit Kompetensi (Opsional)</label>
                    <select name="competency_unit_id" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none bg-white">
                        <option value="">-- Umum / Seluruh Skema --</option>
                        @foreach($schemes ?? [] as $scheme)
                            @foreach($scheme->competencyUnits ?? [] as $unit)
                                <option value="{{ $unit->id }}" {{ old('competency_unit_id') == $unit->id ? 'selected' : '' }}>
                                    [{{ $scheme->code }}] {{ $unit->code }} - {{ $unit->title }}
                                </option>
                            @endforeach
                        @endforeach
                    </select>
                    @error('competency_unit_id')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Teks Pertanyaan / Soal <span class="text-rose-500">*</span></label>
                <textarea name="question_text" rows="4" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none" placeholder="Tuliskan pertanyaan uji tertulis di sini...">{{ old('question_text') }}</textarea>
                @error('question_text')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-3 pt-2 border-t border-slate-100">
                <h3 class="text-sm font-bold text-slate-800">Pilihan Jawaban & Kunci</h3>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Pilihan A <span class="text-rose-500">*</span></label>
                    <input type="text" name="option_a" required value="{{ old('option_a') }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    @error('option_a')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Pilihan B <span class="text-rose-500">*</span></label>
                    <input type="text" name="option_b" required value="{{ old('option_b') }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    @error('option_b')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Pilihan C <span class="text-rose-500">*</span></label>
                    <input type="text" name="option_c" required value="{{ old('option_c') }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    @error('option_c')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Pilihan D <span class="text-rose-500">*</span></label>
                    <input type="text" name="option_d" required value="{{ old('option_d') }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    @error('option_d')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Kunci Jawaban Benar <span class="text-rose-500">*</span></label>
                    <div class="flex items-center space-x-6">
                        @foreach(['A', 'B', 'C', 'D'] as $choice)
                            <label class="flex items-center space-x-2 text-sm font-bold text-slate-800 cursor-pointer">
                                <input type="radio" name="correct_answer" value="{{ $choice }}" required {{ old('correct_answer') == $choice ? 'checked' : '' }} class="text-brand-600 focus:ring-brand-500">
                                <span>Pilihan {{ $choice }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('correct_answer')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('admin.question-banks.index') }}" class="px-5 py-2.5 text-sm font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition">Batal</a>
            <button type="submit" class="px-6 py-2.5 text-sm font-bold bg-brand-600 hover:bg-brand-700 text-white rounded-lg shadow-sm transition">
                Simpan Butir Soal
            </button>
        </div>
    </form>
</div>
@endsection
