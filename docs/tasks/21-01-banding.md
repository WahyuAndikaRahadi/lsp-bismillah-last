# Task 21-01 — Banding Asesmen (SOP 07)

Status: [x] selesai. Dependency: **17, 04, 20**.

## Tujuan

Membangun alur lean penanganan banding atas keputusan sertifikasi (SOP 07, FR-SOP-07.1, FR-SOP-07.2) dari pengajuan formulir AK-04 oleh asesi.

## Kontrak yang harus dibaca

`../CONTEXT.md`, bagian relevan `../DATA_MODEL.md`, `../FIELD_COVERAGE.md` + baris CSV keluarga/skema task ini, dan `../PAGE_CONTRACT.md`. Path file di bawah relatif root repo. File bertanda baru dibuat oleh task; path existing diperiksa dengan `rg` sebelum edit. Jika file baseline berbeda, catat pemetaan path aktual; jangan membuat controller duplikat hanya untuk mengikuti nama rencana.

Batas tetap: hanya core SOP04/05/06/10; tiga role admin/asesor/asesi; satu LSP; tanpa PDF export, complaint/staff/capability/finance/chat/messages/letters/quality/impersonation; banding, surveilans, resertifikasi, dan ketidakberpihakan hanya melalui task 21-01 sampai 21-04. Original read-only. Jangan menyalin kode arsip. Nilai hasil/consent/attendance harus event nyata, actor dan timestamp; semua akses object-authorized. Ketidakjelasan source = blocker, bukan izin menebak.

## Scope dan file

app/Models/AssessmentAppeal.php; app/Models/AppealInvestigation.php (baru); app/Http/Controllers/Admin/AppealController.php; app/Http/Controllers/Asesi/AssessmentFeedbackController.php; app/Policies/AppealPolicy.php (baru); resources/views/admin/appeals/* (baru); resources/views/asesi/appeals/* (baru); routes/web.php; tests/Feature/CoreAppealTest.php (baru).

## Perilaku wajib

Pengajuan AK-04 oleh asesi masuk sebagai permohonan banding tercatat (FR-SOP-07.1 intake). Batas waktu banding (appeal deadline) configurable dan dinonaktifkan secara default kecuali SOP atau setting menentukan jumlah hari setelah tanggal keputusan/rekomendasi. Penugasan tim investigasi banding independen (FR-SOP-07.1): investigator yang ditugaskan harus independen dan bukan asesor yang menguji kandidat pada asesmen terkait. Berita acara investigasi banding (FR-SOP-07.2): waktu, tempat, identitas asesi dan asesor, unit kompetensi yang dibantah, temuan investigasi faktual, rekomendasi tindak lanjut, serta pencatatan actor dan timestamp pengesah. Rekaman hasil banding dicatat eksplisit; tidak ada pemberian kompetensi atau keputusan otomatis. Asesi hanya dapat melihat status dan pengumuman hasil banding miliknya; orang lain dilarang mengakses (kerahasiaan).

## Acceptance criteria

- [x] Asesi dapat mengajukan banding AK-04 dalam batas waktu yang dikonfigurasi; pengajuan lewat tenggat waktu ditolak.
- [x] Asesor yang terlibat dalam asesmen yang dibanding dilarang ditugaskan sebagai investigator banding (pemeriksaan independensi).
- [x] Berita acara dan rekomendasi investigasi (FR-SOP-07.2) tercatat dengan temuan faktual, rekomendasi tindak lanjut, actor, dan waktu nyata tanpa perubahan hasil kompetensi otomatis.
- [x] Akses data banding terlindungi secara rahasia; asesi lain dan pihak tidak berwenang ditolak (403/404).

## Test sempit / bukti

php artisan test --compact tests/Feature/CoreAppealTest.php. Filing AK-04, validasi deadline, larangan investigator terafiliasi, pencatatan FR-SOP-07.2, dan isolasi akses antar pengguna.

## Rollback

Kembalikan status kasus ke draf investigasi bila ada kekeliruan administratif; tidak menghapus rekaman pengajuan dan riwayat investigasi yang telah terdaftar.

## Handoff sesi

Commit hanya file task dan perubahan dokumen progress yang relevan; jangan staging file owner. Isi baris evidence EXECUTION_PLAN dengan commit, command/hasil, blocker dan source decision yang digunakan.
