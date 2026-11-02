# LSP PrO Indo — Sistem Sertifikasi Kompetensi Profesi (Audit Ready BNSP)

Aplikasi web terpadu Lembaga Sertifikasi Profesi Praktisi Otomotif Indonesia (**LSP PrO Indo**) berlisensi resmi **Badan Nasional Sertifikasi Profesi (BNSP)**. Sistem ini mendigitalisasi seluruh tahapan asesmen kompetensi teknisi otomotif dengan kepatuhan format 1:1 terhadap instrumen Materi Uji Kompetensi (MUK) resmi BNSP untuk keperluan audit kepatuhan.

---

## 1. Arsitektur Aktor (Tepat Tiga Aktor)

Sistem beroperasi tepat dengan tiga aktor utama:

1. **`ADMIN_LSP` (Administrator LSP):**
   - Mengelola master data: skema sertifikasi (7 skema otomotif), unit kompetensi, elemen, KUK, bank soal CBT, dan jadwal asesmen.
   - Membuka jadwal dan memplot asesor yang bertugas.
   - Melakukan pengesahan hasil kelulusan pada **Sidang Pleno** serta menerbitkan nomor sertifikat resmi BNSP.
   - *Catatan:* Admin **TIDAK** ikut memeriksa atau memverifikasi berkas permohonan asesi. Proses verifikasi kelayakan permohonan sepenuhnya menjadi wewenang teknis Asesor.
2. **`ASESOR` (Asesor Kompetensi):**
   - Pemegang wewenang penuh dalam penilaian teknis.
   - Memverifikasi berkas permohonan FR.APL.01 dan bukti asesmen mandiri FR.APL.02 (VATM).
   - Menandatangani persetujuan asesmen dan komitmen kerahasiaan (FR.AK.01).
   - Mengarahkan proses pengujian di Tempat Uji Kompetensi (TUK).
   - Melakukan observasi unjuk kerja (FR.IA.01), verifikasi lembar kerja teknis (FR.IA.01B), penilaian demonstrasi (FR.IA.02), pertanyaan lisan (FR.IA.03), serta memantau ujian teori tertulis CBT (FR.IA.05).
   - Mengisi penyesuaian yang wajar dan beralasan (FR.AK.07) jika terdapat kondisi khusus kandidat di TUK.
   - Merekam hasil asesmen kompetensi (FR.AK.02), memberikan rekomendasi akhir (K / BK), serta menyusun laporan jadwal (FR.AK.05) dan kaji ulang asesmen (FR.AK.06).
3. **`ASESI` (Peserta Uji Sertifikasi):**
   - Mendaftar secara mandiri menggunakan **Kode Jadwal 4 Digit** dari TUK/penyelenggara.
   - Melengkapi profil diri dan tanda tangan digital canvas.
   - Mengisi formulir permohonan (FR.APL.01) dan asesmen mandiri (FR.APL.02) dengan memilih K/BK.
   - Menyetujui FR.AK.01 dengan 1-klik menggunakan tanda tangan terdaftar.
   - Mengikuti ujian teori CBT di web dan menyelesaikan instruksi praktik sesuai arahan langsung asesor di tempat uji.
   - Memberikan umpan balik pelaksanaan asesmen (FR.AK.03) dan mengajukan banding (FR.AK.04) bila diperlukan.

> **PENTING UNTUK AUDITOR & PENGEMBANG:**
> Role **Komite Teknis** dan **TUK** telah dihapus secara menyeluruh dari kode, routing, enum, middleware, dan seeder. TUK hanya hadir sebagai entitas data (nama TUK, jenis TUK: Sewaktu/Mandiri/Tempat Kerja, dan alamat).

---

## 2. Paradigma Sistem: Sistem Per-Modul MUK (Modular MUK Architecture)

