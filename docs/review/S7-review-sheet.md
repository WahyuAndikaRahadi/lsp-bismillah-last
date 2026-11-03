# Review Sheet — Skema S7: Servis Sepeda Motor Injeksi (SKM-07-2026)

**Status Dataset**: `needs-review` (Belum disahkan untuk penerbitan live)  
**Skema**: Klaster Servis Sepeda Motor Injeksi (`SKM-07-2026`)  
**Sumber Asli**: `07. MUK KLASTER SERVIS SEPEDA MOTOR INJEKSI`  

---

## 1. Ringkasan Inventaris & Hitungan Elemen

| Komponen Formulir | Jumlah Entitas Terdata | Status Verifikasi |
| :--- | :--- | :--- |
| **Unit Kompetensi** | 19 unit | Meliputi bongkar pasang cover body, sistem injeksi PGM-FI/YMJET |
| **Elemen Kompetensi** | 53 elemen | Struktur kompetensi klaster servis injeksi motor |
| **Kriteria Unjuk Kerja (KUK)** | 271 KUK | Sesuai materi kurikulum SKKNI No. 147/2019 |
| **Soal CBT (FR.IA.05)** | 65 butir pertanyaan | Pilihan ganda 4 opsi (A, B, C, D) |
| **Opsi Pilihan Ganda (IA.05)** | 260 opsi total | Tiap soal memiliki 1 kunci jawaban valid |
| **Pertanyaan Pendukung Observasi (FR.IA.03)** | 16 butir pertanyaan | Reset TP sensor, kode kedipan MIL, pompa bensin |
| **Tugas Praktik Demonstrasi (FR.IA.02)** | 3 kelompok skenario | Pengetesan sensor, pembacaan scanner diagnostik, servis injeksi |
| **Sel Lembar Observasi (FR.IA.01)** | 18 sel standar | Tekanan fuel pump (min 294 kPa), voltase O2 sensor, putaran idle |

---

## 2. Formulir Bersama & Slot Keluarga MUK

- **FR.APL.01 & FR.APL.02**: Lengkap (19 unit kompetensi servis motor injeksi).
- **FR.MAPA.01 & FR.MAPA.02**: Lengkap (Pendekatan asesmen klaster injeksi roda dua).
- **FR.AK.01, FR.AK.02, FR.AK.03, FR.AK.05, FR.AK.06**: Format baku tersedia.
- **Keluarga Instrumen Tertunda / Blocked**:
  - `FR.IA.04.A`, `FR.IA.04.B`, `FR.IA.06.A/B/C`, `FR.IA.07` s/d `FR.IA.11` tidak disertakan karena berkas fisik asli tidak terdapat pada repositori (tercatat pada `OWNER_QUESTIONS.md` Q2).

---

## 3. Catatan Keraguan & Item yang Memerlukan Review Lapangan

1. **Alat Scanner Diagnostik Multibrand**: Pada pembacaan MIL & DLC reset, ketersediaan scanner diagnostik (misal HIDS Honda vs FIDT Yamaha vs OBD-2 motor) di TUK harus dipastikan sebelum asesmen langsung dimulai.
2. **Kunci Jawaban CBT (65 Soal)**: 65 butir soal dan kunci telah terdata lengkap tanpa kekosongan, siap ditinjau oleh validator teknis BNSP.
3. **Status Publikasi**: Dataset tetap berstatus `needs-review` dan tidak boleh dipublish otomatis tanpa verifikasi validator resmi.
