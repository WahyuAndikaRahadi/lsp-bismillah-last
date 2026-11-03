# Task 00 — Kunci baseline dan lingkungan implementasi

Status: [ ] belum dikerjakan. Dependency: **—**.

## Tujuan

Menyiapkan kerja Gemini tanpa kehilangan file owner atau membawa kode arsip.

## Kontrak yang harus dibaca

`../CONTEXT.md`, bagian relevan `../DATA_MODEL.md`, `../FIELD_COVERAGE.md` + baris CSV keluarga/skema task ini, dan `../PAGE_CONTRACT.md`. Path file di bawah relatif root repo. File bertanda baru dibuat oleh task; path existing diperiksa dengan `rg` sebelum edit. Jika file baseline berbeda, catat pemetaan path aktual; jangan membuat controller duplikat hanya untuk mengikuti nama rencana.

Batas tetap: hanya core SOP04/05/06/10; tiga role admin/asesor/asesi; satu LSP; tanpa PDF export, complaint/staff/capability/finance/chat/messages/letters/quality/impersonation ; banding, surveilans, resertifikasi, dan ketidakberpihakan hanya melalui task 21-01 sampai 21-04. Original read-only. Jangan menyalin kode arsip. Nilai hasil/consent/attendance harus event nyata, actor dan timestamp; semua akses object-authorized. Ketidakjelasan source = blocker, bukan izin menebak.

## Scope dan file

docs/BASELINE.md (hanya tambah bukti); docs/EXECUTION_PLAN.md (progress). Tidak mengubah source aplikasi pada task ini.

## Perilaku wajib

Pastikan branch berawal 003f90f dan tag archive/astra-implementation tetap e7437d8. Catat git status, hash file dirty/untracked owner dan source originals; gunakan staging eksplisit. Pilih database development/test baru dan direktori private file terisolasi; jangan koneksi database produksi. Dependency terpasang/cache bukan bukti versi source. Baca CONTEXT, DATA_MODEL dan task yang akan dikerjakan, bukan mengambil kode archive. Jangan install ulang Boost/ubah lockfile owner hanya untuk task setup.

## Acceptance criteria

- [ ] Working tree milik owner tetap identik; original SOP tidak disentuh atau ikut staging.
- [ ] Tidak ada kode Astra pada branch baseline; DB/file test terpisah dan APP_URL localhost.
- [ ] Versi PHP/Composer/Node dan status ketersediaan sumber dicatat tanpa mengekspos env secrets.

## Test sempit / bukti

Tidak menjalankan full suite/build. Verifikasi git status, git rev-parse, php -v, composer -V, node --version; bandingkan hash owner/source.

Tulis test dari acceptance sekali, fixture berdasarkan sumber/kejadian yang jelas. Test gagal karena bug harus diperbaiki; fixture legacy tidak boleh memaksa consent/hasil palsu. Jangan menjalankan full suite/build pada task selain 24. Selesaikan task hanya dengan bukti command, exit code, dan acceptance; status blocked bukan complete.

## Rollback

Hanya documentation change; revert commit task bila perlu. Jangan git reset/clean terhadap file owner.

## Handoff sesi

Commit hanya file task dan perubahan dokumen progress yang relevan; jangan staging file owner. Isi baris evidence EXECUTION_PLAN dengan commit, command/hasil, blocker dan source decision yang digunakan. Tidak perlu menulis plan baru. Jika scope tidak muat satu sesi, berhenti pada draft/checkpoint konsisten, jangan menyebut task selesai atau mengurangi verifikasi.
