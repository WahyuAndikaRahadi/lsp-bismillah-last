# Rencana eksekusi Gemini — core sertifikasi

Rencana ini menggantikan roadmap implementasi lama. Penulis hanya mengembalikan source ke baseline dan menulis dokumen; **tidak ada task implementasi di bawah yang sudah dikerjakan**. Scope: SOP04 penerimaan sampai penyerahan/penggunaan awal sertifikat, SOP05/06/10, serta alur lean banding (SOP 07), ketidakberpihakan (SOP 01), surveilans (SOP 04 §4 / SOP 19), dan resertifikasi (SOP 04 §5) melalui task 21-01 s.d. 21-04. PDF export, complaint/staff/capability/finance/chat/messages/letters/quality/impersonation tidak termasuk.

## Cara melaksanakan

1. Ikuti urutan tabel, bukan urutan nama file. ID task dipertahankan agar referensi field matrix stabil. Feature MAPA/consent dibangun sebelum APL UI final agar APL tidak mempunyai dependency sementara yang membuat consent palsu; urutan pengguna tetap APL → rencana/AK01 → asesmen.
2. Satu task satu sesi; task reference dipisah per skema, instrumen yang belum bersumber dipisah per keluarga. Jangan melompat dependency. Foundation/UI dapat dikerjakan saat source tertentu blocked, tetapi jangan publish materi atau membuka asesmen yang bergantung padanya.
3. Baca hanya kontrak yang diperlukan dan original terkait. Jangan mengambil kode arsip. Gunakan targeted patches, jaga dirty/untracked owner, stage path eksplisit. Tidak ada plan tambahan di luar dokumen ini.
4. Tulis tests dari acceptance, jalankan narrow test file task. Sebelum run akhir, satu batch existing test files di area berubah untuk memperbaiki fixture usang. Full suite dan build sekali pada task24. Jangan menghapus assertion agar lulus atau mengulang passing tests tanpa perubahan yang relevan.
5. Checkbox selesai hanya setelah acceptance dan bukti command/exit code tercatat. Sumber belum ada/otoritas belum diputuskan = **blocked**, bukan complete/NA. Jangan mengklaim produksi siap dengan blocker mandatory.
6. Semua form layar mengikuti paper. Field semantic di CSV bukan wording UI. Registry konkret source/page/row dibuat pada task02 dan tugas per skema; nilai transaksi tidak pernah diambil dari contoh hasil/nama/tanggal original.

## Urutan dan dependency

| Urutan | Selesai | Task sesi | Dependency | Evidence |
|---|---|---|---|---|

