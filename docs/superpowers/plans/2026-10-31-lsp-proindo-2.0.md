# LSP PrO Indo 2.0 Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Membangun LSP PrO Indo 2.0 yang menggabungkan fitur tata kelola LSP dengan arsitektur asesmen linear satu-arah dari `lsp-proindo-final`, rute root langsung ke login, UI otentik anti-slop, tanda tangan otomatis terpusat, dan generator 24 dokumen MUK BNSP serta ekspor Excel native.

**Architecture:** Mengadopsi domain data model teruji `m_*` dan `t_*` untuk alur asesmen, memasang seeder master 7 skema otomotif lengkap dengan CBT bank soal, mengintegrasikan 24 template print MUK standar BNSP, dan menyediakan generator file `.xlsx` menggunakan PhpSpreadsheet.

**Tech Stack:** Laravel 12, PHP 8.3+, Blade Engine, Tailwind CSS, Alpine.js, PhpSpreadsheet, MySQL / SQLite, PHPUnit.

**Spec:** `docs/superpowers/specs/2026-10-31-lsp-proindo-2.0-design.md`

## Global Constraints
- Root `/` harus langsung me-redirect ke `/login` (atau dashboard bila sudah terautentikasi); tidak boleh ada landing page publik.
- Seluruh desain UI mengadopsi standar visual otentik LSP PrO Indo (Navy `#1E2B6E`, Merah `#CE181E`, Canvas `#F8FAFC`, Plus Jakarta Sans, border 1px, anti-AI-slop).
- Tanda tangan digital hanya direkam 1 kali di profil dan diinjeksikan otomatis ke seluruh formulir asesmen.
- Dokumen MUK (Berita Acara, Daftar Hadir, dan 24 form MUK) harus terisi dinamis dari database tanpa placeholder kosong.
- Automated tests harus lulus 100% di setiap akhir task.

---

## Task 1: Migrasi Skema Data & Model Eloquent LSP PrO Indo (`m_*` & `t_*`)

**Files:**
- Create: `database/migrations/2026_11_01_000010_create_proindo_users_and_profiles_tables.php`
- Create: `database/migrations/2026_11_01_000020_create_proindo_schemes_and_masters_tables.php`
- Create: `database/migrations/2026_11_01_000030_create_proindo_schedules_tables.php`
- Create: `database/migrations/2026_11_01_000040_create_proindo_assessments_and_items_tables.php`
- Create: `database/migrations/2026_11_01_000050_create_proindo_appeals_table.php`
- Create: `app/Models/UserProfile.php`
- Create: `app/Models/Scheme.php`
- Create: `app/Models/Schedule.php`
- Create: `app/Models/Assessment.php`
- Create: `app/Models/AssessmentProof.php`
- Create: `app/Models/AssessmentApl02Item.php`
- Create: `app/Models/AssessmentAk07Item.php`
- Create: `app/Models/AssessmentIaResponse.php`
- Create: `app/Models/AssessmentAppeal.php`
- Create: `app/Models/QuestionBank.php`
- Create: `app/Models/Kuk.php`
- Create: `app/Models/UnitElement.php`
- Test: `tests/Feature/ProindoModelAndSchemaTest.php`

**Steps:**
- [ ] Buat file test `tests/Feature/ProindoModelAndSchemaTest.php` untuk memvalidasi pembuatan dan relasi model `m_*` dan `t_*`.
- [ ] Buat file-file migrasi untuk tabel `m_user_profiles`, `m_schemes`, `m_competency_units`, `m_unit_elements`, `m_kuks`, `m_question_banks`, `t_schedules`, `t_schedule_assessors`, `t_assessments`, `t_assessment_proofs`, `t_assessment_apl02_items`, `t_assessment_ak07_items`, `t_assessment_ia_responses`, dan `t_assessment_appeals`.
- [ ] Definisikan class Model Eloquent lengkap beserta relasi, casts, dan helper methods.
- [ ] Jalankan migrasi dan jalankan test untuk memastikan skema valid.
- [ ] Commit: `feat(db): establish proindo 2.0 schema and eloquent models`.

---

## Task 2: Seeder Master 7 Skema Otomotif & Bank Soal CBT

**Files:**
- Create: `database/seeders/ProindoSchemesAndMukSeeder.php`
- Modify: `database/seeders/DatabaseSeeder.php`
- Test: `tests/Feature/ProindoMasterDataSeederTest.php`

**Steps:**
- [ ] Buat test `tests/Feature/ProindoMasterDataSeederTest.php` untuk memastikan ke-7 skema otomotif terisi beserta unit, elemen, KUK, dan bank soal.
- [ ] Porting data master dari `lsp-proindo-final` ke `database/seeders/ProindoSchemesAndMukSeeder.php` mencakup:
  1. Engine Junior Technician
  2. Klaster Maintenance Gasoline Engine Injection
  3. Maintenence Electrical Vehicle
  4. Memperbaiki Panel Bodi Kendaraan
  5. Mekanik Junior
  6. Servis Sepeda Motor Listrik
  7. Klaster Servis Sepeda Motor Injeksi
