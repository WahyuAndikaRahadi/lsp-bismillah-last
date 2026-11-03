# Task 17 — AK-03 dan AK-04 data-only

Status: [ ] belum dikerjakan. Dependency: **16**.

## Tujuan

Menyimpan umpan balik dan field banding tanpa membangun lifecycle banding.

## Kontrak yang harus dibaca

`../CONTEXT.md`, bagian relevan `../DATA_MODEL.md`, `../FIELD_COVERAGE.md` + baris CSV keluarga/skema task ini, dan `../PAGE_CONTRACT.md`. Path file di bawah relatif root repo. File bertanda baru dibuat oleh task; path existing diperiksa dengan `rg` sebelum edit. Jika file baseline berbeda, catat pemetaan path aktual; jangan membuat controller duplikat hanya untuk mengikuti nama rencana.

Batas tetap: hanya core SOP04/05/06/10; tiga role admin/asesor/asesi; satu LSP; tanpa PDF export, complaint/staff/capability/finance/chat/messages/letters/quality/impersonation atau lifecycle later. Original read-only. Jangan menyalin kode arsip. Nilai hasil/consent/attendance harus event nyata, actor dan timestamp; semua akses object-authorized. Ketidakjelasan source = blocker, bukan izin menebak.

## Scope dan file

app/Http/Controllers/Asesi/AssessmentFeedbackController.php (baru); app/Models/AssessmentAk03Item.php; canonical AK04 form record menggantikan AssessmentAppeal lifecycle; views asesi AK03/AK04; routes/web.php; tests/Feature/CoreFeedbackTest.php (baru).

## Perilaku wajib

AK03 sepuluh komponen original, Ya/Tidak dan komentar, komentar lain; tampil setelah keputusan asesmen (rekomendasi assessor), tidak menunggu certificate. AK04 identity/date, tiga pertanyaan, scheme/no/reason/signature sesuai original. User memilih real answers; no auto-Ya. Tampilkan bahwa proses penanganan berlangsung di luar aplikasi dengan channel resmi hanya bila owner memasok; jangan janji case/investigation/SLA. Tidak ada admin appeal queue, appeal status engine atau complaint. Data privat milik assessment.

## Acceptance criteria

- [ ] Exact paper order/labels dan per-row data terjaga; feedback tidak mengubah kompetensi.
- [ ] AK04 submit hanya menyimpan record+audit, tidak mengarang penerimaan/investigasi/hasil.
- [ ] Other-candidate dan unauthorized assessor denied; no PDF/print CTA.

## Test sempit / bukti

php artisan test --compact tests/Feature/CoreFeedbackTest.php. All rows round-trip, before-result gate, no default answer, data-only lifecycle absence, no result mutation.

Tulis test dari acceptance sekali, fixture berdasarkan sumber/kejadian yang jelas. Test gagal karena bug harus diperbaiki; fixture legacy tidak boleh memaksa consent/hasil palsu. Jangan menjalankan full suite/build pada task selain 24. Selesaikan task hanya dengan bukti command, exit code, dan acceptance; status blocked bukan complete.

## Rollback

Pertahankan jawaban/histori; jika external handoff belum disetujui, tampilkan status data tersimpan tanpa klaim ditindaklanjuti. Jangan delete legacy appeal data tanpa retention.

## Handoff sesi

Commit hanya file task dan perubahan dokumen progress yang relevan; jangan staging file owner. Isi baris evidence EXECUTION_PLAN dengan commit, command/hasil, blocker dan source decision yang digunakan. Tidak perlu menulis plan baru. Jika scope tidak muat satu sesi, berhenti pada draft/checkpoint konsisten, jangan menyebut task selesai atau mengurangi verifikasi.