| 1 | [x] | [00 — Kunci baseline dan lingkungan implementasi](tasks/00-baseline-dan-isolasi.md) | — | 8c0cc29; git rev-parse, php -v, node -v; exit 0; env isolated sqlite memory; no deviations |
| 2 | [x] | [01 — Tutup endpoint berisiko dan modul di luar scope](tasks/01-containment.md) | 00 | d571d00; php artisan test --compact tests/Feature/CoreContainmentTest.php; exit 0; 3 tests passed, 38 assertions; no deviations |
| 3 | [x] | [02 — Tetapkan registry formulir bersama dan konflik sumber](tasks/02-registry-original.md) | 00 | 490c293; php artisan test --compact tests/Unit/FormRegistryTest.php; exit 0; 7 tests passed, 415 assertions; no deviations |
| 4 | [x] | [03 — Satu model core dan reconciliation tanpa collision](tasks/03-model-dan-rekonsiliasi.md) | 01,02 (hanya schema common yang sumbernya tersedia) | 3e993d8; php artisan test --compact tests/Feature/CoreReconciliationTest.php; exit 0; 5 tests passed, 26 assertions; no deviations |
| 5 | [x] | [04 — Tiga role, policy objek, dan private file](tasks/04-role-dan-private-file.md) | 03 | f712d24; php artisan test --compact tests/Feature/CoreAuthorizationTest.php tests/Feature/CorePrivateFileTest.php; exit 0; 13 tests passed, 45 assertions; no deviations |
| 6 | [x] | [05 — Versioned reference dan bootstrap aman](tasks/05-reference-bootstrap.md) | 02,03,04 | 244f0e9; php artisan test --compact tests/Feature/CoreReferenceImportTest.php; exit 0; 7 tests passed, 31 assertions; no deviations |
| 7 | [x] | [05-01 — Referensi S1: Engine Junior Technician](tasks/05-01-skema-1.md) | 02,05 | a6c2e06; php artisan test --compact tests/Feature/Scheme1ReferenceCoverageTest.php; exit 0; 3 tests passed, 95 assertions; no deviations |
| 8 | [x] | [05-02 — Referensi S2: Maintenance Gasoline Engine Injection](tasks/05-02-skema-2.md) | 02,05 | 0a06af5; php artisan test --compact tests/Feature/Scheme2ReferenceCoverageTest.php; exit 0; 3 tests passed, 65 assertions; no deviations |
| 9 | [x] | [05-03 — Referensi S3: Periodical Maintenance Electrified Vehicles](tasks/05-03-skema-3.md) | 02,05 | 9d1c06b; php artisan test --compact tests/Feature/Scheme3ReferenceCoverageTest.php; exit 0; 3 tests passed, 65 assertions; no deviations |
| 10 | [x] | [05-04 — Referensi S4: Memperbaiki Panel Bodi Kendaraan](tasks/05-04-skema-4.md) | 02,05 | f6539ef; php artisan test --compact tests/Feature/Scheme4ReferenceCoverageTest.php; exit 0; 3 tests passed, 65 assertions; no deviations |
| 11 | [ ] | [05-05 — Referensi S5: Mekanik Junior](tasks/05-05-skema-5.md) | 02,05 | Belum dijalankan |
| 12 | [ ] | [05-06 — Referensi S6: Servis Sepeda Motor Listrik](tasks/05-06-skema-6.md) | 02,05 | Belum dijalankan |
| 13 | [ ] | [05-07 — Referensi S7: Servis Sepeda Motor Injeksi](tasks/05-07-skema-7.md) | 02,05 | Belum dijalankan |
| 14 | [ ] | [06 — Shared design system dan halaman public/auth](tasks/06-design-foundation.md) | 01,04 (boleh bersamaan dengan data referensi) | Belum dijalankan |
| 15 | [ ] | [07 — Profil reusable, jadwal dan penunjukan core](tasks/07-profil-jadwal-tuk.md) | 03,04,05,06; reference skema yang hendak dipublish selesai | Belum dijalankan |
| 16 | [ ] | [09 — MAPA-01 lengkap dan plan revision](tasks/09-mapa01.md) | 07,02; reference skema terkait selesai | Belum dijalankan |
| 17 | [ ] | [10 — MAPA-02 semua skema dan AK-07 nyata](tasks/10-mapa02-ak07.md) | 09; reference skema terkait selesai | Belum dijalankan |
| 18 | [ ] | [11 — Consent jadwal, AK-01 dan attendance event](tasks/11-consent-attendance.md) | 07; integrasi AK01 final setelah 09–10 (consent join dapat dikerjakan lebih awal) | Belum dijalankan |
| 19 | [ ] | [08 — APL-01/02 dan review tanpa consent palsu](tasks/08-apl.md) | 07,11 bagian consent join tersedia; reference skema terkait selesai | Belum dijalankan |
| 20 | [ ] | [12 — Ruang uji fokus dan IA-05 A/B/C](tasks/12-ruang-uji-cbt.md) | 08,10,11 | Belum dijalankan |
| 21 | [ ] | [13 — IA-01 dan seluruh sel report sheet](tasks/13-observasi-report-sheet.md) | 10,11,12; source report skema terkait diselesaikan Q2/Q3 | Belum dijalankan |
| 22 | [ ] | [14 — IA-02 skenario dan IA-03 tanggapan](tasks/14-praktik-pmo.md) | 13 | Belum dijalankan |
| 23 | [ ] | [15 — Instrumen lain setelah original tersedia](tasks/15-instrumen-sumber-tertunda.md) | 02,05-01..05-07,14; Q2 terselesaikan untuk form terkait | Belum dijalankan |
| 24 | [ ] | [15-01 — Implementasi IA.04.A setelah source disahkan](tasks/15-01-ia04a.md) | 15,12–14; original keluarga ini disahkan owner | Belum dijalankan |
| 25 | [ ] | [15-02 — Implementasi IA.04.B setelah source disahkan](tasks/15-02-ia04b.md) | 15,12–14; original keluarga ini disahkan owner | Belum dijalankan |
| 26 | [ ] | [15-03 — Implementasi IA.06.A/B/C setelah source disahkan](tasks/15-03-ia06.md) | 15,12–14; original keluarga ini disahkan owner | Belum dijalankan |
| 27 | [ ] | [15-04 — Implementasi IA.07 setelah source disahkan](tasks/15-04-ia07.md) | 15,12–14; original keluarga ini disahkan owner | Belum dijalankan |
| 28 | [ ] | [15-05 — Implementasi IA.08 setelah source disahkan](tasks/15-05-ia08.md) | 15,12–14; original keluarga ini disahkan owner | Belum dijalankan |
| 29 | [ ] | [15-06 — Implementasi IA.09 setelah source disahkan](tasks/15-06-ia09.md) | 15,12–14; original keluarga ini disahkan owner | Belum dijalankan |
| 30 | [ ] | [15-07 — Implementasi IA.10 setelah source disahkan](tasks/15-07-ia10.md) | 15,12–14; original keluarga ini disahkan owner | Belum dijalankan |
| 31 | [ ] | [15-08 — Implementasi IA.11 setelah source disahkan](tasks/15-08-ia11.md) | 15,12–14; original keluarga ini disahkan owner | Belum dijalankan |
| 32 | [ ] | [16 — Rekomendasi asesor eksplisit dan finalisasi](tasks/16-ak02-finalisasi.md) | 12–15 untuk metode yang dipakai; 11 | Belum dijalankan |
| 33 | [ ] | [17 — AK-03 dan Pengajuan Banding AK-04 (intake)](tasks/17-feedback-ak04.md) | 16 | Belum dijalankan |
| 34 | [ ] | [18 — AK-05/06 per asesor dan revision](tasks/18-laporan-ak05-ak06.md) | 16,17 | Belum dijalankan |
| 35 | [ ] | [19 — BA normal dan daftar hadir dari fakta](tasks/19-ba-daftar-hadir.md) | 11,18; Q1 sebelum label final BA | Belum dijalankan |
| 36 | [ ] | [20 — Review independen dan keputusan SOP10](tasks/20-pleno-keputusan.md) | 19; Q4 penunjukan/authority resolved | Belum dijalankan |
| 37 | [ ] | [21 — Upload sertifikat dan FR10.2](tasks/21-sertifikat-eksternal.md) | 20,04,11 | Belum dijalankan |
| 38 | [ ] | [21-01 — Banding Asesmen (SOP 07)](tasks/21-01-banding.md) | 17,04,20 | Belum dijalankan |
| 39 | [ ] | [21-02 — Ketidakberpihakan (SOP 01)](tasks/21-02-ketidakberpihakan.md) | 04,07 | Belum dijalankan |
| 40 | [ ] | [21-03 — Surveilans (SOP 04 §4 & SOP 19)](tasks/21-03-surveilans.md) | 21,04 | Belum dijalankan |
| 41 | [ ] | [21-04 — Resertifikasi (SOP 04 §5)](tasks/21-04-resertifikasi.md) | 21,07,08 | Belum dijalankan |
| 42 | [ ] | [22 — Hapus runtime legacy dan bloat setelah cutover](tasks/22-hapus-legacy-bloat.md) | 01,03–21, 21-01..21-04 untuk fitur terkait | Belum dijalankan |
| 43 | [ ] | [23 — Seluruh halaman satu produk dan alur tanpa jalan buntu](tasks/23-konsistensi-semua-halaman.md) | 06–22 | Belum dijalankan |
| 44 | [ ] | [24 — Integrasi, upgrade produksi dan handoff](tasks/24-release-dan-handoff.md) | 00–23 termasuk 05-01..07, 21-01..21-04; semua blocker owner yang memengaruhi release selesai | Belum dijalankan |

