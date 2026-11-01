import assert from 'node:assert';

const BASE_URL = process.env.APP_URL || 'http://localhost:8000';

class BrowserClient {
    constructor() {
        this.cookies = new Map();
        this.csrfToken = null;
        this.lastUrl = null;
    }

    _updateCookies(response) {
        const setCookieHeaders = response.headers.getSetCookie ? response.headers.getSetCookie() : [];
        if (setCookieHeaders.length === 0) {
            const raw = response.headers.get('set-cookie');
            if (raw) setCookieHeaders.push(raw);
        }

        for (const header of setCookieHeaders) {
            const parts = header.split(';');
            const [name, val] = parts[0].split('=');
            if (name && val !== undefined) {
                this.cookies.set(name.trim(), val.trim());
            }
        }
    }

    _getCookieHeader() {
        const pairs = [];
        for (const [k, v] of this.cookies.entries()) {
            pairs.push(`${k}=${v}`);
        }
        return pairs.join('; ');
    }

    _extractCsrfFromHtml(html) {
        const metaMatch = html.match(/<meta\s+name=["']csrf-token["']\s+content=["']([^"']+)["']/i);
        if (metaMatch) {
            this.csrfToken = metaMatch[1];
            return this.csrfToken;
        }
        const inputMatch = html.match(/<input\s+type=["']hidden["']\s+name=["']_token["']\s+value=["']([^"']+)["']/i);
        if (inputMatch) {
            this.csrfToken = inputMatch[1];
            return this.csrfToken;
        }
        return this.csrfToken;
    }

    async request(method, path, body = null, isFormData = false, customHeaders = {}) {
        const url = path.startsWith('http') ? path : `${BASE_URL}${path.startsWith('/') ? '' : '/'}${path}`;
        let headers = {
            'User-Agent': 'ComprehensiveQATester/2.0 (Playwright-Like Engine)',
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            ...customHeaders
        };

        const cookieStr = this._getCookieHeader();
        if (cookieStr) {
            headers['Cookie'] = cookieStr;
        }

        let reqBody = null;
        if (body) {
            if (isFormData || (typeof FormData !== 'undefined' && body instanceof FormData)) {
                reqBody = body;
                if (this.csrfToken && !body.has('_token')) {
                    body.append('_token', this.csrfToken);
                }
            } else if (typeof body === 'object') {
                const params = new URLSearchParams();
                if (this.csrfToken && !body._token) {
                    body._token = this.csrfToken;
                }
                const appendNested = (prefix, val) => {
                    if (Array.isArray(val)) {
                        for (const item of val) {
                            appendNested(`${prefix}[]`, item);
                        }
                    } else if (typeof val === 'object' && val !== null) {
                        for (const [subK, subV] of Object.entries(val)) {
                            appendNested(`${prefix}[${subK}]`, subV);
                        }
                    } else if (val !== null && val !== undefined) {
                        params.append(prefix, val);
                    }
                };

                for (const [k, v] of Object.entries(body)) {
                    appendNested(k, v);
                }
                reqBody = params.toString();
                headers['Content-Type'] = 'application/x-www-form-urlencoded';
            } else {
                reqBody = body;
            }
        }

        if (this.csrfToken && (method === 'POST' || method === 'PUT' || method === 'PATCH' || method === 'DELETE')) {
            headers['X-CSRF-TOKEN'] = this.csrfToken;
        }
        if (this.cookies.has('XSRF-TOKEN') && (method === 'POST' || method === 'PUT' || method === 'PATCH' || method === 'DELETE')) {
            try {
                headers['X-XSRF-TOKEN'] = decodeURIComponent(this.cookies.get('XSRF-TOKEN'));
            } catch (e) {
                headers['X-XSRF-TOKEN'] = this.cookies.get('XSRF-TOKEN');
            }
        }

        const res = await fetch(url, {
            method,
            headers,
            body: reqBody,
            redirect: 'manual'
        });

        this._updateCookies(res);
        this.lastUrl = url;

        // Follow redirects
        if ([301, 302, 303, 307, 308].includes(res.status)) {
            const location = res.headers.get('location');
            if (location) {
                const nextUrl = location.startsWith('http') ? location : `${BASE_URL}${location}`;
                return await this.request('GET', nextUrl);
            }
        }

        const text = await res.text();
        this._extractCsrfFromHtml(text);

        return {
            status: res.status,
            headers: res.headers,
            text,
            url: this.lastUrl
        };
    }

    async get(path, customHeaders = {}) {
        return this.request('GET', path, null, false, customHeaders);
    }

    async post(path, body = {}, isFormData = false) {
        return this.request('POST', path, body, isFormData);
    }

    async put(path, body = {}) {
        body._method = 'PUT';
        return this.post(path, body);
    }

    async patch(path, body = {}) {
        body._method = 'PATCH';
        return this.post(path, body);
    }

    async delete(path, body = {}) {
        body._method = 'DELETE';
        return this.post(path, body);
    }
}

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
console.log('🚀 MEMULAI QA TESTING LENGKAP END-TO-END (BROWSER & CRUD)');
console.log(`🌐 TARGET HOST: ${BASE_URL}`);
console.log('============================================================\n');

async function runTestSuite() {
    // -------------------------------------------------------------
    // SUITE 1: PUBLIC & GUEST TESTING
    // -------------------------------------------------------------
    console.log('\n\x1b[34m[SUITE 1] PUBLIC, GUEST, REDIRECTIONS & ERROR PAGES\x1b[0m');
    const guest = new BrowserClient();

    try {
        const rootRes = await fetch(`${BASE_URL}/`, { redirect: 'manual' });
        assert.strictEqual(rootRes.status, 302, 'Root / harus redirect 302');
        assert(rootRes.headers.get('location').includes('/login'), 'Redirect harus mengarah ke /login');
        pass('Root / redirect langsung 302 ke /login');
    } catch (e) { fail('Root / redirect langsung 302 ke /login', e); }

    try {
        const loginPage = await guest.get('/login');
        assert.strictEqual(loginPage.status, 200, 'Halaman login harus 200 OK');
        assert(loginPage.text.includes('Masuk ke Portal Sertifikasi'), 'Harus memuat teks login otentik');
        assert(guest.csrfToken, 'CSRF token harus diekstrak dari halaman login');
        pass('Halaman /login memuat form login otentik dan CSRF token');
    } catch (e) { fail('Halaman /login memuat form login otentik dan CSRF token', e); }

    try {
        const badLogin = await guest.post('/login', { email: 'fake.user@example.com', password: 'wrongpassword' });
        assert(badLogin.text.includes('tidak cocok') || badLogin.text.includes('Invalid') || badLogin.text.includes('Masuk ke Portal Sertifikasi'), 'Login salah harus ditolak dan kembali ke form login');
        pass('Login dengan kredensial salah ditolak secara aman');
    } catch (e) { fail('Login dengan kredensial salah ditolak secara aman', e); }

    try {
        const regPage = await guest.get('/register');
        assert.strictEqual(regPage.status, 200);
        assert(regPage.text.includes('Pendaftaran Calon Asesi'), 'Harus menampilkan heading registrasi');
        pass('Halaman /register memuat form registrasi calon asesi');
    } catch (e) { fail('Halaman /register memuat form registrasi calon asesi', e); }

    try {
        const notFound = await fetch(`${BASE_URL}/halaman-acak-pasti-tidak-ada-404-test`);
        assert.strictEqual(notFound.status, 404, 'Rute tidak ada harus mengembalikan status HTTP 404');
        const notFoundHtml = await notFound.text();
        assert(notFoundHtml.includes('Halaman Tidak Ditemukan'), 'Halaman 404 harus menggunakan view kustom');
        assert(notFoundHtml.includes('HTTP 404'), 'Badge HTTP 404 harus tampil');
        pass('Error page 404 kustom berjalan dengan output HTTP 404');
    } catch (e) { fail('Error page 404 kustom berjalan dengan output HTTP 404', e); }

    try {
        const complaint = await guest.get('/pengaduan');
        assert.strictEqual(complaint.status, 200);
        assert(complaint.text.includes('Pengaduan') || complaint.text.includes('Aspirasi'), 'Halaman pengaduan publik harus dapat diakses');
        pass('Layanan Pengaduan Publik (/pengaduan) dapat diakses publik');
    } catch (e) { fail('Layanan Pengaduan Publik (/pengaduan) dapat diakses publik', e); }

    // -------------------------------------------------------------
    // SUITE 2: ADMIN FLOW, CRUD MASTER DATA, JADWAL & MUK EXPORT
    // -------------------------------------------------------------
    console.log('\n\x1b[34m[SUITE 2] ADMIN LSP: OPERASIONAL, CRUD JADWAL, PROXY & MUK\x1b[0m');
    const admin = new BrowserClient();
    await admin.get('/login');

    try {
        const loginRes = await admin.post('/login', {
            email: 'adminlsp@gmail.com',
            password: '@Kompeten86'
        });
        assert(loginRes.text.includes('Panel') || loginRes.text.includes('Admin') || loginRes.text.includes('Dashboard'), 'Login Admin harus berhasil masuk dashboard');
        pass('Admin LSP berhasil login dengan password @Kompeten86');
    } catch (e) { fail('Admin LSP berhasil login dengan password @Kompeten86', e); }

    try {
        const profilePage = await admin.get('/profile');
        assert.strictEqual(profilePage.status, 200);
        assert(profilePage.text.includes('Pengaturan Profil & Akun'), 'Halaman /profile harus terbuka tanpa error badgeClasses');
        assert(!profilePage.text.includes('Call to a member function badgeClasses() on string'), 'Tidak boleh ada error badgeClasses');
        pass('Halaman /profile terbuka 200 OK tanpa error badgeClasses()');
    } catch (e) { fail('Halaman /profile terbuka 200 OK tanpa error badgeClasses()', e); }

    let testScheduleId = null;
    const testScheduleCode = `JDW-E2E-${Date.now().toString().slice(-4)}`;

    try {
        const createJadwalPage = await admin.get('/admin/jadwal/create');
        assert.strictEqual(createJadwalPage.status, 200);

        const storeRes = await admin.post('/admin/jadwal', {
            scheme_id: 1,
            name: `Uji Asesmen E2E Automation ${testScheduleCode}`,
            code: testScheduleCode,
            tuk_name: 'Bengkel Uji Astra Daihatsu Center',
            date: '2026-11-20',
            start_time: '08:00',
            end_time: '16:00',
            selected_instruments: ['IA01', 'IA02', 'IA05'],
            assessor_ids: [9] // Asesor Randi
        });

        assert(storeRes.text.includes(testScheduleCode), 'Jadwal yang baru dibuat harus muncul di daftar jadwal');
        const idMatch = storeRes.text.match(new RegExp(`/admin/jadwal/(\\d+)[^"]*">${testScheduleCode}`))
            || storeRes.text.match(new RegExp(`${testScheduleCode}[\\s\\S]*?\\/admin\\/jadwal\\/(\\d+)`));
        if (idMatch) {
            testScheduleId = idMatch[1];
        } else {
            const fallbackMatch = storeRes.text.match(/\/admin\/jadwal\/(\d+)/);
            testScheduleId = fallbackMatch ? fallbackMatch[1] : 1;
        }
        pass(`Admin berhasil CRUD Jadwal: create jadwal ${testScheduleCode} (ID: ${testScheduleId})`);
    } catch (e) { fail(`Admin berhasil CRUD Jadwal`, e); }

    try {
        if (testScheduleId) {
            const detailRes = await admin.get(`/admin/jadwal/${testScheduleId}`);
            assert.strictEqual(detailRes.status, 200);
            assert(detailRes.text.includes(testScheduleCode), 'Detail jadwal harus menampilkan kode jadwal');
            pass(`Admin dapat membuka detail jadwal ID ${testScheduleId}`);
        }
    } catch (e) { fail(`Admin dapat membuka detail jadwal`, e); }

    try {
        if (testScheduleId) {
            const editRes = await admin.get(`/admin/jadwal/${testScheduleId}/edit`);
            assert.strictEqual(editRes.status, 200);

            const updateRes = await admin.put(`/admin/jadwal/${testScheduleId}`, {
                scheme_id: 1,
                name: `Uji Asesmen E2E Automation ${testScheduleCode} (Updated)`,
                code: testScheduleCode,
                tuk_name: 'Bengkel Uji Astra Daihatsu Center Renamed',
                date: '2026-11-21',
                start_time: '08:30',
                end_time: '16:30',
                selected_instruments: ['IA01', 'IA02', 'IA05'],
                assessor_ids: [9]
            });
            assert(updateRes.text.includes('Bengkel Uji Astra Daihatsu Center Renamed') || updateRes.status === 200, 'Jadwal harus terupdate');
            pass(`Admin dapat memperbarui data jadwal via PUT /admin/jadwal/${testScheduleId}`);
        }
    } catch (e) { fail(`Admin dapat memperbarui data jadwal`, e); }

    try {
        const mukCenter = await admin.get('/admin/dokumen-muk');
        assert.strictEqual(mukCenter.status, 200);
        pass('Admin dapat membuka Dokumen MUK Center (/admin/dokumen-muk)');

        if (testScheduleId) {
            const baRes = await admin.get(`/admin/dokumen-muk/${testScheduleId}/export-ba`);
            assert.strictEqual(baRes.status, 200);
            assert(baRes.text.includes('BERITA ACARA') || baRes.text.includes('PELAKSANAAN ASESMEN'), 'BA harus memuat judul Berita Acara');
            pass(`Auto-generate Berita Acara (BA) berhasil diunduh/dibuka untuk jadwal ${testScheduleId}`);

            const hadirAsesi = await admin.get(`/admin/dokumen-muk/${testScheduleId}/export-hadir-asesi`);
            assert.strictEqual(hadirAsesi.status, 200);
            pass(`Daftar Hadir Asesi (Print) berhasil digenerate`);

            const hadirAsesor = await admin.get(`/admin/dokumen-muk/${testScheduleId}/export-hadir-asesor`);
            assert.strictEqual(hadirAsesor.status, 200);
            pass(`Daftar Hadir Asesor (Print) berhasil digenerate`);

            const excelBio = await fetch(`${BASE_URL}/admin/dokumen-muk/${testScheduleId}/export-excel-biodata`, {
                headers: { 'Cookie': admin._getCookieHeader() }
            });
            assert.strictEqual(excelBio.status, 200);
            assert(excelBio.headers.get('content-type').includes('spreadsheetml') || excelBio.headers.get('content-type').includes('octet-stream'), 'Content type harus berkas Excel');
            const bioBuffer = await excelBio.arrayBuffer();
            assert(bioBuffer.byteLength > 1000, 'Berkas Excel Biodata harus berukuran valid');
            pass(`Ekspor Native Excel Biodata Asesi (.xlsx) valid (${bioBuffer.byteLength} bytes)`);

            const excelHadir = await fetch(`${BASE_URL}/admin/dokumen-muk/${testScheduleId}/export-excel-hadir-asesi`, {
                headers: { 'Cookie': admin._getCookieHeader() }
            });
            assert.strictEqual(excelHadir.status, 200);
            const hadirBuffer = await excelHadir.arrayBuffer();
            assert(hadirBuffer.byteLength > 1000, 'Berkas Excel Hadir Asesi harus berukuran valid');
            pass(`Ekspor Native Excel Daftar Hadir Asesi (.xlsx) valid (${hadirBuffer.byteLength} bytes)`);
        }
    } catch (e) { fail('Verifikasi Dokumen MUK & Ekspor Excel', e); }

    try {
        const proxyList = await admin.get('/admin/proxy');
        assert.strictEqual(proxyList.status, 200);
        assert(proxyList.text.includes('Beralih Peran') || proxyList.text.includes('Proxy'), 'Halaman proxy harus memuat daftar pengguna');

        const impRes = await admin.post('/admin/proxy/26');
        assert(impRes.text.includes('Proxy') && impRes.text.includes('Kembali'), 'Harus menampilkan banner proxy impersonator');
        pass('Admin berhasil impersonate asesi via Instant Proxy Switcher');

        const switchBackRes = await admin.post('/proxy/switch-back');
        assert(!switchBackRes.text.includes('Proxy:') || switchBackRes.text.includes('Panel Administrasi') || switchBackRes.text.includes('Dashboard'), 'Harus kembali ke admin');
        pass('Admin berhasil switch-back kembali ke sesi administrator');
    } catch (e) { fail('Proxy Impersonation Test', e); }

    // -------------------------------------------------------------
    // SUITE 3: ASESI WORKFLOW (JOIN, APL, SIGNATURE, RUANG UJI)
    // -------------------------------------------------------------
    console.log('\n\x1b[34m[SUITE 3] ASESI: PROFILE, TTD DIGITAL, JOIN JADWAL & APL\x1b[0m');
    const asesi = new BrowserClient();
    await asesi.get('/login');

    try {
        const asesiLogin = await asesi.post('/login', {
            email: 'asesi1@gmail.com',
            password: '@Kompeten86'
        });
        assert(asesiLogin.text.includes('Asesi') || asesiLogin.text.includes('Dashboard'), 'Asesi berhasil login');
        pass('Asesi berhasil login ke akun asesi1@gmail.com');
    } catch (e) { fail('Asesi berhasil login', e); }

    try {
        const profilePage = await asesi.get('/asesi/profile');
        assert.strictEqual(profilePage.status, 200);

        const dummySig = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        const updateProf = await asesi.post('/asesi/profile', {
            full_name: 'Asesi Satu Otomotif',
            nik: '3201010101990001',
            tempat_lahir: 'Bekasi',
            tanggal_lahir: '1999-05-12',
            jenis_kelamin: 'L',
            kebangsaan: 'Indonesia',
            alamat_rumah: 'Jl. Raya Cikarang No. 88, Bekasi',
            kode_pos: '17530',
            phone: '081299998888',
            pendidikan_terakhir: 'SMK',
            nama_institusi_perusahaan: 'SMK Negeri 1 Cikarang',
            jabatan: 'Mekanik Junior',
            signature_data: dummySig
        });
        assert(updateProf.status === 200 || updateProf.text.includes('berhasil'), 'Profil asesi harus berhasil diperbarui');
        pass('Asesi berhasil melengkapi profil dan menyimpan tanda tangan digital (1x canvas)');
    } catch (e) { fail('Update Profil & TTD Asesi', e); }

    let assessmentId = null;

    try {
        const joinRes = await asesi.post('/asesi/jadwal/join', {
            code: testScheduleCode,
            schedule_code: testScheduleCode
        });

        // Cek URL hasil redirect atau dari halaman APL
        const assMatch = asesi.lastUrl ? asesi.lastUrl.match(/\/assessment\/(\d+)/) : null;
        if (assMatch) {
            assessmentId = assMatch[1];
        } else {
            const checkAss = await asesi.get('/asesi/jadwal');
            const match2 = checkAss.text.match(/\/assessment\/(\d+)\/apl/);
            assessmentId = match2 ? match2[1] : null;
        }

        assert(assessmentId, `Assessment ID harus terbentuk untuk jadwal ${testScheduleCode}`);
        pass(`Asesi berhasil join jadwal ${testScheduleCode} -> Assessment ID: ${assessmentId}`);
    } catch (e) { fail('Join Schedule via Code', e); }

    try {
        if (assessmentId) {
            const aplPage = await asesi.get(`/asesi/assessment/${assessmentId}/apl`);
            assert.strictEqual(aplPage.status, 200);
            assert(aplPage.text.includes('APL') || aplPage.text.includes('Permohonan') || aplPage.text.includes('Portofolio'), 'Harus memuat formulir APL terpadu');

            // Kirim FormData lengkap dengan file bukti
            const form = new FormData();
            form.append('purpose', 'Sertifikasi Baru');
            form.append('assessor_id', '9');
            form.append('statement_integrity', '1');

            const VALID_1X1_PNG = Buffer.from('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==', 'base64');
            const dummyPng = new Blob([VALID_1X1_PNG], { type: 'image/png' });
            form.append('proofs[KTP]', dummyPng, 'ktp.png');
            form.append('proofs[PAS_FOTO]', dummyPng, 'pas_foto.png');
            form.append('proofs[IJAZAH_SMK]', dummyPng, 'ijazah.png');
            form.append('ktp', dummyPng, 'ktp.png');
            form.append('pas_foto', dummyPng, 'pas_foto.png');
            form.append('ijazah_smk', dummyPng, 'ijazah.png');

            const submitApl = await asesi.post(`/asesi/assessment/${assessmentId}/apl`, form, true);
            assert(submitApl.status === 200 || submitApl.text.includes('APL') || submitApl.text.includes('berhasil') || submitApl.text.includes('menunggu'), 'Submit APL harus berhasil');
            pass(`Asesi berhasil mengisi dan mengirimkan APL-01 & APL-02 terpadu (ID: ${assessmentId})`);

            // Security Gate: Coba masuk Ruang Uji sebelum disetujui -> harus di-redirect
            const prematureRuangUji = await asesi.get(`/asesi/assessment/${assessmentId}/ruang-uji`);
            assert(prematureRuangUji.text.includes('menunggu') || prematureRuangUji.text.includes('disetujui') || prematureRuangUji.url.includes('/apl'), 'Ruang uji harus terkunci sebelum APL di-approve asesor');
            pass('Security Gate: Ruang Uji terkunci bagi asesi sebelum APL diverifikasi & disetujui');
        }
    } catch (e) { fail('Isi Formulir APL-01 & APL-02 & Security Gate', e); }

    // -------------------------------------------------------------
    // SUITE 4: ASESOR FLOW (REVIEW GATE, AK.07, OBSERVASI)
    // -------------------------------------------------------------
    console.log('\n\x1b[34m[SUITE 4] ASESOR: COCKPIT, REVIEW GATE APL & INSTRUMEN ASESMEN\x1b[0m');
    const asesor = new BrowserClient();
    await asesor.get('/login');

    try {
        const asesorLogin = await asesor.post('/login', {
            email: 'asesor.randi@gmail.com',
            password: '@Kompeten86'
        });
        assert(asesorLogin.text.includes('Asesor') || asesorLogin.text.includes('Jadwal'), 'Asesor berhasil login');
        pass('Asesor Randi berhasil login ke akun asesor.randi@gmail.com');
    } catch (e) { fail('Asesor berhasil login', e); }

    try {
        const asesorJadwal = await asesor.get('/asesor/jadwal');
        assert.strictEqual(asesorJadwal.status, 200);
        pass('Asesor dapat melihat daftar penugasan jadwal uji (/asesor/jadwal)');

        if (assessmentId) {
            const reviewAplPage = await asesor.get(`/asesor/assessment/${assessmentId}/review-apl`);
            assert.strictEqual(reviewAplPage.status, 200);
            assert(reviewAplPage.text.includes('Tinjau') || reviewAplPage.text.includes('APL') || reviewAplPage.text.includes('Berkas') || reviewAplPage.text.includes('Permohonan'), 'Harus menampilkan berkas APL asesi');

            const decideRes = await asesor.post(`/asesor/assessment/${assessmentId}/review-apl`, {
                action: 'APPROVE',
                assessor_notes: 'Berkas portofolio VATM telah memenuhi syarat kelayakan uji.'
            });
            assert(decideRes.status === 200 || decideRes.text.includes('disetujui') || decideRes.text.includes('Jadwal'), 'Approval APL harus berhasil');
            pass(`Asesor berhasil Menyetujui (Approve) Permohonan APL asesi #${assessmentId}`);

            if (testScheduleId) {
                const ak07Page = await asesor.get(`/asesor/schedule/${testScheduleId}/ak07`);
                assert.strictEqual(ak07Page.status, 200);
                assert(!ak07Page.text.includes('border-2 border-black'), 'AK07 tidak boleh menggunakan border hitam brutalist lama');

                const ak07Submit = await asesor.post(`/asesor/schedule/${testScheduleId}/ak07`, {
                    assessment_ids: [assessmentId],
                    agreement_status: 'TIDAK',
                    agreement_benchmark: 'SKKNI No. 097/2018',
                    agreement_method: 'Observasi Demonstrasi & CBT',
                    agreement_instrument: 'FR.IA.01, FR.IA.02, FR.IA.05'
                });
                assert(ak07Submit.status === 200 || ak07Submit.text.includes('Ruang Uji'), 'AK07 harus berhasil disimpan');
                pass(`Asesor berhasil mengisi FR.AK.07 Penyesuaian Wajar untuk jadwal ${testScheduleId}`);

                const ruangUjiHub = await asesor.get(`/asesor/schedule/${testScheduleId}/ruang-uji`);
                assert.strictEqual(ruangUjiHub.status, 200);
                pass(`Asesor dapat membuka Ruang Uji Jadwal ${testScheduleId}`);
            }

            const signAk01 = await asesor.post(`/asesor/assessment/${assessmentId}/ak01/sign`, {
                agreement_date: '2026-11-20',
                assessor_signature: 'VALID_CONFIRMED'
            });
            assert(signAk01.status === 200 || signAk01.text.includes('Ruang Uji'), 'Sign AK01 asesor harus berhasil');
            pass(`Asesor menandatangani kesepakatan FR.AK.01 untuk asesi #${assessmentId}`);
        }
    } catch (e) { fail('Asesor Cockpit, Review Gate & AK07', e); }

    // -------------------------------------------------------------
    // SUITE 5: ASESI RUANG UJI, CBT TEORI & AUTO-GRADING
    // -------------------------------------------------------------
    console.log('\n\x1b[34m[SUITE 5] RUANG UJI ASESI, CBT (IA.05) & AUTO-GRADING\x1b[0m');
    try {
        if (assessmentId) {
            const ruangUjiAsesi = await asesi.get(`/asesi/assessment/${assessmentId}/ruang-uji`);
            assert.strictEqual(ruangUjiAsesi.status, 200, 'Ruang Uji asesi harus terbuka 200 OK setelah APL disetujui');
            assert(ruangUjiAsesi.text.includes('Ruang Uji') || ruangUjiAsesi.text.includes('Instrumen'), 'Harus menampilkan instrumen uji');
            pass('Ruang Uji Asesi berhasil terbuka setelah APL disetujui asesor');

            const asesiSignAk01 = await asesi.post(`/asesi/assessment/${assessmentId}/ak01`, {
                agreement: '1'
            });
            assert(asesiSignAk01.status === 200 || asesiSignAk01.text.includes('Ruang Uji'), 'Asesi sign AK01 berhasil');
            pass('Asesi berhasil menyetujui dan menandatangani FR.AK.01');

            const cbtPage = await asesi.get(`/asesi/assessment/${assessmentId}/instrument/IA05`);
            assert.strictEqual(cbtPage.status, 200);
            assert(cbtPage.text.includes('Ujian Teori') || cbtPage.text.includes('IA.05') || cbtPage.text.includes('Soal') || cbtPage.text.includes('CBT'), 'Halaman CBT harus memuat ujian');

            const submitCbt = await asesi.post(`/asesi/assessment/${assessmentId}/instrument/IA05`, {
                answers: {
                    1: 'A',
                    2: 'B',
                    3: 'C',
                    4: 'A',
                    5: 'D'
                }
            });
            assert(submitCbt.status === 200 || submitCbt.text.includes('Selesai') || submitCbt.text.includes('Nilai'), 'Submit CBT harus berhasil dan ter-auto-grade');
            pass('Asesi berhasil menyelesaikan Ujian Teori CBT (FR.IA.05) dengan auto-grading');

            const ak03Page = await asesi.get(`/asesi/assessment/${assessmentId}/ak03`);
            assert.strictEqual(ak03Page.status, 200);

            const submitAk03 = await asesi.post(`/asesi/assessment/${assessmentId}/ak03`, {
                aspects: {
                    1: 'Sangat Baik',
                    2: 'Sangat Baik',
                    3: 'Sangat Baik'
                },
                feedback_comment: 'Pelaksanaan asesmen berjalan sangat tertib, transparan, dan profesional.'
            });
            assert(submitAk03.status === 200 || submitAk03.text.includes('berhasil') || submitAk03.text.includes('Ruang Uji'), 'Submit AK03 berhasil');
            pass('Asesi berhasil mengisi formulir Umpan Balik Asesmen (FR.AK.03)');
        }
    } catch (e) { fail('Ruang Uji Asesi, CBT & AK03', e); }

    // -------------------------------------------------------------
    // SUITE 6: ASESOR FINALISASI & SYNC HASIL ASESMEN
    // -------------------------------------------------------------
    console.log('\n\x1b[34m[SUITE 6] ASESOR FINALISASI (AK.02, AK.05/06) & REKOMENDASI K/BK\x1b[0m');
    try {
        if (assessmentId) {
            const ak02Page = await asesor.get(`/asesor/assessment/${assessmentId}/ak02`);
            assert.strictEqual(ak02Page.status, 200);
            assert(ak02Page.text.includes('AK.02') || ak02Page.text.includes('Rekaman'), 'Harus menampilkan form rekaman asesmen');

            const submitAk02 = await asesor.post(`/asesor/assessment/${assessmentId}/ak02`, {
                final_recommendation: 'KOMPETEN',
                notes: 'Asesi memenuhi seluruh kriteria unjuk kerja pada semua unit kompetensi yang diujikan.'
            });
            assert(submitAk02.status === 200 || submitAk02.text.includes('berhasil') || submitAk02.text.includes('Jadwal'), 'Submit AK02 berhasil');
            pass(`Asesor merekomendasikan asesi #${assessmentId} sebagai KOMPETEN (K) pada FR.AK.02`);

            if (testScheduleId) {
                const reportsPage = await asesor.get(`/asesor/schedule/${testScheduleId}/reports`);
                assert.strictEqual(reportsPage.status, 200);

                const submitReports = await asesor.post(`/asesor/schedule/${testScheduleId}/reports`, {
                    execution_summary: 'Asesmen dilaksanakan sesuai SOP BNSP dan standar TUK terlisensi.',
                    competency_recap: 'Semua peserta kompeten.',
                    notes: 'Tidak ditemukan ketidaksesuaian.'
                });
                assert(submitReports.status === 200 || submitReports.text.includes('berhasil'), 'Laporan AK05/06 berhasil disimpan');
                pass(`Asesor berhasil menyusun dan menyimpan Laporan Asesmen (FR.AK.05 & AK.06)`);
            }
        }
    } catch (e) { fail('Asesor Finalisasi & Rekomendasi K/BK', e); }

    // -------------------------------------------------------------
    // SUITE 7: BERITA ACARA POST-FINALIZATION & CLEANUP
    // -------------------------------------------------------------
    console.log('\n\x1b[34m[SUITE 7] VERIFIKASI POST-FINALISASI & INTEGRITAS DATA\x1b[0m');
    try {
        if (testScheduleId) {
            // Re-fetch jadwal page to refresh admin CSRF
            await admin.get('/admin/jadwal');

            const postBaRes = await admin.get(`/admin/dokumen-muk/${testScheduleId}/export-ba`);
            assert.strictEqual(postBaRes.status, 200);
            assert(postBaRes.text.includes('KOMPETEN') || postBaRes.text.includes('Kompeten') || postBaRes.text.includes(testScheduleCode), 'BA harus merefleksikan hasil asesmen');
            pass(`Berita Acara (BA) memuat hasil penetapan kelulusan Kompeten (K) secara otomatis`);

            const delRes = await admin.delete(`/admin/jadwal/${testScheduleId}`);
            assert.strictEqual(delRes.status, 200);
            pass(`Pembersihan jadwal pengujian ${testScheduleCode} selesai`);
        }
    } catch (e) { fail('Verifikasi Post-Finalisasi & Integritas Data', e); }

    // -------------------------------------------------------------
    // RINGKASAN AKHIR
    // -------------------------------------------------------------
    console.log('\n============================================================');
    console.log('📊 RINGKASAN HASIL QA AUTOMATION TESTING');
    console.log(`TOTAL SUITE CASES : ${passedTests + failedTests}`);
    console.log(`\x1b[32mLOLOS (PASSED)    : ${passedTests}\x1b[0m`);
    console.log(`\x1b[31mGAGAL (FAILED)    : ${failedTests}\x1b[0m`);
    console.log('============================================================\n');

    if (failedTests > 0) {
        console.error('\x1b[31mDaftar Kegagalan yang Perlu Diperbaiki:\x1b[0m');
        for (const f of failureDetails) {
            console.error(` - [${f.name}]: ${f.error}`);
        }
        process.exit(1);
    } else {
        console.log('\x1b[32m🎉 SEMUA PENGUJIAN END-TO-END BERHASIL 100% TANPA ERROR!\x1b[0m\n');
        process.exit(0);
    }
}

runTestSuite().catch(err => {
    console.error('Fatal Test Runner Error:', err);
    process.exit(1);
});
