@extends('layouts.app')

@section('title', 'Detail & Investigasi Banding (FR-SOP-07)')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Penanganan Banding Asesmen</h1>
            <p class="text-sm text-gray-600 mt-1">Formulir FR-SOP-07.1 (Intake) & Berita Acara FR-SOP-07.2 (Investigasi Independen).</p>
        </div>
        <a href="{{ route('admin.appeals.index') }}" class="text-sm text-gray-600 hover:text-gray-900 font-medium">
            ← Kembali ke Daftar Banding
        </a>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm rounded">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded">
            {{ session('error') }}
        </div>
    @endif

    {{-- Kartu Ringkasan Intake Banding (FR.AK.04 / FR-SOP-07.1) --}}
    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm mb-6 space-y-4 text-sm">
        <div class="flex items-center justify-between border-b border-gray-200 pb-3">
            <h2 class="font-semibold text-gray-900 text-base">Berkas Pengajuan Banding (FR.AK.04)</h2>
            <span class="inline-flex px-2.5 py-0.5 rounded text-xs font-semibold
                {{ $appeal->status === 'ACCEPTED' ? 'bg-green-100 text-green-800' : ($appeal->status === 'REJECTED' ? 'bg-gray-100 text-gray-800' : 'bg-amber-100 text-amber-800') }}">
                Status: {{ $appeal->status }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <span class="text-xs text-gray-500 block">Nama Asesi Pemohon:</span>
                <span class="font-bold text-gray-900">{{ $appeal->assessment?->asesi?->name }}</span>
            </div>
            <div>
                <span class="text-xs text-gray-500 block">NIK:</span>
                <span class="font-mono text-gray-900">{{ $appeal->assessment?->asesi?->profile?->nik ?? '-' }}</span>
            </div>
            <div>
                <span class="text-xs text-gray-500 block">Skema Sertifikasi:</span>
                <span class="font-medium text-gray-900">{{ $appeal->assessment?->scheme?->name }}</span>
            </div>
            <div>
                <span class="text-xs text-gray-500 block">Asesor Penguji (Terkait Kasus):</span>
                <span class="font-medium text-red-700">{{ $appeal->assessment?->assessor?->name ?? 'Belum ditentukan' }}</span>
                <span class="text-xs text-red-500 block italic">*Tidak boleh ditugaskan sebagai investigator</span>
            </div>
            <div>
                <span class="text-xs text-gray-500 block">Tanggal Pengajuan:</span>
                <span class="text-gray-900">{{ $appeal->appeal_date?->format('d/m/Y') }}</span>
            </div>
        </div>

        <div class="mt-4 pt-3 border-t border-gray-100">
            <span class="text-xs text-gray-500 block mb-1">Alasan Banding oleh Asesi:</span>
            <div class="p-3 bg-gray-50 border rounded text-xs text-gray-800 whitespace-pre-wrap">{{ $appeal->appeal_reasons }}</div>
        </div>
    </div>

    {{-- Riwayat Berita Acara Investigasi (FR-SOP-07.2) --}}
    @if ($appeal->investigations->isNotEmpty())
        <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm mb-6 space-y-4">
            <h2 class="font-semibold text-gray-900 text-base border-b border-gray-200 pb-3">Riwayat Berita Acara Investigasi (FR-SOP-07.2)</h2>
            <div class="space-y-4 text-sm">
                @foreach ($appeal->investigations as $inv)
                    <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-semibold text-gray-900">Investigator: {{ $inv->investigator?->name }} ({{ $inv->investigator_role }})</span>
                            <span class="text-gray-500">{{ $inv->investigation_date?->format('d/m/Y') }} di {{ $inv->investigation_place }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">Temuan Faktual:</span>
                            <p class="text-xs text-gray-800 whitespace-pre-wrap">{{ $inv->factual_findings }}</p>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-gray-200 text-xs">
                            <div>
                                <span class="text-gray-500">Rekomendasi Tindak Lanjut:</span>
                                <span class="font-bold text-primary">{{ $inv->recommendation }}</span>
                            </div>
                            <span class="text-gray-400">Disahkan oleh: {{ $inv->decider?->name ?? 'Admin LSP' }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Form Pencatatan Investigasi Baru (FR-SOP-07.2) --}}
    <form action="{{ route('admin.appeals.investigate', $appeal->id) }}" method="POST" class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm space-y-5 text-sm">
        @csrf

        <div class="border-b border-gray-200 pb-3">
            <h2 class="font-bold text-gray-900 text-base">Pencatatan Investigasi Banding Baru (FR-SOP-07.2)</h2>
            <p class="text-xs text-gray-500 mt-1">Investigator harus independen dan terbukti tidak terlibat dalam pengujian asesi ini.</p>
        </div>

        <div>
            <label for="investigator_id" class="block text-sm font-medium text-gray-700 mb-1">
                Tugaskan Investigator Banding Independen <span class="text-red-500">*</span>
            </label>
            <select name="investigator_id" id="investigator_id" required class="w-full px-3 py-2 border rounded-md text-sm border-gray-300 focus:ring-primary focus:border-primary">
                <option value="">-- Pilih Asesor Independen --</option>
                @foreach ($potentialInvestigators as $invUser)
                    <option value="{{ $invUser->id }}" {{ old('investigator_id') == $invUser->id ? 'selected' : '' }}>
                        {{ $invUser->name }} ({{ $invUser->email }})
                    </option>
                @endforeach
            </select>
            @error('investigator_id')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
            <p class="text-xs text-gray-500 mt-1">Asesor {{ $appeal->assessment?->assessor?->name }} secara otomatis dikecualikan dari daftar demi objektivitas.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="investigation_date" class="block text-sm font-medium text-gray-700 mb-1">
                    Tanggal Investigasi <span class="text-red-500">*</span>
                </label>
                <input type="date" name="investigation_date" id="investigation_date" required value="{{ old('investigation_date', now()->toDateString()) }}"
                    class="w-full px-3 py-2 border rounded-md text-sm border-gray-300 focus:ring-primary focus:border-primary">
                @error('investigation_date')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="investigation_place" class="block text-sm font-medium text-gray-700 mb-1">
                    Tempat Investigasi <span class="text-red-500">*</span>
                </label>
                <input type="text" name="investigation_place" id="investigation_place" required value="{{ old('investigation_place', 'Ruang Sidang LSP ProIndo') }}"
                    class="w-full px-3 py-2 border rounded-md text-sm border-gray-300 focus:ring-primary focus:border-primary">
                @error('investigation_place')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label for="factual_findings" class="block text-sm font-medium text-gray-700 mb-1">
                Temuan Faktual Investigasi (FR-SOP-07.2) <span class="text-red-500">*</span>
            </label>
            <textarea name="factual_findings" id="factual_findings" rows="4" required
                class="w-full px-3 py-2 border rounded-md text-sm border-gray-300 focus:ring-primary focus:border-primary"
                placeholder="Uraikan temuan faktual hasil telaah bukti instrumen asesmen dan wawancara para pihak...">{{ old('factual_findings') }}</textarea>
            @error('factual_findings')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="recommendation" class="block text-sm font-medium text-gray-700 mb-1">
                    Rekomendasi Tindak Lanjut <span class="text-red-500">*</span>
                </label>
                <select name="recommendation" id="recommendation" required class="w-full px-3 py-2 border rounded-md text-sm border-gray-300 focus:ring-primary focus:border-primary">
                    <option value="">-- Pilih Rekomendasi --</option>
                    <option value="PERTAHANKAN_KEPUTUSAN" {{ old('recommendation') == 'PERTAHANKAN_KEPUTUSAN' ? 'selected' : '' }}>Pertahankan Keputusan Asesor (Tolak Banding)</option>
                    <option value="ASESMEN_ULANG" {{ old('recommendation') == 'ASESMEN_ULANG' ? 'selected' : '' }}>Asesmen Ulang pada Unit Terkait (Terima Banding)</option>
                    <option value="BATALKAN_KEPUTUSAN" {{ old('recommendation') == 'BATALKAN_KEPUTUSAN' ? 'selected' : '' }}>Batalkan Rekomendasi Asesor (Terima Banding)</option>
                </select>
                @error('recommendation')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="recommendation_notes" class="block text-sm font-medium text-gray-700 mb-1">
                    Catatan Rekomendasi
                </label>
                <input type="text" name="recommendation_notes" id="recommendation_notes" value="{{ old('recommendation_notes') }}"
                    class="w-full px-3 py-2 border rounded-md text-sm border-gray-300 focus:ring-primary focus:border-primary"
                    placeholder="misal: Asesmen ulang dijadwalkan pada siklus berikutnya">
                @error('recommendation_notes')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="pt-4 border-t border-gray-200 flex items-center justify-end space-x-3">
            <a href="{{ route('admin.appeals.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded hover:bg-gray-50">
                Batal
            </a>
            <button type="submit" class="px-5 py-2 bg-primary text-white text-sm font-medium rounded hover:bg-primary/90 focus:outline-none">
                Simpan berita acara investigasi
            </button>
        </div>
    </form>
</div>
@endsection
