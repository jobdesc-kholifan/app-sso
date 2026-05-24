<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Landing Page
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="w-full flex items-center justify-center min-h-[80vh]">
	<div class="w-full max-w-5xl h-[70vh] flex flex-col items-center justify-center text-center p-8 bg-surface rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 relative overflow-hidden">
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
<?= $this->endSection() ?>
