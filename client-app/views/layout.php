<?php
$userName = $_SESSION['user_profile']['full_name'] ?? 'Guest Developer';
$userRole = isset($_SESSION['user_profile']) ? 'Authorized User' : 'Developer Mode';
$avatarUrl = isset($_SESSION['user_profile']['full_name'])
    ? 'https://ui-avatars.com/api/?name=' . urlencode($_SESSION['user_profile']['full_name']) . '&background=0ea5e9&color=fff'
    : 'https://ui-avatars.com/api/?name=Guest+Dev&background=4f46e5&color=fff';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->e($title ?? 'Demo Client App - SSO Tester Suite') ?></title>

    <!-- Vibe UI Premium Resources -->
    <script src="http://localhost:8080/js/vendor/tailwindcss.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <link href="http://localhost:8080/css/boxicons.min.css" rel="stylesheet">
    <script src="http://localhost:8080/js/tailwind-config.js"></script>
    <link rel="stylesheet" href="http://localhost:8080/css/theme.css">
    <link rel="stylesheet" href="http://localhost:8080/css/layout.css">
    <link rel="stylesheet" href="http://localhost:8080/css/components.css">

    <script>
        // Anti-FOUC theme loader
        if (localStorage.getItem('vibe-template.color-theme') === 'dark' || (!('vibe-template.color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>

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

<body class="bg-body text-slate-900 dark:text-slate-100 font-sans transition-colors duration-300">
    <div id="app-loader">
        <div class="loader-spinner"></div>
    </div>

    <div id="app-layout" class="flex w-full h-screen overflow-hidden transition-all duration-500 bg-body">

        <!-- Main Content -->
        <div id="content-wrapper" class="flex flex-col flex-1 min-w-0 overflow-hidden transition-all duration-500">
            <!-- Top Header -->
            <header id="app-header"
                class="h-16 flex items-center justify-between px-8 border-b sticky top-0 z-30 transition-all duration-500">
                <div class="flex items-center">
                    <button id="mobile-sidebar-toggle"
                        class="md:hidden mr-4 p-2 rounded-lg text-muted hover:bg-slate-100 dark:hover:bg-slate-800">
                        <i class="bx bx-menu text-2xl"></i>
                    </button>
                    <h1 class="text-sm font-bold flex items-center">
                        <i class="bx bxs-shield-alt-2 mr-2 text-primary-600"></i> SSO Integration Tester
                    </h1>
                </div>
                <div class="flex items-center gap-2">
                    <button id="theme-toggle"
                        class="flex items-center justify-center w-9 h-9 rounded-lg text-muted hover:bg-slate-200 dark:hover:bg-slate-700 transition-all focus:outline-none border shadow-sm"
                        style="background-color: rgb(var(--color-bg-alt)); border-color: rgb(var(--color-border))">
                        <i id="theme-toggle-dark-icon" class="bx bx-moon text-xl hidden"></i>
                        <i id="theme-toggle-light-icon" class="bx bx-sun text-xl hidden"></i>
                    </button>
                    <button id="settings-toggle"
                        class="flex items-center justify-center w-9 h-9 rounded-lg text-muted hover:bg-slate-200 dark:hover:bg-slate-700 transition-all focus:outline-none border shadow-sm"
                        style="background-color: rgb(var(--color-bg-alt)); border-color: rgb(var(--color-border))">
                        <i class="bx bx-cog text-xl"></i>
                    </button>
                </div>
            </header>

            <main id="main-content" class="flex-1 overflow-y-auto w-full space-y-8 transition-all duration-500">
                <div id="main-canvas" class="w-full space-y-8 transition-all duration-500 p-6">
                    <?= $this->section('content') ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Core Scripts -->
    <script src="http://localhost:8080/js/vendor/floating-ui.core.min.js"></script>
    <script src="http://localhost:8080/js/vendor/floating-ui.dom.min.js"></script>
    <script src="http://localhost:8080/js/config.js"></script>
    <script src="http://localhost:8080/js/menu.js"></script>
    <script src="http://localhost:8080/js/app.js"></script>

    <?= $this->section('scripts') ?>
</body>

</html>