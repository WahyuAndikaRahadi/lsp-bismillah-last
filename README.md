# Sistem Sertifikasi LSP PrO Indo

Aplikasi Laravel untuk pendaftaran, perencanaan, pelaksanaan asesmen, keputusan sertifikasi, dan penyerahan sertifikat. Satu instalasi melayani satu LSP; identitas dan materi skema berasal dari konfigurasi/data referensi.

**Branch ini berisi source baseline dan rencana pembangunan, bukan implementasi yang sudah diperbaiki.** Baseline `003f90f` masih memiliki kelemahan otorisasi, file publik, mutasi lewat GET, dan penetapan hasil otomatis. Jangan membuka baseline ke internet atau memasukkan data pribadi produksi sebelum pengamanan selesai.

## Mulai membaca

1. [CONTEXT](docs/CONTEXT.md): keputusan owner, scope, SOP, peran, alur.
2. [EXECUTION_PLAN](docs/EXECUTION_PLAN.md): task, dependency, pertanyaan owner.
3. [DATA_MODEL](docs/DATA_MODEL.md), [FIELD_COVERAGE](docs/FIELD_COVERAGE.md), [SOURCE_INDEX](docs/SOURCE_INDEX.md): kontrak data dan sumber.
4. [DEFECTS](docs/DEFECTS.md), [RETIREMENT](docs/RETIREMENT.md), [PAGE_CONTRACT](docs/PAGE_CONTRACT.md): perbaikan, penghapusan, halaman.
5. [BASELINE](docs/BASELINE.md): arsip dan perlindungan file lokal.

Gemini melaksanakan satu task `docs/tasks/` per sesi. Semua checkbox masih kosong. Arsip boleh dibaca untuk diagnosis; jangan mengambil kodenya.

## Scope target

Core SOP 04 (penerimaan sampai penyerahan sertifikat), 05, 06, 10: profil reusable, APL-01/02, TUK/jadwal/asesor, MAPA-01/02, data seluruh instrumen MUK, ruang uji/CBT, hasil asesor, BA/kehadiran, pleno/keputusan, upload sertifikat eksternal. Seluruh keluarga MUK berlaku bagi tujuh skema; isi teknis masing-masing mengikuti original sendiri.

Tiga role: **admin**, **asesor**, **asesi**. Fungsi Ketua TUK, koordinator, komite teknis, dan pimpinan adalah penunjukan nyata pada record core, bukan portal/role tambahan. Admin tidak dapat mengarang hasil asesor atau memutus sertifikasi atas asesmen yang ia ases sendiri.

Di luar scope: PDF export/generator, penerbitan sertifikat, complaint system, staff/capability portal, finance, chat/messages, generic letters, quality, impersonation, serta workflow banding, surveilans, resertifikasi, ketidakberpihakan. AK-04 tetap menyimpan field formulir; penanganan banding di luar aplikasi.

## Stack baseline

- PHP `^8.3`, Laravel `^13.17`, Blade, Alpine, Tailwind CSS 4, Vite 8; konfigurasi contoh memakai MySQL.
- Node `^20.19.0` atau `>=22.12.0` sesuai engine Vite; Composer 2; npm dengan lockfile.
- PHPUnit 12, Laravel Pint, Playwright untuk browser.
- Dompdf dan PhpSpreadsheet masih ada. Task pembersihan membuang dependency yang hanya digunakan fitur di luar scope. Boost di working tree merupakan perubahan lokal owner, bukan baseline commit.

Versi pasti ada pada `composer.lock` dan `package-lock.json`. Jangan upgrade dependency sambil mengerjakan domain tanpa alasan dan pengujian tersendiri.

## Menjalankan lokal

Gunakan database **khusus development**, tidak berbagi database produksi. Pertahankan `.env` yang sudah ada; salin `.env.example` hanya jika belum ada. Perintah berikut adalah instruksi, **bukan bukti aplikasi telah dijalankan oleh penulis rencana**.

```sh
php -v
composer -V
node --version
composer install
npm ci
```

Isi `.env`: `APP_ENV=local`, `APP_DEBUG=true`, `APP_URL=http://127.0.0.1:8000`, `DB_CONNECTION=mysql`, database baru dan kredensial development. Gunakan `SESSION_DRIVER=file`, `CACHE_STORE=file`, `QUEUE_CONNECTION=sync` untuk percobaan lokal. Buat database kosong melalui administrasi MySQL. Jangan memakai `root/root` dari contoh. Jalankan `php artisan key:generate` **hanya untuk instalasi baru**; mengganti key instalasi lama merusak data terenkripsi.

```sh
php artisan migrate
npm run build
php artisan serve --host=127.0.0.1 --port=8000
```

Untuk frontend development jalankan `npm run dev` di terminal lain. Jangan menjalankan `composer setup`, `migrate:fresh`, atau `db:seed` terhadap data yang perlu dipertahankan. Seeder baseline belum menjadi sumber produksi tervalidasi; bootstrap admin dan reference seed aman dibangun pada task 04–05. Jangan membuat symlink publik untuk bukti, identitas, signature, atau sertifikat.

Jalankan test sempit sesuai task. Full suite dan build dijalankan sekali pada task akhir: `php artisan test --compact`, kemudian `npm run build`. Database test harus terisolasi. Rencana ini tidak menyatakan test baseline lulus.

## Struktur

| Lokasi | Isi / target |
|---|---|
| `app/Models`, `app/Http`, `app/Services` | Dua domain masih bercampur; target satu domain Assessment/Schedule/Scheme |
| `routes/web.php` | Publik, auth, admin, asesor, asesi; perlu pengurangan dan object policy |
| `resources/views`, `resources/css`, `resources/js` | Target shared tokens dan tiga page pattern |
| `database/migrations` | Riwayat schema; upgrade produksi additive sebelum cutover |
| `database/seeders/data` | Kandidat data tujuh skema, wajib dibandingkan dengan original |
| `tests` | Test baseline dan test baru per task |
| `docs/reference` | Original SOP/MUK; jangan diubah atau diganti dengan hasil AI |
| `docs/tasks` | Kontrak implementasi per sesi |

`ALUR_SERTIFIKASI.md`, `DECISIONS.md`, `OPEN_ISSUES.md` adalah catatan historis; bila bertentangan, `CONTEXT.md` dan SOP/original menang.
