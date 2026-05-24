<aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 border-r transition-all duration-300 transform -translate-x-full md:translate-x-0 md:relative flex flex-col flex-shrink-0 lg:translate-x-0">
	<div class="flex flex-col h-full w-full">
		<!-- Floating Sidebar Toggle (Desktop) -->
		<button id="desktop-sidebar-toggle" class="hidden md:flex absolute -right-3 top-6 w-6 h-6 bg-primary-600 text-white rounded-full items-center justify-center shadow-lg transition-all hover:scale-110 z-[60] focus:outline-none">
			<i id="toggle-icon" class="bx bx-chevron-left text-lg"></i>
		</button>
		<div id="profile-container" class="pt-4 pb-2 px-6 flex flex-row items-center transition-all duration-500">
			<div class="relative w-max h-auto group flex-shrink-0">
				<img id="side-avatar" src="https://ui-avatars.com/api/?name=Admin&background=4f46e5&color=fff" class="w-12 h-12 rounded-full shadow-lg transition-all duration-500 border-2 border-white/20" style="background-color: rgb(var(--color-primary))" alt="side-avatar">
				<div class="absolute -left-1 -top-1 sidebar-hide">
					<button class="bg-amber-500 text-white w-6 h-6 rounded-full flex items-center justify-center shadow-lg hover:bg-amber-600 transition border-2 border-white">
						<i class="bx bx-edit-alt text-xs"></i>
					</button>
				</div>
			</div>
			<div class="ml-4 sidebar-hide overflow-hidden whitespace-nowrap transition-all duration-300">
				<span class="block text-sm font-bold text-muted/80">Administrator</span>
				<span class="block text-[11px] text-muted/60 font-medium uppercase tracking-tight">Super Admin</span>
			</div>
		</div>
		<nav id="sidebar-nav" class="flex-1 px-3 py-2 space-y-1 overflow-y-auto pt-8"></nav>
	</div>
</aside>
