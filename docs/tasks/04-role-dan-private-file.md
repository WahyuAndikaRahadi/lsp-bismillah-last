# Task 04 — Tiga role, policy objek, dan private file

Status: [ ] belum dikerjakan. Dependency: **03**.

## Tujuan

Menutup seluruh akses lintas-user/domain dan memisahkan key/PII dari aset publik.

## Kontrak yang harus dibaca

`../CONTEXT.md`, bagian relevan `../DATA_MODEL.md`, `../FIELD_COVERAGE.md` + baris CSV keluarga/skema task ini, dan `../PAGE_CONTRACT.md`. Path file di bawah relatif root repo. File bertanda baru dibuat oleh task; path existing diperiksa dengan `rg` sebelum edit. Jika file baseline berbeda, catat pemetaan path aktual; jangan membuat controller duplikat hanya untuk mengikuti nama rencana.

Batas tetap: hanya core SOP04/05/06/10; tiga role admin/asesor/asesi; satu LSP; tanpa PDF export, complaint/staff/capability/finance/chat/messages/letters/quality/impersonation ; banding, surveilans, resertifikasi, dan ketidakberpihakan hanya melalui task 21-01 sampai 21-04. Original read-only. Jangan menyalin kode arsip. Nilai hasil/consent/attendance harus event nyata, actor dan timestamp; semua akses object-authorized. Ketidakjelasan source = blocker, bukan izin menebak.

## Scope dan file

app/Models/User.php; app/Http/Middleware/EnsureUserRole.php; RoleMiddleware.php; app/Policies/* (baru/refactor); app/Http/Controllers/SecureFileController.php; Auth/RegisterController.php; config/filesystems.php; routes/web.php; app/Services/PrivateFileService.php (baru); tests/Feature/CoreAuthorizationTest.php; tests/Feature/CorePrivateFileTest.php (keduanya baru).

## Perilaku wajib

Normalisasi role dengan mapping eksplisit dari nilai legacy yang ditemukan; alias ambigu/unknown dinonaktifkan untuk review, tidak jadi admin/asesi default. Registrasi publik tidak menerima role/user_id. Setiap read/write/preview/download form,file,certificate punya policy owner atau assigned assessor atau admin core yang berwenang; GET ke original berisi answer key dilarang bagi asesi. Migrasi sensitive public files ke private dengan hash+mapping; hilangkan public disk fallback dan symlink exposure untuk file tersebut tanpa menghapus public logo. Validate size,MIME,extension,decode image; path server-generated; no user-supplied disk/path. Cache private/no-store, safe Content-Disposition, nosniff. CSRF/throttle/login/reset/session hardening tetap.

## Acceptance criteria

- [ ] Matriks tamu/admin/asesor assigned/asesor lain/asesi owner/asesi lain lulus untuk semua route file dan form.
- [ ] Dua asesor satu jadwal tidak saling edit; revoked assignment/consent/file state dievaluasi ulang, signed URL bukan bypass.
- [ ] Traversal, MIME spoof, oversize, duplicate upload, missing file dan unauthorized receipt tidak membocorkan path/PII; original answer keys tidak publik.

## Test sempit / bukti

php artisan test --compact tests/Feature/CoreAuthorizationTest.php tests/Feature/CorePrivateFileTest.php. Tambah route inventory assertion agar route file baru tanpa policy gagal.

Tulis test dari acceptance sekali, fixture berdasarkan sumber/kejadian yang jelas. Test gagal karena bug harus diperbaiki; fixture legacy tidak boleh memaksa consent/hasil palsu. Jangan menjalankan full suite/build pada task selain 24. Selesaikan task hanya dengan bukti command, exit code, dan acceptance; status blocked bukan complete.

## Rollback

Pertahankan salinan file asal di backup non-public sampai hash/count tervalidasi. Jika copy gagal, record unavailable dengan alasan; rollback tidak memublikasikan file sensitif kembali.

## Handoff sesi

Commit hanya file task dan perubahan dokumen progress yang relevan; jangan staging file owner. Isi baris evidence EXECUTION_PLAN dengan commit, command/hasil, blocker dan source decision yang digunakan. Tidak perlu menulis plan baru. Jika scope tidak muat satu sesi, berhenti pada draft/checkpoint konsisten, jangan menyebut task selesai atau mengurangi verifikasi.
