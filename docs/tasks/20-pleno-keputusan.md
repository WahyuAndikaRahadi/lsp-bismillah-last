# Task 20 — Review independen dan keputusan SOP10

Status: [ ] belum dikerjakan. Dependency: **19; Q4 penunjukan/authority resolved**.

## Tujuan

Memisahkan rekomendasi asesor dari keputusan sertifikasi yang dapat diaudit.

## Kontrak yang harus dibaca

`../CONTEXT.md`, bagian relevan `../DATA_MODEL.md`, `../FIELD_COVERAGE.md` + baris CSV keluarga/skema task ini, dan `../PAGE_CONTRACT.md`. Path file di bawah relatif root repo. File bertanda baru dibuat oleh task; path existing diperiksa dengan `rg` sebelum edit. Jika file baseline berbeda, catat pemetaan path aktual; jangan membuat controller duplikat hanya untuk mengikuti nama rencana.

Batas tetap: hanya core SOP04/05/06/10; tiga role admin/asesor/asesi; satu LSP; tanpa PDF export, complaint/staff/capability/finance/chat/messages/letters/quality/impersonation ; banding, surveilans, resertifikasi, dan ketidakberpihakan hanya melalui task 21-01 sampai 21-04. Original read-only. Jangan menyalin kode arsip. Nilai hasil/consent/attendance harus event nyata, actor dan timestamp; semua akses object-authorized. Ketidakjelasan source = blocker, bukan izin menebak.

## Scope dan file

app/Models/PlenoMeeting.php; CertificationDecision.php (baru); app/Http/Controllers/Admin/PlenoController.php; app/Services/CertificationDecisionService.php (baru); pleno/review/decision views; tests/Feature/CoreCertificationDecisionTest.php (baru).

## Perilaku wajib

Interface admin core merekam penunjukan tim/komite, scope/members/functions/authority, jadwal/agenda/tempat, actual participation FR10.1, notulen/BA pleno, review recommendation dan ketidaksesuaian/corrective action/verification. Anggota undangan tidak auto-present. Assessor peserta tidak bisa menjadi decision-maker untuk peserta itu; roles tetap tiga. Reviewer mengembalikan koreksi, tidak mengedit finding asesor. Final decision memerlukan documented administrative validation dan pengesahan pimpinan sesuai SOP04.3.4; external signed decision boleh direkam oleh admin dengan bukti/actor pencatat terpisah dari pengambil keputusan, setelah Q4. No quorum default atau fabricated vote. Semua anggota/approval/time nyata, no generic letters/assignment portal.

## Acceptance criteria

- [ ] Keputusan approved/rejected/correction terpisah AK02; positive recommendation tidak auto-certified.
- [ ] Self-decision, missing authority/evidence, unresolved discrepancy dan stale report version ditolak.
- [ ] FR10.1 attendance actual, decision immutable; reversal/supersession menonaktifkan certificate terdampak tanpa menghapus history.

## Test sempit / bukti

php artisan test --compact tests/Feature/CoreCertificationDecisionTest.php. Conflict of interest, admin tanpa penunjukan, external actor evidence, correction/resubmit, missing attendance proof dan duplicate/superseded decisions.

Tulis test dari acceptance sekali, fixture berdasarkan sumber/kejadian yang jelas. Test gagal karena bug harus diperbaiki; fixture legacy tidak boleh memaksa consent/hasil palsu. Jangan menjalankan full suite/build pada task selain 24. Selesaikan task hanya dengan bukti command, exit code, dan acceptance; status blocked bukan complete.

## Rollback

Decision tidak dihapus oleh rollback; reversal eksplisit dengan alasan/pejabat sah. Release bermasalah menutup decision write, mempertahankan read-only history.

## Handoff sesi

Commit hanya file task dan perubahan dokumen progress yang relevan; jangan staging file owner. Isi baris evidence EXECUTION_PLAN dengan commit, command/hasil, blocker dan source decision yang digunakan. Tidak perlu menulis plan baru. Jika scope tidak muat satu sesi, berhenti pada draft/checkpoint konsisten, jangan menyebut task selesai atau mengurangi verifikasi.
