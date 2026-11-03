import { chromium } from 'playwright';
import { spawn } from 'node:child_process';
import http from 'node:http';

const PORT = 8011;
const BASE_URL = `http://127.0.0.1:${PORT}`;

async function waitForServer(url, timeoutMs = 15000) {
    const start = Date.now();
    while (Date.now() - start < timeoutMs) {
        try {
            await new Promise((resolve, reject) => {
                const req = http.get(url, (res) => {
                    resolve(res.statusCode);
                });
                req.on('error', reject);
                req.setTimeout(1000, () => {
                    req.destroy();
                    reject(new Error('timeout'));
                });
            });
            return true;
        } catch {
            await new Promise((r) => setTimeout(r, 250));
        }
    }
    throw new Error(`Server failed to start at ${url} within ${timeoutMs}ms`);
}

async function checkPage(page, path, name) {
    const res = await page.goto(`${BASE_URL}${path}`, { waitUntil: 'domcontentloaded' });
    const status = res?.status() ?? 0;
    if (status >= 400) {
        throw new Error(`Page ${path} returned status ${status}`);
    }

    const hasHorizontalOverflow = await page.evaluate(() => {
        return document.documentElement.scrollWidth > window.innerWidth + 2;
    });

    if (hasHorizontalOverflow) {
        console.warn(`  [WARN] Horizontal scroll detected on ${path}`);
    }

    return { status, hasHorizontalOverflow };
}

async function loginUser(page, email, password) {
    await page.goto(`${BASE_URL}/login`, { waitUntil: 'domcontentloaded' });
    await page.fill('input[name="email"], input[type="email"]', email);
    await page.fill('input[name="password"], input[type="password"]', password);
    await Promise.all([
        page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
        page.click('button[type="submit"]'),
    ]);
}

async function logoutUser(page) {
    const logoutForm = await page.$('form[action*="logout"]');
    if (logoutForm) {
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
            logoutForm.evaluate((form) => form.submit()),
        ]);
    } else {
        await page.goto(`${BASE_URL}/login`, { waitUntil: 'domcontentloaded' });
    }
}

async function run() {
    console.log(`Starting PHP built-in server on port ${PORT}...`);
    const server = spawn('php', ['artisan', 'serve', `--port=${PORT}`], {
        stdio: 'pipe',
    });

    server.stderr.on('data', (d) => {
        // silence normal dev server messages
    });

    try {
        await waitForServer(`${BASE_URL}/login`);
        console.log(`Server is ready at ${BASE_URL}`);

        const browser = await chromium.launch({ headless: true });

        const viewports = [
            { name: 'Mobile (390x844)', width: 390, height: 844 },
            { name: 'Tablet (768x1024)', width: 768, height: 1024 },
            { name: 'Desktop (1280x800)', width: 1280, height: 800 },
        ];

        let totalChecks = 0;

        for (const vp of viewports) {
            console.log(`\nTesting viewport: ${vp.name}`);
            const context = await browser.newContext({
                viewport: { width: vp.width, height: vp.height },
            });
            const page = await context.newPage();

            // 1. Public & Auth routes
            const publicRoutes = ['/', '/login', '/register', '/forgot-password'];
            for (const r of publicRoutes) {
                await checkPage(page, r, 'Public');
                totalChecks++;
            }

            // 2. Admin Flow
            await loginUser(page, 'admin@lsp.localhost', 'password123');
            const adminRoutes = [
                '/admin/dashboard',
                '/admin/jadwal',
                '/admin/pleno',
                '/admin/certificates',
                '/admin/appeals',
                '/admin/impartiality',
                '/admin/surveillance',
                '/admin/recertification',
            ];
            for (const r of adminRoutes) {
                await checkPage(page, r, 'Admin');
                totalChecks++;
            }
            await logoutUser(page);

            // 3. Asesor Flow
            await loginUser(page, 'asesor@lsp.localhost', 'password123');
            const asesorRoutes = [
                '/asesor/dashboard',
                '/asesor/jadwal',
                '/asesor/profile',
                '/asesor/impartiality/commitment',
            ];
            for (const r of asesorRoutes) {
                await checkPage(page, r, 'Asesor');
                totalChecks++;
            }
            await logoutUser(page);

            // 4. Asesi Flow
            await loginUser(page, 'asesi@lsp.localhost', 'password123');
            const asesiRoutes = [
                '/asesi/dashboard',
                '/asesi/jadwal',
                '/asesi/profile',
                '/asesi/certificates',
                '/asesi/surveillance',
            ];
            for (const r of asesiRoutes) {
                await checkPage(page, r, 'Asesi');
                totalChecks++;
            }
            await logoutUser(page);

            await context.close();
        }

        await browser.close();
        console.log(`\nBrowser test completed successfully: ${totalChecks} page assertions passed across 3 viewports.`);
    } finally {
        server.kill('SIGTERM');
    }
}

run().catch((err) => {
    console.error('Browser test failed:', err);
    process.exit(1);
});