> ### ⚠️ PANDUAN WAJIB BAGI PENGEMBANG DAN AI AGENT LAINNYA
> **JANGAN PERNAH** mengubah dasbor atau alur sistem ini menjadi wizard sekuensial linear kaku bertahap ala video-game (misal: "Tahap 1 dari 5", "Langkah Berikutnya" terkunci kaku).
>
> **Rasional Ilmiah & Prosedural BNSP:**
> 1. Di dunia nyata (di TUK), **pelaksanaan asesmen diarahkan secara langsung oleh Asesor**.
> 2. Paket MUK bersifat fleksibel tergantung skema dan skenario uji yang ditentukan pada jadwal.
> 3. Oleh karena itu, arsitektur sistem ini dibangun secara **PER-MODUL (Modular MUK Matrix)**. Setiap instrumen asesmen adalah komponen modular mandiri yang dapat aktif sesuai kebutuhan jadwal.

### Modul-Modul MUK Saat Ini:

| Kode Modul | Nama Formulir BNSP | Aktor Pengisi | Keterangan & Karakteristik |
| :--- | :--- | :--- | :--- |
| **FR.APL.01** | Permohonan Sertifikasi Kompetensi | Asesi | Bagian 1 (Data Pribadi & Pekerjaan), Bagian 2 (Data Sertifikasi), Bagian 3 (Bukti Kelengkapan). |
| **FR.APL.02** | Asesmen Mandiri | Asesi (isi) & Asesor (verifikasi) | Penilaian mandiri per KUK (K/BK) disertai bukti relevan dan verifikasi VATM asesor. |
| **FR.AK.01** | Persetujuan Asesmen & Kerahasiaan | Asesi & Asesor | Surat perjanjian pelaksanaan uji. Asesi menandatangani dengan 1-klik; asesor membubuhkan tanda tangan pendamping. |
| **FR.IA.01** | Ceklis Observasi Aktivitas di Tempat Kerja | Asesor | Ceklis observasi langsung KUK teknis saat asesi praktik di TUK. |
| **FR.IA.01B** | Lembar Pemeriksaan Teknis (Report Sheet) | Asesor | Verifikasi lembar kerja pengukuran mekanis / elektrikal kendaraan. |
| **FR.IA.02** | Tugas Praktik Demonstrasi (TPD) | Asesor | Penilaian skenario penugasan kerja nyata di workshop. |
| **FR.IA.03** | Pertanyaan Mendukung Observasi (PMO) | Asesor | Konfirmasi pemahaman lisan (wawancara teknis) untuk memastikan aspek transfer skills. |
| **FR.IA.05** | Pertanyaan Tertulis Pilihan Ganda (CBT) | Asesi | Ujian teori berbasis web. Auto-scoring, timer, dan penyimpanan draf instan (localStorage + debounced AJAX). |
| **FR.AK.07** | Penyesuaian yang Wajar dan Beralasan | **Asesor (100%)** | Diisi **hanya oleh asesor** bila ada kondisi khusus kandidat (misal disabilitas, listrik TUK padam). **Asesi tidak pernah mengisi formulir ini.** |
| **FR.AK.02** | Rekaman Asesmen Kompetensi | Asesor | Matriks resmi Unit vs Bukti Asesmen, penetapan rekomendasi final (K / BK), dan catatan tindak lanjut. |
| **FR.AK.03** | Umpan Balik Asesmen | Asesi | Evaluasi 10 butir pertanyaan penyelenggaraan asesmen oleh asesi setelah penilaian selesai. |
| **FR.AK.04** | Banding Asesmen | Asesi | Hak asesi mengajukan banding bila rekomendasi asesor dinilai tidak adil/objektif (hanya aktif jika BK). |
| **FR.AK.05** | Laporan Asesmen | Asesor | Rekapitulasi peserta, distribusi hasil K/BK, dan penutupan jadwal asesmen. |
| **FR.AK.06** | Kaji Ulang Asesmen | Asesor | Tinjauan kepatuhan prosedur dan dimensi kompetensi pasca asesmen. |
| **PLENO** | Sidang Pleno & Sertifikat BNSP | Admin LSP | Pengesahan yudisium hasil akhir dan penerbitan nomor blanko sertifikat BNSP. |

---

## 3. Cara Menambah Modul Asesmen Baru (Panduan Pengembangan Masa Depan)

