# Task 17 — AK-03 dan Pengajuan Banding AK-04

Status: [x] selesai. Dependency: **16**.

## Tujuan

Menyimpan umpan balik (AK-03) dan pengajuan banding oleh asesi (AK-04) sebagai tahap intake resmi yang menjadi masukan bagi alur penanganan banding independen pada task 21-01.

## Kontrak yang harus dibaca

`../CONTEXT.md`, bagian relevan `../DATA_MODEL.md`, `../FIELD_COVERAGE.md` + baris CSV keluarga/skema task ini, dan `../PAGE_CONTRACT.md`. Path file di bawah relatif root repo. File bertanda baru dibuat oleh task; path existing diperiksa dengan `rg` sebelum edit. Jika file baseline berbeda, catat pemetaan path aktual; jangan membuat controller duplikat hanya untuk mengikuti nama rencana.

Batas tetap: hanya core SOP04/05/06/10; tiga role admin/asesor/asesi; satu LSP; tanpa PDF export, complaint/staff/capability/finance/chat/messages/letters/quality/impersonation; banding, surveilans, resertifikasi, dan ketidakberpihakan hanya melalui task 21-01 sampai 21-04. Original read-only. Jangan menyalin kode arsip. Nilai hasil/consent/attendance harus event nyata, actor dan timestamp; semua akses object-authorized. Ketidakjelasan source = blocker, bukan izin menebak.

## Scope dan file

app/Http/Controllers/Asesi/AssessmentFeedbackController.php (baru); app/Models/AssessmentAk03Item.php; canonical AK04 form record pengajuan banding (intake untuk task 21-01); views asesi AK03/AK04; routes/web.php; tests/Feature/CoreFeedbackTest.php (baru).

## Perilaku wajib

AK03 sepuluh komponen original, Ya/Tidak dan komentar, komentar lain; tampil setelah rekomendasi asesor, tidak menunggu certificate. AK04 diajukan oleh asesi: identity/date, tiga pertanyaan, scheme/no/reason/contested units/signature sesuai original. Asesi memilih real answers; no auto-Ya. AK-04 tersimpan sebagai record pengajuan resmi teratribusi yang menjadi masukan investigasi independen (task 21-01). Batas waktu banding configurable dan nonaktif kecuali diatur. Pengajuan AK-04 tidak mengubah kompetensi secara otomatis. Data privat milik assessment.

## Acceptance criteria

- [x] Exact paper order/labels dan per-row data terjaga; feedback AK-03 tidak mengubah kompetensi.
- [x] AK-04 tersimpan sebagai record pengajuan teratribusi dari asesi dengan alasan dan timestamp, siap ditindaklanjuti oleh task 21-01 tanpa hasil otomatis.
- [x] Other-candidate dan unauthorized assessor denied; no PDF/print CTA.

## Test sempit / bukti

php artisan test --compact tests/Feature/CoreFeedbackTest.php. All rows round-trip, before-result gate, no default answer, filing lifecycle record intact, no result mutation.

Tulis test dari acceptance sekali, fixture berdasarkan sumber/kejadian yang jelas. Test gagal karena bug harus diperbaiki; fixture legacy tidak boleh memaksa consent/hasil palsu. Jangan menjalankan full suite/build pada task selain 24. Selesaikan task hanya dengan bukti command, exit code, dan acceptance; status blocked bukan complete.

## Rollback

Pertahankan jawaban/histori; jika external handoff belum disetujui, tampilkan status data tersimpan tanpa klaim ditindaklanjuti. Jangan delete legacy appeal data tanpa retention.

## Handoff sesi

Commit hanya file task dan perubahan dokumen progress yang relevan; jangan staging file owner. Isi baris evidence EXECUTION_PLAN dengan commit, command/hasil, blocker dan source decision yang digunakan. Tidak perlu menulis plan baru. Jika scope tidak muat satu sesi, berhenti pada draft/checkpoint konsisten, jangan menyebut task selesai atau mengurangi verifikasi.
