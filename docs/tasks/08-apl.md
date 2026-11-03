# Task 08 — APL-01/02 dan review tanpa consent palsu

Status: [ ] belum dikerjakan. Dependency: **07,11 bagian consent join tersedia; reference skema terkait selesai**.

## Tujuan

Alur permohonan singkat dengan form yang mengikuti original dan review nyata.

## Kontrak yang harus dibaca

`../CONTEXT.md`, bagian relevan `../DATA_MODEL.md`, `../FIELD_COVERAGE.md` + baris CSV keluarga/skema task ini, dan `../PAGE_CONTRACT.md`. Path file di bawah relatif root repo. File bertanda baru dibuat oleh task; path existing diperiksa dengan `rg` sebelum edit. Jika file baseline berbeda, catat pemetaan path aktual; jangan membuat controller duplikat hanya untuk mengikuti nama rencana.

Batas tetap: hanya core SOP04/05/06/10; tiga role admin/asesor/asesi; satu LSP; tanpa PDF export, complaint/staff/capability/finance/chat/messages/letters/quality/impersonation atau lifecycle later. Original read-only. Jangan menyalin kode arsip. Nilai hasil/consent/attendance harus event nyata, actor dan timestamp; semua akses object-authorized. Ketidakjelasan source = blocker, bukan izin menebak.

## Scope dan file

app/Http/Controllers/Asesi/AplController.php; Apl02Controller.php; Asesor/AplReviewController.php; Apl02ReviewController.php; app/Models/AssessmentProof.php; AssessmentApl02Item.php; app/Http/Requests/Apl01Request.php dan Apl02Request.php (baru); resources/views/asesi/apl*; resources/views/asesor/apl*; tests/Feature/CoreAplWorkflowTest.php (baru).

## Perilaku wajib

APL01 Bagian1a/1b/2/3.1/3.2/rekomendasi/signatures dan APL02 unit/elemen/KUK K/BK/evidence/footer sama urutan/wording original. Prefill snapshot/profile; bukan seluruh field wajib diisi ulang. Persyaratan alternatif OR sesuai skema. File existence hanya Ada; admin menyimpan administrative checks, assigned asesor review sesuai SOP04.1.4. Return revision punya reason per field, asesi resubmit tanpa kehilangan evidence/history. Submit/approve tidak mengisi AK01 consent, AK07 Tidak, K/BK asesor atau membuka instrument tanpa plan. One primary next action; pending review bukan dead end.

## Acceptance criteria

- [ ] All source fields covered/round-trip dan footers actor/timestamp nyata; self K/BK terpisah hasil asesor.
- [ ] Valid alternative eligibility diterima; missing required evidence ditolak dengan error per field; cross-user file reference ditolak.
- [ ] Review/resubmit audit lengkap, no GET mutation, approve tidak fabricate consent/result.

## Test sempit / bukti

php artisan test --compact tests/Feature/CoreAplWorkflowTest.php. Dataset tujuh skema approved test fixtures, alternative requirements, edit/resubmit, invalid foreign KUK/evidence ID dan reviewer identity.

Tulis test dari acceptance sekali, fixture berdasarkan sumber/kejadian yang jelas. Test gagal karena bug harus diperbaiki; fixture legacy tidak boleh memaksa consent/hasil palsu. Jangan menjalankan full suite/build pada task selain 24. Selesaikan task hanya dengan bukti command, exit code, dan acceptance; status blocked bukan complete.

## Rollback

Revert view/controller tanpa menghapus submitted revisions; data draft tetap. Jika validation contract berubah, migrasi payload eksplisit dan simpan revisi asal.

## Handoff sesi

Commit hanya file task dan perubahan dokumen progress yang relevan; jangan staging file owner. Isi baris evidence EXECUTION_PLAN dengan commit, command/hasil, blocker dan source decision yang digunakan. Tidak perlu menulis plan baru. Jika scope tidak muat satu sesi, berhenti pada draft/checkpoint konsisten, jangan menyebut task selesai atau mengurangi verifikasi.
