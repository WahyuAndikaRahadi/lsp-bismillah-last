# Task 05 — Versioned reference dan bootstrap aman

Status: [ ] belum dikerjakan. Dependency: **02,03,04**.

## Tujuan

Membuat jalur import/reference approval serta akun awal tanpa demo data.

## Kontrak yang harus dibaca

`../CONTEXT.md`, bagian relevan `../DATA_MODEL.md`, `../FIELD_COVERAGE.md` + baris CSV keluarga/skema task ini, dan `../PAGE_CONTRACT.md`. Path file di bawah relatif root repo. File bertanda baru dibuat oleh task; path existing diperiksa dengan `rg` sebelum edit. Jika file baseline berbeda, catat pemetaan path aktual; jangan membuat controller duplikat hanya untuk mengikuti nama rencana.

Batas tetap: hanya core SOP04/05/06/10; tiga role admin/asesor/asesi; satu LSP; tanpa PDF export, complaint/staff/capability/finance/chat/messages/letters/quality/impersonation atau lifecycle later. Original read-only. Jangan menyalin kode arsip. Nilai hasil/consent/attendance harus event nyata, actor dan timestamp; semua akses object-authorized. Ketidakjelasan source = blocker, bukan izin menebak.

## Scope dan file

app/Services/SchemeReferenceService.php (baru); app/Console/Commands/ImportSchemeReference.php (baru); app/Console/Commands/BootstrapLspAdmin.php (baru); database/seeders/DatabaseSeeder.php; database/seeders/LspReferenceSeeder.php (baru); app/Models/LspProfile.php; tests/Feature/CoreReferenceImportTest.php (baru).

## Perilaku wajib

Implement import dry-run, validate, draft, approve/publish, retire immutable version. Published memerlukan source hashes, semua field rows, unit/KUK/group/questions/key matching dan reviewer/trial evidence nyata SOP04.2.5/05. Jangan auto-publish karena JSON valid. Bootstrap LSP identity/config dari data nyata; admin password dari input secret, tidak log/env contoh hard-coded. Seed reference idempotent berdasarkan key+version dan checksum; tidak overwrite edit approved atau create transaction/demo users. Skema dengan source unresolved tetap draft. Mekanisme sama untuk seven scheme tasks berikut.

## Acceptance criteria

- [ ] Seed ulang tidak menggandakan/mengubah approved reference; perubahan source menghasilkan versi baru.
- [ ] No demo passwords, no automatic approval, no fabricated LSP name/signatory/numbering; missing config menghalangi publish.
- [ ] Import error menunjuk source/field; dry-run read-only; rollback version mempertahankan assessment snapshot.

## Test sempit / bukti

php artisan test --compact tests/Feature/CoreReferenceImportTest.php. Uji reimport, checksum mismatch, missing key, duplicate KUK, unknown group/unit, draft unresolved, dan bootstrap repeat.

Tulis test dari acceptance sekali, fixture berdasarkan sumber/kejadian yang jelas. Test gagal karena bug harus diperbaiki; fixture legacy tidak boleh memaksa consent/hasil palsu. Jangan menjalankan full suite/build pada task selain 24. Selesaikan task hanya dengan bukti command, exit code, dan acceptance; status blocked bukan complete.

## Rollback

Retire draft/version salah; jangan edit atau delete versi yang sudah dipakai assessment. Bootstrap user salah dinonaktifkan beralasan, tidak menghapus audit.

## Handoff sesi

Commit hanya file task dan perubahan dokumen progress yang relevan; jangan staging file owner. Isi baris evidence EXECUTION_PLAN dengan commit, command/hasil, blocker dan source decision yang digunakan. Tidak perlu menulis plan baru. Jika scope tidak muat satu sesi, berhenti pada draft/checkpoint konsisten, jangan menyebut task selesai atau mengurangi verifikasi.
