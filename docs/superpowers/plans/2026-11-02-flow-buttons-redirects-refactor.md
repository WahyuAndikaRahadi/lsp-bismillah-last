# Flow, Buttons, and Redirects Refactor Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Menata ulang seluruh alur pengguna (Asesi, Asesor, Admin) menjadi linier tanpa redirect berantai (mengeliminasi 111 pemanggilan redirect berulang), menyatukan antarmuka formulir asesmen sesuai tata letak resmi BNSP, merampingkan tombol aksi (1 primary action per layar), serta memastikan kepatuhan penuh terhadap SOP BNSP (APL-01, APL-02, AK-01, CBT IA.05, AK-02, AK-05, dan Sidang Pleno).

**Architecture:** Mengonsolidasikan seluruh transaksi sertifikasi pada model ProIndo Core (`Schedule` pada `t_schedules` dan `Assessment` pada `t_assessments`). Asesi mengikuti satu garis langkah berikutnya (*Next Step Indicator*); Asesor bekerja di ruang kerja tunggal (*Unified Workspace*); Admin memiliki fitur *1-Click Batch Approval* untuk kecepatan ritme uji dan modul Rapat Pleno untuk pengesahan sertifikat BNSP. Seluruh route guard mengalihkan pengguna ke dashboard masing-masing dengan pesan informatif (tanpa error 403 mentah).

**Tech Stack:** Laravel 12/13 (PHP 8.3), Blade Templates, Tailwind CSS 4, Alpine.js / Vanilla JS untuk auto-save CBT, PHPUnit untuk TDD Feature Tests.

