# SPESIFIKASI DESAIN SISTEM: LSP PrO INDO 2.0
**Tanggal Dokumen:** 31 Oktober 2026  
**Status:** Approved by User (Pendekatan 1)  
**Tujuan Dokumen:** Menetapkan arsitektur, skema data, alur linear 1-arah, generator dokumen MUK BNSP, dan paket ekspor Excel untuk pengembangan LSP PrO Indo 2.0.

---

## 1. Intent & The Why
Menggabungkan keunggulan arsitektur sistem operasional LSP dengan kepraktisan alur sertifikasi satu arah (*linear zero-bloat*) dari `lsp-proindo-final`. Sistem dirancang untuk institusi sertifikasi profesi modern yang memerlukan kepatuhan penuh terhadap standar BNSP tanpa kerumitan alur kerja manual atau redundansi data.

---

## 2. Goals, Non-Goals, & Anti-Goals

### 2.1 Goals (Sasaran Utama)
1. **Sistem Murni (No Landing Page):** Akses rute root `/` langsung mengarahkan pengguna ke `/login` (atau dashboard bila sudah login). Tidak ada marketing page.
2. **Desain Otentik & Anti-Slop:** Seluruh visual mengadopsi standar LSP PrO Indo (Navy `#1E2B6E`, Merah `#CE181E`, Background Canvas `#F8FAFC`, Typography *Plus Jakarta Sans*, border halus 1px `#E2E8F0`, tanpa gradien ungu/efek melayang).
3. **Alur Sertifikasi 1-Arah & Simple:**
   - **Asesi:** Pengisian profil & tanda tangan canvas 1x di awal $\rightarrow$ input kode jadwal $\rightarrow$ formulir gabungan APL.01 + APL.02 (fitur bulk *Set Semua K/BK* dan selektor bukti) $\rightarrow$ Waiting Approval Gate $\rightarrow$ persetujuan AK.01 $\rightarrow$ Ruang Uji (CBT IA.05 autograding + observasi praktik IA.01–03).
   - **Asesor:** Review APL (Setujui/Revisi/Tolak) $\rightarrow$ Penyesuaian Wajar AK.07 (batch & single) $\rightarrow$ Penilaian observasi praktik $\rightarrow$ Finalisasi asesmen (AK.02, AK.05, AK.06).
   - **Admin/Superadmin:** Manajemen jadwal (TUK teks bebas, multi-asesor, kode jadwal unik), impersonasi instan (Proxy/Switch role), dan Pusat Dokumen MUK.
4. **Pusat Dokumen MUK & Ekspor Data:**
   - 24 Formulir MUK BNSP siap cetak presisi (A4 portrait sesuai standar dokumen master).
   - Generator Berita Acara Pelaksanaan Uji Kompetensi.
   - Generator Daftar Hadir Asesi dan Daftar Hadir Asesor.
   - Ekspor Excel `.xlsx` native untuk Biodata Asesi, Daftar Hadir Peserta, dan Asesor.
5. **Otomatisasi Tanda Tangan:** Tanda tangan digital hanya digambar satu kali saat registrasi/profil, kemudian diinjeksikan secara otomatis ke seluruh berkas asesmen yang relevan.

### 2.2 Non-Goals & Anti-Goals
- **Anti-Goal:** Tidak menggunakan template UI generik ("AI slop") seperti kartu dengan bayangan tebal, gradien ungu-biru mengambang, atau animasi transisi yang lambat.
- **Non-Goal:** Tidak membuat portal pendaftaran publik massal yang rumit; asesi bergabung ke jadwal khusus menggunakan kode jadwal yang diterbitkan Admin.
- **Non-Goal:** Tidak memerlukan verifikasi OTP SMS/Email pihak ketiga yang memperlambat alur pengujian.

---

## 3. Approaches & Selected Option

### 3.1 Pilihan Pendekatan yang Dipertimbangkan
1. **Pendekatan 1: Unified Engine Porting (`m_*` & `t_*` Schema + Full 7-Scheme Seeder + Native Excel Generator) — [DIPILIH]**
   - Mengadopsi struktur tabel teruji `m_*` dan `t_*` dari `lsp-proindo-final`.
   - Mengimpor data master 7 skema otomotif lengkap beserta unit, KUK, dan bank soal CBT.
   - Memasang 24 template Blade MUK resmi yang langsung *plug-and-play*.
   - Menambahkan generator Excel native menggunakan `phpoffice/phpspreadsheet`.
2. **Pendekatan 2: Skema Hybrid / Adapter Layer**
   - Mempertahankan tabel lama dan membuat lapisan pemetaan data ke template MUK baru.
   - *Ditolak karena:* Rawan ketidakcocokan kolom (*missing fields*) pada dokumen cetak MUK dan query yang lambat.

