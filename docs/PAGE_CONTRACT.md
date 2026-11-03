# Kontrak halaman dan design system

Ini inventaris **target**. Route path dapat mempertahankan nama baseline bila semantics sama; tidak boleh menyisakan dua route/domain untuk aksi sama. Task 23 memetakan setiap route aktual ke baris target atau daftar hapus, termasuk route tanpa menu.

## Foundation wajib

`resources/css/app.css`: token semantic `--brand-primary`, `--brand-accent`, `--surface`, `--text`, `--muted`, `--border`, `--danger`, spacing, radius, focus ring. Default navy dengan red jarang untuk aksen; warna/logo tervalidasi dari LspProfile, fallback lokal. Simpan satu keluarga font self-host (pilihan: Noto Sans dengan berkas lisensi) dan monospace lokal untuk kode. Jangan download asset/font saat render. Kontras teks biasa minimal 4.5:1; warna tidak satu-satunya indikator.

Komponen Blade di `resources/views/components/ui/`: button, field/error, section, status, table, empty-state, step-status. Tiga pattern di `components/page/`: list (judul/filter/list/pagination), detail (status/fakta/next action), form (paper sections/errors/action footer). Shell public/auth/admin/asesor/asesi berbagi komponen. Shell focused untuk ruang uji/CBT/instrumen menyembunyikan sidebar, banner dan menu tak relevan.

Control text minimal 14px; input mobile 16px; target sentuh minimal 44×44px. Tidak ada blur/glass, decorative gradient, emoji icon, oversized card. Ikon SVG konsisten dengan label aksesibel. Layout 390/768/1280 tanpa horizontal page scroll/overlap/cutoff. Tabel kertas memakai section/row grouping, sticky row label atau scroll lokal berlabel bila tabel intrinsik lebar; tidak mengubah urutan/nomor/wording/kelompok. Tidak meratakan tabel menjadi field dengan makna berbeda. Keyboard, focus-visible, label-for, error summary/link field dan reduced motion wajib.

Primary action kanan footer form di desktop, full-width bagian bawah di mobile (tidak menutupi field/error). Secondary `Simpan draf` hanya bila berguna, di kiri primary; navigasi `Kembali` di header. Destructive di menu tindakan terpisah dengan konfirmasi objek+akibat+alasan, bukan di sebelah submit. Label aksi sama di semua role: `Simpan perubahan`, `Simpan draf`, `Kirim permohonan`, `Minta perbaikan`, `Setujui permohonan`, `Masuk ruang uji`, `Kirim jawaban`, `Simpan rekomendasi`, `Kirim untuk review`, `Unggah sertifikat`, `Unduh sertifikat`. Label tidak ambigu seperti `Proses`, `Go`, `Submit`, `Cetak` untuk download.

## Inventaris target

| Role / halaman | Pattern | Primary / keadaan kosong atau blocker |
|---|---|---|
| Publik: beranda/profil LSP | detail | `Lihat skema`; identitas nyata, tanpa statistik demo |
| Publik: skema list/detail | list/detail | `Daftar asesmen`; syarat dan unit approved, tidak ada answer key |
| Publik: jadwal detail | detail | `Daftar asesmen`; penuh/tutup = alasan dan kembali daftar |
| Login/register/reset | form | `Masuk`/`Buat akun`/`Kirim tautan reset`; registrasi asesi saja |
| Asesi: beranda | detail | Satu assessment aktif yang dipilih + next action; riwayat tetap dapat dibuka |
| Asesi/asesor: profil | form | `Simpan perubahan`; signature reusable, data wajib ditunjukkan per konteks |
| Asesi: jadwal/join | list/detail | `Daftar asesmen`; consent eksplisit satu kali, retry aman |
| Asesi: APL-01/02 | form | `Lanjut ke APL-02` lalu `Kirim permohonan`; tetap dua original berurutan dalam satu alur |
| Asesi: detail permohonan | detail | `Perbaiki permohonan` jika revisi; jika menunggu tampil status, bukan tombol inert |
| Asesi: AK-01 | form | `Setujui rencana asesmen`; identitas/jadwal/bukti/kerahasiaan sesuai original |
| Asesi: ruang uji | focused detail | `Masuk ruang uji` lalu satu instrumen berikut; daftar progres ringkas, no menu dokumen massal |
| Asesi: CBT/IA jawaban/report | focused form | `Kirim jawaban`; save status/timer/progress; keluar dengan warning jika ada draft belum tersimpan |
| Asesi: hasil/AK-03/AK-04 | detail/form | `Isi umpan balik` setelah rekomendasi; AK-04 secondary relevan, tanpa janji investigasi aplikasi |
| Asesi: sertifikat list/detail | list/detail | `Unduh sertifikat`; tersedia setelah upload sah, menunggu upload dijelaskan |
| Asesor: beranda/jadwal/roster | list/detail | Tugas berikut sesuai tahap; filter peserta tanpa memindahkan portal |
| Asesor: review APL | detail/form | `Setujui permohonan` jika lengkap; `Minta perbaikan` terpisah; tidak membuka consent/hasil otomatis |
| Asesor: MAPA-01/02/AK-07 | form | `Simpan rencana` / `Konfirmasi penyesuaian`; paper order, exact source wording |
| Asesor: ruang uji/instrumen | focused form | `Simpan observasi`/`Simpan rekomendasi`; per peserta dan assigned ownership jelas |
| Asesor: AK-05/06 | form | `Kirim untuk review`; roster dari hasil aktual dan catatan nyata |
| Admin: beranda | detail | Antrean core/blocker; tidak menampilkan finance/quality/lifecycle |
| Admin: pengguna/asesor/TUK/skema/versi | list/detail/form | `Simpan perubahan`/`Publikasikan versi`; aksi sesuai objek, tidak semua tombol di setiap row |
| Admin: jadwal/roster/penunjukan | list/detail/form | `Publikasikan jadwal`; credential/TUK/source readiness blocker terlihat |
| Admin: bukti administratif | detail/form | `Simpan pemeriksaan`; tidak mengganti verifikasi teknis asesor |
| Admin: BA/kehadiran | detail/form | `Finalkan berita acara`; koreksi event dalam tindakan terpisah, no Hadir button |
| Admin: pleno/review/keputusan | list/detail/form | `Simpan hasil review`/`Sahkan keputusan` sesuai state dan authority |
| Admin: sertifikat per assessment | form/detail | `Unggah sertifikat`; tidak ada generate/print, tidak menimpa file lama |

Tidak ada menu dokumen berisi semua form yang dapat diedit terlepas state. Ringkasan record read-only boleh di detail assessment, dengan policy. Tidak ada tombol PDF sampai proyek PDF terpisah. Public contact boleh informasi LSP nyata, bukan complaint form.

## State dan navigasi

Satu service murni `AssessmentNextAction` menerima role, objek yang terotorisasi, state dan readiness lalu mengembalikan label/URL/blocker. Jangan service ini menulis DB. GET render current state; POST sukses maksimal satu redirect ke detail/next step yang dapat diakses. Middleware hanya mengamankan akses, tidak mengulang navigasi berdasar last_step. Draft tidak hilang saat validation error; multi-tab stale update menghasilkan konflik yang dapat dipulihkan.

Task 23 memeriksa setiap route/view masih aktif, sekali per flow pada browser. Tidak perlu screenshot berulang; simpan hasil ukuran viewport, URL, actor, tindakan, dan masalah yang belum selesai. Jangan menyatakan responsive dari CSS saja.
