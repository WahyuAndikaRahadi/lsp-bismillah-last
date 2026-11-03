# Task 02 — Tetapkan registry formulir bersama dan konflik sumber

Status: [ ] belum dikerjakan. Dependency: **00**.

## Tujuan

Mengubah field matrix menjadi kontrak source yang terukur tanpa mengarang formulir.

## Kontrak yang harus dibaca

`../CONTEXT.md`, bagian relevan `../DATA_MODEL.md`, `../FIELD_COVERAGE.md` + baris CSV keluarga/skema task ini, dan `../PAGE_CONTRACT.md`. Path file di bawah relatif root repo. File bertanda baru dibuat oleh task; path existing diperiksa dengan `rg` sebelum edit. Jika file baseline berbeda, catat pemetaan path aktual; jangan membuat controller duplikat hanya untuk mengikuti nama rencana.

Batas tetap: hanya core SOP04/05/06/10; tiga role admin/asesor/asesi; satu LSP; tanpa PDF export, complaint/staff/capability/finance/chat/messages/letters/quality/impersonation ; banding, surveilans, resertifikasi, dan ketidakberpihakan hanya melalui task 21-01 sampai 21-04. Original read-only. Jangan menyalin kode arsip. Nilai hasil/consent/attendance harus event nyata, actor dan timestamp; semua akses object-authorized. Ketidakjelasan source = blocker, bukan izin menebak.

## Scope dan file

docs/SOURCE_INDEX.csv; docs/FIELD_COVERAGE.csv; docs/REPORT_SOURCE_CELLS.csv; docs/CONTEXT.md (jawaban owner saja); database/seeders/data/form-registry.json (baru); tests/Unit/FormRegistryTest.php (baru). Original docs/reference read-only.

## Perilaku wajib

Baca original core SOP/FR dan master keluarga MUK yang telah ditemukan. Registry memuat source hash/page/section/row/label/urutan/tipe/opsi dan printed versus internal_metadata. Detail isi per skema diselesaikan task 05-01..07. Pecah composite field/array menggunakan source row, bukan nama semantic dianggap label. Simpan alias kode cetak/report sheet, jangan memilih versi ok otomatis. IA04A/B, IA06A/B/C, IA07–11 tetap blocked sampai source/keputusan Q2 tersedia. Form shared lintas-skema hanya layout, tidak data teknis. Semua MUK termasuk MAPA02 tetap berlaku S1–S7. Catat konflik tanpa mengubah PDF.

## Acceptance criteria

- [ ] Semua source hashed dan form family mempunyai sumber atau BLOCKED-SOURCE eksplisit; tidak ada placeholder yang dianggap approved.
- [ ] Setiap common field dan FR04.1/04.2/04.3/BA/10.1/10.2 dipetakan ke halaman/label asli; semua footer/signatures diverifikasi.
- [ ] Versi/source konflik mempunyai owner decision reference atau blokir publish; registry tidak memuat contoh hasil/nama sebagai data transaksi.

## Test sempit / bukti

php artisan test --compact tests/Unit/FormRegistryTest.php. Uji missing source, duplicate field key, invalid page, unknown option, printed field tanpa label, serta semua tujuh scheme slots.

Tulis test dari acceptance sekali, fixture berdasarkan sumber/kejadian yang jelas. Test gagal karena bug harus diperbaiki; fixture legacy tidak boleh memaksa consent/hasil palsu. Jangan menjalankan full suite/build pada task selain 24. Selesaikan task hanya dengan bukti command, exit code, dan acceptance; status blocked bukan complete.

## Rollback

Revert registry draft yang belum dipakai. Versi approved tidak ditimpa; supersede bila source salah. Source originals tidak dimigrasi.

## Handoff sesi

Commit hanya file task dan perubahan dokumen progress yang relevan; jangan staging file owner. Isi baris evidence EXECUTION_PLAN dengan commit, command/hasil, blocker dan source decision yang digunakan. Tidak perlu menulis plan baru. Jika scope tidak muat satu sesi, berhenti pada draft/checkpoint konsisten, jangan menyebut task selesai atau mengurangi verifikasi.
