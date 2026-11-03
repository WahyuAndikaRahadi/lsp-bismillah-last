# Task 16 — Rekomendasi asesor eksplisit dan finalisasi

Status: [ ] belum dikerjakan. Dependency: **12–15 untuk metode yang dipakai; 11**.

## Tujuan

Mencegah hasil otomatis dan finalisasi tanpa bukti yang lengkap.

## Kontrak yang harus dibaca

`../CONTEXT.md`, bagian relevan `../DATA_MODEL.md`, `../FIELD_COVERAGE.md` + baris CSV keluarga/skema task ini, dan `../PAGE_CONTRACT.md`. Path file di bawah relatif root repo. File bertanda baru dibuat oleh task; path existing diperiksa dengan `rg` sebelum edit. Jika file baseline berbeda, catat pemetaan path aktual; jangan membuat controller duplikat hanya untuk mengikuti nama rencana.

Batas tetap: hanya core SOP04/05/06/10; tiga role admin/asesor/asesi; satu LSP; tanpa PDF export, complaint/staff/capability/finance/chat/messages/letters/quality/impersonation atau lifecycle later. Original read-only. Jangan menyalin kode arsip. Nilai hasil/consent/attendance harus event nyata, actor dan timestamp; semua akses object-authorized. Ketidakjelasan source = blocker, bukan izin menebak.

## Scope dan file

app/Http/Controllers/Asesor/FinalizationController.php; InstrumentController.php; app/Services/AkWorkflowService.php; app/Services/AssessmentReadinessService.php (baru); app/Models/AssessmentAk02Item.php; AK02 view; tests/Feature/CoreRecommendationTest.php (baru).

## Perilaku wajib

AK02 exact table evidence per unit, overall K/BK, tindak lanjut, comments, start/end, names/registration/signatures dan lampiran. Internal unit findings eksplisit oleh assigned assessor; jangan menambah kolom paper yang tidak ada. Validate valid/asli/cukup/terkini evidence dan metode plan dengan alasan nyata; skor CBT hanya fakta pendukung. Tidak ada default/prefill/bulk all-K. Unset findings memblokir finalize dengan daftar unit/field. Command transaction+version lock/idempotency; recompute readiness server-side, no hidden field trust. Correction sesudah final melalui revision dan dependent decision review, tidak overwrite.

## Acceptance criteria

- [ ] GET tidak mutasi; CBT sempurna tanpa observasi/finding tetap belum competent.
- [ ] AK02 footer/reference bukti lengkap; setiap unit assessment versi itu memiliki temuan yang sah.
- [ ] Asesor lain/admin tidak mengganti findings; stale/concurrent finalize tidak double decision; BK bukan absen.

## Test sempit / bukti

php artisan test --compact tests/Feature/CoreRecommendationTest.php. Missing findings, incomplete planned methods, evidence other user, all-score-pass-no-K, explicit BK/K, expiry/reassignment dan replay finalize.

Tulis test dari acceptance sekali, fixture berdasarkan sumber/kejadian yang jelas. Test gagal karena bug harus diperbaiki; fixture legacy tidak boleh memaksa consent/hasil palsu. Jangan menjalankan full suite/build pada task selain 24. Selesaikan task hanya dengan bukti command, exit code, dan acceptance; status blocked bukan complete.

## Rollback

Finalized recommendations immutable; correction creates supersession and blocks affected certification. Code rollback cannot restore automatic results.

## Handoff sesi

Commit hanya file task dan perubahan dokumen progress yang relevan; jangan staging file owner. Isi baris evidence EXECUTION_PLAN dengan commit, command/hasil, blocker dan source decision yang digunakan. Tidak perlu menulis plan baru. Jika scope tidak muat satu sesi, berhenti pada draft/checkpoint konsisten, jangan menyebut task selesai atau mengurangi verifikasi.
