@extends('layouts.app')

@section('title', 'Deklarasi Benturan Kepentingan (FR-SOP-01.5)')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">FORMULIR FR-SOP-01.5</h1>
            <p class="text-sm text-gray-600 mt-1">Deklarasi Benturan Kepentingan Asesor pada Jadwal: {{ $schedule->name }} ({{ $schedule->code }})</p>
        </div>
        <a href="{{ route('asesor.jadwal.asesi', $schedule->id) }}" class="text-sm text-gray-600 hover:text-gray-900 font-medium">
            ← Kembali ke Daftar Asesi
        </a>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6 text-xs text-amber-800">
        <strong>Penting (SOP 01):</strong> Asesor wajib mendeklarasikan apabila terdapat hubungan keluarga, rekan kerja satu divisi langsung, atau hubungan komersial/pelatihan dengan peserta. Asesor yang memiliki benturan kepentingan terbuka tidak diperkenankan melakukan pengujian terhadap peserta terkait dan akan digantikan oleh Manajer Sertifikasi.
    </div>

    <form action="{{ route('asesor.schedule.conflict-declaration.store', $schedule->id) }}" method="POST" class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
        @csrf

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-gray-700">
                    <tr>
                        <th class="py-3 px-4 text-left font-semibold">Nama Asesi</th>
                        <th class="py-3 px-4 text-left font-semibold">NIK</th>
                        <th class="py-3 px-4 text-left font-semibold w-48">Status Benturan</th>
                        <th class="py-3 px-4 text-left font-semibold">Uraian Benturan Kepentingan (Bila Ada)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($assessments as $item)
                        @php
                            $dec = $existingDeclarations->get($item->id);
                            $hasConflict = old("declarations.{$item->id}.has_conflict", $dec?->has_conflict ? 1 : 0);
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4 font-medium text-gray-900">
                                {{ $item->asesi?->name }}
                            </td>
                            <td class="py-3 px-4 text-xs font-mono text-gray-600">
                                {{ $item->asesi?->profile?->nik ?? '-' }}
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center space-x-3 text-xs">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="declarations[{{ $item->id }}][has_conflict]" value="0"
                                            {{ $hasConflict == 0 ? 'checked' : '' }} class="text-primary focus:ring-primary">
                                        <span class="ml-1 text-green-700 font-medium">Bebas benturan</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="declarations[{{ $item->id }}][has_conflict]" value="1"
                                            {{ $hasConflict == 1 ? 'checked' : '' }} class="text-red-600 focus:ring-red-500">
                                        <span class="ml-1 text-red-700 font-medium">Ada benturan</span>
                                    </label>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <input type="text" name="declarations[{{ $item->id }}][conflict_nature]"
                                    value="{{ old("declarations.{$item->id}.conflict_nature", $dec?->conflict_nature) }}"
                                    class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:ring-primary focus:border-primary"
                                    placeholder="Contoh: Saudara kandung / mantan atasan langsung">
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-6 text-center text-gray-500 text-sm">
                                Tidak ada peserta yang ditugaskan kepada Anda pada jadwal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end">
            <button type="submit" class="px-5 py-2 bg-primary text-white text-sm font-medium rounded hover:bg-primary/90 focus:outline-none">
                Simpan deklarasi ketidakberpihakan
            </button>
        </div>
    </form>
</div>
@endsection