Bila di masa depan LSP PrO Indo memerlukan modul asesmen tambahan—seperti **CRP** (Ceklis Verifikasi Portofolio), **CVP** (Ceklis Verifikasi Pihak Ketiga), atau **FR.IA.06** (Pertanyaan Esai Terstruktur)—ikuti konvensi modular berikut:

1. **Daftarkan Kode Modul Baru:**
   - Tambahkan kode (misal `CRP`, `CVP`, `IA06`) ke dalam array `selected_instruments` pada konfigurasi jadwal (`Schedule::$selected_instruments`).
2. **Buat View Format MUK BNSP:**
   - Ikuti tata letak dokumen master di `docs/reference/all-document-lsp-ProIndo/`.
   - Gunakan format kop resmi LSP PrO Indo, kotak metadata (Skema, TUK, Asesor, Asesi), dan tabel bergaris formal BNSP (`border border-slate-900`).
   - Hindari animasi dekoratif, gradien warna berlebihan, atau gaya visual kasual.
3. **Integrasikan ke Workspace Terpadu Asesor:**
   - Buka [`resources/views/asesor/workspace/show.blade.php`](resources/views/asesor/workspace/show.blade.php).
   - Tambahkan tab modul baru pada navigasi tab workspace.
4. **Hubungkan Penyimpanan Data:**
   - Gunakan tabel `t_assessment_ia_responses` (dengan kolom `instrument_code` yang sesuai) atau buat relasi terdedikasi bila memiliki struktur field khusus.
5. **Verifikasi Otomatis:**
   - Buat feature test di `tests/Feature/` untuk memverifikasi hak akses asesor/asesi dan persistensi datanya.

---

## 4. Standar UI/UX (Anti-Slop & Professional Dignity)

- **Identitas Visual:**
  - Navy Institusional: `#1E2B6E` (`bg-lsp-navy`)
  - Red LSP Accent: `#CE181E` (`bg-lsp-red`)
  - Border & Garis: Slate netral (`border-slate-200`, `border-slate-300`, atau `border-slate-900` untuk tabel MUK).
- **Tipografi:** Plus Jakarta Sans & Inter untuk keterbacaan tinggi di komputer umum TUK maupun perangkat genggam.
- **Formulir Asesmen:**
  - Menyerupai kertas fisik MUK resmi BNSP: kop surat lengkap dengan logo lisensi BNSP, kode dokumen ISO/MUK di pojok kanan atas, serta kolom tanda tangan digital dua kolom (Asesi & Asesor).
- **Aksi Tegas:** Satu tombol aksi utama per layar. Tidak ada tombol membingungkan atau aksi tidak relevan yang membuat asesi tersesat.

---

## 5. Menjalankan Aplikasi & Pengujian

### Prasyarat:
- PHP 8.2 atau PHP 8.3
- Composer 2+
- Node.js 18+ & npm
- SQLite atau MySQL/MariaDB

### Instalasi & Setup Lokal:
```bash
# Clone repository
git clone <repo-url>
cd rea-application-lsp

# Install dependencies PHP & JS
composer install
npm install
npm run build

# Setup environment & database
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
```

### Akun Pengujian Default:
| Role | Email | Password |
| :--- | :--- | :--- |
| **Admin LSP** | `admin@proindo.id` | `password` |
| **Asesor** | `asesor1@proindo.id` | `password` |
| **Asesi** | `asesi1@proindo.id` | `password` |

### Menjalankan Test Otomatis:
```bash
# Jalankan seluruh rangkaian test
php artisan test

# Jalankan test khusus paritas MUK & Dasbor
php artisan test tests/Feature/AsesiDashboardNextStepTest.php
php artisan test tests/Feature/AsesiAplFormParityTest.php
php artisan test tests/Feature/CbtAnswerPersistenceTest.php
```

---

## 6. Dokumentasi Pendukung
- `docs/DECISIONS.md`: Log keputusan arsitektur dan pilihan teknis.
- `docs/OPEN_ISSUES.md`: Daftar isu terbuka atau limitasi yang teridentifikasi secara jujur.
- `docs/reference/all-document-lsp-ProIndo/`: Berkas PDF MUK master resmi BNSP untuk 7 skema teknisi otomotif.
