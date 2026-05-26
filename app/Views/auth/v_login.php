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
    <title>Login - App SSO</title>
    <!-- Use Vite for assets -->
    <?= vite_client() ?>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= base_url('dist/css/boxicons.min.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('dist/css/theme.css') ?>">
    <link rel="stylesheet" href="<?= base_url('dist/css/layout.css') ?>">
    <link rel="stylesheet" href="<?= base_url('dist/css/components.css') ?>">
    <?= vite_asset('resources/css/main.css', false) ?>
    <style>
        .split-visual {
            background-image: url('<?= base_url("dist/images/auth_bg_abstract_1776397340943.png") ?>');
            background-color: #4f46e5;
            background-size: cover;
            background-position: center;
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
            border: 3px solid rgba(79, 70, 229, 0.1);
            border-top-color: #4f46e5;
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

<body class="font-sans antialiased bg-body overflow-hidden">
    <div class="flex min-h-screen">
        <!-- Visual Side -->
        <div class="hidden lg:flex lg:w-3/5 split-visual relative p-12 flex-col justify-between overflow-hidden">
            <div class="absolute inset-0 bg-primary-900/10 backdrop-blur-[2px]"></div>

            <div class="relative z-10">
                <div class="flex items-center gap-3 text-white">
                    <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center border border-white/20">
                        <i class="bx bxs-shield-alt-2 text-2xl"></i>
                    </div>
                    <span class="text-2xl font-black tracking-tighter uppercase">SSO Master</span>
                </div>
            </div>

            <div class="relative z-10 max-w-xl">
                <h1 class="text-5xl font-black text-white leading-tight mb-6 animate-in slide-in-from-left duration-700">
                    Secure Enterprise Access with <span class="text-primary-400">Single Sign-On</span>.
                </h1>
                <p class="text-lg text-white/80 font-medium leading-relaxed mb-8">
                    Centralized authentication system for all your integrated enterprise applications.
                </p>
            </div>

            <div class="relative z-10 flex items-center justify-between text-white/50 text-xs font-bold uppercase tracking-widest">
                <span>© <?= date('Y') ?> App SSO. All rights reserved.</span>
                <div class="flex gap-6">
                    <a href="#" class="hover:text-white">Privacy</a>
                    <a href="#" class="hover:text-white">Terms</a>
                </div>
            </div>
        </div>

        <!-- Form Side -->
        <div class="w-full lg:w-2/5 flex flex-col justify-center p-8 md:p-16 lg:p-20 bg-surface relative z-10">
            <div class="max-w-[440px] mx-auto w-full">
                <!-- Mobile Logo -->
                <div class="lg:hidden flex items-center gap-3 mb-12">
                    <div class="w-10 h-10 rounded-xl bg-primary-600 flex items-center justify-center text-white shadow-lg shadow-primary-600/30">
                        <i class="bx bxs-shield-alt-2 text-2xl"></i>
                    </div>
                    <span class="text-2xl font-black tracking-tighter uppercase text-slate-900 dark:text-white">SSO Master</span>
                </div>

                <div class="mb-10">
                    <h2 class="text-4xl font-black text-slate-900 dark:text-white tracking-tight leading-tight">Welcome Back</h2>
                    <p class="text-slate-500 dark:text-slate-400 mt-3 font-medium text-lg">Enter your credentials to access the system.</p>
                </div>

                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="mb-6 bg-rose-100 text-rose-700 p-4 rounded-xl text-sm font-semibold flex items-center gap-3">
                        <i class="bx bx-error-circle text-xl"></i>
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')) : ?>
                    <div class="mb-6 bg-emerald-100 text-emerald-700 p-4 rounded-xl text-sm font-semibold flex items-center gap-3">
                        <i class="bx bx-check-circle text-xl"></i>
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>

                <div class="grid grid-cols-2 gap-4 mb-8">
                    <button type="button" class="btn btn-default py-3.5 shadow-sm">
                        <i class="bx bxl-google text-xl text-red-500"></i>
                        <span class="font-bold">Google</span>
                    </button>
                    <button type="button" class="btn btn-default py-3.5 shadow-sm">
                        <i class="bx bxl-github text-xl text-slate-900 dark:text-white"></i>
                        <span class="font-bold">GitHub</span>
                    </button>
                </div>

                <div class="relative mb-8">
                    <div class="absolute inset-0 flex items-center"><span class="w-full border-t border-slate-200 dark:border-slate-800"></span></div>
                    <div class="relative flex justify-center text-xs uppercase"><span class="bg-surface px-4 text-slate-400 font-bold tracking-widest">Or sign in with email</span></div>
                </div>

                <form action="<?= base_url('login/process') ?>" method="POST" class="space-y-6">
                    <?= csrf_field() ?>
                    <div>
                        <label class="form-label mb-2 block">Username</label>
                        <input type="text" name="email" value="<?= old('email') ?>" class="form-control form-control-lg h-14" placeholder="admin" required autofocus>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="form-label">Password</label>
                            <a href="#" class="text-xs font-bold text-primary-600 hover:underline">Forgot password?</a>
                        </div>
                        <input type="password" name="password" class="form-control form-control-lg h-14" placeholder="••••••••" required>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="form-check flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="form-check-input">
                            <span class="text-sm font-semibold text-slate-600 dark:text-slate-400">Stay logged in</span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block h-14 text-lg shadow-xl shadow-primary-600/30">
                        Sign In Now <i class="bx bx-right-arrow-alt ml-2 text-xl"></i>
                    </button>
                </form>

                <p class="mt-10 text-center text-sm font-semibold text-slate-500 dark:text-slate-400">
                    New to App SSO?
                    <a href="#" class="text-primary-600 font-extrabold hover:underline">Contact Administrator</a>
                </p>
            </div>

            <!-- Float theme toggle for form side -->
            <div class="absolute top-8 right-8">
                <button id="theme-toggle" class="flex items-center justify-center w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 transition-all hover:scale-105">
                    <i id="theme-toggle-dark-icon" class="bx bx-moon text-lg hidden!"></i>
                    <i id="theme-toggle-light-icon" class="bx bx-sun text-lg hidden!"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Vendor Scripts -->
    <script src="<?= base_url('dist/js/vendor/floating-ui.core.min.js') ?>"></script>
    <script src="<?= base_url('dist/js/vendor/floating-ui.dom.min.js') ?>"></script>
    <?= vite_asset('login.js', false) ?>
    <script src="<?= base_url('dist/js/app.js') ?>"></script>
</body>

</html>