# Task 18 — AK-05/06 per asesor dan revision

Status: [x] selesai. Dependency: **16,17**.

## Tujuan

Laporan lengkap dengan kaji ulang yang benar-benar dilakukan.

## Kontrak yang harus dibaca

`../CONTEXT.md`, bagian relevan `../DATA_MODEL.md`, `../FIELD_COVERAGE.md` + baris CSV keluarga/skema task ini, dan `../PAGE_CONTRACT.md`. Path file di bawah relatif root repo. File bertanda baru dibuat oleh task; path existing diperiksa dengan `rg` sebelum edit. Jika file baseline berbeda, catat pemetaan path aktual; jangan membuat controller duplikat hanya untuk mengikuti nama rencana.

Batas tetap: hanya core SOP04/05/06/10; tiga role admin/asesor/asesi; satu LSP; tanpa PDF export, complaint/staff/capability/finance/chat/messages/letters/quality/impersonation ; banding, surveilans, resertifikasi, dan ketidakberpihakan hanya melalui task 21-01 sampai 21-04. Original read-only. Jangan menyalin kode arsip. Nilai hasil/consent/attendance harus event nyata, actor dan timestamp; semua akses object-authorized. Ketidakjelasan source = blocker, bukan izin menebak.

## Scope dan file

app/Models/AssessmentReport.php (baru); app/Http/Controllers/Asesor/AssessmentReportController.php (baru); app/Http/Requests/AssessmentReportRequest.php (baru); views laporan asesor (buat resources/views/asesor/reports/ak05.blade.php dan ak06.blade.php); tests/Feature/CoreAssessmentReportTest.php (baru).

## Perilaku wajib

AK05 roster assigned participants dari finalized AK02, K/BK dan unit BK, positive/negative aspects, penolakan hasil nyata, improvement, notes, author/MET/sign/date. AK06 matrix setiap procedure row × validitas/reliabel/fleksibel/adil, improvement, lima dimensi skills dan evidence/instrument references, lead assessor/assessor/comments/sign/date sesuai source. Tidak mengganti matrix dengan empat global yes/no. Auto-populate fakta identitas/roster saja; evaluasi/review memerlukan actor input. Scope unique schedule+assessor+revision, snapshots dan submission locked.

## Acceptance criteria

- [x] Dua asesor satu jadwal tidak saling menimpa report; roster/result tepat assigned scope.
- [x] AK06 semua row/dimension terisi real finding/evidence atau N/A beralasan bila diizinkan original; no default puas/valid.
- [x] Submit for review memblokir missing findings dan mencatat author/reviewer nyata.

## Test sempit / bukti

php artisan test --compact tests/Feature/CoreAssessmentReportTest.php. Multi-assessor, all matrix cells round-trip, changing roster/version, missing evidence, duplicate submit dan stale revision.

Tulis test dari acceptance sekali, fixture berdasarkan sumber/kejadian yang jelas. Test gagal karena bug harus diperbaiki; fixture legacy tidak boleh memaksa consent/hasil palsu. Jangan menjalankan full suite/build pada task selain 24. Selesaikan task hanya dengan bukti command, exit code, dan acceptance; status blocked bukan complete.

## Rollback

Report baru supersedes lama; restore pointer tercatat bila salah submit. Jangan overwrite report yang telah menjadi dasar keputusan.

## Handoff sesi

Commit hanya file task dan perubahan dokumen progress yang relevan; jangan staging file owner. Isi baris evidence EXECUTION_PLAN dengan commit, command/hasil, blocker dan source decision yang digunakan. Tidak perlu menulis plan baru. Jika scope tidak muat satu sesi, berhenti pada draft/checkpoint konsisten, jangan menyebut task selesai atau mengurangi verifikasi.
