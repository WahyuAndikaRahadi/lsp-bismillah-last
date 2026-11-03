# Task 07 — Profil reusable, jadwal dan penunjukan core

Status: [ ] belum dikerjakan. Dependency: **03,04,05,06; reference skema yang hendak dipublish selesai**.

## Tujuan

Memastikan data identitas/jadwal/TUK/penanggung jawab benar sebelum permohonan.

## Kontrak yang harus dibaca

`../CONTEXT.md`, bagian relevan `../DATA_MODEL.md`, `../FIELD_COVERAGE.md` + baris CSV keluarga/skema task ini, dan `../PAGE_CONTRACT.md`. Path file di bawah relatif root repo. File bertanda baru dibuat oleh task; path existing diperiksa dengan `rg` sebelum edit. Jika file baseline berbeda, catat pemetaan path aktual; jangan membuat controller duplikat hanya untuk mengikuti nama rencana.

Batas tetap: hanya core SOP04/05/06/10; tiga role admin/asesor/asesi; satu LSP; tanpa PDF export, complaint/staff/capability/finance/chat/messages/letters/quality/impersonation ; banding, surveilans, resertifikasi, dan ketidakberpihakan hanya melalui task 21-01 sampai 21-04. Original read-only. Jangan menyalin kode arsip. Nilai hasil/consent/attendance harus event nyata, actor dan timestamp; semua akses object-authorized. Ketidakjelasan source = blocker, bukan izin menebak.

## Scope dan file

app/Http/Controllers/Asesi/ProfileController.php; Asesor/ProfileController.php; Admin/JadwalController.php; Admin/TukController.php; Admin/UserController.php; Asesor/TukVerificationController.php; app/Models/UserProfile.php; Schedule.php; TukVerification.php; AsesorProfile.php; related profile/jadwal/tuk views; app/Services/AssessmentNextAction.php (baru); tests/Feature/CoreProfileScheduleTest.php (baru).

## Perilaku wajib

Biodata sesuai APL01: identity type/number, birth, sex,nationality, address/postal, home/mobile/office contacts, education, institution/position/work address/fax/email. Manual address fallback; NIK vs paspor rules, tidak memaksa telepon Indonesia/pekerjaan fiktif. Signature private versioned reusable. Jadwal memilih approved scheme version, verified TUK, credential-valid asesor dan koordinator nyata. FR04.1/04.2 metadata/nomor/penunjukan/roster tersimpan, bukan generic letters. Join candidate+schedule idempotent, satu assessment, assessment berikutnya reuse profile dengan snapshot baru. Service next action murni; state guard tidak mutasi.

## Acceptance criteria

- [ ] Satu asesi bisa dua jadwal/skema tanpa re-entry profile; edit profile tidak mengubah snapshot lama.
- [ ] Unknown/unqualified/expired asesor, TUK unverified, reference draft menghalangi publish; kapasitas join concurrent aman.
- [ ] Semua FR04.1/04.2 fields tercatat dengan actor berwenang; creator tidak otomatis koordinator/pimpinan.

## Test sempit / bukti

php artisan test --compact tests/Feature/CoreProfileScheduleTest.php. Uji NIK/paspor, foreign address/phone, alternative employment, snapshot reuse, two-schedule join, expiry, capacity race dan assignment scope.

Tulis test dari acceptance sekali, fixture berdasarkan sumber/kejadian yang jelas. Test gagal karena bug harus diperbaiki; fixture legacy tidak boleh memaksa consent/hasil palsu. Jangan menjalankan full suite/build pada task selain 24. Selesaikan task hanya dengan bukti command, exit code, dan acceptance; status blocked bukan complete.

## Rollback

Draft jadwal dapat dibatalkan beralasan; published dengan peserta tidak hard-delete. Profile correction menambah revision, tidak overwrite snapshot.

## Handoff sesi

Commit hanya file task dan perubahan dokumen progress yang relevan; jangan staging file owner. Isi baris evidence EXECUTION_PLAN dengan commit, command/hasil, blocker dan source decision yang digunakan. Tidak perlu menulis plan baru. Jika scope tidak muat satu sesi, berhenti pada draft/checkpoint konsisten, jangan menyebut task selesai atau mengurangi verifikasi.
