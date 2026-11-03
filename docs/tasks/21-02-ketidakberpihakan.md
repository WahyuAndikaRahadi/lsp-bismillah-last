# Task 21-02 — Ketidakberpihakan (SOP 01)

Status: [x] selesai. Dependency: **04, 07**.

## Tujuan

Membangun pencatatan komitmen ketidakberpihakan, deklarasi benturan kepentingan, dan pergantian asesor berdasar rekaman resmi (SOP 01, FR-SOP-01.1, FR-SOP-01.4, FR-SOP-01.5, FR-SOP-01.7).

## Kontrak yang harus dibaca

`../CONTEXT.md`, bagian relevan `../DATA_MODEL.md`, `../FIELD_COVERAGE.md` + baris CSV keluarga/skema task ini, dan `../PAGE_CONTRACT.md`. Path file di bawah relatif root repo. File bertanda baru dibuat oleh task; path existing diperiksa dengan `rg` sebelum edit. Jika file baseline berbeda, catat pemetaan path aktual; jangan membuat controller duplikat hanya untuk mengikuti nama rencana.

Batas tetap: hanya core SOP04/05/06/10; tiga role admin/asesor/asesi; satu LSP; tanpa PDF export, complaint/staff/capability/finance/chat/messages/letters/quality/impersonation; banding, surveilans, resertifikasi, dan ketidakberpihakan hanya melalui task 21-01 sampai 21-04. Original read-only. Jangan menyalin kode arsip. Nilai hasil/consent/attendance harus event nyata, actor dan timestamp; semua akses object-authorized. Ketidakjelasan source = blocker, bukan izin menebak.

## Scope dan file

app/Models/ImpartialityCommitment.php (baru); app/Models/ConflictDeclaration.php (baru); app/Models/AssessorReplacementEvent.php (baru); app/Http/Controllers/Admin/ImpartialityController.php (baru); app/Http/Controllers/Asesor/ImpartialityController.php (baru); views admin dan asesor terkait ketidakberpihakan; routes/web.php; tests/Feature/CoreImpartialityTest.php (baru).

## Perilaku wajib

Komitmen ketidakberpihakan lembaga (FR-SOP-01.1) dicatat oleh Direktur/manajemen. Komitmen personil LSP (FR-SOP-01.4) dan Pakta Integritas Asesor (FR-SOP-01.5) diisi dan ditandatangani oleh personil/asesor terautentikasi (bukan disemai sebagai status otomatis disetujui). Deklarasi benturan kepentingan dicatat per penugasan atau jadwal. Asesor dengan benturan kepentingan yang belum diselesaikan (unresolved conflict) diblokir dari melakukan asesmen, verifikasi APL, dan rekomendasi terhadap peserta terkait. Pergantian asesor (FR-SOP-01.7) dicatat sebagai event teratribusi: mencatat asesi, asesor semula, asesor pengganti, dasar/alasan pergantian, persetujuan Manajer Sertifikasi / Direktur, actor, dan waktu. Bukti asesmen yang telah dibuat tidak dialihkan secara otomatis.

## Acceptance criteria

- [x] Komitmen ketidakberpihakan personil dan pakta integritas asesor tersimpan dari pengguna terautentikasi dengan hash tanda tangan dan timestamp nyata.
- [x] Asesor yang memiliki benturan kepentingan terbuka yang belum terselesaikan diblokir dari tindakan asesmen peserta bersangkutan.
- [x] Penggantian asesor (FR-SOP-01.7) tercatat sebagai rekaman pergantian resmi; penugasan pada asesmen diperbarui ke asesor pengganti tanpa memalsukan bukti lama.
- [x] Akses modifikasi deklarasi dan pergantian asesor dibatasi hanya untuk pihak yang berwenang (admin/manajer).

## Test sempit / bukti

php artisan test --compact tests/Feature/CoreImpartialityTest.php. Pencatatan pakta integritas, pemblokiran tindakan asesor berkonflik, pencatatan event pergantian asesor (FR-SOP-01.7), dan otorisasi akses.

## Rollback

Pembatalan status deklarasi atau pergantian memerlukan pencatatan alasan koreksi administratif; tidak menghapus histori integritas.

## Handoff sesi

Commit hanya file task dan perubahan dokumen progress yang relevan; jangan staging file owner. Isi baris evidence EXECUTION_PLAN dengan commit, command/hasil, blocker dan source decision yang digunakan.
