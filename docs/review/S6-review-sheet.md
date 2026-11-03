# Review Sheet — Skema S6: Servis Sepeda Motor Listrik (SKM-06-2026)

**Status Dataset**: `needs-review` (Belum disahkan untuk penerbitan live)  
**Skema**: Servis Sepeda Motor Listrik (`SKM-06-2026`)  
**Sumber Asli**: `06. MUK KLASTER SERVIS SEPEDA MOTOR LISTRIK`  

---

## 1. Ringkasan Inventaris & Hitungan Elemen

| Komponen Formulir | Jumlah Entitas Terdata | Status Verifikasi |
| :--- | :--- | :--- |
| **Unit Kompetensi** | 10 unit | Termasuk EWD, baterai lithium motor listrik, controller, BLDC |
| **Elemen Kompetensi** | 26 elemen | Struktur kompetensi servis EV roda dua |
| **Kriteria Unjuk Kerja (KUK)** | 146 KUK | Sesuai silabus kejuruan motor listrik |
| **Soal CBT (FR.IA.05)** | 40 butir pertanyaan | Pilihan ganda 4 opsi (A, B, C, D) |
| **Opsi Pilihan Ganda (IA.05)** | 160 opsi total | Tiap soal memiliki 1 kunci jawaban valid |
| **Pertanyaan Pendukung Observasi (FR.IA.03)** | 13 butir pertanyaan | Prosedur BMS (Battery Management System) dan motor hub |
| **Tugas Praktik Demonstrasi (FR.IA.02)** | 3 kelompok skenario | Pengukuran resistansi isolasi, diagnosis controller, kalibrasi gas (throttle) |
| **Sel Lembar Observasi (FR.IA.01)** | 10 sel standar | Tegangan pack baterai, suhu controller, putaran motor BLDC |

---

## 2. Formulir Bersama & Slot Keluarga MUK

- **FR.APL.01 & FR.APL.02**: Lengkap (10 unit kompetensi motor listrik).
- **FR.MAPA.01 & FR.MAPA.02**: Lengkap (Pendekatan asesmen klaster motor listrik).
- **FR.AK.01, FR.AK.02, FR.AK.03, FR.AK.05, FR.AK.06**: Format baku tersedia.
- **Keluarga Instrumen Tertunda / Blocked**:
  - `FR.IA.04.A`, `FR.IA.04.B`, `FR.IA.06.A/B/C`, `FR.IA.07` s/d `FR.IA.11` tidak disertakan karena berkas fisik asli tidak terdapat pada repositori (tercatat pada `OWNER_QUESTIONS.md` Q2).

---

## 3. Catatan Keraguan & Item yang Memerlukan Review Lapangan

1. **Spesifikasi Baterai Traksi Sepeda Motor Listrik**: Beragamnya protokol komunikasi (CAN bus vs UART) pada sepeda motor listrik di Indonesia (Gesits, Alva, Polytron, dll.) mensyaratkan TUK menyediakan unit uji yang kompatibel dengan silabus uji.
2. **Kunci Jawaban CBT (40 Soal)**: Seluruh 40 butir soal dan kunci jawaban telah tervalidasi secara komputasi.
3. **Status Publikasi**: Dataset tetap berstatus `needs-review` dan tidak boleh dipublish otomatis tanpa verifikasi validator resmi.
