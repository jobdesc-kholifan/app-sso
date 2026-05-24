<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Blank Page
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="mb-8">
	<h2 class="text-3xl font-extrabold mb-2">Blank Page</h2>
	<p class="text-muted font-medium">Start your new project from here.</p>
</div>

<div class="h-[60vh] border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-[2rem] flex flex-col items-center justify-center text-center p-8">
	<div class="w-16 h-16 rounded-2xl bg-alt flex items-center justify-center text-muted mb-6">
		<i class="bx bx-plus text-3xl"></i>
	</div>
	<h4 class="text-xl font-black mb-2 italic text-slate-400">Your content goes here...</h4>
	<p class="text-sm text-slate-400 max-w-sm font-medium">This is a clean container to help you start building unique sections for your application.</p>
</div>
<?= $this->endSection() ?>
