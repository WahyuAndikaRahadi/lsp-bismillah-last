<x-app-layout title="Perbaikan Berkas Permohonan: {{ $application->nomor_pendaftaran }}">
    <div class="space-y-6">

        <!-- Top Navigation -->
        <div>
            <nav class="flex items-center text-xs text-zinc-500 mb-1 space-x-1">
                <span>Portal Asesi</span>
                <span>&rsaquo;</span>
                <a href="{{ route('asesi.applications.index') }}" class="hover:underline">Permohonan</a>
                <span>&rsaquo;</span>
                <a href="{{ route('asesi.applications.show', $application) }}" class="hover:underline font-mono">{{ $application->nomor_pendaftaran }}</a>
                <span>&rsaquo;</span>
                <span class="text-zinc-900 font-medium">Perbaikan Dokumen</span>
            </nav>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-900">Perbaikan Berkas Persyaratan (Revisi)</h1>
            <p class="text-xs sm:text-sm text-zinc-500 mt-0.5">
                Unggah ulang dokumen yang ditandai belum memenuhi syarat oleh verifikator LSP.
            </p>
        </div>

        <!-- Catatan Revisi Admin Alert -->
        <div class="p-5 bg-orange-50 border border-orange-200 rounded-2xl text-orange-900 space-y-2">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-orange-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <h3 class="text-sm font-bold">Catatan Perbaikan dari Verifikator LSP:</h3>
            </div>
            <p class="text-xs text-orange-800 leading-relaxed pl-7">
                {{ $application->catatan_revisi ?: 'Mohon periksa kembali dokumen persyaratan yang ditandai tidak memenuhi.' }}
            </p>
        </div>

        <form action="{{ route('asesi.applications.update', $application) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 sm:p-8 space-y-6">
                <div class="border-b border-zinc-100 pb-4">
                    <h2 class="text-base font-bold text-zinc-900">Dokumen Persyaratan Skema: {{ $application->scheme->nama_skema }}</h2>
                    <p class="text-xs text-zinc-500 mt-0.5">Pilih berkas baru untuk memperbarui dokumen yang belum sesuai.</p>
                </div>

                <div class="space-y-4">
                    @foreach ($application->scheme->requirements as $req)
                        @php
                            $existing = $application->documents->firstWhere('requirement_id', $req->id);
                            $isInvalid = $existing && $existing->status_verifikasi === \App\Enums\DocumentVerificationStatus::TidakMemenuhi;
                        @endphp
                        <div class="p-4 rounded-xl border {{ $isInvalid ? 'border-rose-300 bg-rose-50/40' : 'border-zinc-200 bg-zinc-50/30' }} transition flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="space-y-1 flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-zinc-900">{{ $req->nama_dokumen }}</span>
                                    @if($existing)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $existing->status_verifikasi->badgeClasses() }}">
                                            {{ $existing->status_verifikasi->label() }}
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-zinc-200 text-zinc-600">Belum Ada</span>
                                    @endif
                                </div>

                                @if($existing && $existing->catatan)
                                    <p class="text-xs text-rose-700 font-semibold mt-1">
                                        Catatan Verifikator: "{{ $existing->catatan }}"
                                    </p>
                                @endif

                                @if($existing)
                                    <div class="text-[11px] text-zinc-400 mt-1">
                                        File saat ini: <a href="{{ $existing->file_url }}" target="_blank" class="text-blue-600 hover:underline">{{ $existing->file_name }}</a> ({{ $existing->formatted_size }})
                                    </div>
                                @endif
                            </div>

                            <div class="shrink-0 w-full md:w-80">
                                <label class="block text-[11px] font-medium text-zinc-600 mb-1">
                                    {{ $isInvalid ? 'Unggah Berkas Pengganti (Wajib):' : 'Ganti Berkas (Opsional):' }}
                                </label>
                                <input type="file" name="documents[{{ $req->id }}]" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 text-zinc-600">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between">
                <a href="{{ route('asesi.applications.show', $application) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-zinc-300 hover:bg-zinc-100 text-zinc-700 text-xs font-semibold transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span>Batal</span>
                </a>

                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-bold shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Kirim Ulang Berkas Perbaikan</span>
                </button>
            </div>
        </form>

    </div>
</x-app-layout>
