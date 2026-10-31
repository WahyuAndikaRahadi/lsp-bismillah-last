@extends('layouts.app', ['title' => 'Ruang Uji Asesmen - ' . ($schedule->name ?? $schedule->scheme->name)])

@section('content')
<div class="space-y-6">
    {{-- Navigation Breadcrumb --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs font-semibold mb-1">
                <a href="{{ route('asesor.jadwal.index') }}" class="text-brand-600 hover:text-brand-700">&larr; Jadwal</a>
                <span class="text-slate-300">/</span>
                <a href="{{ route('asesor.jadwal.asesi', $schedule->id) }}" class="text-slate-600 hover:text-slate-900">Daftar Asesi</a>
                <span class="text-slate-300">/</span>
                <span class="text-slate-800">Ruang Uji Jadwal</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">{{ $schedule->name ?? $schedule->scheme->name }}</h1>
            <p class="text-sm text-slate-500">Pilih instrumen asesmen untuk memeriksa dan menilai seluruh peserta uji</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('asesor.ak07.create', $schedule->id) }}" class="px-4 py-2 text-xs font-semibold bg-white text-slate-800 border border-slate-300 rounded-lg shadow-sm hover:bg-slate-50 transition">
                FR.AK07 Penyesuaian
            </a>
            <a href="{{ route('asesor.jadwal.asesi', $schedule->id) }}" class="px-4 py-2 text-xs font-semibold bg-slate-900 text-white rounded-lg shadow-sm hover:bg-slate-800 transition">
                Daftar Asesi ({{ $totalAsesi }})
            </a>
        </div>
    </div>

    {{-- Schedule Details Card --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5 sm:p-6 shadow-sm">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Skema Sertifikasi</span>
                <p class="text-slate-900 font-bold mt-1 text-sm">{{ $schedule->scheme->name }}</p>
                <span class="font-mono text-xs text-brand-700 bg-brand-50 px-2 py-0.5 rounded border border-brand-200 mt-1 inline-block">
                    {{ $schedule->scheme->code }}
                </span>
            </div>
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Tempat Uji Kompetensi (TUK)</span>
                <p class="text-slate-900 font-bold mt-1 text-sm">{{ $schedule->tuk_name }}</p>
                <p class="text-xs text-slate-500 mt-0.5">{{ $schedule->code }}</p>
            </div>
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Waktu Pelaksanaan</span>
                <p class="text-slate-900 font-bold mt-1 text-sm">{{ \Carbon\Carbon::parse($schedule->date)->isoFormat('dddd, D MMMM Y') }}</p>
                <p class="text-xs text-slate-500 mt-0.5">{{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }} WIB</p>
            </div>
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Peserta Uji (Asesi)</span>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $totalAsesi }} <span class="text-xs font-semibold text-slate-500">Orang</span></p>
                <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200 mt-1 inline-block">
                    Status: {{ $schedule->status }}
                </span>
            </div>
        </div>
    </div>

    {{-- Selected Instruments Heading --}}
    <div>
        <h2 class="text-lg font-black text-slate-900 tracking-tight">Instrumen Asesmen Terpilih</h2>
        <p class="text-xs text-slate-500">Klik instrumen di bawah untuk memeriksa dan memberi penilaian pada daftar asesi</p>
    </div>

    {{-- Grid of Selected Instruments --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($instruments as $inst)
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm flex flex-col justify-between hover:shadow-md transition">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="font-mono text-xs font-bold px-2.5 py-1 rounded border {{ $inst['badge_color'] }}">
                            {{ $inst['code'] }}
                        </span>
                        <span class="text-xs font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                            {{ $inst['badge'] }}
                        </span>
                    </div>

                    <div>
                        <h3 class="text-base font-bold text-slate-900 leading-snug">{{ $inst['title'] }}</h3>
                        <p class="text-xs text-slate-600 mt-1.5 leading-relaxed">{{ $inst['desc'] }}</p>
                    </div>

                    {{-- Progress Bar --}}
                    <div class="pt-2">
                        <div class="flex items-center justify-between text-xs font-semibold mb-1">
                            <span class="text-slate-600">Progres Penilaian</span>
                            <span class="text-slate-900 font-bold">{{ $inst['completed_count'] }} / {{ $inst['total_asesi'] }} Asesi</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 border border-slate-200 overflow-hidden">
                            <div class="bg-brand-600 h-2 rounded-full transition-all" style="width: {{ $inst['percentage'] }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="pt-5 mt-4 border-t border-slate-100">
                    <a href="{{ route('asesor.schedule.instrument', ['schedule' => $schedule->id, 'code' => $inst['route_code']]) }}"
                       class="w-full flex items-center justify-center space-x-2 px-4 py-2.5 text-xs font-bold bg-brand-600 hover:bg-brand-700 text-white rounded-lg shadow-sm transition">
                        <span>Buka Penilaian Instrumen</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-xl border border-slate-200 p-8 text-center shadow-sm">
                <p class="text-sm text-slate-500">Tidak ada instrumen yang terdaftar untuk jadwal ini.</p>
            </div>
        @endforelse
    </div>

    {{-- Finalize Schedule Banner --}}
    <div class="bg-slate-900 text-white rounded-xl border border-slate-800 p-5 sm:p-6 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h3 class="text-base font-bold text-white">Selesai Menilai Seluruh Instrumen?</h3>
            <p class="text-xs text-slate-300 mt-0.5">Setelah semua asesi dinilai pada instrumen terkait, Anda dapat menyelesaikan jadwal uji dan menerbitkan berita acara.</p>
        </div>
        <div>
            <form action="{{ route('asesor.schedule.finalize', $schedule->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyelesaikan seluruh rangkaian asesmen untuk jadwal ini?')">
                @csrf
                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg shadow-sm transition">
                    Selesaikan & Finalisasi Asesmen
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
