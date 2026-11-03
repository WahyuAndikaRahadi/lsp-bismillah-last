@extends('layouts.app')

@section('title', 'Permohonan Sertifikasi Ulang (Resertifikasi)')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-3xl">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Permohonan Sertifikasi Ulang (Resertifikasi)</h1>
            <p class="text-sm text-gray-600 mt-1">Perpanjangan masa berlaku sertifikat kompetensi sesuai SOP 04 §5 LSP ProIndo.</p>
        </div>
        <a href="{{ route('asesi.certificates.index') }}" class="text-sm text-gray-600 hover:text-gray-900 font-medium">
            ← Kembali ke Sertifikat
        </a>
    </div>

    @if (session('error'))
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded">
            {{ session('error') }}
        </div>
    @endif

    {{-- Kartu Rujukan Sertifikat Lama --}}
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 mb-6 text-sm">
        <h2 class="font-semibold text-gray-800 mb-3 border-b border-gray-200 pb-2">Rujukan Sertifikat Sebelumnya (Prior Certificate)</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <span class="text-gray-500 block text-xs">Nomor Sertifikat:</span>
                <span class="font-mono font-bold text-primary">{{ $certificate->certificate_number }}</span>
            </div>
            <div>
                <span class="text-gray-500 block text-xs">Nomor Seri Blanko:</span>
                <span class="font-mono text-gray-900">{{ $certificate->blanko_number ?? '-' }}</span>
            </div>
            <div>
                <span class="text-gray-500 block text-xs">Skema Sertifikasi:</span>
                <span class="font-medium text-gray-900">{{ $scheme->name }} ({{ $scheme->code }})</span>
            </div>
            <div>
                <span class="text-gray-500 block text-xs">Masa Berlaku Sebelumnya:</span>
                <span class="text-gray-900">{{ $certificate->valid_from?->format('d/m/Y') }} s/d {{ $certificate->valid_until?->format('d/m/Y') }}</span>
            </div>
        </div>

        <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded text-xs text-blue-900">
            <strong>Profil Dapat Digunakan Kembali (Reusable Profile):</strong> Data pribadi, kependudukan, dan bukti dasar yang ada pada profil akun Anda akan disalin otomatis ke berkas asesmen baru ini. Anda tidak perlu mengulang pengisian formulir biodata dasar dari awal.
        </div>
    </div>

    {{-- Form Permohonan Resertifikasi --}}
    <form action="{{ route('asesi.certificates.recertify.store', $certificate->id) }}" method="POST" class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm space-y-5 text-sm">
        @csrf

        <div>
            <label for="schedule_id" class="block text-sm font-medium text-gray-700 mb-1">
                Pilih Jadwal Uji Asesmen Resertifikasi <span class="text-red-500">*</span>
            </label>
            <select name="schedule_id" id="schedule_id" required class="w-full px-3 py-2 border rounded-md text-sm border-gray-300 focus:ring-primary focus:border-primary">
                <option value="">-- Pilih Jadwal Tersedia untuk Skema Ini --</option>
                @forelse ($availableSchedules as $sch)
                    <option value="{{ $sch->id }}" {{ old('schedule_id') == $sch->id ? 'selected' : '' }}>
                        {{ $sch->name }} ({{ $sch->code }} - {{ $sch->date?->format('d/m/Y') }} - {{ $sch->tuk_name }})
                    </option>
                @empty
                    <option value="" disabled>Belum ada jadwal pelaksanaan terbuka untuk skema ini. Hubungi admin LSP.</option>
                @endforelse
            </select>
            @error('schedule_id')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
            <p class="text-xs text-gray-500 mt-1">Asesmen resertifikasi akan dijadwalkan secara resmi pada jadwal yang Anda pilih.</p>
        </div>

        <div>
            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                Catatan Permohonan Tambahan (Bila Ada)
            </label>
            <textarea name="notes" id="notes" rows="3"
                class="w-full px-3 py-2 border rounded-md text-sm border-gray-300 focus:ring-primary focus:border-primary"
                placeholder="Catatan mengenai pengalaman kerja terkini atau permohonan khusus...">{{ old('notes') }}</textarea>
            @error('notes')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-4 border-t border-gray-200 flex items-center justify-end space-x-3">
            <a href="{{ route('asesi.certificates.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded hover:bg-gray-50">
                Batal
            </a>
            <button type="submit" class="px-5 py-2 bg-primary text-white text-sm font-medium rounded hover:bg-primary/90 focus:outline-none">
                Ajukan sertifikasi ulang
            </button>
        </div>
    </form>
</div>
@endsection
