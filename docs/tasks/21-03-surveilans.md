# Task 21-03 — Surveilans (SOP 04 §4 & SOP 19)

Status: [x] selesai. Dependency: **21, 04**.

## Tujuan

Membangun alur periodik surveilans pemegang sertifikat yang telusur dan akuntabel sesuai SOP 04 §4 dan SOP 19 (FR.SPS.01, FR.SK.01, FR.SP.01).

## Kontrak yang harus dibaca

`../CONTEXT.md`, bagian relevan `../DATA_MODEL.md`, `../FIELD_COVERAGE.md` + baris CSV keluarga/skema task ini, dan `../PAGE_CONTRACT.md`. Path file di bawah relatif root repo. File bertanda baru dibuat oleh task; path existing diperiksa dengan `rg` sebelum edit. Jika file baseline berbeda, catat pemetaan path aktual; jangan membuat controller duplikat hanya untuk mengikuti nama rencana.

Batas tetap: hanya core SOP04/05/06/10; tiga role admin/asesor/asesi; satu LSP; tanpa PDF export, complaint/staff/capability/finance/chat/messages/letters/quality/impersonation; banding, surveilans, resertifikasi, dan ketidakberpihakan hanya melalui task 21-01 sampai 21-04. Original read-only. Jangan menyalin kode arsip. Nilai hasil/consent/attendance harus event nyata, actor dan timestamp; semua akses object-authorized. Ketidakjelasan source = blocker, bukan izin menebak.

## Scope dan file

app/Models/SurveillancePlan.php (baru); app/Models/SurveillanceReport.php (baru); app/Http/Controllers/Admin/SurveillanceController.php; app/Http/Controllers/Asesi/SurveillanceController.php (baru); app/Policies/SurveillancePolicy.php (baru); resources/views/admin/surveillance/* (baru); resources/views/asesi/surveillance/* (baru); routes/web.php; tests/Feature/CoreSurveillanceTest.php (baru).

## Perilaku wajib

Admin/manajer sertifikasi menyusun rencana surveilans berkala (periode, mekanisme, bukti yang dipersyaratkan). Pemegang sertifikat aktif (asesi pemilik sertifikat) melihat jadwal/pemberitahuan surveilans untuk sertifikat miliknya dan mengirim laporan berkala disertai bukti kegiatan praktik/profesi (FR.SP.01). Tim/evaluator independen yang ditugaskan memeriksa bukti dan mencatat temuan evaluasi (FR.SPS.01). Keputusan hasil surveilans (FR.SK.01) ditetapkan secara eksplisit oleh pejabat berwenang (misal: kompetensi terpelihara, perlu tindakan perbaikan, atau pembekuan); dilarang membuat pembersihan status atau pencabutan otomatis tanpa evaluasi manusia.

## Acceptance criteria

- [x] Rencana surveilans periodik tercatat dan terhubung ke sertifikat aktif pemegang.
- [x] Asesi pemegang sertifikat dapat mengunggah laporan dan dokumen bukti pemeliharaan kompetensi; asesi lain ditolak.
- [x] Evaluasi independen dan penetapan keputusan hasil surveilans dicatat eksplisit dengan temuan dan actor yang berwenang tanpa kelulusan otomatis.
- [x] Pemegang sertifikat dapat melihat status hasil surveilans yang telah ditetapkan.

## Test sempit / bukti

php artisan test --compact tests/Feature/CoreSurveillanceTest.php. Pembuatan rencana berkala, submisi laporan pemegang sertifikat, larangan akses pihak lain, dan pencatatan keputusan surveilans eksplisit.

## Rollback

Koreksi hasil evaluasi dicatat melalui revisi tindak lanjut; histori laporan dan bukti asesi tetap dipertahankan.

## Handoff sesi

Commit hanya file task dan perubahan dokumen progress yang relevan; jangan staging file owner. Isi baris evidence EXECUTION_PLAN dengan commit, command/hasil, blocker dan source decision yang digunakan.
