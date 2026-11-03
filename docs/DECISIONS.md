# Log Keputusan Arsitektur & Teknis (docs/DECISIONS.md)

> Catatan historis baseline. Keputusan di bawah yang bertentangan dengan [CONTEXT.md](CONTEXT.md) tidak berlaku untuk implementasi Gemini. Rencana aktif: [EXECUTION_PLAN.md](EXECUTION_PLAN.md).

Dokumen ini mencatat seluruh keputusan arsitektur, alasan mendalam, dan alternatif yang ditolak selama pengerjaan sistem sertifikasi kompetensi LSP PrO Indo.

---

### Keputusan 1: Penghapusan Role Komite Teknis dan TUK Secara Menyeluruh
- **Keputusan:** Menghapus peran pengguna (user role) `KOMITE_TEKNIS` dan `TUK` dari login, routing, middleware, enum, seeder, dan menu navigasi. TUK hanya hadir sebagai nilai data (misalnya nama TUK, tipe TUK, dan lokasi pada jadwal).
- **Alasan:** Keputusan final pemilik sistem untuk menyederhanakan alur dan mempercepat proses. TUK di dunia nyata adalah lokasi fisik bengkel/workshop tempat uji, bukan pengguna aplikasi mandiri. Seluruh wewenang audit dan pelaporan dipegang oleh Admin LSP dan Asesor Kompetensi.
- **Alternatif yang Ditolak:** Mempertahankan akun TUK dan komite teknis dengan login terpisah, yang terbukti menyebabkan kebingungan peran, birokrasi berulang, dan jalan buntu bagi asesi.

---

### Keputusan 2: Peniadaan Verifikasi Berkas APL oleh Admin (Direct to Asesor)
- **Keputusan:** Modul verifikasi berkas pendaftaran APL oleh Admin ditiadakan. Asesor Kompetensi yang ditugaskan memiliki wewenang penuh untuk memeriksa kelengkapan data (FR.APL.01) dan bukti asesmen mandiri (FR.APL.02) dengan prinsip VATM (Valid, Asli, Terkini, Memadai).
- **Alasan:** Sesuai standar operasional BNSP, penilaian kecukupan bukti pemohon adalah kompetensi teknis asesor. Peniadaan verifikasi ganda admin mempercepat waktu pendaftaran asesi.
- **Alternatif yang Ditolak:** Alur verifikasi dua lapis (Admin memeriksa dokumen dulu, baru diserahkan ke asesor), yang menyebabkan penumpukan antrean berkas pendaftar.

---

### Keputusan 3: Paradigma Sistem Asesmen Berbasis Modul MUK (Per-Modul MUK System)
- **Keputusan:** Menghilangkan wizard sekuensial linear kaku ("Tahap 1 dari 5 / Langkah Berikutnya" bertingkat dengan animasi berlebih) pada dasbor asesi. Menggantikannya dengan **Modular MUK Matrix (Sistem Per-Modul)** yang menampilkan kartu status resmi setiap modul MUK (APL-01/02, AK-01, Ruang Uji Instrumen CBT & Praktik, AK-03, dan Sertifikat BNSP).
- **Alasan:** Di workshop TUK, pelaksanaan asesmen **diarahkan langsung secara dinamis oleh Asesor**. Instrumen asesmen dapat bervariasi per jadwal (misalnya IA.01 observasi, IA.01B lembar ukur, IA.02 demonstrasi, IA.03 lisan, IA.05 CBT, atau modul masa depan seperti CRP/CVP/IA.06). Menempatkan sistem dalam kotak modular memudahkan penambahan modul baru tanpa merusak alur linear.
- **Alternatif yang Ditolak:** Mengunci asesi dalam tahapan linear statis tanpa fleksibilitas arahan asesor.

---

### Keputusan 4: FR.AK.07 (Penyesuaian Wajar) 100% Diisi oleh Asesor
- **Keputusan:** Formulir FR.AK.07 hanya diakses dan diisi oleh Asesor Kompetensi bila ditemukan kondisi khusus kandidat di TUK (misalnya asesi berkebutuhan khusus atau gangguan listrik/alat di bengkel). Asesi tidak pernah diminta mengisi formulir ini.
- **Alasan:** Penyesuaian metode asesmen adalah hak prerogatif dan tanggung jawab profesional asesor BNSP. Menuntut asesi mengisi form ini membingungkan peserta dan melanggar SOP.
- **Alternatif yang Ditolak:** Memaksa asesi mengisi FR.AK.07 sebelum dapat menandatangani FR.AK.01.

---

