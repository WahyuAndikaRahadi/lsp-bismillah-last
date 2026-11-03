# Task 21-04 — Resertifikasi (SOP 04 §5)

Status: [x] selesai. Dependency: **21, 07, 08**.

## Tujuan

Membangun alur sertifikasi ulang / perpanjangan sertifikat yang tertaut ke sertifikat sebelumnya pada skema yang sama sesuai SOP 04 §5.

## Kontrak yang harus dibaca

`../CONTEXT.md`, bagian relevan `../DATA_MODEL.md`, `../FIELD_COVERAGE.md` + baris CSV keluarga/skema task ini, dan `../PAGE_CONTRACT.md`. Path file di bawah relatif root repo. File bertanda baru dibuat oleh task; path existing diperiksa dengan `rg` sebelum edit. Jika file baseline berbeda, catat pemetaan path aktual; jangan membuat controller duplikat hanya untuk mengikuti nama rencana.

Batas tetap: hanya core SOP04/05/06/10; tiga role admin/asesor/asesi; satu LSP; tanpa PDF export, complaint/staff/capability/finance/chat/messages/letters/quality/impersonation; banding, surveilans, resertifikasi, dan ketidakberpihakan hanya melalui task 21-01 sampai 21-04. Original read-only. Jangan menyalin kode arsip. Nilai hasil/consent/attendance harus event nyata, actor dan timestamp; semua akses object-authorized. Ketidakjelasan source = blocker, bukan izin menebak.

## Scope dan file

app/Models/Assessment.php (kolom prior_certificate_id / linked_origin); app/Models/Certificate.php; app/Http/Controllers/Asesi/RecertificationController.php (baru); app/Http/Controllers/Admin/RecertificationController.php (baru); resources/views/asesi/recertification/* (baru); resources/views/admin/recertification/* (baru); routes/web.php; tests/Feature/CoreRecertificationTest.php (baru).

## Perilaku wajib

Permohonan resertifikasi diajukan oleh pemegang sertifikat yang masa berlakunya akan atau telah berakhir untuk skema yang sama. Sistem membuat record assessment baru yang tertaut ke sertifikat lama yang dimiliki (`prior_certificate_id`). Data profil pengguna digunakan kembali (reusable profile) tanpa mengharuskan input ulang data statis. Sertifikat terdahulu dan riwayat asesmen sebelumnya tetap dipertahankan utuh dan tidak ditimpa. Asesmen resertifikasi tetap mengikuti pemenuhan bukti syarat, penugasan asesor, persetujuan jadwal, dan keputusan sertifikasi baru secara sah tanpa auto-approval.

## Acceptance criteria

- [x] Pendaftaran resertifikasi hanya dapat dilakukan oleh pemilik sertifikat yang sah dan mengacu pada sertifikat skema terkait.
- [x] Record assessment baru tercipta dengan referensi tertaut ke prior certificate dan menggunakan data profil yang ada.
- [x] Seluruh riwayat sertifikat terdahulu tetap ada dan dapat diakses (tidak terhapus atau tertimpa).
- [x] Asesmen baru resertifikasi wajib melalui tahapan verifikasi bukti dan keputusan komite tanpa bypass instan.

## Test sempit / bukti

php artisan test --compact tests/Feature/CoreRecertificationTest.php. Inisiasi resertifikasi dari sertifikat sah, penautan prior certificate, isolasi kepemilikan, dan pelestarian sertifikat terdahulu.

## Rollback

Pembatalan permohonan resertifikasi tidak memengaruhi validitas atau keterlihatan sertifikat lama pemegang.

## Handoff sesi

Commit hanya file task dan perubahan dokumen progress yang relevan; jangan staging file owner. Isi baris evidence EXECUTION_PLAN dengan commit, command/hasil, blocker dan source decision yang digunakan.
