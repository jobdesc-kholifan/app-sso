<!DOCTYPE html>
<html lang="en"
    data-theme="light"
    data-accent="growth-green"
    data-bg-preset="Neutral"
    data-canvas-style="Full"
    data-zoom-level="Standard"
    data-nav-mode="sidebar"
    data-light-palette="Modern Gray"
    data-dark-palette="midnight-onyx"
    data-sidebar-theme="expanded">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OAuth Error - App SSO</title>
    <!-- Use Vite for assets -->
    <?= vite_client() ?>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <link href="<?= base_url('dist/css/boxicons.min.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('dist/css/theme.css') ?>">
    <link rel="stylesheet" href="<?= base_url('dist/css/layout.css') ?>">
    <link rel="stylesheet" href="<?= base_url('dist/css/components.css') ?>">
    <!-- Tailwind CSS CDN for runtime utility class resolution -->
    <?= vite_asset('resources/css/main.css') ?>


</head>

<body class="font-sans antialiased overflow-hidden flex items-center justify-center min-h-screen p-6 relative">
    <div class="auth-bg-overlay fixed inset-0 z-0"></div>

    <?php
    $clientExists = $client_exists ?? false;
    $isRedirectMismatch = (strpos(strtolower($message ?? ''), 'redirect') !== false) ||
        (strpos(strtolower($hint ?? ''), 'redirect') !== false) ||
        ($error_type === 'invalid_client' && $clientExists);

    // Choose theme colors and icons based on the error case
    if ($error_type === 'invalid_client' && !$isRedirectMismatch) {
        // CASE 1: Fake Client ID
        $themeColor = 'amber';
        $accentClass = 'text-amber-500 bg-amber-500/10 border-amber-500/20';
        $glowingClass = 'bg-amber-500/20';
        $iconClass = 'bx bx-ghost';
        $titleText = 'Client ID Tidak Valid';
        $subtitleText = 'Client ID Palsu / Tidak Terdaftar';
    } elseif ($isRedirectMismatch) {
        // CASE 2: Redirect URI Mismatch
        $themeColor = 'orange';
        $accentClass = 'text-orange-500 bg-orange-500/10 border-orange-500/20';
        $glowingClass = 'bg-orange-500/20';
        $iconClass = 'bx bx-link-external';
        $titleText = 'Redirect URI Mismatch';
        $subtitleText = 'URL Callback Tidak Terdaftar';
    } else {
        // CASE 3: General / User Account / Server Error
        $themeColor = 'rose';
        $accentClass = 'text-rose-500 bg-rose-500/10 border-rose-500/20';
        $glowingClass = 'bg-rose-500/20';
        $iconClass = 'bx bx-shield-x';
        $titleText = 'Integration Error';
        $subtitleText = 'OAuth Authorization Failed';
    }
    ?>

    <div class="relative z-10 w-full max-w-[420px] animate-in fade-in zoom-in duration-500">
        <div class="glass-card p-8 md:p-10 rounded-[2rem] shadow-2xl overflow-hidden border border-slate-200/50 dark:border-slate-800/50">

            <!-- Header Section -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl <?= $accentClass ?> mb-6 relative">
                    <!-- Glowing aura effect -->
                    <div class="absolute inset-0 rounded-3xl <?= $glowingClass ?> blur-xl animate-pulse"></div>
                    <i class="<?= $iconClass ?> text-5xl relative z-10"></i>
                </div>
                <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight"><?= $titleText ?></h2>
                <p class="text-<?= $themeColor ?>-600 dark:text-<?= $themeColor ?>-400 font-semibold mt-2 text-sm uppercase tracking-wider"><?= $subtitleText ?></p>
            </div>

            <!-- Error Alert Box: CASE 1 (Client ID Palsu) -->
            <?php if ($error_type === 'invalid_client' && !$isRedirectMismatch): ?>
                <div class="mb-6 bg-amber-50 dark:bg-amber-950/20 border border-amber-200/50 dark:border-amber-900/30 p-4 rounded-2xl text-slate-800 dark:text-slate-200 text-sm flex flex-col gap-2">
                    <div class="flex items-center gap-3 text-amber-600 dark:text-amber-400 font-bold">
                        <i class="bx bx-error-circle text-2xl animate-bounce"></i>
                        <span class="text-[15px]">Client ID Palsu detected</span>
                    </div>
                    <p class="text-slate-500 dark:text-slate-400 font-normal leading-relaxed text-xs">
                        Aplikasi mengirimkan parameter <code>client_id</code> yang tidak terdaftar di database server SSO. Harap daftarkan aplikasi Anda di menu manajemen klien.
                    </p>
                </div>
            <?php endif; ?>

            <!-- Error Alert Box: CASE 2 (Redirect URI Mismatch) -->
            <?php if ($isRedirectMismatch): ?>
                <div class="mb-6 bg-orange-50 dark:bg-orange-950/20 border border-orange-200/50 dark:border-orange-900/30 p-4 rounded-2xl text-slate-800 dark:text-slate-200 text-sm flex flex-col gap-2">
                    <div class="flex items-center gap-3 text-orange-600 dark:text-orange-400 font-bold">
                        <i class="bx bx-unlink text-2xl animate-pulse"></i>
                        <span class="text-[15px]">Callback URL Mismatch</span>
                    </div>
                    <p class="text-slate-500 dark:text-slate-400 font-normal leading-relaxed text-xs">
                        Parameter <code>redirect_uri</code> yang diminta oleh aplikasi klien tidak terdaftar di konfigurasi klien SSO server ini. Hal ini memicu proteksi keamanan pencegahan pembajakan token.
                    </p>
                </div>
            <?php endif; ?>

            <!-- Error Alert Box: CASE 3 (General Server Error / Inactive User) -->
            <?php if (in_array($error_type, ['server_error', 'invalid_grant'])): ?>
                <div class="mb-6 bg-rose-50 dark:bg-rose-950/20 border border-rose-200/50 dark:border-rose-900/30 p-4 rounded-2xl text-slate-800 dark:text-slate-200 text-sm flex flex-col gap-2">
                    <div class="flex items-center gap-3 text-rose-600 dark:text-rose-400 font-bold">
                        <i class="bx bx-error text-2xl"></i>
                        <span class="text-[15px]">Kesalahan Akun Pengguna</span>
                    </div>
                    <p class="text-slate-500 dark:text-slate-400 font-normal leading-relaxed text-xs">
                        Terdapat masalah pada akun pengguna Anda, misalnya akun berstatus tidak aktif atau kredensial yang dimasukkan tidak ditemukan.
                    </p>
                </div>
            <?php endif; ?>

            <!-- Error Terminal Box -->
            <div class="bg-slate-50/60 dark:bg-slate-950/60 border border-slate-200/40 dark:border-slate-800 rounded-2xl p-5 space-y-4 font-mono text-[11px] leading-relaxed text-slate-700 dark:text-slate-300 mb-8">
                <div>
                    <span class="text-rose-600 dark:text-rose-400 font-bold">ERROR_TYPE:</span>
                    <span class="text-slate-900 dark:text-white font-bold ml-1"><?= esc($error_type) ?></span>
                </div>
                <div>
                    <span class="text-slate-500 dark:text-slate-400 font-bold">MESSAGE:</span>
                    <span class="ml-1 text-slate-700 dark:text-slate-300"><?= esc($message) ?></span>
                </div>
                <?php if (!empty($hint)): ?>
                    <div class="border-t border-slate-100 dark:border-slate-900 pt-3 mt-3">
                        <span class="text-indigo-600 dark:text-indigo-400 font-bold">DEVELOPER_HINT:</span>
                        <p class="mt-1 text-slate-600 dark:text-slate-400 leading-normal"><?= esc($hint) ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-3">
                <a href="#" onclick="window.close(); return false;" class="btn btn-primary btn-block btn-lg h-12 flex items-center justify-center gap-2 bg-<?= $themeColor ?>-600 hover:bg-<?= $themeColor ?>-500 text-white shadow-lg shadow-<?= $themeColor ?>-500/30 border-0 transition-all font-semibold">
                    <i class="bx bx-power-off text-lg"></i>
                    <span>Close Window</span>
                </a>
            </div>

            <div class="text-center text-slate-400 dark:text-slate-500 text-[10px] uppercase tracking-widest mt-8 flex items-center justify-center gap-1">
                <i class="bx bx-info-circle text-xs"></i>
                <span>App SSO Secure Gateway</span>
            </div>
        </div>
    </div>

    <!-- Theme Switcher Floating -->
    <div class="fixed bottom-6 right-6 z-50">
        <button id="theme-toggle" class="flex items-center justify-center w-10 h-10 rounded-lg bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 transition-all hover:scale-105">
            <i id="theme-toggle-dark-icon" class="bx bx-moon text-lg hidden!"></i>
            <i id="theme-toggle-light-icon" class="bx bx-sun text-lg hidden!"></i>
        </button>
    </div>

    <!-- Vendor Scripts -->
    <script src="<?= base_url('dist/js/vendor/floating-ui.core.min.js') ?>"></script>
    <script src="<?= base_url('dist/js/vendor/floating-ui.dom.min.js') ?>"></script>
    <?= vite_asset('resources/js/main.js') ?>
    <?= vite_asset('resources/js/app.js') ?>
</body>

</html>