Task15 hanya source gate; task15-01..08 adalah implementasi satu keluarga per sesi. Bila owner menyediakan source bertahap, hanya keluarga yang lengkap boleh berjalan. Release seluruh cakupan menunggu semuanya terselesaikan atau perubahan scope tertulis. Task05-01..07 dapat berstatus draft data siap review; published memerlukan validasi source dan uji coba oleh pejabat nyata, bukan centang AI.

## Gate antar-kelompok

| Gate | Harus terbukti sebelum lanjut ke runtime terkait |
|---|---|
| G0 baseline | Source branch dari003f90f, tag arsip utuh, hash owner/original tidak berubah, DB test terisolasi |
| G1 containment/security | GET read-only, no auto-K/consent, route out-of-scope tertutup, policy objek/private disk, role canonical |
| G2 reference | Setiap versi yang dipakai mempunyai source disahkan, semua field/row/group/unit/KUK/question/key cocok, tidak ada missing original pada required method |
| G3 intake/plan | Profil reusable/snapshot, APL review nyata, TUK/credential valid, MAPA01/02/AK07, consent schedule dan AK01 acceptance terpisah |
| G4 assessment | Jawaban/observasi nyata, timer/draft aman, assigned ownership, explicit recommendations, AK05/06, BA/attendance dari event |
| G5 decision/delivery | Review independen, corrective action selesai, keputusan berwenang, upload per assessment, private certificates/FR10.2, no generation |
| G6 release | Legacy/bloat runtime hilang, semua halaman konsisten/responsive, test/build/rehearsal/restore terverifikasi, blocker mandatory ditutup |

