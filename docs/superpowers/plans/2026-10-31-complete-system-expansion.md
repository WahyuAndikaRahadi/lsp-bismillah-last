# Full Parity System Rebuild & Modern UI Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Rebuild and expand the LSP certification web application to 100% functional and structural parity with the real live demo system (`https://demo.aplikasilsp.web.id`), delivering complete Asesor and Admin control portals with modern, anti-AI-slop design engineering (Emil Kowalski principles, Impeccable operate mode, web interface guidelines).

**Architecture:** 
- Modular Laravel 13 architecture with dedicated Admin and Asesor domain controllers, service layers, and shared Blade UI components.
- Anti-AI-slop styling using Tailwind CSS v4: restrained palette (Slate/Zinc neutral with bold Emerald/Cobalt accent, hairline borders, crisp typography, no bloated purple gradients or heavy drop-shadows).
- Emil Kowalski micro-interactions: zero animations for high-frequency actions, responsive `:active` button scales, `ease-out` transitions for overlays and drawers, instant URL-state-preserving data tables.

**Tech Stack:** Laravel 13, Tailwind CSS v4, Alpine.js, DomPDF, MySQL.

**Spec Reference:** Live REA-crawled demo system (`/tmp/admin_pages/`, `/tmp/asesor_pages/`, `docs/observasi-aplikasilsp.md`).

## Global Constraints
- Address user strictly as a peer web developer (no formal honorifics like "Bapak/Ibu").
- Strict adherence to BNSP certification workflows (FR-APL-01, FR-APL-02, FR-AK-01, FR-AK-02, FR-AK-03, FR-AK-05, Pleno, Sisfo BNSP 16 Kolom).
- Maintain all 53 existing passing tests while adding new test suites for all added modules.

## Review Focus
1. Asesor workflow completeness (Jadwal Uji, Detail Peserta, Verifikasi TUK, Peninjauan Instrumen, MKVA, Profil Asesor, Notifikasi).
2. Admin 24-domain sidebar architecture with clean collapsible grouping and instant badge counters.
3. Design engineering quality: zero generic AI tropes, high information density, clear typography hierarchy, ergonomic scanability.
4. Data integrity across all newly created tables and relationships.
5. All automated tests pass before committing each milestone.

---

### Task 1: Database Migration for Extended Modules
**Files:**
- Create: `database/migrations/2026_10_31_110000_create_extended_lsp_system_tables.php`
- Modify: `app/Models/User.php`
- Create: `app/Models/AuditInternal.php`, `app/Models/MutuDocument.php`, `app/Models/Letter.php`, `app/Models/FinancialTransaction.php`, `app/Models/FinancialAccount.php`, `app/Models/BankAccount.php`, `app/Models/SystemMessage.php`, `app/Models/TukVerification.php`, `app/Models/InstrumentReview.php`, `app/Models/MkvaAssignment.php`
- Test: `tests/Feature/ExtendedSchemaTest.php`

- [ ] Write failing test `tests/Feature/ExtendedSchemaTest.php` asserting tables exist and relations work.
- [ ] Create migration for the extended domains:
  - `audit_internals`
  - `mutu_documents`
  - `letters` (persuratan & generator)
  - `financial_transactions` & `financial_accounts`
  - `bank_accounts`
  - `system_messages` (sent SMS & inbox)
  - `tuk_verifications` (checklist sarpras TUK)
  - `instrument_reviews` (validasi bank soal / MAPA)
  - `mkva_assignments` (evaluasi perangkat asesmen BNSP)
- [ ] Run migration and verify test passes.
- [ ] Commit with message: `feat: add extended database schema for full parity with demo system`.

---

### Task 2: Complete Asesor Experience & Workflows
**Files:**
- Create: `app/Http/Controllers/Asesor/ScheduleController.php`
- Create: `app/Http/Controllers/Asesor/TukVerificationController.php`
- Create: `app/Http/Controllers/Asesor/InstrumentReviewController.php`
- Create: `app/Http/Controllers/Asesor/MkvaController.php`
- Create: `app/Http/Controllers/Asesor/NotificationController.php`
- Create: `app/Http/Controllers/Asesor/ProfileController.php`
- Modify: `app/Http/Controllers/Asesor/DashboardController.php`
- Modify: `routes/web.php`
- Create Views:
  - `resources/views/asesor/dashboard.blade.php` (rebuilt with task feed, MET license expiry alert, portfolio stats)
  - `resources/views/asesor/schedules/index.blade.php` (active & archived schedules)
  - `resources/views/asesor/schedules/show.blade.php` (participant assessment cockpit: APL-01 VATM, APL-02, AK-01, AK-02, AK-03, AK-05)
  - `resources/views/asesor/tuk_verifications/index.blade.php` & `show.blade.php` (TUK facility inspection checklist)
  - `resources/views/asesor/instrument_reviews/index.blade.php` & `show.blade.php` (MAPA / instrument validation)
  - `resources/views/asesor/mkva/index.blade.php` & `show.blade.php` (MKVA review matrix)
  - `resources/views/asesor/notifications/index.blade.php` (official alerts & assignment letters)
  - `resources/views/asesor/profile/index.blade.php` (full profile, license MET, scheme scopes, portfolio files)
