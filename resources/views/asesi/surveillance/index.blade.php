@extends('layouts.app')

@section('title', 'Jadwal Surveilans Pemeliharaan Kompetensi')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Surveilans Pemeliharaan Kompetensi</h1>
            <p class="text-sm text-gray-600 mt-1">Pemantauan berkala pemeliharaan kompetensi untuk sertifikat aktif yang Anda miliki (SOP 04 §4 / SOP 19).</p>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-4">
        @forelse ($plans as $plan)
            @php $rep = $plan->latestReport; @endphp
            <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs font-semibold px-2.5 py-0.5 rounded bg-blue-100 text-blue-800">
                                {{ $plan->certificate?->certificate_number }}
                            </span>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded
                                {{ $plan->status === 'COMPLETED' ? 'bg-green-100 text-green-800' : ($plan->status === 'REPORTED' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                                {{ $plan->status }}
                            </span>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900">{{ $plan->title }}</h2>
                        <p class="text-xs text-gray-500 mt-1">Skema: {{ $plan->certificate?->assessment?->scheme?->name }} • Batas Waktu: {{ $plan->due_date?->format('d/m/Y') }}</p>

                        @if ($rep && $rep->decision)
                            <div class="mt-3 p-3 bg-green-50 border border-green-200 rounded text-xs text-green-900">
                                <strong>Hasil Keputusan Resmi (FR.SK.01):</strong> {{ $rep->decision }}
                                @if ($rep->evaluator_notes)
                                    <div class="mt-1 text-gray-600">Catatan: {{ $rep->evaluator_notes }}</div>
                                @endif
                            </div>
                        @elseif ($rep)
                            <div class="mt-3 text-xs text-blue-700 font-medium">
                                ✓ Laporan telah Anda kirimkan pada {{ $rep->submitted_at?->format('d/m/Y') }}. Sedang dalam proses evaluasi oleh LSP.
                            </div>
                        @endif
                    </div>

                    <div>
                        <a href="{{ route('asesi.surveillance.show', $plan->id) }}" class="inline-flex items-center px-4 py-2 bg-primary text-white text-xs font-medium rounded hover:bg-primary/90">
                            {{ $rep ? 'Lihat Detail & Laporan' : 'Kirim Laporan Surveilans' }}
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white border border-gray-200 rounded-lg p-8 text-center text-gray-500 text-sm">
                Belum ada jadwal surveilans aktif untuk sertifikat kompetensi Anda.
            </div>
        @endforelse
    </div>
</div>
@endsection
