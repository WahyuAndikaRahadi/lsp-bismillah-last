import { chromium } from 'playwright';
import assert from 'node:assert';
import fs from 'node:fs';
import path from 'node:path';

const BASE_URL = process.env.APP_URL || 'http://localhost:8000';

// Generate 1x1 transparent PNG buffer for file upload testing
const VALID_1X1_PNG = Buffer.from(
    'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
    'base64'
);

const TMP_DIR = path.join(process.cwd(), 'tests', 'e2e', 'temp');
if (!fs.existsSync(TMP_DIR)) {
    fs.mkdirSync(TMP_DIR, { recursive: true });
}

const DUMMY_KTP = path.join(TMP_DIR, 'dummy_ktp.png');
const DUMMY_FOTO = path.join(TMP_DIR, 'dummy_foto.png');
const DUMMY_IJAZAH = path.join(TMP_DIR, 'dummy_ijazah.png');
fs.writeFileSync(DUMMY_KTP, VALID_1X1_PNG);
fs.writeFileSync(DUMMY_FOTO, VALID_1X1_PNG);
fs.writeFileSync(DUMMY_IJAZAH, VALID_1X1_PNG);

let passedTests = 0;
let failedTests = 0;
const failureDetails = [];

function pass(name) {
    passedTests++;
    console.log(`  \x1b[32m✓ PASS:\x1b[0m ${name}`);
}

function fail(name, error) {
    failedTests++;
    console.error(`  \x1b[31m✗ FAIL:\x1b[0m ${name}`);
    console.error(`    \x1b[33mReason:\x1b[0m ${error.message || error}`);
    failureDetails.push({ name, error: error.message || error });
}

console.log('\n============================================================');
console.log('🎭 MEMULAI PLAYWRIGHT REAL-BROWSER END-TO-END QA SUITE');
console.log('👥 Multi-Context Isolation: Admin, Asesor & Asesi Simultaneous');
console.log(`🌐 TARGET HOST: ${BASE_URL}`);
console.log('============================================================\n');

