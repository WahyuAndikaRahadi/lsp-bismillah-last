# Task 21 — Upload sertifikat dan FR10.2

Status: [x] selesai. Dependency: **20,04,11**.

## Tujuan

Menyediakan banyak sertifikat privat per asesi tanpa generator.

## Kontrak yang harus dibaca

`../CONTEXT.md`, bagian relevan `../DATA_MODEL.md`, `../FIELD_COVERAGE.md` + baris CSV keluarga/skema task ini, dan `../PAGE_CONTRACT.md`. Path file di bawah relatif root repo. File bertanda baru dibuat oleh task; path existing diperiksa dengan `rg` sebelum edit. Jika file baseline berbeda, catat pemetaan path aktual; jangan membuat controller duplikat hanya untuk mengikuti nama rencana.

Batas tetap: hanya core SOP04/05/06/10; tiga role admin/asesor/asesi; satu LSP; tanpa PDF export, complaint/staff/capability/finance/chat/messages/letters/quality/impersonation ; banding, surveilans, resertifikasi, dan ketidakberpihakan hanya melalui task 21-01 sampai 21-04. Original read-only. Jangan menyalin kode arsip. Nilai hasil/consent/attendance harus event nyata, actor dan timestamp; semua akses object-authorized. Ketidakjelasan source = blocker, bukan izin menebak.

## Scope dan file

app/Models/Certificate.php; CertificateReceiptEvent.php (baru); app/Http/Controllers/Admin/CertificateController.php; Asesi/CertificateController.php (baru); app/Policies/CertificatePolicy.php (baru); app/Services/PrivateFileService.php; resources/views/admin/certificates/* dan asesi/certificates/* (baru); routes/web.php; tests/Feature/CoreCertificatesTest.php (baru).

## Perilaku wajib

Upload admin hanya assessment dengan decision positive sah, metadata real certificate number/issue date/valid from/until/blanko, validated private file. Candidate hasMany across assessments; active certificate per assessment dengan immutable revisions saat replacement, bukan satu certificate field pada user. Download policy object+decision/file status, no public storage path. Upload/download/acknowledgment event berbeda; FR10.2 sequence/name/date/signature berasal receipt nyata. Jangan klaim byte stream selesai sebagai signature penerimaan; offer one explicit Terima sertifikat acknowledgment sesuai original dengan consent yang sah. Pernyataan pemegang mengikuti original clauses/place/date/name/NIK/scheme/blanko/number dan actual consent. Tautan download semua cert di satu list, status awaiting upload jelas; no generation/print route.

## Acceptance criteria

- [x] Asesi dengan dua skema melihat dua certificate benar; user lain/asesor tidak berhak download; duplicate number dan wrong assessment decision ditolak.
- [x] Upload tanpa decision, expired/superseded authorization, file missing/MIME spoof ditolak tanpa receipt palsu.
- [x] Atomic upload/replacement/retry, private hash/ref, download audit dan actual receipt; certificate validity tidak default fabricated.

## Test sempit / bukti

php artisan test --compact tests/Feature/CoreCertificatesTest.php. Multi-cert ownership, positive-decision gate, upload storage/DB failure, unauthorized/missing-file receipt, replacement/revocation dan number/date validation.

Tulis test dari acceptance sekali, fixture berdasarkan sumber/kejadian yang jelas. Test gagal karena bug harus diperbaiki; fixture legacy tidak boleh memaksa consent/hasil palsu. Jangan menjalankan full suite/build pada task selain 24. Selesaikan task hanya dengan bukti command, exit code, dan acceptance; status blocked bukan complete.

## Rollback

Versi lama tetap private di backup/history; replacement rollback memilih valid prior revision dengan audit. Jangan memulihkan certificate yang decision-nya revoked atau membuka generator lama.

## Handoff sesi

Commit hanya file task dan perubahan dokumen progress yang relevan; jangan staging file owner. Isi baris evidence EXECUTION_PLAN dengan commit, command/hasil, blocker dan source decision yang digunakan. Tidak perlu menulis plan baru. Jika scope tidak muat satu sesi, berhenti pada draft/checkpoint konsisten, jangan menyebut task selesai atau mengurangi verifikasi.
