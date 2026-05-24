<header id="app-header" class="h-16 flex items-center justify-between px-8 border-b sticky top-0 z-30 transition-all duration-500 bg-surface">
	<div class="flex items-center">
		<button id="mobile-sidebar-toggle" class="md:hidden mr-4 p-2 rounded-lg text-muted hover:bg-slate-100 dark:hover:bg-slate-800">
			<i class="bx bx-menu text-2xl"></i>
		</button>
		<h1 class="text-sm font-bold flex items-center">
			<i class="bx bx-circle mr-2 text-primary-600"></i> <?= $pageTitle ?? '' ?>
		</h1>
	</div>
	<div class="flex items-center gap-2">
		<button id="theme-toggle" class="flex items-center justify-center w-9 h-9 rounded-lg text-muted hover:bg-slate-200 dark:hover:bg-slate-700 transition-all focus:outline-none border shadow-sm" style="background-color: rgb(var(--color-bg-alt)); border-color: rgb(var(--color-border))">
			<i id="theme-toggle-dark-icon" class="bx bx-moon text-xl !hidden"></i>
			<i id="theme-toggle-light-icon" class="bx bx-sun text-xl !hidden"></i>
		</button>
		<button id="settings-toggle" class="flex items-center justify-center w-9 h-9 rounded-lg text-muted hover:bg-slate-200 dark:hover:bg-slate-700 transition-all focus:outline-none border shadow-sm" style="background-color: rgb(var(--color-bg-alt)); border-color: rgb(var(--color-border))">
			<i class="bx bx-cog text-xl"></i>
		</button>
		<div class="dropdown">
			<button class="btn btn-default dropdown-toggle p-2">
				<div class="flex items-center gap-2">
					<img src="https://ui-avatars.com/api/?name=<?= urlencode(session()->get('full_name') ?? 'User') ?>&background=4f46e5&color=fff" class="w-6 h-6 rounded-full">
					<span class="text-sm font-semibold"><?= esc(session()->get('full_name') ?? 'Admin') ?></span>
					<i class="bx bx-chevron-down"></i>
				</div>
			</button>
			<div class="dropdown-menu min-w-[240px]">
				<div class="px-4 py-3 flex items-center gap-3">
					<img src="https://ui-avatars.com/api/?name=<?= urlencode(session()->get('full_name') ?? 'User') ?>&background=4f46e5&color=fff" class="w-10 h-10 rounded-full" alt="Avatar">
					<div class="flex flex-col min-w-0">
						<span class="text-sm font-bold truncate"><?= esc(session()->get('full_name') ?? 'Administrator') ?></span>
						<span class="text-xs text-muted truncate"><?= esc(session()->get('role') ?? 'admin') ?></span>
					</div>
				</div>
				<div class="dropdown-divider"></div>
				<a href="#" class="dropdown-item"><i class="bx bx-user mr-2"></i> My Profile</a>
				<a href="#" class="dropdown-item"><i class="bx bx-cog mr-2"></i> Account Settings</a>
				<div class="dropdown-divider"></div>
				<a href="<?= base_url('logout') ?>" class="dropdown-item text-danger"><i class="bx bx-log-out mr-2"></i> Sign Out</a>
			</div>
		</div>
	</div>
</header>
