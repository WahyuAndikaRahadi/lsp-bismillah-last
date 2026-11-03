# Task 24 — Integrasi, upgrade produksi dan handoff

Status: [ ] belum dikerjakan. Dependency: **00–23 termasuk 05-01..07; semua blocker owner yang memengaruhi release selesai**.

## Tujuan

Membuktikan sistem core siap release tanpa membawa proyek PDF.

## Kontrak yang harus dibaca

`../CONTEXT.md`, bagian relevan `../DATA_MODEL.md`, `../FIELD_COVERAGE.md` + baris CSV keluarga/skema task ini, dan `../PAGE_CONTRACT.md`. Path file di bawah relatif root repo. File bertanda baru dibuat oleh task; path existing diperiksa dengan `rg` sebelum edit. Jika file baseline berbeda, catat pemetaan path aktual; jangan membuat controller duplikat hanya untuk mengikuti nama rencana.

Batas tetap: hanya core SOP04/05/06/10; tiga role admin/asesor/asesi; satu LSP; tanpa PDF export, complaint/staff/capability/finance/chat/messages/letters/quality/impersonation atau lifecycle later. Original read-only. Jangan menyalin kode arsip. Nilai hasil/consent/attendance harus event nyata, actor dan timestamp; semua akses object-authorized. Ketidakjelasan source = blocker, bukan izin menebak.

## Scope dan file

phpunit.xml/.env.testing hanya untuk isolasi test bila perlu; deployment configuration existing yang relevan; docs/RELEASE_RUNBOOK.md (baru saat implementasi); docs/EXECUTION_PLAN.md (evidence/progress). Perubahan aplikasi hanya forward fix dari kegagalan nyata, bukan fitur baru.

## Perilaku wajib

Sebelum full run, satu batch existing tests area disentuh untuk menemukan fixture usang; per task sudah menjalankan narrow tests, jangan ulang yang passing tanpa perubahan. Lalu full suite sekali dan build sekali. Rehearse fresh install dan upgrade copy DB+private files non-production dengan migration dry-run/conflict/checksum/FK/restore; test DB PHPunit tidak mengarah app DB. Deploy maintenance jika perlu, migrate additive, release atomic, workers restart/cache aman. Requirements: PHP/extensions sesuai composer platform check, MySQL engine versi yang diuji, Node hanya build, webroot public, HTTPS secure sessions/CSRF/rate limits, APP_DEBUG=false, app key/backup secrets terkelola, private disk tidak served, writable storage/cache terbatas. Tidak memasang PDF renderer/Chromium untuk export. Logging request ID+actor+event tanpa PII/answers/secrets; error handling 403/404/409/422 dan storage/DB failure. Index/pagination/eager load untuk roster/report/cert; ukur query counts dan p95 load pada dataset 1000 peserta/100 jadwal di hardware staging yang dicatat. Target awal p95 halaman biasa≤1s dan save≤1s server-side; bila gagal, catat blocker bukan klaim production-ready. Backup restore test wajib; retention keputusan owner. Performance/load test boleh scoped di akhir, bukan benchmarking tanpa kebutuhan.

## Acceptance criteria

- [ ] Full suite dan build hasil nyata tercatat termasuk jumlah test/skipped; tidak mematikan security tests. Bila failure diperbaiki, jalankan failing subset, catat full run belum diulang/hasilnya, jangan menyebut full suite bersih jika belum.
- [ ] Semua form×scheme source mappings published tanpa unresolved; seluruh owner questions release-critical terselesaikan atau release dinyatakan blocked.
- [ ] Authorization/file/PII, migration/seed idempotency, restore, error logs, concurrency dan responsive evidence tersedia; deployment rollback dapat dilakukan tanpa kehilangan new writes.
- [ ] Dokumen handoff membedakan verified/unverified, tidak mengklaim PDF pixel fidelity/generation atau lifecycle later.

## Test sempit / bukti

Satu kali: php artisan test --compact; npm run build. Tambahan hanya failing subset jika benar-benar gagal, migration rehearsal/restore dan scoped staging load. Jangan re-run passing full suite/build tanpa perubahan yang membatalkan bukti; bila perubahan mengharuskan full verification lagi, jelaskan bukti yang belum valid kepada owner.

Tulis test dari acceptance sekali, fixture berdasarkan sumber/kejadian yang jelas. Test gagal karena bug harus diperbaiki; fixture legacy tidak boleh memaksa consent/hasil palsu. Jangan menjalankan full suite/build pada task selain 24. Selesaikan task hanya dengan bukti command, exit code, dan acceptance; status blocked bukan complete.

## Rollback

Simpan previous code/build, DB+file backup konsisten dan mapping/checkpoint. Sebelum new writes, rollback atomic release+restore. Sesudah writes, freeze/backup tambahan lalu forward fix atau replay teruji; jangan blind migrate:rollback. Keputusan/consent/file histories tidak dihapus.

## Handoff sesi

Commit hanya file task dan perubahan dokumen progress yang relevan; jangan staging file owner. Isi baris evidence EXECUTION_PLAN dengan commit, command/hasil, blocker dan source decision yang digunakan. Tidak perlu menulis plan baru. Jika scope tidak muat satu sesi, berhenti pada draft/checkpoint konsisten, jangan menyebut task selesai atau mengurangi verifikasi.
