@extends('layouts.app', ['title' => 'Kelola Jadwal Asesmen'])

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Kelola Jadwal Asesmen</h1>
            <p class="text-sm text-slate-500">Daftar semua jadwal asesmen sertifikasi BNSP</p>
        </div>
        <div>
            <a href="{{ route('admin.jadwal.create') }}" class="px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-lg text-sm font-bold shadow-sm transition flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Buat Jadwal Baru</span>
            </a>
        </div>
    </div>

    {{-- Schedules Table --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200">
            <h2 class="text-base font-bold text-slate-900">Semua Jadwal ({{ $schedules->total() ?? $schedules->count() }})</h2>
        </div>

        @if($schedules->isEmpty())
            <div class="p-8 text-center text-slate-500 text-sm">
                Belum ada jadwal yang dibuat. Klik tombol di atas untuk membuat jadwal baru.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3">Kode</th>
                            <th class="px-6 py-3">Nama Jadwal / Skema</th>
                            <th class="px-6 py-3">TUK</th>
                            <th class="px-6 py-3">Tanggal & Waktu</th>
                            <th class="px-6 py-3">Asesi</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($schedules as $sch)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="px-6 py-4 font-mono font-bold text-xs text-brand-700">
                                    {{ $sch->code }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900">{{ $sch->name ?? $sch->scheme->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $sch->scheme->name }}</div>
                                </td>
                                <td class="px-6 py-4 text-slate-700">{{ $sch->tuk_name }}</td>
                                <td class="px-6 py-4 text-xs text-slate-600">
                                    <div>{{ \Carbon\Carbon::parse($sch->date)->isoFormat('D MMMM Y') }}</div>
                                    <div class="text-slate-400">{{ substr($sch->start_time, 0, 5) }} - {{ substr($sch->end_time, 0, 5) }} WIB</div>
                                </td>
                                <td class="px-6 py-4 text-xs font-semibold text-slate-700">
                                    {{ $sch->assessments_count ?? $sch->assessments->count() }} Asesi
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('admin.jadwal.show', $sch->id) }}" class="px-3 py-1 text-xs font-semibold text-emerald-700 hover:text-emerald-900 bg-emerald-50 rounded">
                                            Peserta
                                        </a>
                                        <a href="{{ route('admin.jadwal.edit', $sch->id) }}" class="px-3 py-1 text-xs font-semibold text-brand-600 hover:text-brand-800 bg-brand-50 rounded">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.jadwal.destroy', $sch->id) }}" method="POST" class="inline"
                                              onsubmit="return confirm('PERINGATAN KONSEKUENSI PENGHAPUSAN JADWAL:\n\nJika jadwal memiliki peserta atau riwayat asesmen aktif, status jadwal akan dinonaktifkan (FINISHED) untuk melindungi data audit BNSP. Jika kosong, jadwal akan dihapus.\n\nApakah Anda yakin ingin melanjutkan?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1 text-xs font-semibold text-rose-600 hover:text-rose-800 bg-rose-50 rounded">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if(method_exists($schedules, 'links'))
                <div class="p-4 border-t border-slate-200">
                    {{ $schedules->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
