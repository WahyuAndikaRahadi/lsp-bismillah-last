# Task 03 — Satu model core dan reconciliation tanpa collision

Status: [ ] belum dikerjakan. Dependency: **01,02 (hanya schema common yang sumbernya tersedia)**.

## Tujuan

Membangun model canonical dan migrasi yang tidak salah menggabungkan data dua domain.

## Kontrak yang harus dibaca

`../CONTEXT.md`, bagian relevan `../DATA_MODEL.md`, `../FIELD_COVERAGE.md` + baris CSV keluarga/skema task ini, dan `../PAGE_CONTRACT.md`. Path file di bawah relatif root repo. File bertanda baru dibuat oleh task; path existing diperiksa dengan `rg` sebelum edit. Jika file baseline berbeda, catat pemetaan path aktual; jangan membuat controller duplikat hanya untuk mengikuti nama rencana.

Batas tetap: hanya core SOP04/05/06/10; tiga role admin/asesor/asesi; satu LSP; tanpa PDF export, complaint/staff/capability/finance/chat/messages/letters/quality/impersonation atau lifecycle later. Original read-only. Jangan menyalin kode arsip. Nilai hasil/consent/attendance harus event nyata, actor dan timestamp; semua akses object-authorized. Ketidakjelasan source = blocker, bukan izin menebak.

## Scope dan file

app/Models/Assessment.php; Schedule.php; Scheme.php; UserProfile.php; model baru sesuai DATA_MODEL.md; database/migrations/*_add_core_certification_records.php (baru); app/Console/Commands/ReconcileLegacyCertification.php (baru); app/Services/LegacyReconciliationService.php (baru); tests/Feature/CoreReconciliationTest.php (baru).

## Perilaku wajib

Gunakan m_*/t_* canonical. Tambah SchemeVersion, typed records/JSON tervalidasi, snapshots, revision locks, LegacyIdentityMap, private-file/audit/consent/event/decision/certificate relations sesuai DATA_MODEL. Migrations additive; jangan drop atau edit migration lama. Command dry-run default memetakan source_table+id, menghasilkan conflicts/orphans/counts/hash references. Tidak merge hanya integer ID/email. Apply hanya mapping terselesaikan dalam transaction batch/checkpoint. Consent legacy tanpa bukti tidak disahkan; temuan lama tidak diperbaiki menjadi K. Unresolved record dikarantina dari flow aktif dengan alasan.

## Acceptance criteria

- [ ] Fixture dua domain sama-sama id=1 menghasilkan objek berbeda dan mapping benar; tidak ada runtime fallback.
- [ ] Dry-run tidak menulis domain; apply retry idempotent; konflik menghentikan batch terkait tanpa corrupt record lain.
- [ ] Snapshots >64KB tersimpan utuh; unique/FK/index untuk relation/query utama; backup dan rollback rehearsal terdefinisi.

## Test sempit / bukti

php artisan test --compact tests/Feature/CoreReconciliationTest.php. Uji fresh+upgrade fixtures, collision, orphan, file hash mismatch, interrupted retry, duplicate user/profile dan payload besar.

Tulis test dari acceptance sekali, fixture berdasarkan sumber/kejadian yang jelas. Test gagal karena bug harus diperbaiki; fixture legacy tidak boleh memaksa consent/hasil palsu. Jangan menjalankan full suite/build pada task selain 24. Selesaikan task hanya dengan bukti command, exit code, dan acceptance; status blocked bukan complete.

## Rollback

Sebelum cutover, revert code sambil mempertahankan tabel additive. Setelah apply, restore snapshot DB+file konsisten atau replay mapping; jangan down migration yang membuang write baru.

## Handoff sesi

Commit hanya file task dan perubahan dokumen progress yang relevan; jangan staging file owner. Isi baris evidence EXECUTION_PLAN dengan commit, command/hasil, blocker dan source decision yang digunakan. Tidak perlu menulis plan baru. Jika scope tidak muat satu sesi, berhenti pada draft/checkpoint konsisten, jangan menyebut task selesai atau mengurangi verifikasi.
