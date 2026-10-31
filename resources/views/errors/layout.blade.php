<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - LSP Praktisi Otomotif Indonesia</title>

    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        lsp: {
                            navy: '#1E2B6E',
                            'navy-dark': '#162052',
                            'navy-light': '#2A3B94',
                            red: '#CE181E',
                            'red-dark': '#B01419',
                            canvas: '#F8FAFC',
                            title: '#0F172A',
                            muted: '#64748B',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', '"Inter"', 'system-ui', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', 'Inter', system-ui, sans-serif; }
    </style>
</head>
<body class="h-full flex flex-col justify-between text-slate-800 antialiased selection:bg-lsp-navy selection:text-white">

    <!-- Top Accent Stripe -->
    <div class="w-full flex h-1.5 fixed top-0 left-0 right-0 z-50">
        <div class="w-2/3 bg-lsp-navy"></div>
        <div class="w-1/3 bg-lsp-red"></div>
    </div>

    <!-- Main Container -->
    <main class="flex-1 flex items-center justify-center p-4 sm:p-6 lg:p-8">
        <div class="w-full max-w-xl bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <!-- Header bar -->
            <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo-lsp-wisindo.png') }}" alt="Logo LSP" class="h-8 w-auto object-contain">
                    <div class="h-4 w-px bg-slate-200"></div>
                    <span class="text-xs font-bold text-slate-700 tracking-wide">LSP PrO Indo 2.0</span>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-mono font-bold @yield('code-badge-classes', 'bg-red-50 text-lsp-red border border-red-200')">
                    HTTP @yield('code')
                </span>
            </div>

            <!-- Content Area -->
            <div class="p-6 sm:p-8 text-center sm:text-left">
                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5">
                    <div class="shrink-0 p-3.5 rounded-2xl @yield('icon-bg', 'bg-red-50 text-lsp-red border border-red-100')">
                        @yield('icon')
                    </div>
                    <div class="space-y-2">
                        <h1 class="text-xl sm:text-2xl font-extrabold text-lsp-title tracking-tight">
                            @yield('headline')
                        </h1>
                        <p class="text-sm text-lsp-muted leading-relaxed">
                            @yield('message')
                        </p>
                    </div>
                </div>

                @hasSection('action-details')
                    <div class="mt-6 p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600">
                        @yield('action-details')
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="mt-8 pt-6 border-t border-slate-200 flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
                    <a href="javascript:history.back()" class="w-full sm:w-auto px-4 py-2.5 text-xs font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 rounded-lg transition text-center">
                        &larr; Halaman Sebelumnya
                    </a>
                    <a href="{{ url('/') }}" class="w-full sm:w-auto px-5 py-2.5 text-xs font-semibold text-white bg-lsp-navy hover:bg-lsp-navy-dark rounded-lg shadow-xs transition text-center flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Ke Beranda / Portal
                    </a>
                </div>
            </div>
        </div>
    </main>

    <!-- Simple Clean Footer -->
    <footer class="py-4 text-center text-xs text-slate-400">
        &copy; {{ date('Y') }} LSP Praktisi Otomotif Indonesia. Sistem Sertifikasi Terlisensi BNSP.
    </footer>

</body>
</html>