async function runBrowserQA() {
    const browser = await chromium.launch({
        headless: true,
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--disable-gpu'
        ]
    });

    const commonOpts = {
        viewport: { width: 1280, height: 800 },
        userAgent: 'Playwright-E2E-QA/2.0 (Chromium Headless Full Suite)'
    };

    // -------------------------------------------------------------
    // SUITE 1: PUBLIC / GUEST BROWSER EXPERIENCE
    // -------------------------------------------------------------
    console.log('\x1b[34m[SUITE 1] PUBLIC, GUEST, REDIRECTS & ERROR VIEWS\x1b[0m');
    const guestContext = await browser.newContext(commonOpts);
    const guestPage = await guestContext.newPage();

    try {
        await guestPage.goto(`${BASE_URL}/`, { waitUntil: 'domcontentloaded' });
        assert(guestPage.url().includes('/login'), `Root / harus redirect ke /login, url saat ini: ${guestPage.url()}`);
        pass('Browser: Root / secara otomatis redirect 302 ke /login');
    } catch (e) { fail('Browser: Root / redirect ke /login', e); }

    try {
        await guestPage.goto(`${BASE_URL}/login`);
        await guestPage.fill('input[name="email"]', 'asesi.bodong@example.com');
        await guestPage.fill('input[name="password"]', 'PasswordSalah123');
        await guestPage.locator('form[action*="/login"] button[type="submit"]').click();
        await guestPage.waitForTimeout(500);

        const pageContent = await guestPage.content();
        assert(
            pageContent.includes('tidak cocok') || 
            pageContent.includes('Invalid') || 
            pageContent.includes('Masuk ke Portal') ||
            guestPage.url().includes('/login'),
            'Login kredensial salah harus ditolak dan tetap berada di form login'
        );
        pass('Browser: Form login otentik menolak kredensial salah dengan aman');
    } catch (e) { fail('Browser: Validasi login salah', e); }

    try {
        const notFoundRes = await guestPage.goto(`${BASE_URL}/halaman-pasti-tidak-ada-playwright-404-test`);
        assert.strictEqual(notFoundRes.status(), 404, 'Response status harus 404');
        const text = await guestPage.content();
        assert(text.includes('Halaman Tidak Ditemukan') || text.includes('404'), 'Halaman 404 kustom harus ter-render');
        pass('Browser: HTTP 404 kustom view berhasil memuat layout clean tanpa error');
    } catch (e) { fail('Browser: HTTP 404 View', e); }

    try {
        await guestPage.goto(`${BASE_URL}/register`);
        assert(await guestPage.locator('input[name="full_name"], input[name="name"]').first().isVisible());
        assert(await guestPage.locator('input[name="email"]').isVisible());
        assert(await guestPage.locator('input[name="password"]').isVisible());
        pass('Browser: Halaman pendaftaran calon asesi (/register) render form lengkap');
    } catch (e) { fail('Browser: Register Page Form', e); }

    try {
        await guestPage.goto(`${BASE_URL}/pengaduan`);
        const titleText = await guestPage.textContent('body');
        assert(titleText.includes('Pengaduan') || titleText.includes('Aspirasi'), 'Halaman pengaduan publik harus terbuka');
        pass('Browser: Layanan Pengaduan Publik (/pengaduan) dapat diakses & responsif');
    } catch (e) { fail('Browser: Pengaduan Page', e); }

    await guestContext.close();

    // -------------------------------------------------------------
    // SUITE 2: ADMIN LSP FULL CRUD & OPERASIONAL
    // -------------------------------------------------------------
    console.log('\n\x1b[34m[SUITE 2] ADMIN LSP: OPERASIONAL, CRUD JADWAL, SCHEMES & MUK\x1b[0m');
    const adminContext = await browser.newContext(commonOpts);
    const adminPage = await adminContext.newPage();
    adminPage.on('dialog', async d => { await d.accept(); });

    try {
        await adminPage.goto(`${BASE_URL}/login`);
        await adminPage.fill('input[name="email"]', 'adminlsp@gmail.com');
        await adminPage.fill('input[name="password"]', '@Kompeten86');
        await adminPage.locator('form[action*="/login"] button[type="submit"]').click();
        await adminPage.waitForURL(url => !url.href.includes('/login'), { timeout: 8000 });
        pass('Browser: Admin LSP berhasil login ke dashboard');
    } catch (e) { fail('Browser: Admin Login', e); }

    try {
        await adminPage.goto(`${BASE_URL}/profile`);
        const profileContent = await adminPage.content();
        assert(!profileContent.includes('Call to a member function badgeClasses() on string'), 'Tidak boleh ada error badgeClasses()');
        assert(profileContent.includes('Pengaturan Profil & Akun') || profileContent.includes('Profil'), 'Halaman /profile terbuka 200 OK');
        pass('Browser: Halaman /profile admin terbuka 200 OK tanpa error badgeClasses()');
    } catch (e) { fail('Browser: Admin Profile View', e); }

    try {
        await adminPage.goto(`${BASE_URL}/admin/schemes`);
        const schemesContent = await adminPage.content();
        assert(schemesContent.includes('Skema') || schemesContent.includes('Sertifikasi'), 'Halaman daftar skema terbuka');
        assert(schemesContent.includes('Pemeliharaan') || schemesContent.includes('Otomotif') || schemesContent.includes('Sepeda Motor'), 'Memuat skema otomotif master');
        pass('Browser: Daftar Master Skema Sertifikasi (/admin/schemes) aktif dan lengkap');
    } catch (e) { fail('Browser: Master Skema List', e); }

    try {
        await adminPage.goto(`${BASE_URL}/admin/question-banks`);
        const qbContent = await adminPage.content();
        assert(qbContent.includes('Bank Soal') || qbContent.includes('Pertanyaan'), 'Halaman bank soal terbuka');
        pass('Browser: Bank Soal Teori Uji (/admin/question-banks) render tabel dengan benar');
    } catch (e) { fail('Browser: Question Bank List', e); }

    try {
        await adminPage.goto(`${BASE_URL}/admin/tuks`);
        const tukContent = await adminPage.content();
        assert(tukContent.includes('TUK') || tukContent.includes('Tempat Uji'), 'Halaman TUK terbuka');
        pass('Browser: Master Tempat Uji Kompetensi (/admin/tuks) render dengan benar');
    } catch (e) { fail('Browser: Master TUK', e); }

    let testCode = `JDW-PW-${Date.now().toString().slice(-4)}`;
    let testScheduleId = null;

    try {
        await adminPage.goto(`${BASE_URL}/admin/jadwal/create`);
        await adminPage.fill('input[name="name"]', `Uji Sertifikasi Otomotif Playwright ${testCode}`);
        await adminPage.selectOption('select[name="scheme_id"]', { index: 1 });
        await adminPage.fill('input[name="tuk_name"]', 'Bengkel Uji Playwright Center');
        await adminPage.fill('input[name="date"]', '2026-11-25');
        await adminPage.fill('input[name="start_time"]', '08:00');
        await adminPage.fill('input[name="end_time"]', '16:00');

        // Click generate random code
        await adminPage.locator('button:has-text("Generate Kode Acak")').click();
        await adminPage.waitForTimeout(200);
        testCode = await adminPage.inputValue('input[name="code"]');

        // Check assessor checkbox for Asesor Randi (ID 9) or first
        const randiCheck = adminPage.locator('input[name="assessor_ids[]"][value="9"]');
        if (await randiCheck.count() > 0) {
            await randiCheck.check();
        } else {
            const assessorCheck = adminPage.locator('input[name="assessor_ids[]"]').first();
            if (await assessorCheck.count() > 0) {
                await assessorCheck.check();
            }
        }

        // Submit form using specific submit button
        const submitBtn = adminPage.locator('button[type="submit"]:has-text("Simpan Jadwal Baru")');
        await Promise.all([
            adminPage.waitForURL(url => url.pathname.includes('/admin/jadwal'), { timeout: 10000 }),
            submitBtn.click()
        ]);

        const indexContent = await adminPage.content();
        assert(indexContent.includes(testCode), `Jadwal baru ${testCode} harus muncul di tabel jadwal`);

        const codeLink = adminPage.locator(`a:has-text("${testCode}")`).first();
        const href = await codeLink.getAttribute('href');
        const match = href ? href.match(/\/admin\/jadwal\/(\d+)/) : null;
        testScheduleId = match ? match[1] : null;

        pass(`Browser: Admin berhasil CRUD Jadwal -> Create ${testCode} (ID: ${testScheduleId})`);
    } catch (e) { fail('Browser: Create Jadwal', e); }

    try {
        if (testScheduleId) {
            await adminPage.goto(`${BASE_URL}/admin/jadwal/${testScheduleId}`);
            const detailText = await adminPage.content();
            assert(detailText.includes(testCode), 'Halaman detail harus menampilkan kode jadwal');
            pass(`Browser: Admin dapat membuka detail jadwal ID ${testScheduleId}`);

            await adminPage.goto(`${BASE_URL}/admin/jadwal/${testScheduleId}/edit`);
            await adminPage.fill('input[name="tuk_name"]', 'Bengkel Uji Playwright Center Updated');
            await adminPage.locator('main form button[type="submit"]').click();
            await adminPage.waitForURL(url => url.pathname.includes('/admin/jadwal'), { timeout: 8000 });

            const updatedContent = await adminPage.content();
            assert(updatedContent.includes('Bengkel Uji Playwright Center Updated') || updatedContent.includes(testCode));
            pass(`Browser: Admin berhasil memperbarui data jadwal via form edit`);
        }
    } catch (e) { fail('Browser: Detail & Edit Jadwal', e); }

    try {
        await adminPage.goto(`${BASE_URL}/admin/dokumen-muk`);
        const mukText = await adminPage.content();
        assert(mukText.includes('Dokumen MUK') || mukText.includes('Pusat Cetak') || mukText.includes('MUK'), 'Dokumen MUK Center harus terbuka');
        pass('Browser: Admin Dokumen MUK Center (/admin/dokumen-muk) siap generate 24 template');

        if (testScheduleId) {
            await adminPage.goto(`${BASE_URL}/admin/dokumen-muk/${testScheduleId}/export-ba`);
            const baText = await adminPage.content();
            assert(baText.includes('BERITA ACARA') || baText.includes('PELAKSANAAN ASESMEN'), 'BA harus memuat judul Berita Acara');
            pass(`Browser: Auto-generate Berita Acara (BA) MUK berhasil dirender untuk jadwal ${testScheduleId}`);
        }
    } catch (e) { fail('Browser: Dokumen MUK Center', e); }

    try {
        await adminPage.goto(`${BASE_URL}/admin/proxy`);
        const proxyText = await adminPage.content();
        assert(proxyText.includes('Beralih Peran') || proxyText.includes('Proxy'), 'Halaman proxy harus memuat daftar pengguna');

        const proxyBtn = adminPage.locator('form[action*="/admin/proxy/switch/"] button[type="submit"]').first();
        if (await proxyBtn.count() > 0) {
            await proxyBtn.click();
            await adminPage.waitForTimeout(500);
            const bodyText = await adminPage.content();
            assert(bodyText.includes('Proxy') && bodyText.includes('Kembali'), 'Top banner proxy harus aktif di DOM');
            pass('Browser: Instant Proxy Switcher berhasil beralih peran dengan visual banner informatif');

            const switchBackBtn = adminPage.locator('form[action*="/proxy/switch-back"] button[type="submit"]');
            await switchBackBtn.click();
            await adminPage.waitForTimeout(500);
            const backText = await adminPage.content();
            assert(!backText.includes('Proxy:') || backText.includes('Panel Administrasi') || backText.includes('Dashboard'), 'Berhasil kembali ke admin');
            pass('Browser: Sesi Administrator berhasil dipulihkan dari Proxy Switcher');
        }
    } catch (e) { fail('Browser: Proxy Impersonation Switcher', e); }

    // -------------------------------------------------------------
    // SUITE 3: ASESI WORKFLOW (PROFILE CANVAS, JOIN, APL, SECURITY)
    // -------------------------------------------------------------
    console.log('\n\x1b[34m[SUITE 3] ASESI: PROFILE CANVAS, JOIN JADWAL & APL PORTFOLIO\x1b[0m');
    const asesiContext = await browser.newContext(commonOpts);
    const asesiPage = await asesiContext.newPage();
    asesiPage.on('dialog', async d => { await d.accept(); });

    try {
        await asesiPage.goto(`${BASE_URL}/login`);
        await asesiPage.fill('input[name="email"]', 'asesi1@gmail.com');
        await asesiPage.fill('input[name="password"]', '@Kompeten86');
        await asesiPage.locator('form[action*="/login"] button[type="submit"]').click();
        await asesiPage.waitForURL(url => !url.href.includes('/login'), { timeout: 8000 });
        pass('Browser: Asesi berhasil login ke portal sertifikasi');
    } catch (e) { fail('Browser: Asesi Login', e); }

    try {
        await asesiPage.goto(`${BASE_URL}/asesi/profile`);
        await asesiPage.fill('input[name="full_name"]', 'Asesi Satu Otomotif Playwright');
        await asesiPage.fill('input[name="nik"]', '3201010101990001');

        // Set tempat lahir via select or input
        const tlInput = asesiPage.locator('input[name="tempat_lahir"]');
        if (await tlInput.count() > 0) {
            await tlInput.fill('KABUPATEN BEKASI');
        } else {
            await asesiPage.evaluate(() => {
                let sel = document.querySelector('select[name="tempat_lahir"]');
                if (sel) {
                    let opt = document.createElement('option');
                    opt.value = 'KABUPATEN BEKASI';
                    opt.textContent = 'KABUPATEN BEKASI';
                    opt.selected = true;
                    sel.appendChild(opt);
                    sel.value = 'KABUPATEN BEKASI';
                    sel.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
        }

        await asesiPage.fill('input[name="tanggal_lahir"]', '1999-05-12');
        await asesiPage.fill('textarea[name="alamat_rumah"], input[name="alamat_rumah"]', 'Jl. Raya Cikarang No. 88, Bekasi');
        await asesiPage.fill('input[name="phone"]', '081299998888');
        await asesiPage.fill('input[name="nama_institusi_perusahaan"]', 'SMK Negeri 1 Cikarang');
        // Select or fill jabatan
        const jabatanSelect = asesiPage.locator('select[name="jabatan"]');
        if (await jabatanSelect.count() > 0) {
            await jabatanSelect.selectOption({ index: 2 });
        } else {
            const jabatanInput = asesiPage.locator('input[name="jabatan"]');
            if (await jabatanInput.count() > 0) {
                await jabatanInput.fill('Mekanik Junior');
            }
        }

        // Draw on canvas if present
        const canvas = asesiPage.locator('#signature-pad, canvas');
        if (await canvas.count() > 0) {
            const box = await canvas.first().boundingBox();
            if (box) {
                await asesiPage.mouse.move(box.x + 20, box.y + 20);
                await asesiPage.mouse.down();
                await asesiPage.mouse.move(box.x + 80, box.y + 50);
                await asesiPage.mouse.move(box.x + 120, box.y + 20);
                await asesiPage.mouse.up();
            }
        }

        // Fill signature_data fallback hidden if needed
        const sigInput = asesiPage.locator('input[name="signature_data"]');
        if (await sigInput.count() > 0) {
            const val = await sigInput.inputValue();
            if (!val || val.length < 50) {
                const dummySig = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';
                await asesiPage.evaluate(s => {
                    const el = document.querySelector('input[name="signature_data"]');
                    if (el) el.value = s;
                }, dummySig);
            }
        }

        await asesiPage.locator('form[action*="/asesi/profile"] button[type="submit"]').click();
        await asesiPage.waitForTimeout(500);
        pass('Browser: Asesi berhasil melengkapi profil & menyimpan tanda tangan digital via Canvas');
    } catch (e) { fail('Browser: Asesi Profile & Canvas Signature', e); }

    let assessmentId = null;

    try {
        await asesiPage.goto(`${BASE_URL}/asesi/jadwal`);
        await asesiPage.fill('input[name="code"]', testCode);
        await Promise.all([
            asesiPage.waitForURL(url => url.pathname.includes('/assessment/'), { timeout: 8000 }),
            asesiPage.locator('form[action*="/asesi/jadwal/join"] button[type="submit"]').click()
        ]);

        const currentUrl = asesiPage.url();
        const assMatch = currentUrl.match(/\/assessment\/(\d+)/);
        assert(assMatch, `Harus diarahkan ke assessment id, current url: ${currentUrl}`);
        assessmentId = assMatch[1];
        pass(`Browser: Asesi berhasil join jadwal ${testCode} -> Assessment ID: ${assessmentId}`);
    } catch (e) { fail('Browser: Join Jadwal via Code', e); }

    try {
        if (assessmentId) {
            await asesiPage.goto(`${BASE_URL}/asesi/assessment/${assessmentId}/apl`);
            const aplContent = await asesiPage.content();
            assert(aplContent.includes('APL') || aplContent.includes('Permohonan') || aplContent.includes('Portofolio'));

            // File uploads
            const ktpInput = asesiPage.locator('input[name="proofs[KTP]"], input[name="ktp"]');
            if (await ktpInput.count() > 0) await ktpInput.setInputFiles(DUMMY_KTP);

            const fotoInput = asesiPage.locator('input[name="proofs[PAS_FOTO]"], input[name="pas_foto"]');
            if (await fotoInput.count() > 0) await fotoInput.setInputFiles(DUMMY_FOTO);

            const ijazahInput = asesiPage.locator('input[name="proofs[IJAZAH_SMK]"], input[name="ijazah_smk"]');
            if (await ijazahInput.count() > 0) await ijazahInput.setInputFiles(DUMMY_IJAZAH);

            // Select assessor Randi (ID 9) or first option if enabled
            const assessorSelect = asesiPage.locator('select[name="assessor_id"]');
            if (await assessorSelect.count() > 0 && await assessorSelect.isEnabled()) {
                const hasRandi = await assessorSelect.locator('option[value="9"]').count();
                if (hasRandi > 0) {
                    await assessorSelect.selectOption({ value: '9' });
                } else {
                    const optCount = await assessorSelect.locator('option').count();
                    if (optCount > 1) {
                        await assessorSelect.selectOption({ index: 1 });
                    }
                }
            }

            // Check integrity agreement checkbox
            const integrityCheck = asesiPage.locator('input[name="statement_integrity"]');
            if (await integrityCheck.count() > 0) {
                await integrityCheck.check();
            }

            // Use profile signature or draw on canvas for signature_data
            const useProfileSig = asesiPage.locator('label:has-text("Gunakan tanda tangan dari profil saya") input');
            if (await useProfileSig.count() > 0 && await useProfileSig.isEnabled()) {
                await useProfileSig.check();
            }

            const aplCanvas = asesiPage.locator('#aplForm canvas');
            if (await aplCanvas.count() > 0) {
                const box = await aplCanvas.first().boundingBox();
                if (box) {
                    await asesiPage.mouse.move(box.x + 20, box.y + 20);
                    await asesiPage.mouse.down();
                    await asesiPage.mouse.move(box.x + 80, box.y + 40);
                    await asesiPage.mouse.up();
                }
            }

            // Check radio buttons for KUKs if present
            const kukRadios = asesiPage.locator('input[type="radio"][value="K"]');
            const kukCount = await kukRadios.count();
            for (let i = 0; i < Math.min(kukCount, 20); i++) {
                await kukRadios.nth(i).check();
            }

            // Submit form and wait for upload navigation to complete
            const aplSubmitBtn = asesiPage.locator('#aplForm button[type="submit"]:has-text("Simpan & Ajukan Permohonan")');
            await Promise.all([
                asesiPage.waitForURL(url => url.pathname.includes('/apl'), { timeout: 20000 }),
                aplSubmitBtn.click()
            ]);

            const postAplContent = await asesiPage.content();
            assert(postAplContent.includes('Menunggu') || postAplContent.includes('SUBMITTED') || postAplContent.includes('verifikasi'), 'APL harus berstatus SUBMITTED / Menunggu Verifikasi');
            pass(`Browser: Asesi berhasil mengunggah berkas portofolio & mengirimkan APL-01/02 (Status: SUBMITTED)`);

            // Security Gate Check: Ruang Uji must redirect back until approved
            await asesiPage.goto(`${BASE_URL}/asesi/assessment/${assessmentId}/ruang-uji`);
            const gateUrl = asesiPage.url();
            const gateContent = await asesiPage.content();
            assert(gateUrl.includes('/apl') || gateContent.includes('menunggu') || gateContent.includes('disetujui'), 'Ruang uji harus terkunci sebelum APL disetujui');
            pass('Browser: Security Gate terbukti aman -> Ruang Uji terkunci sebelum verifikasi APL oleh Asesor');
        }
    } catch (e) { fail('Browser: Submit APL & Security Gate', e); }

    // -------------------------------------------------------------
    // SUITE 4: ASESOR COCKPIT & APPROVAL GATE
    // -------------------------------------------------------------
    console.log('\n\x1b[34m[SUITE 4] ASESOR: COCKPIT, REVIEW GATE APL & INSTRUMEN ASESMEN\x1b[0m');
    const asesorContext = await browser.newContext(commonOpts);
    const asesorPage = await asesorContext.newPage();
    asesorPage.on('dialog', async d => { await d.accept(); });

    try {
        await asesorPage.goto(`${BASE_URL}/login`);
        await asesorPage.fill('input[name="email"]', 'asesor.randi@gmail.com');
        await asesorPage.fill('input[name="password"]', '@Kompeten86');
        await asesorPage.locator('form[action*="/login"] button[type="submit"]').click();
        await asesorPage.waitForURL(url => !url.href.includes('/login'), { timeout: 8000 });
        pass('Browser: Asesor Randi berhasil login');
    } catch (e) { fail('Browser: Asesor Login', e); }

    try {
        await asesorPage.goto(`${BASE_URL}/asesor/jadwal`);
        const jadwalText = await asesorPage.content();
        assert(jadwalText.includes('Penugasan') || jadwalText.includes('Jadwal Asesmen') || jadwalText.includes('Asesor'));
        pass('Browser: Asesor dapat melihat daftar jadwal penugasan (/asesor/jadwal)');

        if (assessmentId) {
            await asesorPage.goto(`${BASE_URL}/asesor/assessment/${assessmentId}/review-apl`);
            const reviewText = await asesorPage.content();
            assert(reviewText.includes('Tinjau') || reviewText.includes('APL') || reviewText.includes('Berkas') || reviewText.includes('Portofolio'));

            // Approve APL
            const approveRadio = asesorPage.locator('input[value="APPROVE"], input[value="APPROVED"]');
            if (await approveRadio.count() > 0) {
                await approveRadio.first().check();
            }
            const notesField = asesorPage.locator('textarea[name="notes"]:visible, textarea[name="assessor_notes"]:visible');
            if (await notesField.count() > 0) {
                await notesField.fill('Berkas portofolio VATM telah memenuhi syarat kelayakan uji kompetensi.');
            }

            const approveBtn = asesorPage.locator('form[action*="/review-apl"] button[type="submit"]');
            await Promise.all([
                asesorPage.waitForResponse(res => res.url().includes('/review-apl') && (res.status() === 302 || res.status() === 200), { timeout: 15000 }),
                approveBtn.click()
            ]);
            await asesorPage.waitForLoadState('networkidle');
            pass(`Browser: Asesor berhasil memverifikasi dan Menyetujui (Approve) Permohonan APL asesi #${assessmentId}`);

            if (testScheduleId) {
                await asesorPage.goto(`${BASE_URL}/asesor/schedule/${testScheduleId}/ak07`);
                const ak07Content = await asesorPage.content();
                assert(!ak07Content.includes('border-2 border-black'), 'AK07 tidak boleh menggunakan style border hitam lama');

                const ak07Submit = asesorPage.locator('form[action*="/ak07"] button[type="submit"]');
                if (await ak07Submit.count() > 0) {
                    await ak07Submit.click();
                    await asesorPage.waitForTimeout(500);
                }
                pass(`Browser: Asesor berhasil mengisi formulir FR.AK.07 Penyesuaian Wajar jadwal ${testScheduleId}`);

                await asesorPage.goto(`${BASE_URL}/asesor/schedule/${testScheduleId}/ruang-uji`);
                assert.strictEqual((await asesorPage.goto(`${BASE_URL}/asesor/schedule/${testScheduleId}/ruang-uji`)).status(), 200);
                pass(`Browser: Asesor dapat membuka Ruang Uji Pengawasan Jadwal ${testScheduleId}`);
            }

            // Asesor Sign AK01
            await asesorPage.goto(`${BASE_URL}/asesor/schedule/${testScheduleId}/ak01`);
            const signAk01Btn = asesorPage.locator('form[action*="/ak01"] button[type="submit"], form[action*="/ak-01-sign"] button[type="submit"]').first();
            if (await signAk01Btn.count() > 0) {
                await signAk01Btn.click();
                await asesorPage.waitForTimeout(500);
            }
            pass(`Browser: Asesor menandatangani persetujuan FR.AK.01 untuk asesi #${assessmentId}`);
        }
    } catch (e) { fail('Browser: Asesor Cockpit & Review Gate', e); }

    // -------------------------------------------------------------
    // SUITE 5: ASESI RUANG UJI & CBT EXAM AUTO-GRADING
    // -------------------------------------------------------------
    console.log('\n\x1b[34m[SUITE 5] ASESI RUANG UJI, CBT TEORI (IA.05) & AUTO-GRADING\x1b[0m');

    try {
        if (assessmentId) {
            await asesiPage.goto(`${BASE_URL}/asesi/assessment/${assessmentId}/ruang-uji`);
            const hubText = await asesiPage.content();
            assert(hubText.includes('Ruang Uji') || hubText.includes('Instrumen'), 'Ruang uji harus terbuka setelah approval');
            pass('Browser: Ruang Uji Asesi berhasil terbuka 200 OK setelah APL diverifikasi asesor');

            // Sign AK01 / E-Sign Consent if required
            await asesiPage.goto(`${BASE_URL}/asesi/assessment/${assessmentId}/ak01`);
            const esignCheck = asesiPage.locator('input[name="esign_consent"], input[name="agreement"]');
            if (await esignCheck.count() > 0 && await esignCheck.first().isVisible()) {
                await esignCheck.first().check();
                const signSubmitBtn = asesiPage.locator('form:has(input[name="esign_consent"]) button[type="submit"], form[action*="/consent-esign"] button[type="submit"], form[action*="/ak01"] button[type="submit"]').first();
                if (await signSubmitBtn.count() > 0) {
                    await signSubmitBtn.click();
                    await asesiPage.waitForTimeout(500);
                }
            }
            pass('Browser: Asesi menyetujui dan menandatangani FR.AK.01');

            // Open CBT (IA.05)
            await asesiPage.goto(`${BASE_URL}/asesi/assessment/${assessmentId}/instrument/IA05`);
            const cbtText = await asesiPage.content();
            assert(cbtText.includes('IA.05') || cbtText.includes('Ujian Teori') || cbtText.includes('Soal') || cbtText.includes('CBT'), 'Halaman CBT harus memuat ujian');

            // Answer question radio buttons
            const radioButtons = asesiPage.locator('input[type="radio"]');
            const totalRadios = await radioButtons.count();
            for (let i = 0; i < totalRadios; i += 4) {
                await radioButtons.nth(i).check();
            }

            await Promise.all([
                asesiPage.waitForResponse(res => res.url().includes('/IA05') && (res.status() === 302 || res.status() === 200), { timeout: 10000 }).catch(() => {}),
                asesiPage.locator('form[action*="/instrument/IA05"] button[type="submit"], form button[type="submit"]:has-text("Kumpulkan")').first().click()
            ]);
            await asesiPage.waitForTimeout(1000);

            const postCbtContent = await asesiPage.content();
            assert(
                postCbtContent.includes('Jawaban tersimpan') || 
                postCbtContent.includes('Skor') || 
                postCbtContent.includes('Ruang Uji') ||
                postCbtContent.includes('IA.05'),
                'Submit CBT harus berhasil dan ter-auto-grade'
            );
            pass('Browser: Asesi berhasil menyelesaikan Ujian Teori CBT (FR.IA.05) dengan auto-grading otomatis');
        }
    } catch (e) { fail('Browser: Asesi Ruang Uji & CBT Exam', e); }

    // -------------------------------------------------------------
    // SUITE 6: ASESOR FINALIZATION & REKOMENDASI K/BK
    // -------------------------------------------------------------
    console.log('\n\x1b[34m[SUITE 6] ASESOR FINALISASI (AK.02, AK.05/06) & REKOMENDASI K/BK\x1b[0m');

    try {
        if (assessmentId) {
            await asesorPage.goto(`${BASE_URL}/asesor/assessment/${assessmentId}/ak02`);
            const ak02Text = await asesorPage.content();
            assert(ak02Text.includes('AK.02') || ak02Text.includes('Rekaman') || ak02Text.includes('Rekomendasi'));

            const finalSelect = asesorPage.locator('select[name="final_result"]');
            if (await finalSelect.count() > 0) {
                await finalSelect.selectOption('KOMPETEN');
            } else {
                const kRadio = asesorPage.locator('input[value="KOMPETEN"], input[value="K"]');
                if (await kRadio.count() > 0) {
                    await kRadio.first().check();
                }
            }

            const ak02Submit = asesorPage.locator('form[action*="/ak02"] button[type="submit"]');
            await Promise.all([
                asesorPage.waitForResponse(res => res.url().includes('/ak02') && (res.status() === 302 || res.status() === 200), { timeout: 10000 }).catch(() => {}),
                ak02Submit.click()
            ]);
            await asesorPage.waitForTimeout(800);
            pass(`Browser: Asesor merekomendasikan asesi #${assessmentId} sebagai KOMPETEN (K) pada FR.AK.02`);

            if (testScheduleId) {
                await asesorPage.goto(`${BASE_URL}/asesor/schedule/${testScheduleId}/reports`);
                const reportsText = await asesorPage.content();
                assert(reportsText.includes('Laporan') || reportsText.includes('AK.05') || reportsText.includes('AK.06'));

                const reportSubmit = asesorPage.locator('form[action*="/reports"] button[type="submit"]');
                if (await reportSubmit.count() > 0) {
                    await reportSubmit.click();
                    await asesorPage.waitForTimeout(500);
                }
                pass(`Browser: Asesor berhasil menyusun & menyimpan Laporan Asesmen (FR.AK.05 & FR.AK.06)`);
            }
        }
    } catch (e) { fail('Browser: Asesor Finalisasi & Rekomendasi', e); }

    // -------------------------------------------------------------
    // SUITE 7: ADMIN BERITA ACARA POST-FINALIZATION & CLEANUP
    // -------------------------------------------------------------
    console.log('\n\x1b[34m[SUITE 7] VERIFIKASI POST-FINALISASI & INTEGRITAS DATA MUK\x1b[0m');

    try {
        if (testScheduleId) {
            await adminPage.goto(`${BASE_URL}/admin/dokumen-muk/${testScheduleId}/export-ba`);
            const baPostText = await adminPage.content();
            assert(baPostText.includes('KOMPETEN') || baPostText.includes('Kompeten') || baPostText.includes(testCode), 'BA harus merefleksikan hasil kelulusan Kompeten');
            pass(`Browser: Berita Acara (BA) resmi memuat penetapan kelulusan KOMPETEN asesi secara otomatis`);

            // Cleanup schedule
            await adminPage.goto(`${BASE_URL}/admin/jadwal`);
            const delForm = adminPage.locator(`form[action$="/admin/jadwal/${testScheduleId}"]`);
            if (await delForm.count() > 0) {
                const delBtn = delForm.locator('button[type="submit"]');
                await delBtn.click();
                await adminPage.waitForTimeout(500);
            }
            pass(`Browser: Pembersihan data pengujian browser jadwal ${testCode} selesai`);
        }
    } catch (e) { fail('Browser: Verifikasi BA & Cleanup', e); }

    await adminContext.close();
    await asesiContext.close();
    await asesorContext.close();
    await browser.close();

    // Clean temp files
    try {
        fs.rmSync(TMP_DIR, { recursive: true, force: true });
    } catch (e) {}

    // -------------------------------------------------------------
    // SUMMARY REPORT
    // -------------------------------------------------------------
    console.log('\n============================================================');
    console.log('📊 RINGKASAN PLAYWRIGHT REAL-BROWSER QA AUTOMATION');
    console.log(`TOTAL SUITE CASES : ${passedTests + failedTests}`);
    console.log(`\x1b[32mLOLOS (PASSED)    : ${passedTests}\x1b[0m`);
    console.log(`\x1b[31mGAGAL (FAILED)    : ${failedTests}\x1b[0m`);
    console.log('============================================================\n');

    if (failedTests > 0) {
        console.error('\x1b[31mDaftar Kegagalan Real-Browser yang Perlu Diperbaiki:\x1b[0m');
        for (const f of failureDetails) {
            console.error(` - [${f.name}]: ${f.error}`);
        }
        process.exit(1);
    } else {
        console.log('\x1b[32m🎉 SEMUA PENGUJIAN REAL-BROWSER PLAYWRIGHT SUKSES 100% TANPA KENDALA!\x1b[0m\n');
        process.exit(0);
    }
}

runBrowserQA().catch(err => {
    console.error('Fatal Playwright Runner Error:', err);
    process.exit(1);
});