## Coverage kebutuhan → pekerjaan

| Kebutuhan | Task / dokumen kontrak |
|---|---|
| Bloat/dua domain/ID collision | 01,03,22; RETIREMENT dan DEFECTS D01/D08/D20 |
| Satu model, FK, migration/seed aman | 03–05,24; DATA_MODEL |
| Seven scheme reference lengkap, no fabricated units/KUK/questions | 02,05-01..07,15-01..08; SOURCE_INDEX dan FIELD_COVERAGE |
| Form data dan source-identical screens | 08–21 + task per skema; FIELD_COVERAGE.csv dan registry source/page/row |
| Consent, attendance, no GET/no auto-result | 01,11,16,19; CONTEXT dan DEFECTS |
| Role/object/file security, answer key | 04,12,20–21; DATA_MODEL policies |
| Ruang uji/CBT sederhana, state/redirect benar | 07–12,16,23; PAGE_CONTRACT |
| Design seluruh web | 06, tiap task halaman,23; PAGE_CONTRACT |
| Multi-cert upload, private access/receipts | 20–21; no generation/print |
| Error/validation/logging/concurrency/performance/server/restore | 03–04,07–21,24; no PDF server requirement |

## Strategi pengujian

- **Source tests:** SHA, approved version, setiap field/section/row, exact unit/KUK/question/key counts, group membership, cross-form consistency, tujuh skema. Expected berasal original terverifikasi, bukan copy actual importer.
- **Unit/feature tests:** pure next action/readiness/state guards, draft/final, revision locks, idempotency, actual event attribution, no automatic competence, no GET writes. Clock dibekukan untuk tanggal/timer/expiry.
- **Authorization matrix:** tamu; admin berwenang/tidak berwenang keputusan; asesor assigned; asesor lain pada jadwal yang sama; asesi owner; asesi lain; assignment/consent/decision dicabut. Uji route langsung, bukan hanya menu tersembunyi.
- **File tests:** private disk, path traversal, spoof MIME/extension, oversized/missing file, answer-key exposure, staged upload failure/DB rollback, receipt tidak tercatat pada akses ditolak. Original `docs/reference` tidak dilayani webserver.
- **Data migration tests:** old/new ID sama, orphans/conflicts, partial retry, counts/hashes, dry-run non-mutating, seed repeat, snapshots besar, fresh install dan upgrade/restore data representative. Jangan pakai data pribadi produksi sebagai fixture.
- **Flow tests:** tiga role ujung ke ujung, dua asesor satu jadwal, dua assessment/certificate satu asesi, BK dan revisi/koreksi, absent bukan BK, decision independent, multi-tab/retry/timezone.
- **Browser:** satu cek per flow baru di akhir, 390/768/1280, keyboard/error/empty/long data, no page horizontal scroll/overlap, local table usability dan focused exam. Tidak re-crawl/screenshot tanpa defect baru.
- **Run akhir:** satu full suite dan build. Catat passing/failing/skipped dan unverified secara jujur. Jangan menyebut semua lulus setelah hanya rerun subset bila full run awal gagal; minta owner menilai bukti yang tersisa, tidak mengubah fakta.

