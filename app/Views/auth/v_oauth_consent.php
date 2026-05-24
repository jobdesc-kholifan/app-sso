<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authorize App - App SSO</title>
    <!-- Use Vite for assets -->
    <?= vite_client() ?>
    <?= vite_asset('resources/css/main.css', false) ?>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= base_url('dist/css/boxicons.min.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('dist/css/theme.css') ?>">
    <link rel="stylesheet" href="<?= base_url('dist/css/layout.css') ?>">
    <link rel="stylesheet" href="<?= base_url('dist/css/components.css') ?>">
    
    <script>
        if (localStorage.getItem('vibe-template.color-theme') === 'dark' || (!('vibe-template.color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
</head>

<body class="font-sans antialiased bg-slate-50 dark:bg-slate-900 flex items-center justify-center min-h-screen p-4">
    <div class="max-w-md w-full bg-white dark:bg-slate-800 rounded-3xl shadow-2xl border border-slate-100 dark:border-slate-700 overflow-hidden">
        
        <!-- Header -->
        <div class="bg-primary-600 p-8 text-center relative overflow-hidden">
            <!-- Decorative Elements -->
            <div class="absolute -top-[50%] -left-[20%] w-[80%] h-[150%] rounded-full bg-white/10 blur-3xl mix-blend-overlay"></div>
            
            <div class="relative z-10">
                <div class="w-20 h-20 mx-auto bg-white rounded-2xl shadow-lg flex items-center justify-center mb-4">
                    <i class="bx bxl-codepen text-5xl text-primary-600"></i>
                </div>
                <h2 class="text-2xl font-black text-white tracking-tight">Authorization Required</h2>
            </div>
        </div>

        <!-- Body -->
        <div class="p-8">
            <div class="text-center mb-8">
                <p class="text-slate-600 dark:text-slate-300 text-lg">
                    The application <strong class="text-slate-900 dark:text-white font-black px-1"><?= esc($clientName) ?></strong> is requesting access to your account.
                </p>
            </div>

            <div class="bg-slate-50 dark:bg-slate-900/50 rounded-2xl p-5 border border-slate-100 dark:border-slate-700 mb-8">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4">Requested Access:</h3>
                <ul class="space-y-4">
                    <?php foreach($scopes as $scope): ?>
                        <li class="flex items-start gap-3">
                            <div class="mt-0.5">
                                <i class="bx bx-check-circle text-xl text-emerald-500"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 dark:text-slate-200 capitalize"><?= esc($scope) ?></h4>
                                <p class="text-sm text-slate-500 dark:text-slate-400">
                                    <?php 
                                        if($scope === 'openid') echo 'Authenticate using your identity.';
                                        if($scope === 'profile') echo 'View your basic profile info (name, role).';
                                        if($scope === 'email') echo 'View your email address.';
                                    ?>
                                </p>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-xl font-bold text-slate-600 dark:text-slate-300">
                    <?= strtoupper(substr(session()->get('full_name'), 0, 1)) ?>
                </div>
                <div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Signed in as</p>
                    <p class="font-bold text-slate-900 dark:text-white"><?= esc(session()->get('full_name')) ?></p>
                </div>
            </div>

            <hr class="my-6 border-slate-200 dark:border-slate-700">

            <form action="<?= base_url('oauth/authorize') ?>" method="POST" class="flex gap-4">
                <?= csrf_field() ?>
                <button type="submit" name="approve" value="0" class="btn btn-default flex-1 py-3 text-lg font-bold">
                    Deny
                </button>
                <button type="submit" name="approve" value="1" class="btn btn-primary flex-1 py-3 text-lg font-bold shadow-lg shadow-primary-500/30">
                    Authorize
                </button>
            </form>
            
            <p class="text-center text-xs text-slate-400 mt-6">
                By authorizing this app, you agree to their Terms of Service and Privacy Policy.
            </p>
        </div>
    </div>
</body>
</html>
