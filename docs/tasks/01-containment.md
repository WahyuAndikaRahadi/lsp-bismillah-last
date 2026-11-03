# Task 01 — Tutup endpoint berisiko dan modul di luar scope

Status: [x] selesai. Dependency: **00**.

## Tujuan

Menghilangkan jalur eksploitasi baseline sebelum fitur baru dibuat.

## Kontrak yang harus dibaca

`../CONTEXT.md`, bagian relevan `../DATA_MODEL.md`, `../FIELD_COVERAGE.md` + baris CSV keluarga/skema task ini, dan `../PAGE_CONTRACT.md`. Path file di bawah relatif root repo. File bertanda baru dibuat oleh task; path existing diperiksa dengan `rg` sebelum edit. Jika file baseline berbeda, catat pemetaan path aktual; jangan membuat controller duplikat hanya untuk mengikuti nama rencana.

Batas tetap: hanya core SOP04/05/06/10; tiga role admin/asesor/asesi; satu LSP; tanpa PDF export, complaint/staff/capability/finance/chat/messages/letters/quality/impersonation; banding, surveilans, resertifikasi, dan ketidakberpihakan hanya melalui task 21-01 sampai 21-04. Original read-only. Jangan menyalin kode arsip. Nilai hasil/consent/attendance harus event nyata, actor dan timestamp; semua akses object-authorized. Ketidakjelasan source = blocker, bukan izin menebak.

## Scope dan file

routes/web.php; app/Http/Controllers/Asesi/RuangUjiController.php; app/Http/Controllers/Asesor/WorkspaceController.php; app/Services/AkWorkflowService.php; resources/views/layouts/app.blade.php; resources/views/layouts/guest.blade.php; tests/Feature/CoreContainmentTest.php (baru).

## Perilaku wajib

Disable seluruh PDF/export/print/generate certificate, proxy/switchback, complaint public tracking, surveillance lama, finance/letters/messages/quality/appeal workflow lama (kode baseline appeal dan surveillance ditutup sementara hingga implementasi lean pengganti siap pada task 21-01 dan 21-03). Hapus link menu ke route ditutup. GET/HEAD workspace/ruang uji hanya membaca: buang implicit consent, create/update finding dan default K. Jika fitur lama belum punya command aman, tampilkan blocker read-only; jangan memindahkan perilaku berbahaya ke POST tanpa validasi. Jangan delete data/model dahulu. Endpoint baru tidak dibuat sebagai compatibility fallback ke ID legacy.

## Acceptance criteria

- [x] Route out-of-scope tidak dapat dipanggil langsung walau URL diketahui; no proxy session recovery.
- [x] Snapshot tabel domain sebelum/sesudah GET/HEAD sama; tidak ada automatic K/BK/consent.
- [x] Tidak ada link aktif ke endpoint yang ditutup; aplikasi dapat memberi status belum tersedia yang jelas.

## Test sempit / bukti

Buat lalu jalankan php artisan test --compact tests/Feature/CoreContainmentTest.php. Kasus tamu, tiga role, GET dua kali, route print/proxy/complaint langsung. Jangan full suite.

Tulis test dari acceptance sekali, fixture berdasarkan sumber/kejadian yang jelas. Test gagal karena bug harus diperbaiki; fixture legacy tidak boleh memaksa consent/hasil palsu. Jangan menjalankan full suite/build pada task selain 24. Selesaikan task hanya dengan bukti command, exit code, dan acceptance; status blocked bukan complete.

## Rollback

Revert UI jika perlu, tetapi jangan menghidupkan endpoint berbahaya. Jika regression menghalangi akses, maintenance/read-only sampai forward fix.

## Handoff sesi

Commit hanya file task dan perubahan dokumen progress yang relevan; jangan staging file owner. Isi baris evidence EXECUTION_PLAN dengan commit, command/hasil, blocker dan source decision yang digunakan. Tidak perlu menulis plan baru. Jika scope tidak muat satu sesi, berhenti pada draft/checkpoint konsisten, jangan menyebut task selesai atau mengurangi verifikasi.