## Definisi selesai

Selesai berarti semua checkbox yang berlaku mempunyai commit dan bukti acceptance; seluruh form×scheme×field dipetakan konkret dari original; mandatory source/authority questions resolved; fitur core berjalan pada tiga role dengan policy objek; histori dan file aman; seed/migration/restore teruji; semua halaman mengikuti design/paper; full test/build tercatat. Tidak mengklaim kepatuhan seluruh SOP organisasi, PDF identik, certificate generation, atau lifecycle later.

Isi evidence per task di tabel: `commit; command; exit code; jumlah test; blocker/unverified`. Jangan menambah laporan panjang per langkah. Perubahan owner decision dicatat pada CONTEXT beserta sumber jawaban/tanggal, lalu update dependency/matrix yang terkena.

## Pertanyaan terbuka untuk owner

1. **Q1 — BA:** untuk UJK biasa gunakan kode/format FR-SOP-04.4 atau rujukan 04.5 pada SOP04? Original04.5 menyebut asesmen ulang. Mohon penetapan kode resmi; data BA normal tetap disiapkan tanpa mengubah original.
2. **Q2 — original belum lengkap:** mohon original IA04A/B, IA06A/B/C, IA07–11 dan report sheet S5, atau keputusan tertulis daftar yang benar-benar berlaku. MAPA02 tetap semua skema sesuai keputusan yang sudah ada; apakah master struktur S1 dan AK05/06 S3 disahkan sebagai layout bersama dengan isi masing-masing skema?
3. **Q3 — versi/kode materi:** mana versi resmi untuk report sheet yang bernama IA01B tetapi mencetak IA02A/IA01, nomor skema XI/2025 versus I/2026, dan dua report sheet/PMO S3? Mohon penetapan versi nama/nomor skema dan penyelesaian ketidaksesuaian unit/soal/key yang ditemukan dalam transkripsi; AI tidak boleh memilih otomatis.
4. **Q4 — kewenangan nyata:** siapa pejabat/fungsi yang dapat mengesahkan penunjukan, verifikasi TUK, validasi rekomendasi dan keputusan sertifikasi; apakah komite/pimpinan menggunakan akun admin atau dokumen bertanda tangan eksternal; apa bukti partisipasi dan aturan rapat/quorum yang sah? Tidak menambah role/portal.
5. **Q5 — data operasional:** mohon identitas LSP/pejabat, pola penomoran, kebijakan validitas sertifikat, sumber spesifikasi/manual report sheet, field yang boleh N/A, dan kanal resmi penanganan AK04 di luar aplikasi. Tidak memakai nilai contoh original sebagai fakta produksi.
6. **Q6 — migrasi/retensi:** adakah database/file produksi yang wajib dimigrasikan, pemilik keputusan conflict mapping, serta kebijakan retensi bukti/identitas/sertifikat dan backup? Sampai jelas, tabel historis/file tidak dihapus dan deployment produksi tetap ditahan.
7. **Q7 — penyelidik dan reviewer independen:** siapa personel/pejabat yang berhak ditugaskan sebagai tim investigasi banding (FR-SOP-07.1) dan evaluator surveilans independen; bagaimana aturan penugasan memastikan tidak ada konflik kepentingan dengan kandidat atau asesor terkait?
8. **Q8 — batas waktu banding (appeal deadline):** berapa hari kalender/kerja batas waktu pengajuan banding AK-04 oleh asesi sejak penerbitan rekomendasi asesmen atau keputusan; apakah default tanpa batas waktu sampai ditetapkan SOP/kebijakan resmi?
9. **Q9 — interval dan bukti surveilans:** berapa interval berkala pelaksanaan surveilans bagi pemegang sertifikat (misal tahun ke-1, tahun ke-2), apa jenis dokumen bukti pemeliharaan kompetensi yang wajib diunggah pemegang sertifikat, dan apa konsekuensi jika pemegang sertifikat tidak melapor?
10. **Q10 — aturan resertifikasi LSP:** apa syarat teknis kelayakan sertifikasi ulang / perpanjangan sertifikat (misal: verifikasi portofolio pekerjaan berkelanjutan atau uji ulang unit tertentu), dan berapa bulan sebelum kedaluwarsa permohonan resertifikasi mulai dapat diajukan?

