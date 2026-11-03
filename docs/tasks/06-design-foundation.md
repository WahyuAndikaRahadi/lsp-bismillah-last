# Task 06 — Shared design system dan halaman public/auth

Status: [ ] belum dikerjakan. Dependency: **01,04 (boleh bersamaan dengan data referensi)**.

## Tujuan

Satu identitas produk dan tiga pattern sebelum form core dibuat.

## Kontrak yang harus dibaca

`../CONTEXT.md`, bagian relevan `../DATA_MODEL.md`, `../FIELD_COVERAGE.md` + baris CSV keluarga/skema task ini, dan `../PAGE_CONTRACT.md`. Path file di bawah relatif root repo. File bertanda baru dibuat oleh task; path existing diperiksa dengan `rg` sebelum edit. Jika file baseline berbeda, catat pemetaan path aktual; jangan membuat controller duplikat hanya untuk mengikuti nama rencana.

Batas tetap: hanya core SOP04/05/06/10; tiga role admin/asesor/asesi; satu LSP; tanpa PDF export, complaint/staff/capability/finance/chat/messages/letters/quality/impersonation ; banding, surveilans, resertifikasi, dan ketidakberpihakan hanya melalui task 21-01 sampai 21-04. Original read-only. Jangan menyalin kode arsip. Nilai hasil/consent/attendance harus event nyata, actor dan timestamp; semua akses object-authorized. Ketidakjelasan source = blocker, bukan izin menebak.

## Scope dan file

resources/css/app.css; resources/js/app.js; resources/views/layouts/app.blade.php; guest.blade.php; resources/views/layouts/focused.blade.php (baru); resources/views/components/ui/* dan page/{list,detail,form}.blade.php (baru); resources/views/auth/*; public views yang dirender PublicController; app/Http/Controllers/PublicController.php; app/Http/Controllers/Admin/LspProfileController.php; public/fonts/* + lisensi (baru); tests/Feature/CorePublicAuthTest.php (baru).

## Perilaku wajib

Terapkan PAGE_CONTRACT: navy/red jarang, CSS vars dari profile tervalidasi, font self-host satu keluarga+mono, control≥14px, input mobile16px, target44px, contrast/focus. Hapus inline duplicate style/CDN/blur/glass/gradient/emoji hanya pada halaman disentuh; role pages menyusul task masing-masing. Public hanya identity/skema approved/syarat/jadwal nyata; no sample metrics. Login/register/reset konsisten dan role tidak dapat dipilih. Tiga pattern list/detail/form dipakai langsung, bukan katalog komponen demo.

## Acceptance criteria

- [ ] Public/auth memakai tokens/components sama; logo/color invalid memakai safe fallback tanpa CSS injection.
- [ ] Accessible label/error/focus, empty state jelas dan satu primary action; registration hanya asesi.
- [ ] Tidak ada dependency font/CDN remote saat render; lisensi font disertakan. Browser lintas viewport sekali pada task23, bukan screenshot berulang.

## Test sempit / bukti

php artisan test --compact tests/Feature/CorePublicAuthTest.php. Uji approved-only public data, sensitive fields absent, role injection, auth validation dan asset fallback.

Tulis test dari acceptance sekali, fixture berdasarkan sumber/kejadian yang jelas. Test gagal karena bug harus diperbaiki; fixture legacy tidak boleh memaksa consent/hasil palsu. Jangan menjalankan full suite/build pada task selain 24. Selesaikan task hanya dengan bukti command, exit code, dan acceptance; status blocked bukan complete.

## Rollback

Revert asset/view commit; data LSP profile tetap. Asset deploy atomic dengan previous build untuk rollback, tidak menghapus user-uploaded logo.

## Handoff sesi

Commit hanya file task dan perubahan dokumen progress yang relevan; jangan staging file owner. Isi baris evidence EXECUTION_PLAN dengan commit, command/hasil, blocker dan source decision yang digunakan. Tidak perlu menulis plan baru. Jika scope tidak muat satu sesi, berhenti pada draft/checkpoint konsisten, jangan menyebut task selesai atau mengurangi verifikasi.
