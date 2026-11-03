# Baseline dan arsip

Tanggal: 3 November 2026. Pekerjaan ini hanya dokumentasi, bukan release aplikasi.

- Baseline asli `003f90f`, parent commit pertama Astra `f257678`.
- Branch `plan/gemini-core-certification` dibuat dari baseline itu.
- Tag `archive/astra-implementation` menunjuk `e7437d8`.
- `e7437d8` menyimpan pekerjaan Phase 6–7 yang belum selesai dan **belum diverifikasi**. Riwayat Phase 0–4 tetap utuh; tidak ada commit lama dihapus/ditulis ulang.
- Source aplikasi branch rencana kembali ke baseline; implementasi Astra tidak dibawa ke branch ini.
- Perubahan lokal owner pada `CLAUDE.md`, `composer.json`, `composer.lock`, `.claude/`, `.mcp.json`, `boost.json`, dan `docs/reference/all-document-lsp-ProIndo/SOP PROINDO/` dipertahankan. Jangan commit/reset/clean/overwrite file tersebut tanpa instruksi owner.
- Dependency terpasang, build asset, cache dan database bukan bukti versi source. Tidak ada klaim runtime/database dikembalikan atau diuji. Implementasi memakai database terisolasi dan membangun asset ulang.

Arsip hanya untuk diagnosis; jangan cherry-pick atau menyalin kode, migration, view, config, atau test dari arsip. Commit task memilih path eksplisit; jangan `git add .` karena original SOP masih untracked milik owner.

Original SOP lokal tidak ikut commit dokumentasi. Jika clone implementer tidak memilikinya, dapatkan dari owner dengan hash sesuai `SOURCE_INDEX.md`. Ketidaktersediaan source adalah blocker, bukan izin menebak.

Verifikasi dokumentasi: tautan lokal README/dokumen baru/task valid; 40 task terindeks; 3.766 baris matriks mempunyai tujuh skema, sumber nilai, task capture dan actor; 252 hash original pada indeks cocok dengan file lokal. Diff source `app`, `database`, `resources`, `config`, `routes`, `tests` terhadap `003f90f` kosong. Tidak menjalankan suite/build/runtime aplikasi karena task ini hanya dokumentasi. Verifikasi hash dan struktur CSV tidak berarti seluruh sel original sudah disahkan; pekerjaan transkripsi/konflik yang belum selesai dijelaskan di FIELD_COVERAGE dan pertanyaan owner.
