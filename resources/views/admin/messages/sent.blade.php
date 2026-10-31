<x-app-layout title="Pesan Terkirim / Log SMS">
    <div class="space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-zinc-900 dark:text-white tracking-tight">Pesan Terkirim (Log SMS & WhatsApp)</h1>
                <p class="text-xs text-zinc-500 mt-1">Log riwayat seluruh pesan notifikasi, OTP, dan password yang dikirimkan oleh sistem LSP.</p>
            </div>
            <a href="{{ route('admin.messages.inbox') }}" class="px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition">
                Kotak Masuk (Inbox)
            </a>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/60 border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 font-semibold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="p-3.5 w-12 text-center">No</th>
                            <th class="p-3.5">Waktu Kirim</th>
                            <th class="p-3.5">Kanal</th>
                            <th class="p-3.5">Tujuan / No. Handphone</th>
                            <th class="p-3.5">Isi Pesan Notifikasi</th>
                            <th class="p-3.5 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse ($messages as $idx => $m)
                            <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition">
                                <td class="p-3.5 text-center font-mono text-zinc-400">{{ $messages->firstItem() + $idx }}</td>
                                <td class="p-3.5 font-mono text-zinc-600 dark:text-zinc-400 whitespace-nowrap">
                                    {{ $m->created_at?->translatedFormat('d M Y H:i:s') }}
                                </td>
                                <td class="p-3.5 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold uppercase font-mono
                                        {{ $m->channel === 'whatsapp' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                        {{ $m->channel }}
                                    </span>
                                </td>
                                <td class="p-3.5 font-mono font-semibold text-zinc-800 dark:text-zinc-200">
                                    {{ $m->recipient_or_sender }}
                                </td>
                                <td class="p-3.5 text-zinc-800 dark:text-zinc-200 text-xs font-mono">
                                    {{ $m->body }}
                                </td>
                                <td class="p-3.5 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        {{ $m->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-zinc-500">
                                    Belum ada log pesan terkirim.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($messages->hasPages())
                <div class="p-4 border-t border-zinc-200 dark:border-zinc-800">
                    {{ $messages->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
