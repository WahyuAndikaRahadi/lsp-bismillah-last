@extends('layouts.app')

@section('title', 'Tanda Terima Sertifikat Kompetensi (FR-10.2)')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-3xl">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">FORMULIR FR-10.2</h1>
            <p class="text-sm text-gray-600 mt-1">Tanda Terima Penyerahan Sertifikat Kompetensi</p>
        </div>
        <a href="{{ route('asesi.certificates.index') }}" class="text-sm text-gray-600 hover:text-gray-900 font-medium">
            ← Kembali ke Daftar Sertifikat
        </a>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden mb-6">
        <div class="p-5 border-b border-gray-200 bg-gray-50">
            <h2 class="text-base font-semibold text-gray-900">Data Sertifikat & Pemegang</h2>
        </div>

        <div class="p-6 space-y-4 text-sm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <span class="text-gray-500 block text-xs">Nama Lengkap Pemegang:</span>
                    <span class="font-bold text-gray-900">{{ $certificate->assessment?->asesi?->name }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block text-xs">Nomor Induk Kependudukan (NIK):</span>
                    <span class="font-mono text-gray-900">{{ $certificate->assessment?->asesi?->profile?->nik ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block text-xs">Skema Sertifikasi:</span>
                    <span class="font-medium text-gray-900">{{ $certificate->assessment?->scheme?->name }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block text-xs">Nomor Sertifikat:</span>
                    <span class="font-mono font-bold text-primary">{{ $certificate->certificate_number }}</span>
                </div>
                @if ($certificate->blanko_number)
                    <div>
                        <span class="text-gray-500 block text-xs">Nomor Seri Blanko:</span>
                        <span class="font-mono text-gray-900">{{ $certificate->blanko_number }}</span>
                    </div>
                @endif
                <div>
                    <span class="text-gray-500 block text-xs">Masa Berlaku Sertifikat:</span>
                    <span class="text-gray-900">{{ $certificate->valid_from?->format('d/m/Y') }} sampai dengan {{ $certificate->valid_until?->format('d/m/Y') }}</span>
                </div>
            </div>

            {{-- Klausul Pemegang Sertifikat Sesuai SOP --}}
            <div class="mt-6 p-4 bg-gray-50 border border-gray-200 rounded text-xs text-gray-700 space-y-2">
                <p class="font-semibold text-gray-900">Pernyataan Pemegang Sertifikat:</p>
                <ol class="list-decimal list-inside space-y-1 pl-1">
                    <li>Saya menyatakan telah menerima sertifikat kompetensi asli sebagaimana rincian di atas dalam keadaan baik.</li>
                    <li>Saya bersedia memelihara kompetensi yang telah diakui dan menaati kode etik profesi yang berlaku.</li>
                    <li>Saya bersedia mengikuti kegiatan surveilans pemeliharaan kompetensi yang diselenggarakan oleh LSP sesuai ketentuan SOP LSP ProIndo.</li>
                    <li>Apabila terjadi penyalahgunaan sertifikat ini, saya bersedia menerima sanksi pencabutan status sertifikasi.</li>
                </ol>
            </div>

            {{-- Status Penerimaan --}}
            <div class="pt-4 border-t border-gray-200">
                @if ($certificate->isAcknowledged())
                    @php $ack = $certificate->acknowledgment(); @endphp
                    <div class="p-3 bg-green-50 border border-green-200 rounded flex items-center justify-between text-xs text-green-800">
                        <div>
                            <strong>✓ Tanda terima telah dikonfirmasi</strong> pada {{ $ack?->occurred_at?->format('d/m/Y H:i') }}.
                        </div>
                    </div>
                @else
                    <form action="{{ route('asesi.certificates.acknowledge', $certificate->id) }}" method="POST" class="space-y-3">
                        @csrf
                        <div class="p-3 bg-amber-50 border border-amber-200 rounded text-xs text-amber-800">
                            Silakan klik konfirmasi penerimaan di bawah ini untuk menandatangani tanda terima resmi (FR-10.2).
                        </div>
                        <button type="submit" class="px-5 py-2 bg-green-600 text-white text-sm font-medium rounded hover:bg-green-700 focus:outline-none">
                            Terima sertifikat
                        </button>
                    </form>
                @endif
            </div>

            {{-- Unduh Berkas --}}
            <div class="pt-4 border-t border-gray-200 flex items-center justify-between">
                <span class="text-xs text-gray-500">Unduh salinan digital dokumen sertifikat:</span>
                <a href="{{ route('asesi.certificates.download', $certificate->id) }}" class="px-4 py-2 bg-primary text-white text-xs font-medium rounded hover:bg-primary/90">
                    Unduh sertifikat
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
