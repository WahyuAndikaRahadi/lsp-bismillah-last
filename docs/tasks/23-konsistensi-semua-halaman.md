# Task 23 — Seluruh halaman satu produk dan alur tanpa jalan buntu

Status: [ ] belum dikerjakan. Dependency: **06–22**.

## Tujuan

Menutup sisa UI/route/validation mismatch untuk setiap role, termasuk assessment mobile.

## Kontrak yang harus dibaca

`../CONTEXT.md`, bagian relevan `../DATA_MODEL.md`, `../FIELD_COVERAGE.md` + baris CSV keluarga/skema task ini, dan `../PAGE_CONTRACT.md`. Path file di bawah relatif root repo. File bertanda baru dibuat oleh task; path existing diperiksa dengan `rg` sebelum edit. Jika file baseline berbeda, catat pemetaan path aktual; jangan membuat controller duplikat hanya untuk mengikuti nama rencana.

Batas tetap: hanya core SOP04/05/06/10; tiga role admin/asesor/asesi; satu LSP; tanpa PDF export, complaint/staff/capability/finance/chat/messages/letters/quality/impersonation ; banding, surveilans, resertifikasi, dan ketidakberpihakan hanya melalui task 21-01 sampai 21-04. Original read-only. Jangan menyalin kode arsip. Nilai hasil/consent/attendance harus event nyata, actor dan timestamp; semua akses object-authorized. Ketidakjelasan source = blocker, bukan izin menebak.

## Scope dan file

resources/views public/auth/admin/asesor/asesi yang masih aktif; resources/css/app.css; resources/js/app.js; app/Services/AssessmentNextAction.php; app/Http/Middleware/CheckAplApproved.php; TrackLastStep.php; routes/web.php; tests/Feature/CoreNavigationTest.php (baru); tests/Browser/core-flows.mjs (baru).

## Perilaku wajib

Inventaris semua route aktif sekali dan mapping ke PAGE_CONTRACT atau hapus. Terapkan components/pattern untuk sisa halaman tanpa membuat portal baru. Form screens identik urutan/number/wording/group/table source; responsive tidak boleh rename sections. Remove redundant CTA/redirect last_step mutator, competing menu/banner di exam, dead href/action. Pure next action resolver jadi satu aturan role/state. Test browser sekali per flow public/auth, candidate onboarding/APL, assessor plan/exam/report, admin review/certificate pada 390/768/1280; inspect horizontal page scroll, local table usability, tap/focus/errors, long data, empty state. Jangan membuat screenshot berulang tanpa masalah baru.

## Acceptance criteria

- [ ] Setiap route aktif punya role/object policy dan usable primary action/status; no empty href/inert buttons atau loop.
- [ ] Semua halaman memakai tokens/three patterns; control text≥14px, mobile input16px, tap44px, keyboard focus/contrast, no visual effects terlarang.
- [ ] Assessment focused dengan progress/timer/save status; data/error/draft tidak hilang; source wording/table preserved.

## Test sempit / bukti

php artisan test --compact tests/Feature/CoreNavigationTest.php; node tests/Browser/core-flows.mjs. Browser memakai test DB terisolasi, akun fixture dan base URL localhost. Catat hasil nyata/yang belum diperiksa, jangan klaim dari CSS.

Tulis test dari acceptance sekali, fixture berdasarkan sumber/kejadian yang jelas. Test gagal karena bug harus diperbaiki; fixture legacy tidak boleh memaksa consent/hasil palsu. Jangan menjalankan full suite/build pada task selain 24. Selesaikan task hanya dengan bukti command, exit code, dan acceptance; status blocked bukan complete.

## Rollback

Revert UI-only change tanpa mengubah domain; keep policy fixes. Jika next-action bermasalah, render detail read-only dengan blocker, bukan redirect loop.

## Handoff sesi

Commit hanya file task dan perubahan dokumen progress yang relevan; jangan staging file owner. Isi baris evidence EXECUTION_PLAN dengan commit, command/hasil, blocker dan source decision yang digunakan. Tidak perlu menulis plan baru. Jika scope tidak muat satu sesi, berhenti pada draft/checkpoint konsisten, jangan menyebut task selesai atau mengurangi verifikasi.
