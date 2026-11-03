# Task 12 — Ruang uji fokus dan IA-05 A/B/C

Status: [ ] belum dikerjakan. Dependency: **08,10,11**.

## Tujuan

Memberi satu langkah berikut dan CBT yang tidak kehilangan jawaban atau membocorkan key.

## Kontrak yang harus dibaca

`../CONTEXT.md`, bagian relevan `../DATA_MODEL.md`, `../FIELD_COVERAGE.md` + baris CSV keluarga/skema task ini, dan `../PAGE_CONTRACT.md`. Path file di bawah relatif root repo. File bertanda baru dibuat oleh task; path existing diperiksa dengan `rg` sebelum edit. Jika file baseline berbeda, catat pemetaan path aktual; jangan membuat controller duplikat hanya untuk mengikuti nama rencana.

Batas tetap: hanya core SOP04/05/06/10; tiga role admin/asesor/asesi; satu LSP; tanpa PDF export, complaint/staff/capability/finance/chat/messages/letters/quality/impersonation ; banding, surveilans, resertifikasi, dan ketidakberpihakan hanya melalui task 21-01 sampai 21-04. Original read-only. Jangan menyalin kode arsip. Nilai hasil/consent/attendance harus event nyata, actor dan timestamp; semua akses object-authorized. Ketidakjelasan source = blocker, bukan izin menebak.

## Scope dan file

app/Http/Controllers/Asesi/RuangUjiController.php; AssessmentExecutionController.php; Asesor/AssessmentExecutionController.php; app/Services/AssessmentNextAction.php; app/Services/CbtAttemptService.php (baru); ruang-uji/CBT views aktif; resources/js/cbt.js (baru); tests/Feature/CoreExamFlowTest.php (baru).

## Perilaku wajib

Ruang uji focused shell: progress, timer jika relevan, save status, satu next instrument. Instrument tersedia dari accepted plan dan role field ownership; manual unlocked flag bukan authority. IA05A exact questions/options versi, IA05B hanya server/private assessor, IA05C actual answers/achievement/feedback/footer. Timer server authoritative; autosave per attempt/version dan retry idempotent; reconnect/reload tidak reset waktu, submit final tidak ganda. Local cache hanya draft scoped assessment+user dengan clear-on-logout/submit; no key/PII di cache. Akomodasi waktu dari AK07 tercatat. Score dihitung dari key version, tidak membuat K/BK.

## Acceptance criteria

- [ ] Candidate tidak menerima correct_answer dalam HTML/JSON/assets/reference; tugas lain tidak muncul jika di luar rencana.
- [ ] Reload/network retry/multi-tab/timer expiry aman, rejected save menjelaskan error dan mempertahankan draft.
- [ ] Tidak ada redirect loop, competing menu atau tombol generate/Hadir; submit mengarah satu kali ke status/next action.

## Test sempit / bukti

php artisan test --compact tests/Feature/CoreExamFlowTest.php. Timer boundary, concurrency/version, unauthorized attempt, hidden answer-key assertions, invalid option/question ID dan no auto competence.

Tulis test dari acceptance sekali, fixture berdasarkan sumber/kejadian yang jelas. Test gagal karena bug harus diperbaiki; fixture legacy tidak boleh memaksa consent/hasil palsu. Jangan menjalankan full suite/build pada task selain 24. Selesaikan task hanya dengan bukti command, exit code, dan acceptance; status blocked bukan complete.

## Rollback

Jangan reset attempt berjalan; disable new attempts sementara, tetap izinkan recovery aman. Deploy rollback harus mempertahankan versi soal/answers/time; forward fix jika payload tidak kompatibel.

## Handoff sesi

Commit hanya file task dan perubahan dokumen progress yang relevan; jangan staging file owner. Isi baris evidence EXECUTION_PLAN dengan commit, command/hasil, blocker dan source decision yang digunakan. Tidak perlu menulis plan baru. Jika scope tidak muat satu sesi, berhenti pada draft/checkpoint konsisten, jangan menyebut task selesai atau mengurangi verifikasi.
