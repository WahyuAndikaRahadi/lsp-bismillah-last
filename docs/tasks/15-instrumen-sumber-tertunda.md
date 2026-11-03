# Task 15 — Instrumen lain setelah original tersedia

Status: [ ] belum dikerjakan. Dependency: **02,05-01..05-07,14; Q2 terselesaikan untuk form terkait**.

## Tujuan

Melengkapi IA lain tanpa membuat schema, unit atau pertanyaan rekaan.

## Kontrak yang harus dibaca

`../CONTEXT.md`, bagian relevan `../DATA_MODEL.md`, `../FIELD_COVERAGE.md` + baris CSV keluarga/skema task ini, dan `../PAGE_CONTRACT.md`. Path file di bawah relatif root repo. File bertanda baru dibuat oleh task; path existing diperiksa dengan `rg` sebelum edit. Jika file baseline berbeda, catat pemetaan path aktual; jangan membuat controller duplikat hanya untuk mengikuti nama rencana.

Batas tetap: hanya core SOP04/05/06/10; tiga role admin/asesor/asesi; satu LSP; tanpa PDF export, complaint/staff/capability/finance/chat/messages/letters/quality/impersonation atau lifecycle later. Original read-only. Jangan menyalin kode arsip. Nilai hasil/consent/attendance harus event nyata, actor dan timestamp; semua akses object-authorized. Ketidakjelasan source = blocker, bukan izin menebak.

## Scope dan file

database/seeders/data/form-registry.json; database/seeders/data/field-maps/S1.json sampai S7.json; docs/FIELD_COVERAGE.csv; tests/Unit/RemainingInstrumentSourceGateTest.php (baru). Task ini hanya gate/registry; implementasi dibagi task 15-01 sampai 15-08.

## Perilaku wajib

Gate wajib: original IA04A/B, IA06A/B/C, IA07–11 tersedia dan owner memilih versi, atau owner merevisi daftar yang berlaku secara tertulis. Tanpa itu task BLOCKED, bukan complete; jangan membuat field berdasarkan judul form. Setelah tersedia, tetapkan mapping exact source fields/sections/options/ownership untuk task 15-01..15-08, satu keluarga per sesi. Jangan implementasi seluruh keluarga pada task gate ini. Gunakan komponen existing, bukan engine workflow. Semua tujuh skema mempunyai mapping; metode individual dari plan. Jika sumber memerlukan kegiatan yang belum dijelaskan, catat pertanyaan owner sebelum dependent behavior.

## Acceptance criteria

- [ ] Tidak ada placeholder form aktif atau passing test yang menganggap missing original=N/A.
- [ ] Setiap keluarga yang dipublikasikan mempunyai exact field map, unit/question provenance dan ownership tests S1–S7.
- [ ] Jawaban/key privat sesuai actor; findings hanya tindakan nyata; semua unresolved ditutup owner sebelum release penuh.

## Test sempit / bukti

php artisan test --compact tests/Unit/RemainingInstrumentSourceGateTest.php. Uji missing original, unresolved alias, owner scope decision dan publish gate; tidak menguji renderer yang belum diimplementasikan.

Tulis test dari acceptance sekali, fixture berdasarkan sumber/kejadian yang jelas. Test gagal karena bug harus diperbaiki; fixture legacy tidak boleh memaksa consent/hasil palsu. Jangan menjalankan full suite/build pada task selain 24. Selesaikan task hanya dengan bukti command, exit code, dan acceptance; status blocked bukan complete.

## Rollback

Retire draft family bila source ditolak; tidak delete captured records. Jika source baru menambah scope, pecah task sesi dengan persetujuan owner dan dependency eksplisit.

## Handoff sesi

Commit hanya file task dan perubahan dokumen progress yang relevan; jangan staging file owner. Isi baris evidence EXECUTION_PLAN dengan commit, command/hasil, blocker dan source decision yang digunakan. Tidak perlu menulis plan baru. Jika scope tidak muat satu sesi, berhenti pada draft/checkpoint konsisten, jangan menyebut task selesai atau mengurangi verifikasi.
