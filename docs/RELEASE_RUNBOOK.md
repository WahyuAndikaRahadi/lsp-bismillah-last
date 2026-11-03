# RELEASE RUNBOOK — LSP PR O INDO CERTIFICATION PLATFORM

## 1. Prasyarat Lingkungan Produksi

### Runtime & Platform
- **PHP**: PHP 8.2 atau 8.3 dengan ekstensi wajib: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `filter`, `json`, `mbstring`, `openssl`, `pcre`, `pdo`, `pdo_mysql` (atau `pdo_sqlite` untuk testing/staging), `tokenizer`, `xml`, `xmlwriter`.
- **Database**: MySQL 8.0+ / MariaDB 10.5+ (InnoDB engine, utf8mb4_unicode_ci collation) atau SQLite 3.35+ (WAL mode untuk staging/rehearsal).
- **Web Server**: Nginx atau Apache dengan root diarahkan secara eksklusif ke folder `public/`. File `.env`, `storage/app/private/`, dan berkas aplikasi lainnya tidak boleh dapat diakses melalui webroot.
- **Node.js**: Node 18+ hanya digunakan untuk tahap build aset statis (`npm run build`). Node **tidak** berjalan sebagai daemon di lingkungan server produksi.
- **Zero PDF / Chromium Dependency**: Sistem mengandalkan print stylesheet CSS bawaan browser (`@media print`) yang aman dan terstandarisasi. **Tidak ada** instalasi Chromium, Puppeteer, wkhtmltopdf, Browsershot, atau PDF generator backend berisiko tinggi lainnya.

### Environment & Security Configuration (`.env`)
```ini
APP_NAME="LSP PrO Indo"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:... # Generate via php artisan key:generate
APP_URL=https://sertifikasi.proindo.id

LOG_CHANNEL=daily
LOG_LEVEL=info

# Database Production
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lsp_proindo_prod
DB_USERNAME=lsp_user
DB_PASSWORD=secure_production_password

# Session & Security
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

# Storage Disks
FILESYSTEM_DISK=local
# Catatan: Dokumen portofolio, KTP, dan bukti asesmen disimpan pada private local disk
# (storage/app/private/) dan hanya disajikan lewat SecureFileController dengan otorisasi ketat.
```

---

## 2. Prosedur Rilis & Deployment Baru (Atomic Release)

Sistem menggunakan skema atomic symlink deployment (misalnya via Deployer atau Envoyer) agar proses peralihan versi berjalan tanpa downtime dan mudah di-rollback.

```
/var/www/lsp-proindo/
├── current -> releases/202611040001
├── releases/
│   ├── 202611040000 (previous release)
│   └── 202611040001 (active release)
└── shared/
    ├── .env
    └── storage/
        ├── app/
        │   └── private/
        ├── framework/
        └── logs/
```

### Langkah-Langkah Deployment:
1. **Persiapan Direktori Rilis Baru**:
   ```bash
   git clone --branch impl/gemini-core-certification --depth 1 <repo-url> /var/www/lsp-proindo/releases/<release-id>
   cd /var/www/lsp-proindo/releases/<release-id>
   ```

2. **Tautkan Shared Directories & Environment**:
   ```bash
   ln -nfs /var/www/lsp-proindo/shared/.env .env
   rm -rf storage
   ln -nfs /var/www/lsp-proindo/shared/storage storage
   ```

3. **Install Dependencies & Build Aset**:
   ```bash
   composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
   npm ci
   npm run build
   rm -rf node_modules
   ```

4. **Maintenance Mode & Migrasi Database Additive**:
   ```bash
   php artisan down --secret="bypass-release-token" --render="errors::503"
   php artisan migrate --force
   ```

5. **Optimasi Cache Konfigurasi & Route**:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

6. **Switch Symlink Atomic**:
   ```bash
   ln -nfs /var/www/lsp-proindo/releases/<release-id> /var/www/lsp-proindo/current
   ```

7. **Restart Service & Matikan Maintenance Mode**:
   ```bash
   php artisan queue:restart
   sudo systemctl reload php8.2-fpm
   sudo systemctl reload nginx
   php artisan up
   ```

---

## 3. Verifikasi Paska Rilis (Health Check)

Segera sesudah deployment, jalankan serangkaian verifikasi cepat:
1. **Public Route Health**:
   - `curl -I https://sertifikasi.proindo.id/` -> HTTP 200 (Menampilkan profil LSP resmi, skema aktif, dan TUK terverifikasi tanpa memuat CDN remote).
   - `curl -I https://sertifikasi.proindo.id/login` -> HTTP 200 (Form autentikasi terisolasi).
2. **Private File Isolation**:
   - Pastikan URL langsung menuju `/storage/app/private/...` mengembalikan HTTP 404/403 dari web server Nginx.
3. **Audit Event Logging**:
   - Pastikan tabel `audit_events` mencatat aktivitas login, persetujuan APL, pengisian AK-01, asesmen, dan keputusan pleno dengan actor ID, action, dan timestamp lengkap.

---

## 4. Prosedur Rollback Cepat

Jika ditemukan anomali kritis paska rilis:

### A. Rollback Sebelum Transaksi Baru Terjadi (Zero New Writes)
1. Aktifkan mode maintenance:
   ```bash
   php artisan down
   ```
2. Arahkan kembali symlink ke rilis sebelumnya:
   ```bash
   ln -nfs /var/www/lsp-proindo/releases/<previous-release-id> /var/www/lsp-proindo/current
   cd /var/www/lsp-proindo/current
   ```
3. Kembalikan backup database sebelum migrasi (jika terdapat perubahan skema rollback-breaking):
   ```bash
   mysql -u lsp_user -p lsp_proindo_prod < /var/backups/pre-release-backup.sql
   ```
4. Reload service dan buka kembali aplikasi:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan queue:restart
   sudo systemctl reload php8.2-fpm
   php artisan up
   ```

### B. Rollback Sesudah Terdapat Transaksi Baru
- **DILARANG** menjalankan `migrate:rollback` secara membabi-buta karena data asesmen, persetujuan (AK-01), atau hasil banding yang baru masuk akan terhapus.
- Buat snapshot backup tambahan segera:
  ```bash
  mysqldump --single-transaction -u lsp_user -p lsp_proindo_prod > /var/backups/incident-snapshot.sql
  ```
- Lakukan perbaikan ke depan (forward fix / hotfix) pada branch rilis dengan patch migrasi aditif tanpa mengubah atau menghapus data persetujuan, keputusan, dan jejak audit yang sah.

---

## 5. Kebijakan Cadangan & Retensi Data (Backup & Retention)

1. **Frekuensi Cadangan**:
   - **Database**: Dump otomatis setiap 6 jam (`mysqldump --single-transaction --quick`) dan log binari MySQL (Point-in-Time Recovery).
   - **Private Storage**: Snapshot direktori `storage/app/private/` setiap hari menggunakan rsync incremental terenkripsi.
2. **Retensi & Kepatuhan Regulasi BNSP**:
   - Sesuai regulasi BNSP dan SOP LSP PrO Indo, dokumen asesmen (APL-01, APL-02, bukti portofolio, rekaman AK-02, AK-03, AK-05, AK-06, berita acara pleno, dan sertifikat) wajib dipertahankan minimal selama **3 tahun** sejak tanggal keputusan asesmen.
   - Kebijakan pemusnahan berkas kadaluarsa menunggu persetujuan definitif manajemen LSP (lihat dokumen `OWNER_QUESTIONS.md`).
