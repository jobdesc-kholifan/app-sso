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
	<title><?= $title ?? '' ?> - Vibe UI Kit</title>
	<meta name="csrf-token" content="<?= csrf_token() ?>">
	<meta name="csrf-hash" content="<?= csrf_hash() ?>">
	<meta name="base-url" content="<?= rtrim(base_url(), '/') ?>">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
	<link href="<?= base_url('dist/css/boxicons.min.css') ?>" rel="stylesheet">
	<?= vite_client() ?>

	<link rel="stylesheet" href="<?= base_url('dist/css/theme.css') ?>">
	<link rel="stylesheet" href="<?= base_url('dist/css/layout.css') ?>">
	<link rel="stylesheet" href="<?= base_url('dist/css/components.css') ?>">
	<?= vite_asset('resources/css/main.css', false) ?>

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

	<?= $this->renderSection('style_head') ?>
	<?= $this->renderSection('script_head') ?>
</head>

<body class="bg-body text-slate-900 dark:text-slate-100 font-sans transition-colors duration-300">

	<div id="app-layout" class="flex w-full h-screen overflow-hidden transition-all duration-500 bg-body">
		<!-- Overlay Layer for Mobile -->
		<div id="sidebar-overlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 hidden transition-opacity duration-300 opacity-0"></div>

		<?= $this->include('partials/sidebar') ?>

		<div id="content-wrapper" class="flex flex-col flex-1 min-w-0 overflow-hidden">
			<?= $this->include('partials/header') ?>

			<main id="main-content" class="flex-1 overflow-y-auto w-full space-y-8 transition-all duration-500">
				<div id="main-canvas" class="w-full space-y-8 transition-all duration-500 p-6">
					<?= $this->renderSection('content') ?>
				</div>
			</main>
		</div>
	</div>

	<!-- Vendor Scripts -->
	<script src="<?= base_url('dist/js/vendor/floating-ui.core.min.js') ?>"></script>
	<script src="<?= base_url('dist/js/vendor/floating-ui.dom.min.js') ?>"></script>
	<script src="<?= base_url('dist/js/config.js') ?>"></script>
	<script src="<?= base_url('dist/js/menu.js') ?>"></script>
	<script src="<?= base_url('dist/js/app.js') ?>"></script>
	<?= vite_asset('resources/js/main.js', false) ?>

	<?= $this->renderSection('script_foot') ?>
</body>

</html>