- [ ] Daftarkan seeder di `DatabaseSeeder.php` dan jalankan seeding.
- [ ] Jalankan test untuk memverifikasi seluruh skema dan bank soal terisi.
- [ ] Commit: `feat(seed): seed 7 automotive schemes with units elements kuks and cbt question banks`.

---

## Task 3: Root Redirect ke `/login` & Desain UI Otentik ProIndo

**Files:**
- Modify: `routes/web.php`
- Create/Modify: `resources/views/layouts/app.blade.php`
- Create/Modify: `resources/views/auth/login.blade.php`
- Create/Modify: `resources/views/auth/register.blade.php`
- Create: `resources/views/components/signature-pad.blade.php`
- Test: `tests/Feature/ProindoAuthAndLayoutTest.php`

**Steps:**
- [ ] Buat test `tests/Feature/ProindoAuthAndLayoutTest.php` yang memverifikasi akses `/` redirect ke `/login` (tamu) dan dashboard (login), serta styling layout proindo.
- [ ] Update rute `/` di `routes/web.php` untuk langsung mengarahkan ke `/login` tanpa landing page.
- [ ] Pasang token warna LSP PrO Indo (Navy `#1E2B6E`, Red `#CE181E`, Canvas `#F8FAFC`, font *Plus Jakarta Sans*) pada layout utama.
- [ ] Perbarui view login dan register agar bersih, modern, dan anti-slop.
- [ ] Buat komponen Blade `signature-pad.blade.php` berbasis HTML5 canvas.
- [ ] Jalankan test dan `npm run build`.
- [ ] Commit: `feat(ui): eliminate public landing page and apply authentic proindo styling`.

---

## Task 4: Alur Asesi (Profil, Gabung Jadwal, 1-Halaman APL, Approval Gate, Ruang Uji)

**Files:**
- Create: `app/Http/Controllers/Asesi/ProfileController.php`
- Create: `app/Http/Controllers/Asesi/JadwalController.php`
- Create: `app/Http/Controllers/Asesi/AplController.php`
- Create: `app/Http/Controllers/Asesi/RuangUjiController.php`
- Create: `app/Http/Middleware/CheckAplApprovedGate.php`
- Create: `app/Services/AutoGradeService.php`
- Create: `resources/views/asesi/dashboard.blade.php`
- Create: `resources/views/asesi/profile.blade.php`
- Create: `resources/views/asesi/jadwal/index.blade.php`
- Create: `resources/views/asesi/apl.blade.php`
- Create: `resources/views/asesi/ruang-uji/hub.blade.php`
- Create: `resources/views/asesi/ruang-uji/ak01.blade.php`
- Create: `resources/views/asesi/ruang-uji/ia05.blade.php`
- Create: `resources/views/asesi/ruang-uji/ak03.blade.php`
- Create: `resources/views/asesi/ruang-uji/ak04-appeal.blade.php`
- Test: `tests/Feature/ProindoAsesiWorkflowTest.php`

**Steps:**
- [ ] Buat test `tests/Feature/ProindoAsesiWorkflowTest.php` menguji seluruh alur asesi: simpan profil + TTD canvas, join jadwal via kode, submit APL 1-halaman (Set Semua K/BK), gate pembatas, AK.01, CBT IA.05 autograding, dan umpan balik.
- [ ] Implementasikan controller, middleware gate, dan service autograding.
- [ ] Buat tampilan-tampilan asesi yang responsif dan elegan.
- [ ] Pastikan tanda tangan asesi otomatis dipakai di seluruh tahapan.
- [ ] Jalankan test hingga green.
- [ ] Commit: `feat(asesi): implement streamlined 1-way asesi certification flow and cbt engine`.

---

## Task 5: Alur Asesor (Review APL, AK.07 Batch, Penilaian Observasi Praktik, Finalisasi)

**Files:**
- Create: `app/Http/Controllers/Asesor/ProfileController.php`
- Create: `app/Http/Controllers/Asesor/JadwalController.php`
- Create: `app/Http/Controllers/Asesor/AplReviewController.php`
- Create: `app/Http/Controllers/Asesor/Ak07Controller.php`
- Create: `app/Http/Controllers/Asesor/InstrumentController.php`
- Create: `app/Http/Controllers/Asesor/FinalizationController.php`
- Create: `resources/views/asesor/jadwal/index.blade.php`
- Create: `resources/views/asesor/jadwal/asesi.blade.php`
- Create: `resources/views/asesor/review-apl.blade.php`
- Create: `resources/views/asesor/ak07.blade.php`
- Create: `resources/views/asesor/ruang-uji/schedule-hub.blade.php`
- Create: `resources/views/asesor/ruang-uji/hub.blade.php`
- Create: `resources/views/asesor/ruang-uji/ia01.blade.php`
- Create: `resources/views/asesor/ruang-uji/ia01b.blade.php`
- Create: `resources/views/asesor/ruang-uji/ia02.blade.php`
- Create: `resources/views/asesor/ruang-uji/ia03.blade.php`
- Create: `resources/views/asesor/ruang-uji/ia05.blade.php`
- Create: `resources/views/asesor/ak02.blade.php`
- Create: `resources/views/asesor/reports-ak05-ak06.blade.php`
- Test: `tests/Feature/ProindoAsesorWorkflowTest.php`