**Spec:** [`docs/superpowers/specs/2026-11-02-flow-redirects-refactor-design.md`](file:///home/wahyuandika/Dokumen/coding-experiment/rea-application-lsp/docs/superpowers/specs/2026-11-02-flow-redirects-refactor-design.md)

---

## Global Constraints

- **Stack Tunggal:** Gunakan `App\Models\Schedule` dan `App\Models\Assessment` sebagai sumber kebenaran data; jangan membuat tabel paralel baru.
- **Tiga Aktor Login:** Hanya ada 3 role login: `admin`, `asesor`, `asesi`. Fitur Komite Teknis dan TUK dijalankan di dalam portal Admin.
- **Tanda Tangan 1-Kali:** Tanda tangan digital digambar satu kali di profil pengguna (canvas), lalu otomatis disematkan ke formulir MUK.
- **Sertifikat Hanya untuk Kompeten:** Nomor sertifikat resmi BNSP hanya diterbitkan bagi kandidat dengan rekomendasi KOMPETEN yang telah disahkan di Sidang Pleno.
- **Anti-AI-Slop:** Hindari gradien dekoratif ungu/biru generik, shadow melayang, dan animasi berlebihan. Terapkan tabel HTML rapat dan palet slate/navy profesional.
- **Kemandirian CBT:** Jawaban CBT FR.IA.05 tidak boleh hilang saat refresh atau koneksi terputus sesaat.

## Review Focus

1. **Redirect Loop Prevention:** Memastikan tidak ada siklus redirect berantai antara `asesi.assessment.ruang-uji` dan `asesi.assessment.apl`.
2. **State Transition Deadlock:** Memastikan setiap status asesi (Draft $\rightarrow$ Submitted $\rightarrow$ Approved $\rightarrow$ Assessment $\rightarrow$ Review $\rightarrow$ Completed) memiliki rute jalan keluar yang pasti tanpa halaman buntu.
3. **Cross-Role Authorization Gracefulness:** Memastikan pengguna yang membuka URL milik role lain dialihkan ke dashboard sendiri dengan pesan informatif, bukan error 403.
4. **CBT State Resiliency:** Memastikan jawaban pilihan ganda tetap tersimpan di browser jika koneksi terputus sesaat sebelum tombol kirim ditekan.
5. **Decoupled Schedule Closure:** Memastikan finalisasi nilai 1 asesi di workspace tidak menutup jadwal sebelum seluruh peserta lain selesai dinilai.

---

## Task Breakdown

### Task 1: Unify Database Models & Certification Fields on ProIndo Core

**Files:**
- Create: `database/migrations/2026_11_02_000000_unify_proindo_core_certification_fields.php`
- Modify: `app/Models/Assessment.php`, `app/Models/Schedule.php`, `app/Models/PlenoMeeting.php`
- Test: `tests/Feature/ProindoModelAndSchemaTest.php`

**Requirements:**
- Tambahkan kolom sertifikat ke `t_assessments`: `no_sertifikat` (string nullable), `tgl_terbit_sertifikat` (date nullable), `masa_berlaku_sertifikat` (date nullable).
- Tambahkan relasi `plenoMeeting()` pada model `Schedule` (`t_schedules`) dan `pleno_meetings.schedule_id` mendukung relasi ke `t_schedules`.
- Buat method helper pada `Assessment`: `isCompetent()`, `isApprovedByAdmin()`, `isCompleted()`.

**Step-by-step instructions:**
- [x] 1. Buat test di `tests/Feature/ProindoModelAndSchemaTest.php` untuk memverifikasi atribut sertifikat di `t_assessments` dan relasi pleno di `Schedule`.
- [x] 2. Jalankan `php artisan test --filter=ProindoModelAndSchemaTest` dan pastikan gagal karena kolom belum ada.
- [x] 3. Buat file migrasi `2026_11_02_000000_unify_proindo_core_certification_fields.php` yang menambahkan kolom sertifikat pada `t_assessments` dan foreign key relasi.
- [x] 4. Jalankan `php artisan migrate`.
- [x] 5. Perbarui model `app/Models/Assessment.php`, `app/Models/Schedule.php`, dan `app/Models/PlenoMeeting.php` dengan fillable, casts, dan relasi yang sesuai.
- [x] 6. Jalankan kembali `php artisan test --filter=ProindoModelAndSchemaTest` dan pastikan hijau.
- [x] 7. Commit perubahan dengan pesan `feat: unify certification and pleno fields onto proindo core models`.

---

### Task 2: Cross-Role Route Guarding & Intended URL Redirection

**Files:**
- Modify: `app/Http/Middleware/RoleMiddleware.php`
- Modify: `app/Http/Middleware/EnsureUserRole.php`
- Modify: `app/Http/Controllers/Auth/LoginController.php`
- Modify: `app/Models/User.php`
- Test: `tests/Feature/RoleAccessAndRedirectionTest.php`

**Requirements:**
- Jika user terautentikasi mengakses rute yang bukan perannya, jangan lemparkan `abort(403)`. Arahkan user ke dashboard miliknya (`dashboardRoute()`) dengan flash warning: "Anda dialihkan ke dashboard Anda karena tidak memiliki akses ke halaman tersebut."
- Jika user belum login mengakses URL tertentu, setelah login berhasil, sistem harus mengembalikan user ke URL tujuan semula (*intended URL*), bukan selalu ke home.

**Step-by-step instructions:**
- [x] 1. Tulis test di `tests/Feature/RoleAccessAndRedirectionTest.php` untuk memvalidasi: asesi akses `/admin/schedules` dialihkan ke `/asesi/dashboard` dengan flash warning; asesor akses `/admin/users` dialihkan ke `/asesor/dashboard`; login mengembalikan ke intended URL.
- [x] 2. Jalankan test untuk memverifikasi kegagalan (masih melempar 403).
- [x] 3. Perbarui `RoleMiddleware.php` dan `EnsureUserRole.php`: tangkap ketidaksesuaian role, ambil `$user->dashboardRoute()`, dan lakukan redirect dengan session flash info.
- [x] 4. Perbarui `LoginController.php` agar menggunakan `redirect()->intended(route($user->dashboardRoute()))`.
- [x] 5. Jalankan `php artisan test --filter=RoleAccessAndRedirectionTest` dan pastikan seluruh test lolos.
- [x] 6. Commit dengan pesan `feat: graceful cross-role redirection and intended url support`.

---

### Task 3: Audit & Eliminasi Redirect Berantai dan Halaman Buntu

**Files:**
- Modify: `app/Http/Controllers/Asesi/RuangUjiController.php`
- Modify: `app/Http/Controllers/Asesi/AplController.php`
- Modify: `app/Http/Controllers/Asesi/JadwalController.php`
- Modify: `app/Http/Controllers/Asesor/AplReviewController.php`
- Test: `tests/Feature/RedirectAuditAndLoopEliminationTest.php`

**Requirements:**
- Audit 111 pemanggilan redirect di controller asesi dan asesor.
- Ganti redirect memantul (*bouncing redirects*) dengan direct routing deterministik:
  - Pada `RuangUjiController@index`: jika APL belum disetujui, langsung redirect 1 tingkat ke `asesi.assessment.apl` dengan pesan informatif, tanpa rantai perantara.
  - Pada `AplController@store`: setelah asesi mengirim APL-01 & APL-02, redirect langsung ke `asesi.dashboard` dengan flash success bahwa berkas sedang diverifikasi.
  - Pada `RuangUjiController@submitIa05`: setelah koreksi CBT, jangan lempar kembali ke form CBT, arahkan langsung ke overview ruang uji dengan skor yang tampil.
- Pastikan tidak ada satupun route yang menyebabkan redirect loop (HTTP 302 bolak-balik).

**Step-by-step instructions:**
- [x] 1. Buat test `tests/Feature/RedirectAuditAndLoopEliminationTest.php` yang menguji status asesi pada tiap fase dan memverifikasi respon HTTP bukan rantai redirect berulang (maksimal 1 jump).
- [x] 2. Jalankan test dan catat controller mana yang memicu looping atau redirect ganda.
- [x] 3. Refactor method di `RuangUjiController.php`, `AplController.php`, dan `JadwalController.php` untuk memastikan alur transisi status langsung ke route tujuan tanpa pantulan.
- [x] 4. Jalankan `php artisan test --filter=RedirectAuditAndLoopEliminationTest` dan pastikan lulus.
- [x] 5. Commit dengan pesan `refactor: eliminate chain redirects and ensure deterministic routing`.

---

### Task 4: Linear Asesi Dashboard dengan Satu Penunjuk "Langkah Berikutnya"

**Files:**
- Modify: `app/Http/Controllers/Asesi/DashboardController.php`
- Modify: `app/Http/Controllers/Asesi/JadwalController.php`
- Modify: `resources/views/asesi/dashboard.blade.php`
- Test: `tests/Feature/AsesiDashboardNextStepTest.php`

**Requirements:**
- Dashboard Asesi menyajikan satu kartu fokus *"Langkah Berikutnya"* yang mencolok dan berada di posisi paling atas:
  - Status 1: Belum punya jadwal $\rightarrow$ Input Kode Jadwal 4 digit.
  - Status 2: Belum isi APL $\rightarrow$ Tombol "Isi Formulir APL-01 & APL-02".
  - Status 3: Menunggu Verifikasi Admin $\rightarrow$ Notifikasi "Berkas Anda sedang diverifikasi admin LSP".
  - Status 4: Siap Asesmen $\rightarrow$ Tombol "Masuk Ruang Uji Kompetensi".
  - Status 5: Selesai Asesmen $\rightarrow$ Tampilan ringkasan hasil uji dan status penetapan sidang pleno.
- Maksimal 1 tombol aksi utama (Primary Action) per layar.
- Mengikuti palet resmi: LSP Navy, Dark Navy, LSP Red, dan background bersih tanpa gradien norak.

**Step-by-step instructions:**
- [x] 1. Buat test `tests/Feature/AsesiDashboardNextStepTest.php` yang memeriksa konten kartu "Langkah Berikutnya" untuk tiap status asesmen asesi.
- [x] 2. Jalankan test dan pastikan gagal karena kartu terpadu belum tersedia.
- [x] 3. Di `Asesi\DashboardController.php` dan `Asesi\JadwalController.php`, hitung status aktif dan buat struktur data `$nextStep` (title, description, action_label, action_url, badge).
- [x] 4. Refactor `resources/views/asesi/dashboard.blade.php` dengan tata letak rapat, fungsional, dan kartu "Langkah Berikutnya" yang dominan.
- [x] 5. Jalankan `php artisan test --filter=AsesiDashboardNextStepTest` dan pastikan hijau.
- [x] 6. Commit dengan pesan `feat: add focused single next-step indicator to asesi dashboard`.

---

### Task 5: Fidelitas Tata Letak Formulir APL-01 & APL-02 Resmi BNSP

**Files:**
- Modify: `resources/views/asesi/apl.blade.php`
- Modify: `app/Http/Controllers/Asesi/AplController.php`
- Test: `tests/Feature/AsesiAplFormParityTest.php`

**Requirements:**
- Bagian atas formulir APL-01: Header resmi MUK BNSP, nomor dokumen `FR.APL.01`, tabel data pribadi pemohon (Nama, NIK, Tempat/Tgl Lahir, Jenis Kelamin, Alamat, Lembaga/Perusahaan, Pendidikan) dalam format tabular resmi.
- Bagian APL-02: Header skema dan unit kompetensi, tabel evaluasi mandiri KUK dengan opsi K (Kompeten) dan BK (Belum Kompeten), tombol cepat "Pilih Semua Kompeten", serta bukti portofolio yang relevan.
- Tanda tangan digital otomatis diambil dari profil (HTML5 canvas 1-kali).
- Satu tombol aksi utama di bagian bawah: "Simpan & Kirim Permohonan Asesmen (APL-01/02)".
- Nilai input form dipertahankan jika validasi gagal (`old()`).

**Step-by-step instructions:**
- [x] 1. Tulis test di `tests/Feature/AsesiAplFormParityTest.php` yang menguji render view APL-01/02 dengan data pemohon, unit kompetensi skema, dan submission form.
- [x] 2. Jalankan test untuk memverifikasi kebutuhan yang belum terpenuhi.
- [x] 3. Rancang ulang `resources/views/asesi/apl.blade.php` dengan layout tabel resmi BNSP yang compact, border tegas `border-slate-300`, dan styling Tailwind 4 bersih.
- [x] 4. Pastikan tombol cepat "Pilih Semua Kompeten" berfungsi dengan Vanilla JS tanpa reload.
- [x] 5. Jalankan `php artisan test --filter=AsesiAplFormParityTest` dan pastikan hijau.
- [x] 6. Commit dengan pesan `feat: align asesi apl-01 and apl-02 form layout with official bnsp standards`.

---

### Task 6: Resilient CBT (FR.IA.05) dengan Client-Side Auto-Save & Anti-Hilang

**Files:**
- Modify: `resources/views/asesi/ruang-uji/ia05.blade.php`
- Modify: `app/Http/Controllers/Asesi/RuangUjiController.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/CbtAnswerPersistenceTest.php`

**Requirements:**
- Setiap asesi memilih opsi radio (A, B, C, D), jawaban disimpan seketika di `localStorage` per `assessment_id`.
- Sediakan route endpoint AJAX `POST /asesi/assessment/{assessment}/ia05/save-draft` untuk menyimpan draft jawaban secara berkala ke database (`t_assessment_ia_responses.data_json`).
- Jika asesi me-refresh halaman atau koneksi terputus sesaat, jawaban yang tersimpan otomatis dimuat ulang tanpa hilang.
- Saat tombol "Kirim Jawaban CBT ✓" ditekan dan berhasil dinilai, bersihkan draft di localStorage.

**Step-by-step instructions:**
- [x] 1. Tulis test di `tests/Feature/CbtAnswerPersistenceTest.php` untuk memvalidasi endpoint penyimpanan draft CBT dan restorasi jawaban saat ujian dibuka kembali.
- [x] 2. Jalankan test untuk memverifikasi endpoint draft belum ada.
- [x] 3. Tambahkan method `saveIa05Draft` pada `RuangUjiController.php` dan daftarkan route-nya di `routes/web.php`.
- [x] 4. Di file view `resources/views/asesi/ruang-uji/ia05.blade.php`, pasang script auto-save ke `localStorage` dan debounce sync ke backend.
- [x] 5. Pasang inisialisasi restore jawaban dari draft data backend/localStorage saat halaman di-load.
- [x] 6. Jalankan `php artisan test --filter=CbtAnswerPersistenceTest` dan pastikan berhasil.
- [x] 7. Commit dengan pesan `feat: add client-side and draft persistence for cbt ia05 questions`.

---

### Task 7: Workspace Terpadu Asesor (Single Workspace per Asesi)

**Files:**
- Create: `app/Http/Controllers/Asesor/WorkspaceController.php`
- Create: `resources/views/asesor/workspace/show.blade.php`
- Modify: `routes/web.php`
- Modify: `resources/views/asesor/jadwal/asesi.blade.php`
- Test: `tests/Feature/AsesorUnifiedWorkspaceTest.php`

**Requirements:**
- Endpoint tunggal: `/asesor/assessments/{assessment}/workspace`.
- Asesor mengevaluasi asesi dalam 1 layar dengan navigasi tab tanpa reload halaman penuh:
  - **Tab 1 - Berkas APL:** Meninjau isian APL-01 dan bukti APL-02, tombol "Verifikasi Kesiapan Asesmen".
  - **Tab 2 - AK-01:** Menampilkan persetujuan dan tanda tangan digital asesi & asesor.
  - **Tab 3 - Teori CBT:** Menampilkan skor dan butir jawaban benar/salah dari FR.IA.05.
  - **Tab 4 - Observasi Praktik:** Form input penilaian observasi demonstrasi (IA.01/IA.02) dan pertanyaan lisan (IA.03).
  - **Tab 5 - Rekomendasi Asesmen:** Penetapan rekomendasi individu K (Kompeten) atau BK (Belum Kompeten) pada lembar FR.AK.02 beserta catatan tindak lanjut.
- Tombol aksi primer per tab: "Simpan Penilaian", dengan feedback visual instan.
- Finalisasi individu di Tab 5 mengunci nilai asesi tanpa menutup jadwal secara keseluruhan.

**Step-by-step instructions:**
- [ ] 1. Tulis test di `tests/Feature/AsesorUnifiedWorkspaceTest.php` untuk memvalidasi akses workspace per asesi, navigasi tab, dan penyimpanan rekomendasi AK-02.
- [ ] 2. Jalankan test dan pastikan gagal (workspace controller belum ada).
- [ ] 3. Buat `app/Http/Controllers/Asesor/WorkspaceController.php` yang memuat data asesi, profil, bukti APL, jawaban CBT, instrumen praktik, dan rekaman AK-02.
- [ ] 4. Daftarkan route `/asesor/assessments/{assessment}/workspace` dan route simpan penilaian.
- [ ] 5. Buat view `resources/views/asesor/workspace/show.blade.php` dengan desain tab modern, ringkas, dan tabel rapat berstandar BNSP.
- [ ] 6. Hubungkan tombol "Uji Asesi" pada daftar peserta jadwal (`resources/views/asesor/jadwal/asesi.blade.php`) ke workspace baru ini.
- [ ] 7. Jalankan `php artisan test --filter=AsesorUnifiedWorkspaceTest` dan pastikan hijau.
- [ ] 8. Commit dengan pesan `feat: implement unified single-workspace for assessor candidate evaluation`.

---

### Task 8: Admin 1-Click Batch Approval & Schedule-Level Pleno Integration

**Files:**
- Modify: `app/Http/Controllers/Admin/JadwalController.php`
- Modify: `app/Http/Controllers/Admin/PlenoController.php`
- Modify: `resources/views/admin/jadwal/show.blade.php`
- Modify: `resources/views/admin/pleno/index.blade.php`
- Modify: `resources/views/admin/pleno/show.blade.php`
- Test: `tests/Feature/AdminBatchReviewAndPlenoTest.php`

**Requirements:**
- Di halaman detail jadwal admin (`admin/jadwal/{jadwal}`): Tampilkan tombol menonjol *"Setujui Semua Berkas Jadwal Ini"* (1-Click Batch Approval) yang langsung memvalidasi seluruh berkas pendaftar berstatus Draft/Submitted menjadi Approved dalam 1 transaksi DB.
- Hubungkan modul Pleno (`/admin/pleno`) langsung ke `Schedule` (`t_schedules`) dan `Assessment` (`t_assessments`):
  - Tampilkan tabel rekapitulasi rekomendasi K/BK dari seluruh asesor.
  - Form input Berita Acara Pleno: nomor BA, tanggal pleno, tempat, nama ketua komite teknis.
  - Tombol *"Sahkan Hasil Rapat Pleno & Terbitkan Sertifikat BNSP"*:
    - Menghasilkan nomor sertifikat resmi BNSP **hanya untuk peserta Kompeten (K)**.
    - Mengunci status pleno dan jadwal menjadi `FINISHED`.
  - Tombol unduh Berita Acara Pleno dan Daftar Hadir Pleno.

**Step-by-step instructions:**
- [ ] 1. Tulis test di `tests/Feature/AdminBatchReviewAndPlenoTest.php` untuk memvalidasi: batch approval berkas pendaftar oleh admin, sidang pleno mengesahkan keputusan asesor, nomor sertifikat BNSP terbit hanya untuk yang Kompeten, dan jadwal terkunci.
- [ ] 2. Jalankan test untuk memverifikasi failure mode.
- [ ] 3. Perbarui `Admin\JadwalController.php` untuk mengoptimalkan batch approval 1-klik tanpa friksi.
- [ ] 4. Refactor `Admin\PlenoController.php` agar bekerja secara langsung dengan model `Schedule` dan `Assessment` pada `t_schedules` dan `t_assessments`.
- [ ] 5. Perbarui tampilan `admin/jadwal/show.blade.php` dan `admin/pleno/show.blade.php` dengan tabel ringkas dan tombol aksi eksplisit.
- [ ] 6. Jalankan `php artisan test --filter=AdminBatchReviewAndPlenoTest` dan pastikan seluruh assertions berhasil.
- [ ] 7. Commit dengan pesan `feat: integrate 1-click batch approval and core model pleno ratification`.

---

### Task 9: Rasionalisasi Tombol Aksi, Perapian Sidebar, dan Error Pages

**Files:**
- Modify: `resources/views/layouts/app.blade.php`
- Create/Modify: `resources/views/errors/403.blade.php`
- Create/Modify: `resources/views/errors/404.blade.php`
- Create/Modify: `resources/views/errors/500.blade.php`
- Create/Modify: `resources/views/errors/419.blade.php`
- Modify: Tabel-tabel admin dan asesor untuk membatasi 1-2 aksi sekunder
- Test: `tests/Feature/SmokeUiNavigationTest.php`

**Requirements:**
- Standarisasi tombol: 1 tombol aksi primer per layar (`bg-slate-900` atau `bg-blue-900`), maksimal 1-2 tombol sekunder per baris tabel. Hapus tombol duplikat.
- Label tombol eksplisit (Kata Kerja + Objek, contoh: "Detail Jadwal", "Cetak Berita Acara").
- Halaman error khusus (403, 404, 419, 500) yang rapi dengan branding LSP PrO Indo dan tombol kembali yang jelas ("Kembali ke Dashboard").
- Empty states informatif di setiap tabel jika data belum tersedia (contoh: "Belum ada jadwal asesmen aktif. Klik '+ Tambah Jadwal Baru' untuk memulai.").

**Step-by-step instructions:**
- [ ] 1. Tulis test di `tests/Feature/SmokeUiNavigationTest.php` untuk melakukan smoke test GET ke semua rute utama Admin, Asesor, dan Asesi (memastikan return HTTP 200 tanpa error 500).
- [ ] 2. Rancang template halaman error di `resources/views/errors/403.blade.php`, `404.blade.php`, `419.blade.php`, dan `500.blade.php`.
- [ ] 3. Audit dan rapikan tombol di `admin/schedules/index.blade.php`, `admin/users/index.blade.php`, `asesor/jadwal/index.blade.php`, dan `asesi/jadwal/index.blade.php`.
- [ ] 4. Jalankan `php artisan test --filter=SmokeUiNavigationTest` dan pastikan seluruh route lolos.
- [ ] 5. Commit dengan pesan `style: rationalize action buttons and implement branded error states`.

---

### Task 10: End-to-End Workflow Verification & Regression Testing

**Files:**
- Create: `tests/Feature/EndToEndAuditWorkflowTest.php`
- Test: `php artisan test`

**Requirements:**
- Simulasi end-to-end lengkap dari awal pendaftaran sampai penerbitan sertifikat:
  1. Admin membuat jadwal baru dengan kode 4 digit.
  2. Asesi mendaftar akun dan memasukkan kode jadwal.
  3. Asesi mengisi APL-01 dan APL-02 secara mandiri.
  4. Admin melakukan 1-click batch approval terhadap berkas asesi.
  5. Asesi masuk ke Ruang Uji, menyetujui AK-01, dan menyelesaikan CBT IA.05.
  6. Asesor membuka Unified Workspace, memeriksa nilai CBT, menilai praktik, dan menetapkan rekomendasi K (Kompeten) di AK-02.
  7. Asesi mengisi umpan balik AK-03.
  8. Asesor menyelesaikan laporan asesmen jadwal AK-05.
  9. Admin membuka sidang pleno `/admin/pleno/{schedule}`, menetapkan nomor BA Pleno, mengesahkan hasil, dan nomor sertifikat BNSP terbit.
  10. Seluruh suite pengujian otomatis (`php artisan test`) harus lulus 100% tanpa kegagalan.

**Step-by-step instructions:**
- [ ] 1. Tulis integrasi test komprehensif di `tests/Feature/EndToEndAuditWorkflowTest.php` yang menjalankan skenario 10 langkah di atas.
- [ ] 2. Jalankan test: `php artisan test --filter=EndToEndAuditWorkflowTest`.
- [ ] 3. Perbaiki inkonsistensi data jika ada kegagalan.
- [ ] 4. Jalankan seluruh suite test aplikasi: `php artisan test`.
- [ ] 5. Pastikan seluruh 109+ pengujian hijau dan 0 failure.
- [ ] 6. Commit dengan pesan `test: add end-to-end certification audit workflow integration test`.
