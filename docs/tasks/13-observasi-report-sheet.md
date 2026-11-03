# Task 13 — IA-01 dan seluruh sel report sheet

Status: [ ] belum dikerjakan. Dependency: **10,11,12; source report skema terkait diselesaikan Q2/Q3**.

## Tujuan

Merekam bukti praktik dan pengukuran asli, terpisah dari penilaian diri.

## Kontrak yang harus dibaca

`../CONTEXT.md`, bagian relevan `../DATA_MODEL.md`, `../FIELD_COVERAGE.md` + baris CSV keluarga/skema task ini, dan `../PAGE_CONTRACT.md`. Path file di bawah relatif root repo. File bertanda baru dibuat oleh task; path existing diperiksa dengan `rg` sebelum edit. Jika file baseline berbeda, catat pemetaan path aktual; jangan membuat controller duplikat hanya untuk mengikuti nama rencana.

Batas tetap: hanya core SOP04/05/06/10; tiga role admin/asesor/asesi; satu LSP; tanpa PDF export, complaint/staff/capability/finance/chat/messages/letters/quality/impersonation ; banding, surveilans, resertifikasi, dan ketidakberpihakan hanya melalui task 21-01 sampai 21-04. Original read-only. Jangan menyalin kode arsip. Nilai hasil/consent/attendance harus event nyata, actor dan timestamp; semua akses object-authorized. Ketidakjelasan source = blocker, bukan izin menebak.

## Scope dan file

app/Http/Controllers/Asesor/InstrumentController.php; Asesi/AssessmentExecutionController.php; app/Models/AssessmentIaResponse.php; app/Http/Requests/ObservationRequest.php dan ReportSheetRequest.php (baru); views IA01/report-sheet aktif; tests/Feature/CoreObservationReportTest.php (baru).

## Perilaku wajib

IA01 group/unit/elemen/KUK/benchmark/catatan/rekomendasi/footer menurut source skema. Assigned asesor mengisi observasi, tidak prefill dari APL02/CBT. Report-sheet tiap cell mempunyai exact original label/group/unit/measurement type; asesi mengisi kolom peserta dan asesor penilaian yang menjadi kewenangannya. Nilai manual/spesifikasi berasal kendaraan/manual nyata; jangan default toleransi/0/Bagus. Kode cetak ambigu tidak dinormalisasi sampai keputusan owner. Missing sheet S5 tetap blocker, tidak copy skema lain. Fitur draft+version/conflict sama dengan form lain; no PDF.

## Acceptance criteria

- [ ] Semua cell registry source dapat disimpan dan dibaca, termasuk silinder/komponen berulang dan nilai+unit+kondisi terpisah.
- [ ] KUK foreign/unknown, unauthorized role column dan value type invalid ditolak; 0 valid dibedakan kosong.
- [ ] Unobserved tidak otomatis K/BK; rekomendasi hanya explicit setelah required evidence.

## Test sempit / bukti

php artisan test --compact tests/Feature/CoreObservationReportTest.php. Data provider approved seven-scheme sheets, decimal/zero/negative bila source mengizinkan, missing-specification, same-unit different-version dan stale save.

Tulis test dari acceptance sekali, fixture berdasarkan sumber/kejadian yang jelas. Test gagal karena bug harus diperbaiki; fixture legacy tidak boleh memaksa consent/hasil palsu. Jangan menjalankan full suite/build pada task selain 24. Selesaikan task hanya dengan bukti command, exit code, dan acceptance; status blocked bukan complete.

## Rollback

Retain raw recorded values dan old schema version; jika mapping salah, buat correction migration/revision dengan source proof. Jangan mengubah nilai hasil menjadi default.

## Handoff sesi

Commit hanya file task dan perubahan dokumen progress yang relevan; jangan staging file owner. Isi baris evidence EXECUTION_PLAN dengan commit, command/hasil, blocker dan source decision yang digunakan. Tidak perlu menulis plan baru. Jika scope tidak muat satu sesi, berhenti pada draft/checkpoint konsisten, jangan menyebut task selesai atau mengurangi verifikasi.