### Keputusan 5: Persetujuan FR.AK.01 Berbasis 1-Klik & Tanda Tangan Canvas Profil
- **Keputusan:** Formulir FR.AK.01 tidak mengharuskan asesi menginput data manual; seluruh metadata jadwal, TUK, unit kompetensi, dan rencana bukti telah disiapkan sistem. Asesi cukup membaca perjanjian dan memberikan persetujuan 1-klik menggunakan tanda tangan digital profil yang telah tervalidasi.
- **Alasan:** Menghindari pengetikan berulang di layar gawai kecil atau komputer umum bengkel dan mencegah asesi terjebak pada formulir persetujuan.
- **Alternatif yang Ditolak:** Mengharuskan asesi menggambar ulang tanda tangan dari awal di setiap formulir instrumen.

---

### Keputusan 6: Workspace Terpadu Asesor (Unified Workspace per Asesi)
- **Keputusan:** Membangun `WorkspaceController` dan tampilan satu layar terpadu (`asesor.workspace.show`) dengan 5 tab interaktif: Review APL, FR.AK.01, CBT FR.IA.05, Observasi Praktik & PMO, dan FR.AK.02 Rekaman Asesmen.
- **Alasan:** Menghilangkan keharusan asesor berpindah-pindah 5 URL berbeda per asesi yang sebelumnya memicu 111+ pemanggilan redirect dan rawan kehilangan konteks penilaian.
- **Alternatif yang Ditolak:** Membiarkan asesor membuka 5 halaman terpisah untuk setiap peserta uji.

---

### Keputusan 7: Ketahanan Jawaban CBT (Resilient Dual-Persistence)
- **Keputusan:** Mengimplementasikan penyimpanan jawaban ganda: instan pada browser `localStorage` dan sinkronisasi berkala (debounced AJAX) ke endpoint `POST /asesi/assessment/{id}/ia05/save-draft`.
- **Alasan:** Komputer umum di TUK dan koneksi internet workshop sering tidak stabil. Jawaban asesi tidak boleh hilang saat refresh halaman atau browser tertutup.
- **Alternatif yang Ditolak:** Hanya menyimpan jawaban saat tombol "Selesaikan Ujian" ditekan di akhir sesi.

---

### Keputusan 8: Desain Institusional Anti-Slop (BNSP Audit Ready)
- **Keputusan:** Mengadopsi tata letak resmi formulir BNSP (kop lembaga, logo lisensi BNSP, nomor dokumen di pojok kanan atas, kotak metadata, tabel unit dan KUK bergaris tegas, serta kolom tanda tangan dua sisi). Menghilangkan gradien ungu/pink mencolok dan animasi dekoratif yang tidak perlu.
- **Alasan:** Sistem ini dipersiapkan untuk audit akreditasi BNSP, sehingga tampilan dokumen pada layar dan hasil cetak PDF harus memancarkan kewibawaan institusional lembaga sertifikasi resmi.
- **Alternatif yang Ditolak:** Memakai tema dashboard modern kasual yang tidak mencerminkan format dokumen resmi negara.

---

### Keputusan 9: Seleksi Massal Asesor (Bulk Action) & Sistem Unlock Modul Ruang Uji
- **Keputusan:**
  1. Menghapus kotak pilihan instrumen dari formulir jadwal Admin (`admin/jadwal/create` & `edit`). Admin murni mengelola administrasi (Skema, Tanggal, Jam, TUK, Asesor).
  2. Memindahkan kendali aktivasi instrumen uji (`FR.IA`) ke ranah Asesor dan kesepakatan `FR.AK.01`.
  3. Menyediakan fitur **Seleksi Massal (Bulk Action / Multi-Select)** pada dasbor asesor (`asesor/jadwal/{id}/asesi`) dengan tombol satu-klik *"Setujui APL & Buka Ruang Uji Massal"*, yang serentak mengesahkan APL, menyematkan tanda tangan profil asesi & asesor pada `FR.AK.01`, membuat lembar `FR.AK.07` standar, dan membuka Ruang Uji untuk seluruh kandidat terpilih.
  4. Meniadakan kartu/elemen visual berlabel "Terkunci" pada dasbor asesi dan ruang uji; antarmuka hanya menampilkan modul/instrumen yang memang telah aktif dan siap dikerjakan (*clean progressive unlocking*). Asesi tidak dibebani form penandatanganan manual untuk `FR.AK.01` atau `FR.AK.07`.
- **Alasan:** Asesor di lapangan (TUK) sering menangani 10–30 peserta sekaligus dalam keterbatasan waktu. Memaksa asesor mengeklik dan menyetujui satu per satu, atau memaksa asesi mengisi form persetujuan tambahan di layar komputer umum, sangat menghambat operasional. Secara metodologi BNSP, penentuan bukti instrumen uji disepakati bersama asesor di `FR.AK.01`, bukan dipatok kaku oleh Admin saat membuat jadwal.
- **Alternatif yang Ditolak:** Membiarkan Admin mencentang instrumen di jadwal secara kaku, memaksa asesi membuka form AK-01 mandiri untuk klik tanda tangan, dan menampilkan placeholder kartu mati "🔒 Terkunci" yang memenuhi layar asesi.
