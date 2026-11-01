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
        return await this.request('GET', path, null, false, customHeaders);
    }

    async post(path, body = null, isFormData = false) {
        return await this.request('POST', path, body, isFormData);
    }

    async put(path, body = null) {
        if (body && typeof body === 'object' && !(body instanceof FormData)) {
            body._method = 'PUT';
            return await this.request('POST', path, body);
        }
        return await this.request('PUT', path, body);
    }

    async patch(path, body = null) {
        if (body && typeof body === 'object' && !(body instanceof FormData)) {
            body._method = 'PATCH';
            return await this.request('POST', path, body);
        }
        return await this.request('PATCH', path, body);
    }

    async delete(path, body = null) {
        if (body && typeof body === 'object' && !(body instanceof FormData)) {
            body._method = 'DELETE';
            return await this.request('POST', path, body);
        }
        return await this.request('DELETE', path, { _method: 'DELETE', ...(body || {}) });
    }
}

async function runSideFeaturesQA() {
    console.log('============================================================');
    console.log('🔍 MEMULAI PENGUJIAN SEMUA FITUR TAMBAHAN (SIDE FEATURES)');
    console.log(`🌐 TARGET HOST: ${BASE_URL}`);
    console.log('============================================================\n');

    let passed = 0;
    let failed = 0;
    const failures = [];

    const pass = (desc) => {
        passed++;
        console.log(`  \x1b[32m✓ PASS:\x1b[0m ${desc}`);
    };

    const fail = (desc, err) => {
        failed++;
        console.error(`  \x1b[31m✗ FAIL:\x1b[0m ${desc}`);
        console.error(`    Reason: ${err.message || err}`);
        failures.push({ desc, error: err.message || err });
    };

    // -------------------------------------------------------------
    // GROUP 1: PUBLIC & CASCADING WILAYAH API
    // -------------------------------------------------------------
    console.log('\x1b[34m[GROUP 1] PUBLIC: WILAYAH API, PENGADUAN, TRACKING & SURVEILANS\x1b[0m');
    const guest = new BrowserClient();

    try {
        const provRes = await guest.get('/api/wilayah/provinces');
        assert.strictEqual(provRes.status, 200);
        const provs = JSON.parse(provRes.text);
        assert(Array.isArray(provs) && provs.length > 0, 'Daftar provinsi harus array non-kosong');
        pass(`API Wilayah: GET /api/wilayah/provinces sukses (${provs.length} provinsi dimuat)`);

        const regRes = await guest.get('/api/wilayah/regencies?province_id=32');
        assert.strictEqual(regRes.status, 200);
        const regencies = JSON.parse(regRes.text);
        assert(Array.isArray(regencies) && regencies.length > 0, 'Daftar kabupaten/kota Jawa Barat valid');
        pass(`API Wilayah: GET /api/wilayah/regencies?province_id=32 sukses (${regencies.length} kab/kota dimuat)`);

        const distRes = await guest.get('/api/wilayah/districts?regency_id=3201');
        assert.strictEqual(distRes.status, 200);
        const districts = JSON.parse(distRes.text);
        assert(Array.isArray(districts) && districts.length > 0, 'Daftar kecamatan Kab. Bogor valid');
        pass(`API Wilayah: GET /api/wilayah/districts?regency_id=3201 sukses (${districts.length} kecamatan dimuat)`);

        const vilRes = await guest.get('/api/wilayah/villages?district_id=320101');
        assert.strictEqual(vilRes.status, 200);
        const villages = JSON.parse(vilRes.text);
        assert(Array.isArray(villages) && villages.length > 0, 'Daftar kelurahan/desa valid');
        pass(`API Wilayah: GET /api/wilayah/villages?district_id=320101 sukses (${villages.length} kelurahan/desa dimuat)`);
    } catch (e) { fail('API Wilayah Cascading Dropdown', e); }

    let createdTicket = null;
    try {
        const pengaduanPage = await guest.get('/pengaduan');
        assert.strictEqual(pengaduanPage.status, 200);
        assert(pengaduanPage.text.includes('Pengaduan') || pengaduanPage.text.includes('Aspirasi'), 'Halaman form pengaduan');
        pass('Public: Form Layanan Pengaduan (/pengaduan) render 200 OK');

        const postPengaduan = await guest.post('/pengaduan', {
            nama_pelapor: 'Masyarakat Peduli Mutu',
            email: 'peduli.mutu@example.com',
            telepon: '081234567890',
            jenis_responden: 'Umum',
            judul: 'Pengaduan Pelayanan Uji Kompetensi Otomotif',
            isi_aduan: 'Fasilitas sarana bengkel sangat baik dan pelayanan asesor ramah dan profesional.'
        });
        assert.strictEqual(postPengaduan.status, 200);
        const ticketMatch = postPengaduan.text.match(/PGD-[A-Z0-9-]+/);
        createdTicket = ticketMatch ? ticketMatch[0] : null;
        pass(`Public: Pengajuan pengaduan masyarakat sukses diterbitkan (No Tiket: ${createdTicket || 'PGD-OK'})`);

        if (createdTicket) {
            const trackRes = await guest.get(`/pengaduan/tracking?ticket=${createdTicket}`);
            assert.strictEqual(trackRes.status, 200);
            assert(trackRes.text.includes(createdTicket) || trackRes.text.includes('diproses'), 'Tracking tiket valid');
            pass(`Public: Pelacakan tiket pengaduan (/pengaduan/tracking?ticket=${createdTicket}) memuat status dengan benar`);
        }
    } catch (e) { fail('Public: Pengaduan & Tracking Tiket', e); }

    try {
        const survPage = await guest.get('/surveillance');
        assert.strictEqual(survPage.status, 200);
        assert(survPage.text.includes('Surveilans') || survPage.text.includes('Pemegang Sertifikat'), 'Halaman surveilans');
        pass('Public: Form Surveilans Pemegang Sertifikat (/surveillance) render 200 OK');

        const postSurv = await guest.post('/surveillance', {
            nama_lengkap: 'Budi Santoso Pemegang Sertifikat',
            nik: '3201010101880005',
            email: 'budi.santoso@alumni.proindo.test',
            telepon: '081234567890',
            no_sertifikat: 'SRT-BNSP-AUTO-2026-001',
            no_registrasi: 'REG-BNSP-AUTO-9988',
            tempat_kerja: 'Bengkel Resmi Toyota Cikarang',
            jabatan: 'Senior Service Technician',
            kesesuaian_skk: 'Y',
            keterangan: 'Rutin menangani tune up sistem injeksi bahan bakar dan servis berkala roda empat.'
        });
        assert.strictEqual(postSurv.status, 200);
        pass('Public: Laporan pemeliharaan kompetensi surveilans berhasil dikirimkan');
    } catch (e) { fail('Public: Pelaporan Surveilans', e); }

    // -------------------------------------------------------------
    // GROUP 2: ADMIN MASTER DATA & USER MANAGEMENT CRUD
    // -------------------------------------------------------------
    console.log('\n\x1b[34m[GROUP 2] ADMIN: USERS, LSP PROFILE, STANDAR, TUK, ASESOR & EVENTS\x1b[0m');
    const admin = new BrowserClient();
    await admin.get('/login');
    const adminLogin = await admin.post('/login', {
        email: 'adminlsp@gmail.com',
        password: '@Kompeten86'
    });
    assert.strictEqual(adminLogin.status, 200);
    pass('Admin: Otentikasi Administrator LSP berhasil');

    let createdUserId = null;
    const testUserEmail = `user.qa.${Date.now()}@example.com`;
    try {
        const usersIndex = await admin.get('/admin/users');
        assert.strictEqual(usersIndex.status, 200);
        assert(usersIndex.text.includes('Manajemen Pengguna') || usersIndex.text.includes('Users') || usersIndex.text.includes('Pengguna'));
        pass('Admin: Index Manajemen Pengguna (/admin/users) render 200 OK');

        const userCreatePage = await admin.get('/admin/users/create');
        assert.strictEqual(userCreatePage.status, 200);

        const postUser = await admin.post('/admin/users', {
            full_name: 'Calon Asesi Baru QA Automated',
            email: testUserEmail,
            phone: '081299988877',
            password: 'PasswordValid123',
            role: 'ASESI',
            status: 'ACTIVE'
        });
        assert.strictEqual(postUser.status, 200);
        assert(postUser.text.includes(testUserEmail) || postUser.text.includes('berhasil'), 'User baru tersimpan');
        pass(`Admin: CRUD Users -> Berhasil create user baru (${testUserEmail})`);

        const editMatch = postUser.text.match(new RegExp(`/admin/users/(\\d+)`));
        if (editMatch) {
            createdUserId = editMatch[1];
            const editPage = await admin.get(`/admin/users/${createdUserId}/edit`);
            assert.strictEqual(editPage.status, 200);

            const putUser = await admin.put(`/admin/users/${createdUserId}`, {
                full_name: 'Calon Asesi Baru QA Automated (Updated)',
                email: testUserEmail,
                phone: '081299988877',
                role: 'ASESI',
                status: 'ACTIVE'
            });
            assert.strictEqual(putUser.status, 200);
            pass(`Admin: CRUD Users -> Berhasil update user ID ${createdUserId}`);

            const delUser = await admin.delete(`/admin/users/${createdUserId}`);
            assert.strictEqual(delUser.status, 200);
            pass(`Admin: CRUD Users -> Berhasil deactivate/delete user ID ${createdUserId}`);
        }
    } catch (e) { fail('Admin: User Management CRUD', e); }

    try {
        const lspProfPage = await admin.get('/admin/lsp-profile');
        assert.strictEqual(lspProfPage.status, 200);
        assert(lspProfPage.text.includes('Profil') || lspProfPage.text.includes('Lisensi') || lspProfPage.text.includes('BNSP'));

        const putProfile = await admin.put('/admin/lsp-profile', {
            nama_lsp: 'Lembaga Sertifikasi Profesi Otomotif PrO Indo',
            kode_lsp: 'LSP-029-IDN',
            no_lisensi_bnsp: 'BNSP-LSP-029-ID',
            nama_ketua: 'Dr. Ir. Bambang Hermanto, M.T.',
            alamat: 'Kawasan Industri Otomotif Cikarang Barat, Bekasi',
            telepon: '02189998888',
            email: 'sekretariat@lsp-proindo.co.id'
        });
        assert.strictEqual(putProfile.status, 200);
        pass('Admin: Pengaturan Profil Lembaga Sertifikasi Profesi (/admin/lsp-profile) berhasil diperbarui');
    } catch (e) { fail('Admin: LSP Profile Management', e); }

    let createdStdId = null;
    const stdCode = `STD-SKKNI-${Date.now().toString().slice(-4)}`;
    try {
        const stdIndex = await admin.get('/admin/standards');
        assert.strictEqual(stdIndex.status, 200);
        assert(stdIndex.text.includes('Standar') || stdIndex.text.includes('SKKNI'));

        const postStd = await admin.post('/admin/standards', {
            kode_standar: stdCode,
            judul_standar: 'Standar Kompetensi Kerja Nasional Bidang Otomotif Kendaraan Ringan',
            jenis_standar: 'SKKNI',
            nomor_sk: 'KEP.116/MEN/VII/2026',
            tahun: 2026,
            is_active: 1
        });
        assert.strictEqual(postStd.status, 200);
        assert(postStd.text.includes(stdCode) || postStd.text.includes('berhasil'));
        pass(`Admin: CRUD Standards -> Berhasil create standar SKKNI (${stdCode})`);

        const stdMatch = postStd.text.match(new RegExp(`/admin/standards/(\\d+)`));
        if (stdMatch) {
            createdStdId = stdMatch[1];
            const putStd = await admin.put(`/admin/standards/${createdStdId}`, {
                kode_standar: stdCode,
                judul_standar: 'Standar Kompetensi Kerja Nasional Bidang Otomotif Kendaraan Ringan (Updated)',
                jenis_standar: 'SKKNI',
                nomor_sk: 'KEP.116/MEN/VII/2026-REV',
                tahun: 2026,
                is_active: 1
            });
            assert.strictEqual(putStd.status, 200);
            pass(`Admin: CRUD Standards -> Berhasil update standar ID ${createdStdId}`);

            const delStd = await admin.delete(`/admin/standards/${createdStdId}`);
            assert.strictEqual(delStd.status, 200);
            pass(`Admin: CRUD Standards -> Berhasil delete standar ID ${createdStdId}`);
        }
    } catch (e) { fail('Admin: Master Standards CRUD', e); }

    let createdTukId = null;
    const tukCode = `TUK-QA-${Date.now().toString().slice(-4)}`;
    try {
        const tukIndex = await admin.get('/admin/tuks');
        assert.strictEqual(tukIndex.status, 200);

        const postTuk = await admin.post('/admin/tuks', {
            kode_tuk: tukCode,
            nama_tuk: `TUK Mandiri Bengkel Modern ${tukCode}`,
            jenis_tuk: 'Sewaktu',
            alamat: 'Jl. Industri Otomotif Blok A No. 12, Bekasi',
            telepon: '0218999111',
            penanggung_jawab: 'Budi Santoso, S.T.',
            pic_phone: '081299988877',
            is_active: 1
        });
        assert.strictEqual(postTuk.status, 200);
        assert(postTuk.text.includes(tukCode) || postTuk.text.includes('berhasil'));
        pass(`Admin: CRUD TUK -> Berhasil create TUK baru (${tukCode})`);

        const tukMatch = postTuk.text.match(new RegExp(`/admin/tuks/(\\d+)`));
        if (tukMatch) {
            createdTukId = tukMatch[1];
            const toggleRes = await admin.patch(`/admin/tuks/${createdTukId}/toggle-status`);
            assert.strictEqual(toggleRes.status, 200);
            pass(`Admin: CRUD TUK -> Berhasil toggle status aktif TUK ID ${createdTukId}`);

            const delTuk = await admin.delete(`/admin/tuks/${createdTukId}`);
            assert.strictEqual(delTuk.status, 200);
            pass(`Admin: CRUD TUK -> Berhasil delete TUK ID ${createdTukId}`);
        }
    } catch (e) { fail('Admin: Master TUK CRUD', e); }

    let createdEventId = null;
    const eventCode = `EVT-QA-${Date.now().toString().slice(-4)}`;
    try {
        const eventIndex = await admin.get('/admin/events');
        assert.strictEqual(eventIndex.status, 200);

        const postEvent = await admin.post('/admin/events', {
            kode_event: eventCode,
            nama_event: `PSKK BNSP Otomotif Kendaraan ${eventCode}`,
            tahun_anggaran: 2026,
            sumber_anggaran: 'APBN BNSP',
            tgl_mulai: '2026-11-10',
            tgl_selesai: '2026-11-20',
            keterangan: 'Program Pelaksanaan Sertifikasi Kompetensi Kerja (PSKK)',
            is_active: 1
        });
        assert.strictEqual(postEvent.status, 200);
        assert(postEvent.text.includes(eventCode) || postEvent.text.includes('berhasil'));
        pass(`Admin: CRUD Events -> Berhasil create event sertifikasi (${eventCode})`);

        const evtMatch = postEvent.text.match(new RegExp(`/admin/events/(\\d+)`));
        if (evtMatch) {
            createdEventId = evtMatch[1];
            const evtDetail = await admin.get(`/admin/events/${createdEventId}`);
            assert.strictEqual(evtDetail.status, 200);

            const putEvent = await admin.put(`/admin/events/${createdEventId}`, {
                kode_event: eventCode,
                nama_event: `PSKK BNSP Otomotif Kendaraan ${eventCode} (Updated)`,
                tahun_anggaran: 2026,
                sumber_anggaran: 'APBN BNSP',
                tgl_mulai: '2026-11-10',
                tgl_selesai: '2026-11-20',
                keterangan: 'Program Pelaksanaan Sertifikasi Kompetensi Kerja (PSKK) Revisi',
                is_active: 1
            });
            assert.strictEqual(putEvent.status, 200);
            pass(`Admin: CRUD Events -> Berhasil update data event ID ${createdEventId}`);

            const delEvt = await admin.delete(`/admin/events/${createdEventId}`);
            assert.strictEqual(delEvt.status, 200);
            pass(`Admin: CRUD Events -> Berhasil delete event ID ${createdEventId}`);
        }
    } catch (e) { fail('Admin: Event Sertifikasi CRUD', e); }

    // -------------------------------------------------------------
    // GROUP 3: ADMIN OPERASIONAL, VERIFIKASI & PLENO
    // -------------------------------------------------------------
    console.log('\n\x1b[34m[GROUP 3] ADMIN: VERIFIKASI APL-01, SIDANG PLENO, PENGADUAN & SURVEILANS\x1b[0m');

    try {
        const verifIndex = await admin.get('/admin/verifications');
        assert.strictEqual(verifIndex.status, 200);
        assert(verifIndex.text.includes('Verifikasi') || verifIndex.text.includes('Permohonan') || verifIndex.text.includes('APL'));
        pass('Admin: Antrean Verifikasi Berkas APL-01 (/admin/verifications) render 200 OK');
    } catch (e) { fail('Admin: Verifications Queue', e); }

    try {
        const plenoIndex = await admin.get('/admin/pleno');
        assert.strictEqual(plenoIndex.status, 200);
        assert(plenoIndex.text.includes('Pleno') || plenoIndex.text.includes('Rapat') || plenoIndex.text.includes('Kelulusan'));
        pass('Admin: Panel Sidang Pleno Penetapan Kelulusan (/admin/pleno) render 200 OK');
    } catch (e) { fail('Admin: Pleno Index', e); }

    try {
        const complaintsIndex = await admin.get('/admin/complaints');
        assert.strictEqual(complaintsIndex.status, 200);
        assert(complaintsIndex.text.includes('Pengaduan') || complaintsIndex.text.includes('Layanan'));
        pass('Admin: Panel Manajemen Pengaduan Publik (/admin/complaints) render 200 OK');

        const cMatch = complaintsIndex.text.match(/\/admin\/complaints\/(\d+)/);
        if (cMatch) {
            const cId = cMatch[1];
            const cShow = await admin.get(`/admin/complaints/${cId}`);
            assert.strictEqual(cShow.status, 200);

            const cPatch = await admin.patch(`/admin/complaints/${cId}`, {
                status: 'selesai',
                tindak_lanjut: 'Aduan telah ditindaklanjuti oleh Bagian Mutu LSP dan perbaikan telah diselesaikan.'
            });
            assert.strictEqual(cPatch.status, 200);
            pass(`Admin: Berhasil update status tindak lanjut pengaduan ID ${cId} -> selesai`);
        }
    } catch (e) { fail('Admin: Manajemen Pengaduan', e); }

    try {
        const survIndex = await admin.get('/admin/surveillance');
        assert.strictEqual(survIndex.status, 200);
        assert(survIndex.text.includes('Surveilans') || survIndex.text.includes('Pemegang Sertifikat'));
        pass('Admin: Panel Laporan Surveilans (/admin/surveillance) render 200 OK');

        const sMatch = survIndex.text.match(/\/admin\/surveillance\/(\d+)\/verify/);
        if (sMatch) {
            const sId = sMatch[1];
            const sPatch = await admin.patch(`/admin/surveillance/${sId}/verify`, {
                status: 'terverifikasi',
                keterangan: 'Dokumen portofolio pekerjaan dan surat tugas tempat kerja telah diverifikasi keabsahannya.'
            });
            assert.strictEqual(sPatch.status, 200);
            pass(`Admin: Berhasil memverifikasi laporan surveilans pemegang sertifikat ID ${sId}`);
        }
    } catch (e) { fail('Admin: Verifikasi Surveilans', e); }

    try {
        const appealsIndex = await admin.get('/admin/appeals');
        assert.strictEqual(appealsIndex.status, 200);
        assert(appealsIndex.text.includes('Banding') || appealsIndex.text.includes('Asesmen') || appealsIndex.text.includes('Appeal'));
        pass('Admin: Panel Layanan Banding Asesmen (/admin/appeals) render 200 OK');
    } catch (e) { fail('Admin: Appeals List', e); }

    // -------------------------------------------------------------
    // GROUP 4: ADMIN PENJAMINAN MUTU, PERSURATAN & NOTIFIKASI
    // -------------------------------------------------------------
    console.log('\n\x1b[34m[GROUP 4] ADMIN: PENJAMINAN MUTU, AUDIT INTERNAL, PERSURATAN & PESAN\x1b[0m');

    try {
        const qDocs = await admin.get('/admin/quality/documents');
        assert.strictEqual(qDocs.status, 200);
        assert(qDocs.text.includes('Dokumen Mutu') || qDocs.text.includes('Mutu') || qDocs.text.includes('SOP'));

        const postQDoc = await admin.post('/admin/quality/documents', {
            document_code: `SOP-LSP-QA-${Date.now().toString().slice(-4)}`,
            title: 'Standar Operasional Prosedur Pelaksanaan Uji Kompetensi',
            category: 'SOP',
            version: '2.0',
            effective_date: '2026-01-01'
        });
        assert.strictEqual(postQDoc.status, 200);
        pass('Admin: Dokumen Mutu (/admin/quality/documents) render 200 OK & berhasil simpan dokumen baru');

        const qAudits = await admin.get('/admin/quality/audits');
        assert.strictEqual(qAudits.status, 200);

        const postAudit = await admin.post('/admin/quality/audits', {
            audit_code: `AUDIT-INT-${Date.now().toString().slice(-4)}`,
            audit_date: '2026-11-01',
            auditor_name: 'Tim Auditor Internal Mutu LSP',
            scope: 'Verifikasi Instrumen Asesmen dan Integritas Blanko Sertifikat',
            findings: 'Sistem operasional telah berjalan sesuai pedoman BNSP 201/202.'
        });
        assert.strictEqual(postAudit.status, 200);
        pass('Admin: Audit Internal Mutu (/admin/quality/audits) render 200 OK & berhasil simpan audit baru');
    } catch (e) { fail('Admin: Penjaminan Mutu & Audit', e); }

    try {
        const lettersIndex = await admin.get('/admin/letters');
        assert.strictEqual(lettersIndex.status, 200);
        assert(lettersIndex.text.includes('Surat') || lettersIndex.text.includes('Agenda'));

        const postLetter = await admin.post('/admin/letters', {
            reference_number: `B/${Date.now().toString().slice(-4)}/LSP-PROINDO/XI/2026`,
            category: 'Surat Keluar',
            subject: 'Pemberitahuan Pelaksanaan Uji Sertifikasi Kompetensi Otomotif',
            recipient: 'Direktur SMK Negeri 1 Cikarang',
            letter_date: '2026-11-01',
            content: 'Bersama surat ini disampaikan jadwal pelaksanaan asesmen kompetensi...'
        });
        assert.strictEqual(postLetter.status, 200);
        pass('Admin: Persuratan Resmi (/admin/letters) render 200 OK & berhasil terbitkan surat baru');
    } catch (e) { fail('Admin: Persuratan', e); }

    try {
        const msgSent = await admin.get('/admin/messages/sent');
        assert.strictEqual(msgSent.status, 200);
        assert(msgSent.text.includes('Pesan') || msgSent.text.includes('Terkirim') || msgSent.text.includes('Sent'));
        pass('Admin: Log Notifikasi Terkirim (/admin/messages/sent) render 200 OK');

        const msgInbox = await admin.get('/admin/messages/inbox');
        assert.strictEqual(msgInbox.status, 200);
        assert(msgInbox.text.includes('Kotak Masuk') || msgInbox.text.includes('Inbox') || msgInbox.text.includes('Pesan'));
        pass('Admin: Kotak Masuk Pesan (/admin/messages/inbox) render 200 OK');
    } catch (e) { fail('Admin: Pesan & Notifikasi', e); }

    // -------------------------------------------------------------
    // GROUP 5: ADMIN KEUANGAN & AKUNTANSI
    // -------------------------------------------------------------
    console.log('\n\x1b[34m[GROUP 5] ADMIN: KEUANGAN, REKENING BANK, PRICING & LAPORAN LABA RUGI\x1b[0m');

    try {
        const bankIndex = await admin.get('/admin/finance/bank-accounts');
        assert.strictEqual(bankIndex.status, 200);
        assert(bankIndex.text.includes('Rekening') || bankIndex.text.includes('Bank'));

        const postBank = await admin.post('/admin/finance/bank-accounts', {
            bank_name: 'Bank Mandiri',
            account_number: `15600${Date.now().toString().slice(-6)}`,
            account_holder: 'LSP Otomotif PrO Indo Operasional',
            branch: 'KC Cikarang Jababeka'
        });
        assert.strictEqual(postBank.status, 200);
        pass('Admin: Rekening Bank LSP (/admin/finance/bank-accounts) render 200 OK & berhasil simpan rekening');

        const pricingIndex = await admin.get('/admin/finance/pricing');
        assert.strictEqual(pricingIndex.status, 200);
        assert(pricingIndex.text.includes('Tarif') || pricingIndex.text.includes('Biaya') || pricingIndex.text.includes('Skema'));
        pass('Admin: Tarif Biaya Sertifikasi (/admin/finance/pricing) render 200 OK');

        const transIndex = await admin.get('/admin/finance/transactions');
        assert.strictEqual(transIndex.status, 200);
        assert(transIndex.text.includes('Transaksi') || transIndex.text.includes('Kas') || transIndex.text.includes('Buku'));
        pass('Admin: Buku Kas Transaksi (/admin/finance/transactions) render 200 OK');

        const bsIndex = await admin.get('/admin/finance/balance-sheet');
        assert.strictEqual(bsIndex.status, 200);
        assert(bsIndex.text.includes('Neraca') || bsIndex.text.includes('Keuangan') || bsIndex.text.includes('Aktiva'));
        pass('Admin: Laporan Neraca Keuangan (/admin/finance/balance-sheet) render 200 OK');

        const isIndex = await admin.get('/admin/finance/income-statement');
        assert.strictEqual(isIndex.status, 200);
        assert(isIndex.text.includes('Laba') || isIndex.text.includes('Rugi') || isIndex.text.includes('Pendapatan'));
        pass('Admin: Laporan Laba Rugi Komprehensif (/admin/finance/income-statement) render 200 OK');
    } catch (e) { fail('Admin: Keuangan & Akuntansi', e); }

    // -------------------------------------------------------------
    // GROUP 6: ADMIN SUBMODUL ASESI & LAPORAN LENGKAP
    // -------------------------------------------------------------
    console.log('\n\x1b[34m[GROUP 6] ADMIN: SUBMODUL ASESI & LAPORAN ANALITIK KINERJA\x1b[0m');

    try {
        const asesiUnscheduled = await admin.get('/admin/asesi/unscheduled');
        assert.strictEqual(asesiUnscheduled.status, 200);
        assert(asesiUnscheduled.text.includes('Belum Terjadwal') || asesiUnscheduled.text.includes('Asesi'));
        pass('Admin: Submodul Asesi Belum Terjadwal (/admin/asesi/unscheduled) render 200 OK');

        const asesiScheduled = await admin.get('/admin/asesi/scheduled');
        assert.strictEqual(asesiScheduled.status, 200);
        assert(asesiScheduled.text.includes('Terjadwal') || asesiScheduled.text.includes('Asesi'));
        pass('Admin: Submodul Asesi Terjadwal (/admin/asesi/scheduled) render 200 OK');

        const asesiCompetent = await admin.get('/admin/asesi/competent');
        assert.strictEqual(asesiCompetent.status, 200);
        assert(asesiCompetent.text.includes('Kompeten') || asesiCompetent.text.includes('Bersertifikat'));
        pass('Admin: Submodul Asesi Kompeten (/admin/asesi/competent) render 200 OK');

        const asesiIncompetent = await admin.get('/admin/asesi/incompetent');
        assert.strictEqual(asesiIncompetent.status, 200);
        assert(asesiIncompetent.text.includes('Belum Kompeten') || asesiIncompetent.text.includes('Asesi'));
        pass('Admin: Submodul Asesi Belum Kompeten (/admin/asesi/incompetent) render 200 OK');
    } catch (e) { fail('Admin: Submodul Asesi', e); }

    try {
        const repGeneral = await admin.get('/admin/reports/general');
        assert.strictEqual(repGeneral.status, 200);
        assert(repGeneral.text.includes('Laporan') || repGeneral.text.includes('Statistik') || repGeneral.text.includes('Asesmen'));
        pass('Admin: Laporan Umum Asesmen (/admin/reports/general) render 200 OK');

        const repMonthly = await admin.get('/admin/reports/monthly');
        assert.strictEqual(repMonthly.status, 200);
        pass('Admin: Laporan Bulanan Asesmen (/admin/reports/monthly) render 200 OK');

        const repAnnual = await admin.get('/admin/reports/annual');
        assert.strictEqual(repAnnual.status, 200);
        pass('Admin: Laporan Tahunan Asesmen (/admin/reports/annual) render 200 OK');

        const repAsesor = await admin.get('/admin/reports/asesor-performance');
        assert.strictEqual(repAsesor.status, 200);
        assert(repAsesor.text.includes('Asesor') || repAsesor.text.includes('Kinerja'));
        pass('Admin: Laporan Kinerja Asesor (/admin/reports/asesor-performance) render 200 OK');

        const repTuk = await admin.get('/admin/reports/tuk-performance');
        assert.strictEqual(repTuk.status, 200);
        assert(repTuk.text.includes('TUK') || repTuk.text.includes('Kinerja'));
        pass('Admin: Laporan Kinerja TUK (/admin/reports/tuk-performance) render 200 OK');

        const repCert = await admin.get('/admin/reports/certificates');
        assert.strictEqual(repCert.status, 200);
        assert(repCert.text.includes('Sertifikat') || repCert.text.includes('Terbit'));
        pass('Admin: Laporan Sertifikat Terbit (/admin/reports/certificates) render 200 OK');
    } catch (e) { fail('Admin: Laporan Lengkap', e); }

    // -------------------------------------------------------------
    // GROUP 7: ASESOR EXTENDED SIDE FEATURES
    // -------------------------------------------------------------
    console.log('\n\x1b[34m[GROUP 7] ASESOR: PROFIL, TUK VERIFIKASI, INSTRUMEN, MKVA & NOTIFIKASI\x1b[0m');
    const asesor = new BrowserClient();
    await asesor.get('/login');
    const asesorLogin = await asesor.post('/login', {
        email: 'asesor.randi@gmail.com',
        password: '@Kompeten86'
    });
    assert.strictEqual(asesorLogin.status, 200);
    pass('Asesor: Otentikasi Asesor Randi berhasil');

    try {
        const profEdit = await asesor.get('/asesor/profile');
        assert.strictEqual(profEdit.status, 200);
        assert(profEdit.text.includes('Profil') || profEdit.text.includes('Asesor'));

        const profShow = await asesor.get('/asesor/profile/show');
        assert.strictEqual(profShow.status, 200);

        const postProf = await asesor.post('/asesor/profile', {
            full_name: 'Randi Asesor Otomotif, S.T.',
            phone: '081299988811',
            assessor_registration_number: 'MET.000.009988 2026',
            pendidikan_terakhir: 'S1 Teknik Mesin',
            nama_institusi_perusahaan: 'LSP PrO Indo Asesor Council',
            jabatan: 'Lead Assessor Otomotif'
        });
        assert.strictEqual(postProf.status, 200);
        pass('Asesor: Profil & Data Kompetensi Asesor (/asesor/profile) berhasil dimuat dan diperbarui');
    } catch (e) { fail('Asesor: Profil', e); }

    try {
        const asSched = await asesor.get('/asesor/schedules');
        assert.strictEqual(asSched.status, 200);
        assert(asSched.text.includes('Jadwal') || asSched.text.includes('Penugasan') || asSched.text.includes('Asesor'));
        pass('Asesor: Daftar Jadwal Penugasan (/asesor/schedules) render 200 OK');
    } catch (e) { fail('Asesor: Schedules', e); }

    try {
        const tuksIndex = await asesor.get('/asesor/tuks');
        assert.strictEqual(tuksIndex.status, 200);
        assert(tuksIndex.text.includes('Verifikasi') || tuksIndex.text.includes('TUK'));

        const tukOptMatch = tuksIndex.text.match(/value="(\d+)"[^>]*>[^<]*TUK/i) || tuksIndex.text.match(/<option\s+value="(\d+)"/i);
        const tukId = tukOptMatch ? tukOptMatch[1] : 1;

        const postTukVerif = await asesor.post('/asesor/tuks', {
            tuk_id: tukId,
            verification_date: '2026-11-01',
            verification_type: 'sewaktu',
            recommendation: 'layak',
            notes: 'Peralatan diagnostic tool, lift hidrolik, dan APAR dalam kondisi prima dan terkalibrasi.'
        });
        assert.strictEqual(postTukVerif.status, 200);
        pass('Asesor: Verifikasi Kelayakan TUK (/asesor/tuks) render 200 OK & berhasil simpan verifikasi');
    } catch (e) { fail('Asesor: TUK Verifikasi', e); }

    try {
        const instIndex = await asesor.get('/asesor/instruments');
        assert.strictEqual(instIndex.status, 200);
        assert(instIndex.text.includes('Instrumen') || instIndex.text.includes('Kaji Ulang') || instIndex.text.includes('Validasi'));

        const schOptMatch = instIndex.text.match(/<option\s+value="(\d+)"/i);
        const schemeId = schOptMatch ? schOptMatch[1] : 1;

        const postInstReview = await asesor.post('/asesor/instruments', {
            scheme_id: schemeId,
            instrument_type: 'FR.IA.02 Tugas Praktik Demonstrasi',
            review_date: '2026-11-01',
            recommendation: 'disetujui',
            notes: 'Langkah kerja dan KUK telah sesuai dengan unit SKKNI terkini.'
        });
        assert.strictEqual(postInstReview.status, 200);
        pass('Asesor: Kaji Ulang & Validasi Instrumen (/asesor/instruments) render 200 OK & berhasil simpan');
    } catch (e) { fail('Asesor: Kaji Ulang Instrumen', e); }

    try {
        const mkvaIndex = await asesor.get('/asesor/mkva');
        assert.strictEqual(mkvaIndex.status, 200);
        assert(mkvaIndex.text.includes('MKVA') || mkvaIndex.text.includes('Monitoring') || mkvaIndex.text.includes('Mutu'));

        const mkvaSchMatch = mkvaIndex.text.match(/<option\s+value="(\d+)"/i);
        const schemeId = mkvaSchMatch ? mkvaSchMatch[1] : 1;

        const postMkva = await asesor.post('/asesor/mkva', {
            scheme_id: schemeId,
            assignment_date: '2026-11-01',
            findings: 'Pelaksanaan asesmen mandiri dan demonstrasi berjalan objektif dan valid.',
            improvement_plan: 'Pertahankan sistem paperless terpadu untuk efisiensi audit.'
        });
        assert.strictEqual(postMkva.status, 200);
        pass('Asesor: MKVA Monitoring Asesmen (/asesor/mkva) render 200 OK & berhasil simpan evaluasi');
    } catch (e) { fail('Asesor: MKVA Monitoring', e); }

    try {
        const asNotif = await asesor.get('/asesor/messages');
        assert.strictEqual(asNotif.status, 200);
        assert(asNotif.text.includes('Notifikasi') || asNotif.text.includes('Pesan') || asNotif.text.includes('Inbox'));
        pass('Asesor: Kotak Pesan & Tugas Asesor (/asesor/messages) render 200 OK');
    } catch (e) { fail('Asesor: Notifikasi', e); }

    try {
        const apl02Index = await asesor.get('/asesor/apl-02');
        assert.strictEqual(apl02Index.status, 200);
        assert(apl02Index.text.includes('APL') || apl02Index.text.includes('Portofolio') || apl02Index.text.includes('Mandiri'));
        pass('Asesor: Antrean Evaluasi Mandiri APL-02 (/asesor/apl-02) render 200 OK');
    } catch (e) { fail('Asesor: APL-02 Queue', e); }

    // -------------------------------------------------------------
    // GROUP 8: ASESI SIDE FEATURES & APPLICATIONS
    // -------------------------------------------------------------
    console.log('\n\x1b[34m[GROUP 8] ASESI: PROFIL BIODATA, JADWAL & RIWAYAT PERMOHONAN\x1b[0m');
    const asesi = new BrowserClient();
    await asesi.get('/login');
    const asesiLogin = await asesi.post('/login', {
        email: 'asesi1@gmail.com',
        password: '@Kompeten86'
    });
    assert.strictEqual(asesiLogin.status, 200);
    pass('Asesi: Otentikasi Asesi Satu berhasil');

    try {
        const asesiProf = await asesi.get('/asesi/profile');
        assert.strictEqual(asesiProf.status, 200);
        assert(asesiProf.text.includes('Profil') || asesiProf.text.includes('Biodata'));

        const postAsesiProf = await asesi.post('/asesi/profile', {
            full_name: 'Asesi Satu Otomotif Playwright',
            nik: '3201010101990001',
            tempat_lahir: 'KABUPATEN BEKASI',
            tanggal_lahir: '1999-05-12',
            alamat_rumah: 'Jl. Raya Cikarang No. 88, Bekasi',
            phone: '081299998888',
            nama_institusi_perusahaan: 'SMK Negeri 1 Cikarang',
            jabatan: 'Mekanik Junior'
        });
        assert.strictEqual(postAsesiProf.status, 200);
        pass('Asesi: Profil Biodata Asesi (/asesi/profile) berhasil dimuat dan diperbarui');
    } catch (e) { fail('Asesi: Profil', e); }

    try {
        const asesiJadwal = await asesi.get('/asesi/jadwal');
        assert.strictEqual(asesiJadwal.status, 200);
        assert(asesiJadwal.text.includes('Jadwal') || asesiJadwal.text.includes('Kode') || asesiJadwal.text.includes('Gabung'));
        pass('Asesi: Katalog Jadwal & Form Join (/asesi/jadwal) render 200 OK');

        const asesiApps = await asesi.get('/asesi/applications');
        assert.strictEqual(asesiApps.status, 200);
        assert(asesiApps.text.includes('Permohonan') || asesiApps.text.includes('Riwayat') || asesiApps.text.includes('Aplikasi'));
        pass('Asesi: Riwayat Permohonan Sertifikasi (/asesi/applications) render 200 OK');
    } catch (e) { fail('Asesi: Jadwal & Riwayat Permohonan', e); }

    // -------------------------------------------------------------
    // SUMMARY REPORT
    // -------------------------------------------------------------
    console.log('\n============================================================');
    console.log('📊 RINGKASAN HASIL PENGUJIAN SEMUA FITUR TAMBAHAN (SIDE FEATURES)');
    console.log(`TOTAL FEATURES DIUJI : ${passed + failed}`);
    console.log(`\x1b[32mLOLOS (PASSED)       : ${passed}\x1b[0m`);
    console.log(`\x1b[31mGAGAL (FAILED)       : ${failed}\x1b[0m`);
    console.log('============================================================\n');

    if (failed > 0) {
        console.error('\x1b[31mDaftar Fitur Tambahan yang Mengalami Error:\x1b[0m');
        for (const f of failures) {
            console.error(` - [${f.desc}]: ${f.error}`);
        }
        process.exit(1);
    } else {
        console.log('\x1b[32m🎉 SELURUH FITUR TAMBAHAN DI SEMUA ROLE LOLOS 100% TANPA KENDALA!\x1b[0m\n');
        process.exit(0);
    }
}

runSideFeaturesQA().catch(err => {
    console.error('Fatal Side Features QA Runner Error:', err);
    process.exit(1);
});
