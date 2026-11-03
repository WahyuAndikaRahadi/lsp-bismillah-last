# Task 14 — IA-02 skenario dan IA-03 tanggapan

Status: [ ] belum dikerjakan. Dependency: **13**.

## Tujuan

Menampilkan skenario asli dan mencatat pertanyaan/jawaban yang benar-benar terjadi.

## Kontrak yang harus dibaca

`../CONTEXT.md`, bagian relevan `../DATA_MODEL.md`, `../FIELD_COVERAGE.md` + baris CSV keluarga/skema task ini, dan `../PAGE_CONTRACT.md`. Path file di bawah relatif root repo. File bertanda baru dibuat oleh task; path existing diperiksa dengan `rg` sebelum edit. Jika file baseline berbeda, catat pemetaan path aktual; jangan membuat controller duplikat hanya untuk mengikuti nama rencana.

Batas tetap: hanya core SOP04/05/06/10; tiga role admin/asesor/asesi; satu LSP; tanpa PDF export, complaint/staff/capability/finance/chat/messages/letters/quality/impersonation ; banding, surveilans, resertifikasi, dan ketidakberpihakan hanya melalui task 21-01 sampai 21-04. Original read-only. Jangan menyalin kode arsip. Nilai hasil/consent/attendance harus event nyata, actor dan timestamp; semua akses object-authorized. Ketidakjelasan source = blocker, bukan izin menebak.

## Scope dan file

app/Http/Controllers/Asesor/InstrumentController.php; app/Services/SchemeInstrumentService.php; app/Http/Requests/PmoResponseRequest.php (baru); views IA02/IA03 aktif; tests/Feature/CorePracticalPmoTest.php (baru).

## Perilaku wajib

IA02 petunjuk/group/unit/S-T-A-R/alat/bahan/manual/waktu verbatim approved source. Jangan menambahkan kolom keputusan pada kertas bila tidak ada. IA03 question order/text, tanggapan dan Pencapaian Ya/Tdk sesuai original; pertanyaan yang sudah terpenuhi pada demonstrasi boleh catatan+rujukan observasi sesuai petunjuk, bukan jawaban fiktif. Tambahan pertanyaan hanya yang benar-benar diajukan asesor terkait praktik, disimpan terpisah dari master dengan actor/time/reason. Tidak menggunakan generative questions. Pertahankan footer/tanda tangan source.

## Acceptance criteria

- [ ] Isi skenario/PMO tepat versi skema dan varian approved; conflicting PMO skema3 tidak dipilih otomatis.
- [ ] Tanggapan kosong tidak auto-sufficient; referensi observasi harus milik assessment yang sama.
- [ ] Tambahan pertanyaan tidak mengubah bank soal/master semua peserta.

## Test sempit / bukti

php artisan test --compact tests/Feature/CorePracticalPmoTest.php. Original-order fixtures, real supplementary question, fulfilled-by-observation branch, missing response dan cross-assessment evidence.

Tulis test dari acceptance sekali, fixture berdasarkan sumber/kejadian yang jelas. Test gagal karena bug harus diperbaiki; fixture legacy tidak boleh memaksa consent/hasil palsu. Jangan menjalankan full suite/build pada task selain 24. Selesaikan task hanya dengan bukti command, exit code, dan acceptance; status blocked bukan complete.

## Rollback

Tambahan pertanyaan/response historis tetap; source change menjadi versi baru, bukan rewrite assessment berjalan.

## Handoff sesi

Commit hanya file task dan perubahan dokumen progress yang relevan; jangan staging file owner. Isi baris evidence EXECUTION_PLAN dengan commit, command/hasil, blocker dan source decision yang digunakan. Tidak perlu menulis plan baru. Jika scope tidak muat satu sesi, berhenti pada draft/checkpoint konsisten, jangan menyebut task selesai atau mengurangi verifikasi.
