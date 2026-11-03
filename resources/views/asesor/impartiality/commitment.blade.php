@extends('layouts.app')

@section('title', 'Pakta Integritas Asesor (FR-SOP-01.5)')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-3xl">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">FORMULIR FR-SOP-01.5</h1>
            <p class="text-sm text-gray-600 mt-1">Pakta Integritas & Komitmen Ketidakberpihakan Asesor Kompetensi</p>
        </div>
        <a href="{{ route('asesor.dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900 font-medium">
            ← Kembali ke Dashboard
        </a>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden mb-6">
        <div class="p-5 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-900">Komitmen Integritas Asesor</h2>
            @if ($activeCommitment)
                <span class="inline-flex px-2.5 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-800">
                    ✓ Aktif Ditandatangani ({{ $activeCommitment->signed_at?->format('d/m/Y H:i') }})
                </span>
            @else
                <span class="inline-flex px-2.5 py-0.5 rounded text-xs font-semibold bg-amber-100 text-amber-800">
                    Belum Ditandatangani
                </span>
            @endif
        </div>

        <div class="p-6 space-y-4 text-sm text-gray-700 leading-relaxed">
            <p class="font-medium text-gray-900">Sebagai Asesor Kompetensi pada Lembaga Sertifikasi Profesi Praktisi Otomotif Indonesia (LSP ProIndo), saya menyatakan dengan sungguh-sungguh bahwa:</p>

            <ol class="list-decimal list-inside space-y-2 pl-1 bg-gray-50 p-4 border rounded text-xs text-gray-800">
                <li>Saya akan bertindak independen, jujur, objektif, dan tidak memihak dalam setiap proses asesmen kompetensi yang saya laksanakan.</li>
                <li>Saya menolak segala bentuk suap, gratifikasi, janji, atau pengaruh dari pihak manapun yang berpotensi mempengaruhi objektivitas penilaian kompetensi.</li>
                <li>Saya akan segera menyatakan dan mendeklarasikan benturan kepentingan (FR-SOP-01.4) apabila memiliki hubungan keluarga, rekan kerja satu unit langsung, atau pernah memberikan pelatihan/bimbingan teknis kepada calon asesi dalam kurun waktu 2 (dua) tahun terakhir.</li>
                <li>Saya akan menjaga kerahasiaan materi uji kompetensi, data pribadi asesi, dan rekaman hasil asesmen sesuai ketentuan SOP LSP ProIndo dan Pedoman BNSP.</li>
                <li>Apabila saya melanggar komitmen ini, saya bersedia menerima sanksi pencabutan penugasan asesmen dan pembekuan sertifikat kompetensi asesor saya.</li>
            </ol>

            @if (! $activeCommitment)
                <form action="{{ route('asesor.impartiality.commitment.sign') }}" method="POST" class="pt-4 border-t border-gray-200 space-y-4">
                    @csrf
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="agreement" name="agreement" type="checkbox" required value="1"
                                class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-xs">
                            <label for="agreement" class="font-medium text-gray-900">
                                Saya telah membaca, memahami, dan menyetujui seluruh butir klausul Pakta Integritas Asesor di atas secara sadar tanpa paksaan.
                            </label>
                            @error('agreement')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end">
                        <button type="submit" class="px-5 py-2 bg-primary text-white text-sm font-medium rounded hover:bg-primary/90 focus:outline-none">
                            Tandatangani pakta integritas
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
