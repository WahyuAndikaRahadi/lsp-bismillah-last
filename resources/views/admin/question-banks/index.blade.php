@extends('layouts.app', ['title' => 'Bank Soal'])

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Bank Soal Asesmen</h1>
            <p class="text-sm text-slate-500">Kelola paket bank soal tertulis (CBT) dan pertanyaan lisan (PMO)</p>
        </div>
        <div>
            <a href="{{ route('admin.question-banks.create') }}" class="px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-lg text-sm font-bold shadow-sm transition flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Bank Soal</span>
            </a>
        </div>
    </div>

    {{-- Question Banks Table --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3">Nama Paket / Bank Soal</th>
                        <th class="px-6 py-3">Skema Terkait</th>
                        <th class="px-6 py-3">Tipe Instrumen</th>
                        <th class="px-6 py-3 text-center">Jumlah Butir Soal</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($questionBanks ?? [] as $bank)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $bank->title ?? $bank->name }}</div>
                                @if(isset($bank->code))
                                    <div class="font-mono text-xs text-slate-400 mt-0.5">{{ $bank->code }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600">
                                {{ $bank->scheme->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $typeBadge = match($bank->type ?? 'IA05') {
                                        'IA05' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'IA03' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        default => 'bg-blue-50 text-blue-700 border-blue-200',
                                    };
                                @endphp
                                <span class="px-2 py-0.5 rounded text-xs font-semibold border {{ $typeBadge }}">
                                    {{ $bank->type ?? 'IA05 (CBT)' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-700">
                                    {{ $bank->questions_count ?? ($bank->questions ? $bank->questions->count() : 0) }} Butir
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 text-xs font-semibold rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Aktif
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('admin.question-banks.show', $bank->id) }}" class="px-2.5 py-1 text-xs font-semibold text-brand-600 hover:text-brand-800 bg-brand-50 rounded">
                                        Kelola Soal
                                    </a>
                                    <a href="{{ route('admin.question-banks.edit', $bank->id) }}" class="px-2.5 py-1 text-xs font-semibold text-slate-600 hover:text-slate-800 bg-slate-100 rounded">
                                        Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-500 text-xs">Belum ada bank soal yang tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($questionBanks) && method_exists($questionBanks, 'links'))
            <div class="p-4 border-t border-slate-200">
                {{ $questionBanks->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
