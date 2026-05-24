<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Landing Page - Vibe UI Kit</title>
	<script src="<?= base_url('dist/js/vendor/tailwindcss.js') ?>"></script>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
	<link href="<?= base_url('dist/css/boxicons.min.css') ?>" rel="stylesheet">
	<script src="<?= base_url('dist/js/tailwind-config.js') ?>"></script>
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

	<div class="min-h-screen flex items-center justify-center p-6 bg-body">
		<div class="w-full max-w-5xl h-[80vh] flex flex-col items-center justify-center text-center p-8 bg-surface rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 relative overflow-hidden">
			<!-- Background elements -->
			<div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
				<div class="absolute -top-[20%] -left-[10%] w-[50%] h-[50%] bg-primary-500/20 rounded-full blur-3xl"></div>
				<div class="absolute -bottom-[20%] -right-[10%] w-[50%] h-[50%] bg-purple-500/20 rounded-full blur-3xl"></div>
			</div>
			
			<div class="relative z-10 max-w-3xl">
				<div class="w-24 h-24 rounded-3xl bg-primary-100 text-primary-600 dark:bg-primary-900/50 dark:text-primary-400 flex items-center justify-center mx-auto mb-8 shadow-inner border border-primary-200 dark:border-primary-800">
					<i class="bx bx-rocket text-5xl"></i>
				</div>
				<h1 class="text-5xl md:text-6xl font-extrabold tracking-tight mb-6 bg-clip-text text-transparent bg-gradient-to-r from-primary-600 to-purple-600 dark:from-primary-400 dark:to-purple-400">
					Vibe Coding
				</h1>
				<p class="text-xl md:text-2xl font-medium text-slate-600 dark:text-slate-300 mb-4">
					Integrating using <span class="font-bold text-slate-900 dark:text-white">Vibe UI</span>
				</p>
				<p class="text-muted mb-10 max-w-2xl mx-auto">
					Your application has been successfully initialized and integrated with the Vibe UI templating system. You are now ready to build powerful, themeable interfaces.
				</p>
				<div class="flex items-center justify-center gap-4">
					<a href="/docs-template/pages/docs-setup.html" class="btn btn-primary px-8 py-3 text-lg rounded-xl shadow-lg shadow-primary-500/30 hover:scale-105 transition-transform">
						Get Started <i class="bx bx-right-arrow-alt ml-2"></i>
					</a>
					<a href="/docs-template/dashboards/index.html" class="btn btn-default px-8 py-3 text-lg rounded-xl hover:scale-105 transition-transform">
						Documentation
					</a>
				</div>
			</div>
		</div>
	</div>

	<!-- Vendor Scripts -->
	<script src="<?= base_url('dist/js/config.js') ?>"></script>
	<script src="<?= base_url('dist/js/app.js') ?>"></script>
</body>

</html>
