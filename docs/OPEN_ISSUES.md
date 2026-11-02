# Daftar Isu Terbuka & Rekomendasi Lanjutan (docs/OPEN_ISSUES.md)

Dokumen ini mencatat secara jujur dan transparan isu terbuka, limitasi teknis, serta rekomendasi pengembangan lanjutan untuk LSP PrO Indo.

---

### 1. Bank Soal Ujian Tertulis CBT (FR.IA.05) per Skema
- **Kondisi Saat Ini:**
  Soal ujian tertulis pilihan ganda (FR.IA.05) pada sistem diambil dan diekstrak dari dokumen rujukan paket MUK di `docs/reference/all-document-lsp-ProIndo/`. Seluruh bank soal inti untuk skema Engine Junior Technician, Maintenance Gasoline Engine Injection, dan Mekanik Junior telah terhubung dan memiliki auto-grading.
- **Rekomendasi Lanjutan:**
  Untuk skema baru yang akan ditambahkan ke depan, pastikan komite skema menginput butir soal dan kunci jawaban melalui modul `admin/question-banks` sebelum jadwal asesmen dibuka.

---

### 2. Penyesuaian Pengujian Lapangan di TUK Tanpa Koneksi Internet (Offline-First Sync)
- **Kondisi Saat Ini:**
  Aplikasi berjalan optimal pada arsitektur web client-server dengan dukungan auto-save `localStorage` pada sisi browser asesi. Namun, aplikasi tetap membutuhkan koneksi HTTP ke server LSP saat login, submisi APL, dan penyerahan nilai akhir.
- **Rekomendasi Lanjutan:**
  Bila terdapat Tempat Uji Kompetensi (TUK) di daerah remote/blank spot yang sama sekali tidak memiliki akses internet, disarankan menyediakan bundle aplikasi lokal (misal: Docker container pada laptop asesor / server mini TUK) yang dilengkapi fasilitas export-import database JSON/SQLite untuk disinkronkan ke server pusat pasca pelaksanaan asesmen.

---

### 3. Pipeline OCR Otomatis untuk Verifikasi Berkas (KTP & Ijazah)
- **Kondisi Saat Ini:**
  Verifikasi berkas persyaratan dasar (KTP, pas foto, dan ijazah/surat keterangan kerja) dilakukan secara visual oleh Asesor pada Workspace Terpadu Asesor (Tab 1) dengan bantuan modal preview gambar.
- **Rekomendasi Lanjutan:**
  Dapat ditambahkan integrasi OCR (misalnya Tesseract OCR atau cloud vision API) pada modul upload berkas asesi untuk memvalidasi NIK dan nama secara otomatis sebelum diperiksa asesor.

---

### 4. Status Pengujian Otomatis
- **Rangkaian Pengujian:** Seluruh 136 feature dan unit tests (`tests/Feature/` dan `tests/Unit/`) berstatus **PASSED (136 passed, 910 assertions, 0 errors, 0 failures)**.
- **Pemeriksaan Paritas:** Formulir web APL-01, APL-02, AK-01, AK-02, AK-03, AK-05, AK-06, AK-07, dan IA-05 telah tervalidasi dan mencerminkan format dokumen MUK resmi BNSP.
