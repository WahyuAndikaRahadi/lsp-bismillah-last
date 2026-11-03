# Daftar Pertanyaan Terbuka untuk Pemilik Sistem (docs/OWNER_QUESTIONS.md)

Dokumen ini mencatat pertanyaan terbuka yang membutuhkan keputusan dan kejelasan sumber dari owner. Sesuai prinsip kerja otonom, hal yang bergantung pada pertanyaan terbuka ditangani dengan pendekatan data-driven yang dapat dikonfigurasi tanpa asumsi sepihak, atau ditandai `blocked-source` bila materi originalnya memang belum tersedia.

---

### Q1 — Kode & Format Berita Acara (BA) UJK Biasa
- **Pertanyaan:** Untuk UJK biasa, apakah menggunakan kode/format formulir FR-SOP-04.4 atau rujukan pasal SOP 04.5? Pada dokumen original, FR-SOP-04.5 berisi berita acara asesmen ulang.
- **Dampak Implementasi:** Kolom dan data BA normal tetap disiapkan secara modular; nomor kode dokumen disimpan sebagai data dinamis.

### Q2 — Ketiadaan Dokumen Original Instrumen (IA.04.A/B, IA.06.A/B/C, IA.07–11, Report Sheet S5)
- **Pertanyaan:** Mohon arahan ketersediaan dokumen PDF original terpisah untuk instrumen:
  - FR.IA.04.A (Daftar Cek Verifikasi Portofolio)
  - FR.IA.04.B (Penilaian Portofolio)
  - FR.IA.06.A/B/C (Pertanyaan Tertulis Esai, Kunci Jawaban, Lembar Jawaban)
  - FR.IA.07 (Pertanyaan Lisan)
  - FR.IA.08 (Daftar Cek Verifikasi Pihak Ketiga)
  - FR.IA.09 (Pertanyaan Wawancara)
  - FR.IA.10 (Klarifikasi Bukti Tambahan)
  - FR.IA.11 (Cek Portofolio Lanjutan)
  - Lembar Hasil Pengukuran / Report Sheet untuk Skema 5 (Mekanik Junior)
- **Dampak Implementasi:** Task 15-01 s.d. 15-08 berstatus `blocked-source` dan tidak dimulai tanpa mengarang instrumen fiktif.

### Q3 — Standardisasi Versi & Kode Materi Skema
- **Pertanyaan:** Mana versi resmi untuk report sheet yang dinamai berkas IA.01.B tetapi header tercetak IA.02.A atau IA.01? Terdapat juga perbedaan penomoran skema romawi XI/2025 versus I/2026.
- **Dampak Implementasi:** Diperlakukan sebagai data versi skema berstatus `draft` / `needs-review`.

### Q4 — Kewenangan dan Personel Pengesahan
- **Pertanyaan:** Siapa personel atau pejabat yang berwenang menandatangani Surat Penugasan Asesor (FR-SOP-04.1), SPT Verifikasi TUK (04.2), validasi rekomendasi (SOP 10), dan Surat Keputusan Pemberian Sertifikat (SOP 04 §3.4)?
- **Dampak Implementasi:** Disimpan sebagai data penunjukan orang nyata dan jabatan yang dapat diinput admin.

### Q5 — Parameter Operasional dan Penomoran Sertifikat
- **Pertanyaan:** Bagaimana formula penomoran registrasi sertifikat, tanggal terbit, masa berlaku (default tanpa hardcode 3 tahun), dan blanko BNSP?
- **Dampak Implementasi:** Diinput oleh admin pada saat upload sertifikat fisik per assessment; dilarang auto-generate.

### Q6 — Kebijakan Migrasi & Retensi Data Historis
- **Pertanyaan:** Apakah database historis deployment terdahulu akan dimigrasikan secara live, dan apa kebijakan retensi arsip?
- **Dampak Implementasi:** Tabel historis dipertahankan dan diisolasi; tidak dilakukan drop table destruktif pada release ini.

### Q7 — Penyelidik dan Reviewer Independen (SOP 07 & SOP 19)
- **Pertanyaan:** Siapa pejabat/personel yang berhak ditugaskan sebagai tim investigasi banding (FR-SOP-07.1) dan evaluator surveilans independen (FR.SPS.01)? Bagaimana aturan sistem untuk memvalidasi independensi mutlak dari asesor penguji dan kandidat yang bersangkutan?
- **Dampak Implementasi:** Ditangani data-driven: admin memilih investigator terdaftar dengan validasi ketat bahwa ID investigator tidak boleh sama dengan asesor yang menguji kandidat pada asesmen tersebut.

### Q8 — Batas Waktu Banding (Appeal Deadline)
- **Pertanyaan:** Berapa hari kalender atau hari kerja batas waktu pengajuan banding (FR.AK.04) oleh asesi setelah rekomendasi/keputusan diumumkan?
- **Dampak Implementasi:** Bersifat konfigurabel (`appeal_window_days`). Jika tidak diatur / bernilai null, batas waktu dinonaktifkan (tanpa cutoff default).

### Q9 — Interval Berkala dan Bukti Pemeliharaan Surveilans
- **Pertanyaan:** Kapan jadwal berkala pelaksanaan surveilans (misal tahun ke-1 dan tahun ke-2 setelah sertifikat terbit), apa jenis dokumen bukti pemeliharaan kompetensi yang wajib diunggah pemegang (FR.SP.01), dan apa sanksi pembekuan jika tidak melapor?
- **Dampak Implementasi:** Rencana surveilans dibuat oleh admin per sertifikat aktif secara data-driven; asesi mengunggah berkas bukti; tidak ada pembekuan atau perpanjangan otomatis.

### Q10 — Persyaratan dan Aturan Resertifikasi (SOP 04 §5)
- **Pertanyaan:** Apa saja kriteria kelayakan perpanjangan/sertifikasi ulang (misal: jam kerja terbukti, asesmen portofolio, atau uji ulang unit tertentu), dan berapa bulan sebelum sertifikat kedaluwarsa permohonan dapat mulai dibuka?
- **Dampak Implementasi:** Pendaftaran resertifikasi dapat dilakukan dari sertifikat sah yang dimiliki asesi pada skema yang sama, menghasilkan asesmen baru yang tertaut ke ID sertifikat lama (`prior_certificate_id`).
