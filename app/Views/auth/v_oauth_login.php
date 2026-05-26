<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SSO Login - App SSO</title>
    <!-- Use Vite for assets -->
    <?= vite_client() ?>
    <?= vite_asset('resources/css/main.css') ?>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= base_url('dist/css/boxicons.min.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('dist/css/theme.css') ?>">
    <link rel="stylesheet" href="<?= base_url('dist/css/components.css') ?>">

    <script>
        // Inline theme check to prevent flash
        if (localStorage.getItem('vibe-template.color-theme') === 'dark' || (!('vibe-template.color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
    <style>
        body {
            background-image: url('<?= base_url("dist/img/auth_bg.webp") ?>');
            /* Fallback or relative path */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .auth-bg-overlay {
            background-image: url('<?= base_url("dist/images/auth_bg_abstract_1776397340943.png") ?>');
            background-size: cover;
            background-position: center;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .dark .glass-card {
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .dark .auth-bg-overlay {
            filter: brightness(0.25) saturate(0.6);
            background-color: #020617;
        }
    </style>

    <style id="critical-loader-style">
        #app-loader {
            position: fixed;
            inset: 0;
            z-index: 99999;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1), transform 0.4s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.4s;
        }

        .dark #app-loader {
            background: #0b1120;
        }

        .loader-spinner {
            width: 48px;
            height: 48px;
            border: 3px solid rgba(14, 165, 233, 0.1);
            border-top-color: #0ea5e9;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 1.5rem;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        #app-loader.fade-out {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transform: scale(1.05);
        }
    </style>
</head>

<body class="font-sans antialiased overflow-hidden">
    <div id="app-loader">
        <div class="loader-spinner"></div>
    </div>
    <div class="auth-bg-overlay fixed inset-0 z-0"></div>

    <div class="relative z-10 min-h-screen flex items-center justify-center p-6">
        <div class="glass-card w-full max-w-[420px] p-8 md:p-10 rounded-[2rem] shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-500">
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-sky-500/10 text-sky-500 mb-6 group transition-transform hover:scale-110">
                    <i class="bx bxs-key text-4xl"></i>
                </div>
                <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">SSO Gateway</h2>
                <p class="text-slate-500 dark:text-slate-400 mt-2 font-medium">Verify your identity to continue</p>
            </div>

            <?php if (session()->getFlashdata('error')) : ?>
                <div class="mb-6 bg-rose-100 text-rose-700 p-4 rounded-xl text-sm font-semibold flex items-center gap-3">
                    <i class="bx bx-error-circle text-xl"></i>
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('oauth/login/process') ?>" method="POST" class="space-y-6">
                <?= csrf_field() ?>

                <div>
                    <label class="form-label mb-2 block">Username / Email</label>
                    <div class="input-icon-wrapper">
                        <input type="text" name="email" value="<?= old('email') ?>" class="form-control" placeholder="admin" required autofocus>
                        <i class="bx bx-user"></i>
                    </div>
                </div>

                <div>
                    <label class="form-label mb-2 block">Password</label>
                    <div class="input-icon-wrapper">
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        <i class="bx bx-lock-alt"></i>
                    </div>
                </div>

                <div class="flex flex-col gap-3 mt-8">
                    <button type="submit" class="btn btn-primary btn-block btn-lg shadow-lg bg-sky-500 hover:bg-sky-600 text-white shadow-sky-500/30 border-0">
                        Sign In & Continue
                    </button>

                    <a href="<?= base_url('login') ?>" class="btn btn-default btn-block btn-lg">
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
    <script src="<?= base_url('dist/js/app.js') ?>"></script>
</body>

</html>