**Steps:**
- [ ] Buat test `tests/Feature/ProindoAsesorWorkflowTest.php` memvalidasi asesor login, verifikasi APL (setujui / minta revisi), AK.07 penyesuaian wajar (batch & single), pengisian lembar observasi IA.01–03, cek skor CBT IA.05, dan penandatanganan hasil akhir AK.02.
- [ ] Implementasikan controller dan view asesor.
- [ ] Hubungkan tanda tangan asesor dari profil ke seluruh lembar instrumen secara otomatis.
- [ ] Jalankan test hingga green.
- [ ] Commit: `feat(asesor): implement assessor cockpit, apl review gate, and instrument assessment`.

---

## Task 6: Alur Admin & Fitur Proxy / Switch Role

**Files:**
- Create: `app/Http/Controllers/Admin/DashboardController.php`
- Create: `app/Http/Controllers/Admin/JadwalController.php`
- Create: `app/Http/Controllers/Admin/ProxyController.php`
- Create: `resources/views/admin/dashboard.blade.php`
- Create: `resources/views/admin/jadwal/index.blade.php`
- Create: `resources/views/admin/jadwal/create.blade.php`
- Create: `resources/views/admin/jadwal/edit.blade.php`
- Create: `resources/views/admin/jadwal/show.blade.php`
- Create: `resources/views/admin/proxy/index.blade.php`
- Test: `tests/Feature/ProindoAdminAndProxyTest.php`

**Steps:**
- [ ] Buat test `tests/Feature/ProindoAdminAndProxyTest.php` menguji CRUD jadwal (TUK teks, multi-asesor, instrumen), pembuatan kode unik, serta impersonasi admin ke asesi/asesor dan switch back.
- [ ] Implementasikan controller jadwal dan proxy impersonator.
- [ ] Buat banner melayang (*floating impersonation bar*) untuk kembali ke akun admin.
- [ ] Jalankan test hingga green.
- [ ] Commit: `feat(admin): implement schedule management and instant role switch proxy`.

---

## Task 7: Pusat Dokumen MUK & Native Excel Exporter

**Files:**
- Create: `resources/views/layouts/print-muk.blade.php`
- Create: `resources/views/admin/dokumen-muk/index.blade.php`
- Create: `resources/views/admin/dokumen-muk/export-ba.blade.php`
- Create: `resources/views/admin/dokumen-muk/export-hadir-asesi.blade.php`
- Create: `resources/views/admin/dokumen-muk/export-hadir-asesor.blade.php`
- Create: 24 file template MUK di `resources/views/admin/dokumen-muk/`
- Create: `app/Services/MukExportService.php`
- Create: `app/Http/Controllers/Admin/DokumenMukController.php`
- Test: `tests/Feature/ProindoMukExportTest.php`

**Steps:**
- [ ] Buat test `tests/Feature/ProindoMukExportTest.php` menguji render Berita Acara, Daftar Hadir Asesi/Asesor, dokumen individual MUK, dan download file `.xlsx` Excel biodata.
- [ ] Salin dan sempurnakan 24 template Blade MUK dari `lsp-proindo-final` ke `resources/views/admin/dokumen-muk/`.
- [ ] Kembangkan `MukExportService` untuk mengikat data relasional ke template cetak dan menghasilkan file `.xlsx` native menggunakan PhpSpreadsheet.
- [ ] Hubungkan rute unduh Berita Acara, Daftar Hadir, dan Excel Biodata di dashboard Dokumen MUK.
- [ ] Jalankan test hingga green.
- [ ] Commit: `feat(muk): integrate 24 bnsp muk print templates and native excel export`.

---

## Task 8: Integrasi Modul Ekstensi, Verifikasi End-to-End, & Final Build

**Files:**
- Modify: `resources/views/layouts/app.blade.php`
- Test: `tests/Feature/ProindoEndToEndSuiteTest.php`

**Steps:**
- [ ] Buat `ProindoEndToEndSuiteTest.php` yang menjalankan simulasi lengkap 1 putaran sertifikasi: Buat Jadwal $\rightarrow$ Asesi Daftar $\rightarrow$ Isi APL $\rightarrow$ Asesor Review $\rightarrow$ Uji Teori & Praktik $\rightarrow$ Asesor Selesaikan $\rightarrow$ Cetak BA & Unduh Excel.
- [ ] Pastikan modul pelengkap (Keuangan, Surat, Dokumen Mutu, Audit Internal) tetap terakses dengan baik melalui menu navigasi admin.
- [ ] Jalankan `npm run build` dan pastikan asset frontend terkompilasi bersih.
- [ ] Jalankan `php artisan test` (harus 100% lulus).
- [ ] Bersihkan dan cek `storage/logs/laravel.log`.
- [ ] Commit: `feat(system): finalize lsp proindo 2.0 full integration and end-to-end verification`.
