# Task 19 — BA normal dan daftar hadir dari fakta

Status: [ ] belum dikerjakan. Dependency: **11,18; Q1 sebelum label final BA**.

## Tujuan

Menyatukan rekaman kegiatan tanpa mengarang waktu, koordinator, kehadiran atau hasil.

## Kontrak yang harus dibaca

`../CONTEXT.md`, bagian relevan `../DATA_MODEL.md`, `../FIELD_COVERAGE.md` + baris CSV keluarga/skema task ini, dan `../PAGE_CONTRACT.md`. Path file di bawah relatif root repo. File bertanda baru dibuat oleh task; path existing diperiksa dengan `rg` sebelum edit. Jika file baseline berbeda, catat pemetaan path aktual; jangan membuat controller duplikat hanya untuk mengikuti nama rencana.

Batas tetap: hanya core SOP04/05/06/10; tiga role admin/asesor/asesi; satu LSP; tanpa PDF export, complaint/staff/capability/finance/chat/messages/letters/quality/impersonation ; banding, surveilans, resertifikasi, dan ketidakberpihakan hanya melalui task 21-01 sampai 21-04. Original read-only. Jangan menyalin kode arsip. Nilai hasil/consent/attendance harus event nyata, actor dan timestamp; semua akses object-authorized. Ketidakjelasan source = blocker, bukan izin menebak.

## Scope dan file

app/Models/ScheduleMinutes.php (baru); app/Http/Controllers/Admin/ScheduleMinutesController.php (baru); app/Services/ScheduleMinutesService.php (baru); resources/views/admin/jadwal/minutes.blade.php dan attendance.blade.php (baru); tests/Feature/CoreMinutesAttendanceTest.php (baru).

## Perilaku wajib

Data BA04.4 normal: judul, hari/tanggal/bulan/tahun, actual start/end, Pada/Di, total enrolled/present/absent/K/BK, rows name/result/followup/notes, asesor/koordinator/signature. Nomor final menunggu Q1; jangan gunakan teks uji ulang untuk ordinary session. Absensi FR04.3 dua layout sesuai original dan real event; nomor registrasi hanya bila sesuai orang/source. Koordinator explicit appointed identity, bukan creator. Auto aggregate tidak auto-finalize; actual times dilaporkan actor berwenang dengan evidence, bukan copy planned. Angka/terbilang derived dari count sama, peserta belum dinilai bukan BK. Checklist actual record completeness, no semua centang.

## Acceptance criteria

- [ ] Enrolled=present+absent sesuai session boundaries; pending result tidak dihitung K/BK; cancelled/reversed attendance benar.
- [ ] Finalization diblokir record belum lengkap atau perbedaan count; source aggregate tersimpan per revision.
- [ ] No manual Hadir button, no invented actual time/signatory; Q1 unresolved mencegah label dokumen final.

## Test sempit / bukti

php artisan test --compact tests/Feature/CoreMinutesAttendanceTest.php. Mixed present/absent/pending/K/BK, multiple assessors, reversal, actual-time ordering, missing coordinator dan mutation-free GET.

Tulis test dari acceptance sekali, fixture berdasarkan sumber/kejadian yang jelas. Test gagal karena bug harus diperbaiki; fixture legacy tidak boleh memaksa consent/hasil palsu. Jangan menjalankan full suite/build pada task selain 24. Selesaikan task hanya dengan bukti command, exit code, dan acceptance; status blocked bukan complete.

## Rollback

Reopen BA hanya correction beralasan dan revision baru; dependent decision direview ulang. Jangan menghitung ulang BA historis diam-diam mengikuti state baru.

## Handoff sesi

Commit hanya file task dan perubahan dokumen progress yang relevan; jangan staging file owner. Isi baris evidence EXECUTION_PLAN dengan commit, command/hasil, blocker dan source decision yang digunakan. Tidak perlu menulis plan baru. Jika scope tidak muat satu sesi, berhenti pada draft/checkpoint konsisten, jangan menyebut task selesai atau mengurangi verifikasi.
