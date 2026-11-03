# Task 11 — Consent jadwal, AK-01 dan attendance event

Status: [ ] belum dikerjakan. Dependency: **07; integrasi AK01 final setelah 09–10 (consent join dapat dikerjakan lebih awal)**.

## Tujuan

Mengikat auto-signature dan kehadiran kepada event nyata satu kali.

## Kontrak yang harus dibaca

`../CONTEXT.md`, bagian relevan `../DATA_MODEL.md`, `../FIELD_COVERAGE.md` + baris CSV keluarga/skema task ini, dan `../PAGE_CONTRACT.md`. Path file di bawah relatif root repo. File bertanda baru dibuat oleh task; path existing diperiksa dengan `rg` sebelum edit. Jika file baseline berbeda, catat pemetaan path aktual; jangan membuat controller duplikat hanya untuk mengikuti nama rencana.

Batas tetap: hanya core SOP04/05/06/10; tiga role admin/asesor/asesi; satu LSP; tanpa PDF export, complaint/staff/capability/finance/chat/messages/letters/quality/impersonation ; banding, surveilans, resertifikasi, dan ketidakberpihakan hanya melalui task 21-01 sampai 21-04. Original read-only. Jangan menyalin kode arsip. Nilai hasil/consent/attendance harus event nyata, actor dan timestamp; semua akses object-authorized. Ketidakjelasan source = blocker, bukan izin menebak.

## Scope dan file

app/Models/ScheduleConsent.php; AttendanceEvent.php (baru); app/Services/ScheduleConsentService.php; AttendanceService.php (baru); app/Http/Controllers/Asesi/JadwalController.php; RuangUjiController.php; Asesor/WorkspaceController.php; app/Http/Controllers/Asesi/AssessmentAgreementController.php (baru); AK01/join views; tests/Feature/CoreConsentAttendanceTest.php (baru).

## Perilaku wajib

Consent signature asesi saat join, asesor saat menerima penugasan; explicit unchecked checkbox dengan teks scope/version/signature hash dan server time. Penerimaan AK01 atas plan revision berbeda dari signature authorization; asesi/asesor menerima bagiannya sendiri. POST enter exam pada local date/window setelah eligibility+plan+consents membuat attendance idempotent. Assessor attendance dari first genuine instrument save atau Mulai sesi asesmen untuk pengawasan; GET/login/consent bukan hadir. Admin reverse salah event beralasan tanpa menghapus asal; consent revocation memblokir signature baru. Tidak ada tombol Hadir. Gunakan service yang sama untuk semua route.

## Acceptance criteria

- [ ] Approval APL tidak menciptakan consent; user lain tidak bisa grant; signature profile berubah tidak mengubah consent/document lama.
- [ ] GET dua kali, timezone boundary, early/late entry, duplicate POST dan concurrent requests tidak menghasilkan kehadiran palsu/ganda.
- [ ] Revocation/reversal auditable; existing finalized records mempertahankan provenance, bukan silent resign.

## Test sempit / bukti

php artisan test --compact tests/Feature/CoreConsentAttendanceTest.php. Freeze server clock untuk before/day/after dan timezone; explicit consent/event attribution, correction/role negatives.

Tulis test dari acceptance sekali, fixture berdasarkan sumber/kejadian yang jelas. Test gagal karena bug harus diperbaiki; fixture legacy tidak boleh memaksa consent/hasil palsu. Jangan menjalankan full suite/build pada task selain 24. Selesaikan task hanya dengan bukti command, exit code, dan acceptance; status blocked bukan complete.

## Rollback

Jangan delete event; reversal tersimpan dan dependent records ditandai perlu review. Rollback kode mempertahankan tabel consent/attendance, menutup check-in bila tidak dapat memverifikasi.

## Handoff sesi

Commit hanya file task dan perubahan dokumen progress yang relevan; jangan staging file owner. Isi baris evidence EXECUTION_PLAN dengan commit, command/hasil, blocker dan source decision yang digunakan. Tidak perlu menulis plan baru. Jika scope tidak muat satu sesi, berhenti pada draft/checkpoint konsisten, jangan menyebut task selesai atau mengurangi verifikasi.
