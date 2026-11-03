# Task 22 — Hapus runtime legacy dan bloat setelah cutover

Status: [x] selesai. Dependency: **01, 03–21, 21-01..21-04 untuk fitur terkait**.

## Tujuan

Tidak menyisakan domain kedua, portal atau pipeline ekspor tersembunyi, dengan mempertahankan alur lean banding, ketidakberpihakan, surveilans, dan resertifikasi.

## Kontrak yang harus dibaca

`../CONTEXT.md`, bagian relevan `../DATA_MODEL.md`, `../FIELD_COVERAGE.md` + baris CSV keluarga/skema task ini, dan `../PAGE_CONTRACT.md`. Path file di bawah relatif root repo. File bertanda baru dibuat oleh task; path existing diperiksa dengan `rg` sebelum edit. Jika file baseline berbeda, catat pemetaan path aktual; jangan membuat controller duplikat hanya untuk mengikuti nama rencana.

Batas tetap: hanya core SOP04/05/06/10; tiga role admin/asesor/asesi; satu LSP; tanpa PDF export, complaint/staff/capability/finance/chat/messages/letters/quality/impersonation; banding, surveilans, resertifikasi, dan ketidakberpihakan hanya melalui task 21-01 sampai 21-04. Original read-only. Jangan menyalin kode arsip. Nilai hasil/consent/attendance harus event nyata, actor dan timestamp; semua akses object-authorized. Ketidakjelasan source = blocker, bukan izin menebak.

## Scope dan file

Semua path pada docs/RETIREMENT.md; routes/web.php; app/Models/User.php relationship legacy; database/seeders/* legacy callers; resources/views/layouts/* menus; composer.json/composer.lock hanya hunk dependency task (tanpa menyentuh owner files jika dilarang); tests/Feature/CoreRetiredSurfaceTest.php (baru); tests lama yang mengharuskan fitur dihapus.

## Perilaku wajib

Ikuti RETIREMENT persis: explicit mapping complete, no unresolved record active, then remove legacy controllers/models/views/routes/helpers yang digantikan oleh lean records. Jangan menghapus banding dan surveilans yang telah digantikan oleh implementasi lean (task 21-01 s.d. 21-04); hanya hapus kode baseline yang digantikan. Retain AsesorProfile credentials, TukVerification, PlenoMeeting, Standard/requirements content yang dibutuhkan. Drop dependency PDF jika tidak in-scope, no duplicate export pipeline. Jangan drop historical tables/migrations atau owner originals. Search seluruh caller class/route/menu dan orphan JS/CSS. Test lama yang mengharuskan proxy/print/finance diganti assertion unavailable, security tests tetap. Composer owner files tidak disentuh.

## Acceptance criteria

- [x] Satu Assessment/Schedule/Scheme runtime; zero numeric fallback; route names duplicate/unused legacy binding tidak ada.
- [x] No complaint/staff/capability/finance/chat/messages/letters/quality/impersonation/PDF route, termasuk hidden/direct endpoints.
- [x] No owner file loss; archived historical DB dapat dipulihkan; dependency lock konsisten dengan scope. Alur lean banding, ketidakberpihakan, surveilans, dan resertifikasi tetap berfungsi pada tiga role.

## Test sempit / bukti

php artisan test --compact tests/Feature/CoreRetiredSurfaceTest.php. Jalankan juga existing test files yang disentuh dalam batch yang sama. Route inventory dan grep callers hanya sekali pada cutover, bukan crawl berulang.

Tulis test dari acceptance sekali, fixture berdasarkan sumber/kejadian yang jelas. Test gagal karena bug harus diperbaiki; fixture legacy tidak boleh memaksa consent/hasil palsu. Jangan menjalankan full suite/build pada task selain 24. Selesaikan task hanya dengan bukti command, exit code, dan acceptance; status blocked bukan complete.

## Rollback

Revert deletion commit hanya pada code yang aman; jangan menghidupkan security holes. Historical tables retained memungkinkan restore mapping; no destructive down migration.

## Handoff sesi

Commit hanya file task dan perubahan dokumen progress yang relevan; jangan staging file owner. Isi baris evidence EXECUTION_PLAN dengan commit, command/hasil, blocker dan source decision yang digunakan. Tidak perlu menulis plan baru. Jika scope tidak muat satu sesi, berhenti pada draft/checkpoint konsisten, jangan menyebut task selesai atau mengurangi verifikasi.