---

## 4. Arsitektur Data & Model (Data Architecture)

### 4.1 Master Tables (`m_*`)
- `m_users`: Akun sistem (`SUPERADMIN`, `ADMIN_LSP`, `ASESOR`, `ASESI`).
- `m_user_profiles`: Biodata lengkap NIK, TTL, alamat, institusi/sekolah, nomor kontak, serta `signature_data` (Base64 PNG tanda tangan canvas).
- `m_schemes`: 7 Skema Otomotif (Kode, Nama, Jenis, Passing Grade CBT, Versi).
- `m_competency_units`: Unit kompetensi per skema.
- `m_unit_elements`: Elemen kompetensi per unit.
- `m_kuks`: Kriteria Unjuk Kerja (KUK) dengan kode `1.1`, `1.2`, dll.
- `m_question_banks`: Bank soal pilihan ganda CBT IA.05 per skema.

### 4.2 Transaction Tables (`t_*`)
- `t_schedules`: Jadwal uji kompetensi dengan `code` (misal `LSP-K9P2`), `tuk_name` (teks lokasi), tanggal, jam, dan instrumen terpilih.
- `t_schedule_assessors`: Pivot multi-asesor bertugas per jadwal.
- `t_assessments`: Record asesmen per asesi dalam jadwal (`apl_status`, `status`, `final_result`, `assessor_signature`, `asesi_signature`, `last_active_step`).
- `t_assessment_proofs`: Berkas bukti yang diunggah asesi (Persyaratan Dasar & Administratif).
- `t_assessment_apl02_items`: Data asesmen mandiri per KUK (`assessment_value` K/BK, `selected_proof_type`).
- `t_assessment_ak07_items`: Catatan penyesuaian wajar dan beralasan (single atau batch).
- `t_assessment_ia_responses`: Data JSON observasi/jawaban instrumen IA.01, IA.02, IA.03, IA.05 beserta skor dan umpan balik.
- `t_assessment_appeals`: Pengajuan banding asesi (FR.AK.04) dan keputusan komite.

---

## 5. Rencana Implementasi & Eksekusi

### Fase 1: Fondasi Data, Model, dan Seeder 7 Skema
- Migrasi tabel `m_*` dan `t_*`.
- Definisi Model Eloquent dengan relasi lengkap dan casting yang tepat.
- Seeder 7 Skema Otomotif lengkap dari data `lsp-proindo-final`.

### Fase 2: Alur Otentikasi, Profil, Tanda Tangan, dan Proxy
- Rute `/` redirect langsung ke `/login`.
- Login, Register, Logout dengan styling otentik LSP PrO Indo.
- Canvas Signature Component untuk Asesi & Asesor.
- Modul Proxy / Switch Role untuk Superadmin/Admin LSP.

### Fase 3: Alur Asesi (Pendaftaran Jadwal, APL Terpadu, Ruang Uji)
- Input kode jadwal unik dan validasi kuota/jadwal aktif.
- Formulir APL.01 + APL.02 satu halaman dengan tombol *Set Semua K/BK*.
- Approval Gate (menahan asesi hingga disetujui asesor).
- Ruang Uji: Penandatanganan AK.01, CBT IA.05 autograding, respon umpan balik IA.01–03.

### Fase 4: Alur Asesor (Review APL, AK.07, Penilaian Observasi Praktik)
- Dashboard penugasan jadwal asesor & daftar asesi.
- Halaman Review APL (Setujui, Minta Catatan Revisi, Tolak).
- AK.07 Penyesuaian Wajar (dukungan batch check multi-asesi).
- Ruang Uji Penilaian Asesor: Form observasi IA.01, tugas IA.02, klarifikasi IA.03, rekap IA.05.
- Finalisasi AK.02 dan Rekomendasi Hasil Akhir (K / BK).

### Fase 5: Pusat Dokumen MUK & Generator Ekspor
- Integrasi 24 Template MUK BNSP (`resources/views/admin/dokumen-muk/`).
- Cetak Berita Acara (BA) dan Daftar Hadir (Asesi & Asesor).
- Implementasi ekspor native spreadsheet `.xlsx` untuk:
  - Excel Data Diri / Biodata Asesi
  - Excel Daftar Hadir Peserta
  - Excel Daftar Hadir Asesor

### Fase 6: Verifikasi End-to-End & Automated Tests
- Menulis automated test komprehensif untuk seluruh alur peran (Asesi, Asesor, Admin).
- Uji coba print preview dan download Excel.
