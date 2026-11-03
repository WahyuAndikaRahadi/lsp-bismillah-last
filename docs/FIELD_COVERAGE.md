# Matriks cakupan data formulir × skema × field

File utama: [FIELD_COVERAGE.csv](FIELD_COVERAGE.csv). Setiap semantic field diekspansi menjadi tujuh baris S1–S7; setiap baris mempunyai source ID/path, sumber nilai, task capture, actor, aturan validasi/N/A, dan status. Tidak ada tanda centang implementasi: baseline belum memenuhi kontrak ini.

**Batas ketelitian:** matriks ini adalah kontrak penyimpanan dan backlog verifikasi original, bukan pernyataan bahwa semua kotak pada 127 PDF sudah ditranskripsi/disahkan. `semantic_field` adalah nama kebutuhan data internal, **bukan label UI**. Source yang belum tersedia mempunyai `UNRESOLVED_FIELD_SET`; varian/shared master ditandai eksplisit. Field kompleks dengan `[*]` wajib diekspansi mengikuti baris asli skema pada task referensi, bukan diisi data contoh. Jangan menganggap placeholder unresolved sebagai izin membuat form generik. Selesainya pekerjaan mensyaratkan semua blocker sumber ditutup owner dan semua field aktif memperoleh lokasi halaman/sel yang terverifikasi.

## Cara membaca dan menyelesaikan matriks

1. Cari `form` dan `scheme`. Buka path original dari `source_paths`; hash harus sama dengan `SOURCE_INDEX.csv` atau ada keputusan versi baru yang dicatat.
2. `paper_section` menunjukkan kelompok. Label, urutan, penomoran, checkbox, tabel dan opsi sebenarnya diambil verbatim dari original, bukan dari nama key semantic. Jangan menambah kolom UI karena model mempunyai metadata internal.
3. Baris `units[*]`, `elements[*]`, `kuks[*]`, `questions[*]`, `groups[*]` berarti setiap baris versi original tersebut, dengan source page/row ID stabil. Task `05-01` sampai `05-07` menambah mapping konkret per baris: `source_id`, `page`, `section`, `row_key`, `field_key`, `original_label`, `storage_path`, `capture_task`, `actor`, `validation`, `review_status`. Hasil berupa data registry, bukan prose yang meminta AI menebak.
4. Satu nilai identitas/jadwal dapat memenuhi header banyak form melalui snapshot; tidak perlu meminta user mengisi ulang. Opsi rencana, kebutuhan, jawaban, pengamatan, rekomendasi dan keputusan harus mempunyai tindakan manusia nyata masing-masing.
5. Bedakan field pada kertas dan metadata internal. Contoh: unit finding internal membantu validasi AK-02; jangan menambah kolom K/BK per unit pada tampilan AK-02 bila original hanya tabel metode bukti. Demikian juga `source_version`, event IDs, hash, author user ID tidak ditampilkan sebagai field kertas.
6. Beberapa keluarga mempunyai varian footer/header. Jangan merender semua semantic slots di setiap varian. Mapping harus menyebut `printed`, `internal_metadata`, atau `not_in_this_original`, dengan bukti lokasi sumber; hanya `printed` menjadi field kertas. `not_in_this_original` bukan N/A yang diisikan ke dokumen.
7. Sel kosong wajib tetap kosong dalam draft. Ketika user menuntaskan tahap, validator memberi blocker field yang masih diperlukan. N/A tidak dapat menggantikan original yang hilang, KUK/soal wajib, consent, atau keputusan.

## Unit berulang dan materi skema

Tidak menyalin daftar unit/KUK/soal dari seed baseline sebagai sumber kebenaran. Import harus mencocokkan kode, judul, urutan elemen/KUK, kelompok pekerjaan dan pertanyaan terhadap PDF tiap skema. Materi yang sama secara kata tidak otomatis ID yang sama lintas-versi. Simpan referensi standar/SKKNI dan halaman. Soal A, kunci B dan slot jawaban C harus mempunyai jumlah/nomor yang konsisten; konflik adalah blocker, bukan dipotong ke jumlah paling kecil.

Skenario IA-02, petunjuk, daftar alat/bahan, durasi dan manual adalah reference data asli. Jawaban/hasil pengukuran adalah data kejadian. Kolom spesifikasi report sheet berasal dari manual kendaraan yang benar-benar dipakai; bila manual/angka tidak tersedia, jangan mengisi toleransi generik otomotif.

[REPORT_SOURCE_CELLS.csv](REPORT_SOURCE_CELLS.csv) mencatat kandidat baris pengisian report sheet beserta halaman/line extracted PDF. Ini alat pencarian, bukan schema siap pakai: satu baris bisa berisi beberapa sel, dan garis isian grafis bisa tidak terbaca. Task referensi harus membuka halaman original, memisahkan setiap sel (contoh silinder 1–4, satuan, kondisi, nilai manual, hasil), lalu memverifikasi grouping. Report sheet skema 5 yang belum ditemukan tidak boleh diganti sheet skema lain.

## Keluarga wajib dan saat capture

