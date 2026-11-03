@extends('layouts.app')

@section('title', 'Laporan Pemeliharaan Kompetensi (FR.SP.01)')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-3xl">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">FORMULIR FR.SP.01</h1>
            <p class="text-sm text-gray-600 mt-1">Laporan Surveilans Pemeliharaan Kompetensi Pemegang Sertifikat</p>
        </div>
        <a href="{{ route('asesi.surveillance.index') }}" class="text-sm text-gray-600 hover:text-gray-900 font-medium">
            ← Kembali ke Jadwal Surveilans
        </a>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Detail Program Surveilans --}}
    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm mb-6 text-sm space-y-3">
        <div class="flex items-center justify-between border-b border-gray-200 pb-3">
            <h2 class="font-bold text-gray-900 text-base">{{ $plan->title }}</h2>
            <span class="inline-flex px-2.5 py-0.5 rounded text-xs font-semibold
                {{ $plan->status === 'COMPLETED' ? 'bg-green-100 text-green-800' : ($plan->status === 'REPORTED' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                Status: {{ $plan->status }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <span class="text-xs text-gray-500 block">Nomor Sertifikat:</span>
                <span class="font-mono font-medium text-primary">{{ $plan->certificate?->certificate_number }}</span>
            </div>
            <div>
                <span class="text-xs text-gray-500 block">Skema Sertifikasi:</span>
                <span class="font-medium text-gray-900">{{ $plan->certificate?->assessment?->scheme?->name }}</span>
            </div>
            <div>
                <span class="text-xs text-gray-500 block">Batas Waktu Pengumpulan:</span>
                <span class="text-gray-900">{{ $plan->due_date?->format('d/m/Y') }}</span>
            </div>
        </div>

        <div class="pt-3 border-t border-gray-100">
            <span class="text-xs text-gray-500 block mb-1">Persyaratan Bukti yang Wajib Dikumpulkan:</span>
            <div class="p-3 bg-gray-50 border rounded text-xs text-gray-800 whitespace-pre-wrap">{{ $plan->required_evidence_description }}</div>
        </div>
    </div>

    {{-- Riwayat Laporan Sebelumnya --}}
    @if ($plan->reports->isNotEmpty())
        <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm mb-6 space-y-4 text-sm">
            <h3 class="font-semibold text-gray-900 text-base border-b border-gray-200 pb-2">Riwayat Laporan Anda</h3>
            @foreach ($plan->reports as $rep)
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg space-y-2 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Dikirim: {{ $rep->submitted_at?->format('d/m/Y H:i') }}</span>
                        <span class="font-bold {{ $rep->decision === 'COMPLIANT' ? 'text-green-700' : 'text-primary' }}">{{ $rep->decision ?? 'Menunggu Evaluasi' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block font-medium">Uraian Pekerjaan:</span>
                        <p class="text-gray-800 whitespace-pre-wrap">{{ $rep->activity_summary }}</p>
                    </div>
                    @if ($rep->evaluator_notes)
                        <div class="pt-2 border-t border-gray-200 text-gray-600">
                            <strong>Catatan Evaluator:</strong> {{ $rep->evaluator_notes }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    {{-- Form Pengiriman Laporan (FR.SP.01) --}}
    @if ($plan->status !== 'COMPLETED')
        <form action="{{ route('asesi.surveillance.submit-report', $plan->id) }}" method="POST" enctype="multipart/form-data" class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm space-y-5 text-sm">
            @csrf

            <div class="border-b border-gray-200 pb-3">
                <h3 class="font-bold text-gray-900 text-base">Kirim Laporan Pemeliharaan Kompetensi (FR.SP.01)</h3>
                <p class="text-xs text-gray-500 mt-1">Sampaikan laporan aktivitas kerja/praktik profesi terkini dan lampirkan berkas bukti pendukung.</p>
            </div>

            <div>
                <label for="activity_summary" class="block text-sm font-medium text-gray-700 mb-1">
                    Uraian Kegiatan Praktik / Pekerjaan Terkini <span class="text-red-500">*</span>
                </label>
                <textarea name="activity_summary" id="activity_summary" rows="5" required
                    class="w-full px-3 py-2 border rounded-md text-sm border-gray-300 focus:ring-primary focus:border-primary"
                    placeholder="Uraikan pekerjaan, proyek, atau penanganan kasus perbaikan kendaraan yang telah Anda kerjakan selama periode pemeliharaan kompetensi...">{{ old('activity_summary') }}</textarea>
                @error('activity_summary')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="evidence_file" class="block text-sm font-medium text-gray-700 mb-1">
                    Berkas Bukti Pendukung (PDF, JPG, PNG)
                </label>
                <input type="file" name="evidence_file" id="evidence_file" accept=".pdf,.jpg,.jpeg,.png"
                    class="w-full px-3 py-2 border rounded-md text-sm border-gray-300 focus:ring-primary focus:border-primary">
                @error('evidence_file')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-1">Maks. 10 MB. Satukan logbook, surat keterangan kerja, atau portofolio dalam 1 berkas bila memungkinkan.</p>
            </div>

            <div class="pt-4 border-t border-gray-200 flex items-center justify-end space-x-3">
                <a href="{{ route('asesi.surveillance.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2 bg-primary text-white text-sm font-medium rounded hover:bg-primary/90 focus:outline-none">
                    Kirim laporan surveilans
                </button>
            </div>
        </form>
    @endif
</div>
@endsection
