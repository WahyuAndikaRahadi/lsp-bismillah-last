<x-guest-layout title="Lacak Status Tiket Pengaduan">
    <div class="py-12 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center mb-8">
            <h1 class="text-2xl font-extrabold text-zinc-900 tracking-tight">
                Lacak Status Tiket Pengaduan
            </h1>
            <p class="mt-1 text-xs text-zinc-500">
                Masukkan nomor tiket pengaduan yang Anda terima untuk melihat status penanganan dan catatan tindak lanjut.
            </p>
        </div>

        <!-- Search Bar -->
        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-5 mb-6">
            <form method="GET" action="{{ route('pengaduan.tracking') }}" class="flex items-center gap-2">
                <input
                    type="text"
                    name="ticket"
                    value="{{ request('ticket') }}"
                    required
                    placeholder="Contoh: TKT-20261031-001"
                    class="flex-1 px-4 py-2.5 text-xs font-mono uppercase rounded-xl border border-zinc-300 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600"
                />
                <button
                    type="submit"
                    class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-xs shadow-xs transition shrink-0"
                >
                    Cari Tiket
                </button>
            </form>
        </div>

        <!-- Result -->
        @if ($complaint)
            <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                    <div>
                        <span class="text-[11px] text-zinc-400 block">Nomor Tiket:</span>
                        <span class="font-mono text-base font-bold text-zinc-900">{{ $complaint->ticket_number }}</span>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $complaint->statusBadge() }}">
                        {{ $complaint->statusLabel() }}
                    </span>
                </div>

                <div class="space-y-2 text-xs">
                    <div>
                        <span class="text-zinc-400 block">Pelapor:</span>
                        <span class="font-medium text-zinc-800">{{ $complaint->nama_pelapor }} ({{ $complaint->jenis_responden }})</span>
                    </div>
                    <div>
                        <span class="text-zinc-400 block">Judul Aduan:</span>
                        <span class="font-semibold text-zinc-900 text-sm">{{ $complaint->judul }}</span>
                    </div>
                    <div>
                        <span class="text-zinc-400 block">Isi Aduan:</span>
                        <p class="text-zinc-700 bg-zinc-50 p-3 rounded-xl border border-zinc-200/70 mt-1 whitespace-pre-line">{{ $complaint->isi_aduan }}</p>
                    </div>
                </div>

                @if ($complaint->tindak_lanjut)
                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl space-y-1 text-xs">
                        <span class="font-bold text-blue-900 block">Tindak Lanjut dari LSP:</span>
                        <p class="text-blue-800 whitespace-pre-line">{{ $complaint->tindak_lanjut }}</p>
                        <div class="text-[10px] text-blue-600 mt-2">
                            Diperbarui pada: {{ $complaint->tgl_tindak_lanjut?->isoFormat('D MMMM Y, HH:mm') }} WIB
                        </div>
                    </div>
                @endif
            </div>
        @elseif (request()->filled('ticket'))
            <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-8 text-center text-zinc-500 text-xs">
                Tiket pengaduan dengan nomor <strong>{{ request('ticket') }}</strong> tidak ditemukan dalam sistem.
            </div>
        @endif

    </div>
</x-guest-layout>
