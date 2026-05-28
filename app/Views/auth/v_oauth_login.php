<!DOCTYPE html>
<html lang="en"
    data-theme="light"
    data-accent="solar-yellow"
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
    <title>SSO Login - App SSO</title>
    <!-- Use Vite for assets -->
    <?= vite_client() ?>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= base_url('dist/css/boxicons.min.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('dist/css/theme.css') ?>">
    <link rel="stylesheet" href="<?= base_url('dist/css/components.css') ?>">
    <?= vite_asset('resources/css/main.css') ?>


</head>

<body class="font-sans antialiased overflow-hidden">
    <div class="auth-bg-overlay fixed inset-0 z-0"></div>

    <div class="relative z-10 min-h-screen flex items-center justify-center p-6">
        <div id="auth-card" class="glass-card w-full max-w-[420px] p-8 md:p-10 rounded-4xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-500 hidden">
            <div id="default-header" class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-sky-500/10 text-sky-500 mb-6 group transition-transform hover:scale-110">
                    <i class="bx bxs-key text-4xl"></i>
                </div>
                <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">SSO Gateway</h2>
                <p class="text-slate-500 dark:text-slate-400 mt-2 font-medium">Verify your identity to continue</p>
            </div>

            <div id="account-chooser-header" class="text-center mb-6 hidden animate-in fade-in slide-in-from-top duration-300">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-sky-500/10 text-sky-500 mb-6">
                    <i class="bx bxs-user-account text-4xl"></i>
                </div>
                <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Choose an account</h2>
                <p class="text-slate-500 dark:text-slate-400 mt-2 font-medium">to continue to SSO Gateway</p>
            </div>

            <div id="selected-account-header" class="text-center mb-6 hidden animate-in fade-in zoom-in duration-300">
                <div id="selected-account-avatar-container" class="mb-4 flex justify-center">
                    <img id="selected-account-avatar" src="" class="w-16 h-16 rounded-full border-2 border-sky-500 shadow-md">
                </div>
                <h2 id="selected-account-name" class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">Welcome</h2>
                <div id="selected-account-email" class="text-sm font-semibold text-slate-500 bg-slate-100 dark:bg-slate-800/80 px-3 py-1 rounded-full inline-flex items-center gap-1.5 mt-2 border border-slate-200/50 dark:border-slate-700/50">
                    <i class="bx bx-envelope text-slate-400"></i>
                    <span></span>
                </div>
            </div>

            <!-- Error message container (Dynamic for AJAX & static session errors) -->
            <div id="error-alert" class="mb-6 bg-rose-100 text-rose-700 p-4 rounded-xl text-sm font-semibold flex items-center gap-3 <?= session()->getFlashdata('error') ? '' : 'hidden' ?>">
                <i class="bx bx-error-circle text-xl"></i>
                <span id="error-message"><?= session()->getFlashdata('error') ?? '' ?></span>
            </div>

            <!-- State 1: Account Chooser Section -->
            <div id="account-chooser-section" class="space-y-4 hidden animate-in fade-in slide-in-from-bottom duration-300">
                <div id="accounts-list" class="flex flex-col gap-2 space-y-2.5 max-h-[220px] overflow-y-auto pr-1">
                    <!-- Dynamic Account Items go here -->
                </div>

                <button type="button" id="btn-use-another" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all font-semibold text-sm text-left">
                    <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500">
                        <i class="bx bx-user-plus text-lg"></i>
                    </div>
                    <span class="flex-1 text-slate-700 dark:text-slate-300">Use another account</span>
                </button>
            </div>

            <!-- State 2: Unified Form -->
            <form id="login-form" action="<?= base_url('oauth/login/process') ?>" method="POST" class="space-y-6">
                <?= csrf_field() ?>

                <!-- Hidden inputs for password-only flow -->
                <input type="hidden" name="email" id="hidden-email">

                <!-- Visible Email Input Container -->
                <div id="email-field-container">
                    <label class="form-label mb-2 block">Username / Email</label>
                    <div class="input-icon-wrapper">
                        <input type="text" name="email_visible" id="email-visible" value="<?= old('email') ?>" class="form-control" placeholder="admin" autofocus>
                        <i class="bx bx-user"></i>
                    </div>
                </div>

                <div>
                    <label class="form-label mb-2 block">Password</label>
                    <div class="input-icon-wrapper">
                        <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                        <i class="bx bx-lock-alt"></i>
                    </div>
                </div>

                <div class="flex flex-col gap-3 mt-8">
                    <button type="submit" class="btn btn-primary btn-block btn-lg shadow-lg bg-sky-500 hover:bg-sky-600 text-white shadow-sky-500/30 border-0">
                        Sign In & Continue
                    </button>

                    <button type="button" id="btn-back-to-chooser" class="btn btn-default btn-block btn-lg hidden">
                        Back to accounts
                    </button>

                    <a href="<?= base_url('login') ?>" id="btn-cancel" class="btn btn-default btn-block btn-lg">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Theme Switcher Floating -->
    <div class="fixed bottom-6 right-6 z-50">
        <button id="theme-toggle" class="flex items-center justify-center w-12 h-12 rounded-full glass-card text-slate-600 dark:text-slate-300 shadow-xl border border-white/20 transition-all hover:scale-110">
            <i id="theme-toggle-dark-icon" class="bx bx-moon text-xl hidden"></i>
            <i id="theme-toggle-light-icon" class="bx bx-sun text-xl hidden"></i>
        </button>
    </div>

    <!-- Vendor Scripts -->
    <script src="<?= base_url('dist/js/vendor/floating-ui.core.min.js') ?>"></script>
    <script src="<?= base_url('dist/js/vendor/floating-ui.dom.min.js') ?>"></script>
    <?= vite_asset('resources/js/main.js') ?>
    <?= vite_asset('resources/js/app.js') ?>
    <?= vite_asset('scripts/oauth_login.js', true) ?>
</body>

</html>