- Test: `tests/Feature/AsesorCompletePortalTest.php`

- [ ] Write failing test for Asesor complete portal navigation and interactions.
- [ ] Implement controllers and blade views following Emil Kowalski's clean design engineering.
- [ ] Run tests and verify all pass.
- [ ] Commit with message: `feat: implement complete asesor portal with schedules, tuk verification, instrument review, mkva, and profile`.

---

### Task 3: Admin Sidebar & Extended Navigation Grouping (Anti-AI-Slop Architecture)
**Files:**
- Modify: `resources/views/layouts/admin.blade.php` (redesign complete collapsible sidebar with 20+ grouped domains)
- Create: `resources/views/components/admin/nav-group.blade.php`
- Create: `resources/views/components/admin/nav-item.blade.php`
- Modify: `resources/css/app.css` (clean hairline borders, restrained palette, tactile buttons)
- Test: `tests/Feature/AdminNavigationTest.php`

- [ ] Write test verifying all Admin sidebar navigation links and routes are accessible.
- [ ] Redesign Admin layout:
  - Ergonomic sidebar with collapsible categories: Beranda, Layanan Pengaduan & Banding, Laporan Sertifikasi, Manajemen Mutu, Standar & Skema, Keuangan & Biaya, Event & Jadwal, TUK, Asesi (10 filter kategori), Asesor, Komite Teknis & Pleno, Persuratan, Surveillance, Log Pesan, Pengaturan Sistem.
  - Active state indicator, subtle badge count, quick keyboard search shortcut (`/` or `Ctrl+K`).
- [ ] Verify test passes.
- [ ] Commit with message: `feat: rebuild admin navigation architecture matching demo system with modern restrained design`.

---

### Task 4: Admin Extended Feature Modules & Dashboards
**Files:**
- Create: `app/Http/Controllers/Admin/ReportController.php` (General, Monthly, Annual, Asesor Performance, TUK Performance, Certificates)
- Create: `app/Http/Controllers/Admin/AppealController.php` (Layanan Banding Asesmen)
- Create: `app/Http/Controllers/Admin/QualityController.php` (Dokumen Mutu & Audit Internal)
- Create: `app/Http/Controllers/Admin/FinanceController.php` (Kode Akun, Biaya Uji, Rekening, Transaksi, Laba Rugi, Neraca, Arus Kas, Validasi Pembayaran)
- Create: `app/Http/Controllers/Admin/LetterController.php` (Generator Surat, Arsip SK/ST/Undangan, Blast Pesan)
- Create: `app/Http/Controllers/Admin/MessageController.php` (Log SMS / WA Sent & Inbox)
- Create: `app/Http/Controllers/Admin/AsesiManagementController.php` (Pencarian Asesi, Belum Terjadwal, Terjadwal, Kompeten, Belum Kompeten, Diblokir, Impor)
- Create: `app/Http/Controllers/Admin/SettingsController.php` (Pengguna, Hak Akses, Syarat Asesi, Portofolio, SMTP, WA Gateway, Akun BNSP)
- Create corresponding views in `resources/views/admin/...`
- Test: `tests/Feature/AdminExtendedModulesTest.php`

- [ ] Write comprehensive feature tests for all added admin modules.
- [ ] Implement controllers, forms, filterable data tables, and actions.
- [ ] Run tests and verify all pass.
- [ ] Commit with message: `feat: implement all extended admin modules for complete system parity`.

---

### Task 5: Design Polish & Verification Across All Surfaces
**Files:**
- Audit all views against `emil-design-eng` table requirements (| Before | After | Why |).
- Audit views against `web-design-guidelines` (contrast, labels, responsive layout).
- Verify frontend asset compilation (`npm run build`).
- Run full test suite (`php artisan test`).
- Test interactive flows in browser / HTTP tests.
- Commit with message: `refactor: polish UI interactions, remove all AI slop, and verify end-to-end parity`.