| Keluarga | Sumber nilai utama | Capture / task | Catatan |
|---|---|---|---|
| APL-01 | Profile snapshot, tujuan asesi, syarat/bukti dan review nyata | 07–08 | Kontak rumah/HP/kantor serta fax/email/postal kantor terpisah |
| APL-02 | KUK versi asli, self K/BK/evidence asesi, rekomendasi review | 08 | Bukan keputusan kompetensi asesor |
| MAPA-01 | Konteks, kelompok kerja, bukti, metode, modifikasi, konsultasi/validator | 09 | Per skema/rencana, bukan static JSON global |
| MAPA-02 | Kelompok, instrumen, potensi asesi 1–5, penyusun/validator | 10 | Wajib semua skema walaupun file terpisah hanya pada S1 |
| AK-01 | Rencana aktual, bukti, waktu/tempat, persetujuan dan confidentiality | 11 | Signature scope consent terpisah dari persetujuan rencana |
| AK-07 | Kategori/pilihan original, kebutuhan, acuan/metode/instrumen disepakati | 10 | Tidak otomatis semua Tidak |
| IA-01 | Pengamatan KUK/benchmark/catatan/rekomendasi nyata | 13 | Tidak otomatis K berdasarkan APL02 |
| Report sheet | Setiap sel pemeriksaan, manual/specification, nilai/kondisi | 13 | Alias IA01B/IA02A/IA01 belum boleh diputuskan AI |
| IA-02/03 | Skenario approved, respons/keterangan dan pencapaian aktual | 14 | IA03 tambahan hanya pertanyaan nyata asesor yang diizinkan petunjuk |
| IA-05 A/B/C | Soal, key privat, jawaban dan feedback nyata | 12 | Skor dapat dihitung, kompetensi tidak |
| IA-04A/B, IA-06A/B/C, IA-07–11 | **Original belum tersedia terpisah** | 15, blocked Q2 | Tidak menciptakan field untuk memenuhi checklist |
| AK-02 | Bukti unit, rekomendasi, tindak lanjut, komentar, lampiran, signature event | 16 | Explicit assessor finding, tidak default |
| AK-03 | Sepuluh komponen feedback original dan komentar asesi | 17 | Tidak auto-Ya |
| AK-04 | Pertanyaan YA/TIDAK, identitas, alasan, unit dibantah, signature asesi | 17, 21-01 | Intake banding resmi feeding penanganan investigasi independen (task 21-01) |
| FR-SOP-07.1/07.2 | Penugasan tim investigasi independen, berita acara temuan & rekomendasi | 21-01 | Investigasi independen, tidak mengubah nilai otomatis |
| FR-SOP-01.1/01.4/01.5/01.7 | Komitmen lembaga/personil, pakta integritas asesor, deklarasi konflik, pergantian asesor | 21-02 | Unresolved conflict memblokir tindakan asesor |
| FR.SPS.01/FR.SK.01/FR.SP.01 | Rencana berkala, laporan pemegang sertifikat + bukti, rekaman evaluasi, surat keputusan surveilans | 21-03 | Terikat sertifikat aktif, no auto-clearance |
| Resertifikasi SOP 04 §5 | Prior certificate ID, data permohonan perpanjangan, linked assessment baru | 21-04 | Reusable profile, mempertahankan sertifikat terdahulu |
| AK-05 | Roster/rekomendasi unit BK, aspek, penolakan, improvement/catatan | 18 | Laporan per asesor/revisi |
| AK-06 | Matrix prosedur × prinsip, dimensi kompetensi, evidence/improvement, reviewer | 18 | Tidak diganti empat checkbox global |
| Cover/checklist/pernyataan | Metadata reference, ketersediaan nyata, acknowledgment pemegang | 05/19/21 | Bukan generic letters module |
| FR-04.1/04.2 | Penunjukan, nomor, pejabat, jadwal, roster/unit, verifier/TUK | 07 | Nomor contoh tahun 2023 bukan default produksi |
| FR-04.3 / BA normal | Event kehadiran, waktu aktual, hasil, koordinator | 11/19 | BA number pending Q1; absent bukan BK |
| FR-10.1 | Pleno/partisipasi/pengesah nyata | 20 | Undangan bukan kehadiran |
| FR-10.2 | Penerimaan sertifikat per asesi dari event nyata | 21 | Upload admin bukan penerimaan asesi |

## Acceptance coverage akhir

- Setiap field cetak pada setiap original yang berlaku memiliki mapping konkret atau keputusan owner eksplisit tentang konflik/source, tidak ada `UNRESOLVED` pada versi published.
- Semua form berlaku pada S1–S7. Applicability keluarga berbeda dari pemilihan metode individual oleh asesor: form tersedia lengkap; metode dipilih MAPA/AK-01 berdasarkan bukti, bukan memaksa seluruh jenis pengujian pada setiap peserta.
- Semua source mappings mempunyai test round-trip simpan→baca→tampilan; data assessment baru tidak mengambil hasil assessment lama. Uji data nyata representatif tanpa PII produksi; fixture tidak menjadi seed reference.
- Key IA05B dan reference yang memuat jawaban tidak terpapar public/asesi. Signature, evidence dan file privat diuji terpisah dari public logo.
- Tidak membangun renderer/export PDF dalam pekerjaan ini. Data registry dan screen fidelity adalah keluaran; pixel-identical PDF adalah proyek lain.
