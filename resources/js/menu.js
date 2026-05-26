// --- Helper for Space Detection ---
function getOptimalSide(el) {
	const rect = el.getBoundingClientRect();
	const spaceRight = window.innerWidth - rect.right;
	const spaceLeft = rect.left;
	return spaceRight >= spaceLeft ? "right" : "left";
}

function clearAllFloatingMenus() {
	document.querySelectorAll(".floating-menu-bg").forEach((menu) => {
		menu.classList.add("opacity-0", "pointer-events-none");
	});
	document.querySelectorAll(".hover-active").forEach((el) => {
		el.classList.remove("hover-active");
	});
}

// --- Helper for Relative Paths ---
function getRelativePrefix() {
	const scripts = document.getElementsByTagName("script");
	for (const s of scripts) {
		const src = s.getAttribute("src");
		if (src && src.includes("dist/js/app.js")) {
			return src.split("dist/js/app.js")[0] || "./";
		}
	}
	return "./";
}

// --- Floating Submenu Manager ---
function buildFloatingMenu(items, side = "right") {
	const rootPrefix = getRelativePrefix();

	function checkActive(item) {
		if (!item.link || item.link === "#") return false;
		return window.location.pathname.endsWith(item.link) || !!item.active;
	}

	const menuBg = document.createElement("div");
	menuBg.className = "floating-menu-bg p-2 flex flex-col space-y-1";
	if (side) menuBg.dataset.placementSide = side;

	function buildItems(menuItems, container) {
		menuItems.forEach((item) => {
			const itemEl = document.createElement("div");
			itemEl.className = "floating-item relative w-full";

			const hasChildren = item.children && item.children.length > 0;
			const link = document.createElement(hasChildren ? "div" : "a");
			if (!hasChildren) {
				if (item.link && !item.link.startsWith("#") && !item.link.startsWith("http")) {
					link.href = baseUrl(rootPrefix + item.link);
				} else {
					link.href = item.link || "#";
				}
			}

			link.className = `flex flex-row items-center justify-between px-4 py-2 text-sm font-medium rounded-lg transition-all cursor-pointer whitespace-nowrap`;

			const isDirectActive = checkActive(item);
			const isChildActive = hasChildren && item.children.some((child) => checkActive(child));

			if (isDirectActive) link.setAttribute("data-active", "true");
			if (isChildActive) link.setAttribute("data-child-active", "true");

			if (side === "left") {
				link.innerHTML = `
                    ${hasChildren ? `<i class="bx bx-chevron-left text-lg opacity-50 mr-4"></i>` : ""}
                    <span class="flex-1 text-right">${item.title}</span>
                `;
			} else {
				link.innerHTML = `
                    <span class="mr-4">${item.title}</span>
                    ${hasChildren ? `<i class="bx bx-chevron-right text-lg opacity-50"></i>` : ""}
                `;
			}

			itemEl.appendChild(link);

			if (hasChildren) {
				let subFloatingMenu = null;
				let subCleanup = null;
				let subHideTimeout = null;

				function showSubFloating() {
					if (subHideTimeout) {
						clearTimeout(subHideTimeout);
						subHideTimeout = null;
					}

					if (!subFloatingMenu) {
						let subSide = container.closest(".floating-menu-bg")?.dataset.placementSide || "right";
						if (subSide === "bottom" || subSide === "top") {
							subSide = getOptimalSide(itemEl);
						}
						subFloatingMenu = buildFloatingMenu(item.children, subSide);
						subFloatingMenu.classList.add(
							"fixed",
							"z-[2100]",
							"opacity-0",
							"pointer-events-none",
							"transition-all",
							"duration-200"
						);
						subFloatingMenu.style.display = "block";
						document.body.appendChild(subFloatingMenu);
						subFloatingMenu.dataset.level =
							parseInt(container.closest(".floating-menu-bg")?.dataset.level || 0) + 1;

						subFloatingMenu.addEventListener("mouseenter", () => {
							// Recursive ancestor protection
							let current = subFloatingMenu;
							while (current) {
								if (current._clearHideTimeout) current._clearHideTimeout();

								// Find the parent menu item's owner menu
								const parentMenu = current._triggerParentMenu;
								current = parentMenu;
							}
						});
						subFloatingMenu.addEventListener("mouseleave", hideSubFloating);

						// Store reference to parent menu to allow recursive clearing
						subFloatingMenu._triggerParentMenu = container.closest(".floating-menu-bg");
					}

					// Expose clear function for ancestors
					subFloatingMenu._clearHideTimeout = () => {
						if (subHideTimeout) {
							clearTimeout(subHideTimeout);
							subHideTimeout = null;
						}
					};

					const { computePosition, offset, flip, shift, autoUpdate } = window.FloatingUIDOM || {};

					function update() {
						// Inherit side from parent menu if available
						const parentMenu = container.closest(".floating-menu-bg");
						let sideToken = parentMenu ? parentMenu.dataset.placementSide : null;

						// Force lateral side for sub-levels if parent is top/bottom dropdown
						if (!sideToken || sideToken === "bottom" || sideToken === "top") {
							sideToken = getOptimalSide(itemEl);
						}

						const placement = `${sideToken}-start`;
						const middleware = [offset(15), flip(), shift({ padding: 10 })];

						computePosition(itemEl, subFloatingMenu, {
							placement: placement,
							middleware: middleware,
						}).then(({ x, y, placement: finalPlacement }) => {
							Object.assign(subFloatingMenu.style, {
								left: `${x}px`,
								top: `${y}px`,
							});

							const finalSide = finalPlacement.split("-")[0];
							subFloatingMenu.dataset.placementSide = finalSide;

							// dynamic arrow placement
							subFloatingMenu.className = subFloatingMenu.className.replace(/\bplacement-\S+/g, "");
							subFloatingMenu.classList.add(`placement-${finalPlacement}`);
						});
					}

					subFloatingMenu.classList.remove("opacity-0", "pointer-events-none");
					link.classList.add("hover-active");
					subCleanup = autoUpdate(itemEl, subFloatingMenu, update);
				}

				function hideSubFloating() {
					if (subHideTimeout) clearTimeout(subHideTimeout);

					subHideTimeout = setTimeout(() => {
						if (subFloatingMenu) {
							subFloatingMenu.classList.add("opacity-0", "pointer-events-none");
						}
						link.classList.remove("hover-active");
						if (subCleanup) {
							subCleanup();
							subCleanup = null;
						}
					}, 150);
				}

				itemEl.addEventListener("mouseenter", showSubFloating);
				itemEl.addEventListener("mouseleave", hideSubFloating);
			}

			container.appendChild(itemEl);
		});
	}

	buildItems(items, menuBg);
	return menuBg;
}

// --- Recursive Treeview Menu Implementation ---
function renderMenu(items, container, depth = 0) {
	const rootPrefix = getRelativePrefix();

	// Helper to check if an item or any descendant is active
	function checkActive(item) {
		if (item.link && item.link !== "#" && window.location.pathname.endsWith(item.link)) return true;
		if (item.active) return true;
		if (item.children) {
			return item.children.some((child) => checkActive(child));
		}
		return false;
	}

	items.forEach((item) => {
		// Handle Header Items
		if (item.header) {
			const headerEl = document.createElement("div");
			headerEl.className =
				"px-4 pt-6 pb-2 text-[10px] font-bold uppercase tracking-widest text-sidebar-text/60 sidebar-hide transition-all duration-300";
			headerEl.innerHTML = `
                <div class="flex items-center gap-2">
                    <span class="whitespace-nowrap">${item.header}</span>
                    <div class="h-[1px] flex-1 bg-sidebar-text/60"></div>
                </div>
            `;
			container.appendChild(headerEl);

			// For mini-sidebar: add a subtle divider
			const miniDivider = document.createElement("div");
			miniDivider.className = "sidebar-mini-show hidden px-4 py-2";
			miniDivider.innerHTML = `<div class="h-[1px] w-full bg-sidebar-text/50"></div>`;
			container.appendChild(miniDivider);
			return;
		}

		const itemWrapper = document.createElement("div");
		itemWrapper.className = "w-full my-1";

		const hasChildren = item.children && item.children.length > 0;
		const isDirectActive = checkActive(item);
		const isChildActive = hasChildren ? item.children.some((child) => checkActive(child)) : false;
		const isActiveBranch = isDirectActive || isChildActive;

		const link = document.createElement(hasChildren ? "div" : "a");
		if (!hasChildren) {
			if (item.link && !item.link.startsWith("#") && !item.link.startsWith("http")) {
				link.href = baseUrl(rootPrefix + item.link);
			} else {
				link.href = item.link || "#";
			}
		}

		// Styling via CSS variables — no hardcoded colors
		link.className = `flex items-center justify-start px-4 py-2.5 text-sm rounded-lg transition-all cursor-pointer group sidebar-nav-link leading-4`;
		if (isDirectActive) {
			link.setAttribute("data-active", "true");
			link.classList.add("font-bold");
		} else if (isChildActive) {
			link.setAttribute("data-child-active", "true");
			link.classList.add("font-semibold");
		}
		link.title = item.title;
		link.setAttribute("data-depth", depth);

		if (depth > 0) {
			link.style.paddingLeft = `${depth * 1 + 1}rem`;
		}

		let iconHtml = "";
		if (item.icon) {
			iconHtml = `<i class="bx ${item.icon} lg:mr-3 text-lg sidebar-icon flex-shrink-0 leading-4" style="color: inherit"></i>`;
		} else {
			const dotActive = isDirectActive || isChildActive;
			iconHtml = `<div class="w-5 flex items-center justify-center lg:mr-3 sidebar-icon"><div class="w-1.5 h-1.5 rounded-full" style="background: currentColor; opacity: ${dotActive ? "1" : "0.45"}"></div></div>`;
		}

		link.innerHTML = `
            ${iconHtml}
            <span class="sidebar-hide flex-1 leading-4 truncate" style="color: inherit!important">${item.title}</span>
            ${hasChildren ? `<i class="bx bx-chevron-right sidebar-hide text-lg chevron-rotate leading-4 ${isChildActive ? "rotate" : ""}"></i>` : ""}
        `;

		itemWrapper.appendChild(link);

		if (hasChildren) {
			const submenuWrap = document.createElement("div");
			submenuWrap.className = `submenu-container sidebar-hide ${isChildActive ? "open mb-2" : ""}`;

			renderMenu(item.children, submenuWrap, depth + 1);
			itemWrapper.appendChild(submenuWrap);

			link.addEventListener("click", (e) => {
				const sidebar = document.getElementById("sidebar");
				if (sidebar.classList.contains("sidebar-mini")) return;

				submenuWrap.classList.toggle("open");
				const chevron = link.querySelector(".chevron-rotate");
				if (chevron) chevron.classList.toggle("rotate");
			});

			// Smart Floating Submenu (Floating UI)
			let floatingMenu = null;
			let cleanup = null;
			let hideTimeout = null;

			function showFloating() {
				if (hideTimeout) {
					clearTimeout(hideTimeout);
					hideTimeout = null;
				}

				const sidebar = document.getElementById("sidebar");
				if (!sidebar.classList.contains("sidebar-mini")) return;

				if (!floatingMenu) {
					const optimalSide = getOptimalSide(link);
					floatingMenu = buildFloatingMenu(item.children, optimalSide);
					floatingMenu.classList.add(
						"fixed",
						"z-[2000]",
						"opacity-0",
						"pointer-events-none",
						"transition-all",
						"duration-200"
					);
					floatingMenu.style.display = "block";
					document.body.appendChild(floatingMenu);
					floatingMenu.dataset.level = 1;

					// Allow hover on the menu itself
					floatingMenu.addEventListener("mouseenter", () => {
						if (floatingMenu._clearHideTimeout) floatingMenu._clearHideTimeout();
					});
					floatingMenu.addEventListener("mouseleave", hideFloating);
				}

				// Expose clear function
				floatingMenu._clearHideTimeout = () => {
					if (hideTimeout) {
						clearTimeout(hideTimeout);
						hideTimeout = null;
					}
				};

				const { computePosition, offset, flip, shift, autoUpdate } = window.FloatingUIDOM || {};

				function update() {
					const optimalSide = floatingMenu.dataset.placementSide || "right";
					const basePlacement = `${optimalSide}-start`;

					computePosition(link, floatingMenu, {
						placement: basePlacement,
						middleware: [offset(10), flip(), shift({ padding: 5 })],
					}).then(({ x, y, placement: finalPlacement }) => {
						Object.assign(floatingMenu.style, {
							left: `${x}px`,
							top: `${y}px`,
						});

						const finalSide = finalPlacement.split("-")[0];
						floatingMenu.dataset.placementSide = finalSide;

						// dynamic arrow placement
						floatingMenu.className = floatingMenu.className.replace(/\bplacement-\S+/g, "");
						floatingMenu.classList.add(`placement-${finalPlacement}`);
					});
				}

				floatingMenu.classList.remove("opacity-0", "pointer-events-none");
				link.classList.add("hover-active");
				cleanup = autoUpdate(link, floatingMenu, update);
			}

			function hideFloating() {
				if (hideTimeout) clearTimeout(hideTimeout);

				hideTimeout = setTimeout(() => {
					if (floatingMenu) {
						floatingMenu.classList.add("opacity-0", "pointer-events-none");
					}
					link.classList.remove("hover-active");
					if (cleanup) {
						cleanup();
						cleanup = null;
					}
				}, 100); // 100ms grace period to cross the gap
			}

			link.addEventListener("mouseenter", showFloating);
			itemWrapper.addEventListener("mouseleave", hideFloating);
		}

		container.appendChild(itemWrapper);
	});
}

function renderTopMenu(items, container) {
	if (!container) return;
	container.innerHTML = "";
	const rootPrefix = getRelativePrefix();

	function checkActive(item) {
		if (!item.link || item.link === "#") return false;
		return window.location.pathname.endsWith(item.link) || !!item.active;
	}

	function checkBranchActive(item) {
		if (checkActive(item)) return true;
		if (item.children) return item.children.some((c) => checkBranchActive(c));
		return false;
	}

	// Calculate available width
	const header = document.getElementById("app-header");
	if (!header) return;

	// Calculate available width dynamically
	let availableWidth = container.offsetWidth;

	// Fallback if not yet rendered or measured as 0
	if (!availableWidth || availableWidth <= 0) {
		const header = document.getElementById("app-header");
		availableWidth = header ? header.offsetWidth - 300 : 800; // Safe defaults
	}

	// subtract minor buffer for 'More' button
	availableWidth -= 50;

	const filteredItems = items.filter((item) => !item.header);
	const visibleItems = [];
	const overflowItems = [];

	let currentTotalWidth = 0;
	// Rough estimate per item: 40px base + (charCount * 8px)
	filteredItems.forEach((item) => {
		const estimatedWidth = 60 + item.title.length * 8 + (item.children && item.children.length > 0 ? 20 : 0);

		if (currentTotalWidth + estimatedWidth < availableWidth) {
			visibleItems.push(item);
			currentTotalWidth += estimatedWidth;
		} else {
			overflowItems.push(item);
		}
	});

	const itemsToRender = [...visibleItems];
	if (overflowItems.length > 0) {
		itemsToRender.push({
			title: "More",
			icon: "bx-dots-horizontal-rounded",
			children: overflowItems,
			isMore: true,
		});
	}

	itemsToRender.forEach((item) => {
		const hasChildren = item.children && item.children.length > 0;
		const itemWrapper = document.createElement("div");
		itemWrapper.className = "relative flex items-center h-full px-1";

		const isDirectActive = checkActive(item);
		const isChildActive = hasChildren && item.children.some((c) => checkBranchActive(c));

		const link = document.createElement(hasChildren || item.isMore ? "div" : "a");
		if (!hasChildren && !item.isMore) {
			if (item.link && !item.link.startsWith("#") && !item.link.startsWith("http")) {
				link.href = baseUrl(rootPrefix + item.link);
			} else {
				link.href = item.link || "#";
			}
		}

		// Refactored to use same data-active system as sidebar for perfect consistency
		link.className = `top-nav-link flex items-center gap-2 px-3 py-1.5 text-sm font-medium rounded-lg transition-all cursor-pointer whitespace-nowrap`;

		if (isDirectActive) {
			link.setAttribute("data-active", "true");
		} else if (isChildActive) {
			link.setAttribute("data-child-active", "true");
		}

		link.innerHTML = `
            ${item.icon ? `<i class="bx ${item.icon} text-lg"></i>` : ""}
            ${item.title === "More" ? "" : "<span>" + item.title + "</span>"}
        `;

		itemWrapper.appendChild(link);

		if (hasChildren || item.isMore) {
			let floatingMenu = null;
			let cleanup = null;
			let hideTimeout = null;

			function showFloating() {
				if (hideTimeout) {
					clearTimeout(hideTimeout);
					hideTimeout = null;
				}

				if (!floatingMenu) {
					const optimalSide = getOptimalSide(link);
					floatingMenu = buildFloatingMenu(item.children, optimalSide);
					floatingMenu.classList.add(
						"floating-menu-bg",
						"top-nav-dropdown",
						"fixed",
						"z-[2000]",
						"opacity-0",
						"pointer-events-none",
						"transition-all",
						"duration-200"
					);
					floatingMenu.style.display = "block";
					document.body.appendChild(floatingMenu);
					floatingMenu.dataset.level = 1;

					floatingMenu.addEventListener("mouseenter", () => {
						if (floatingMenu._clearHideTimeout) floatingMenu._clearHideTimeout();
					});
					floatingMenu.addEventListener("mouseleave", hideFloating);
				}

				// Expose clear function
				floatingMenu._clearHideTimeout = () => {
					if (hideTimeout) {
						clearTimeout(hideTimeout);
						hideTimeout = null;
					}
				};

				const { computePosition, offset, flip, shift, autoUpdate } = window.FloatingUIDOM || {};

				function update() {
					const optimalSide = getOptimalSide(link);
					const basePlacement = optimalSide === "right" ? "bottom-start" : "bottom-end";

					computePosition(link, floatingMenu, {
						placement: basePlacement,
						middleware: [offset(8), flip(), shift({ padding: 10 })],
					}).then(({ x, y, placement: finalPlacement }) => {
						Object.assign(floatingMenu.style, {
							left: `${x}px`,
							top: `${y}px`,
						});

						const finalSide = item.isMore ? "left" : finalPlacement.split("-")[0];
						floatingMenu.dataset.placementSide = finalSide;

						// dynamic arrow placement
						floatingMenu.className = floatingMenu.className.replace(/\bplacement-\S+/g, "");
						floatingMenu.classList.add(`placement-${finalPlacement}`);
					});
				}

				floatingMenu.classList.remove("opacity-0", "pointer-events-none");
				link.classList.add("hover-active");
				cleanup = autoUpdate(link, floatingMenu, update);
			}

			function hideFloating() {
				if (hideTimeout) clearTimeout(hideTimeout);

				hideTimeout = setTimeout(() => {
					if (floatingMenu) {
						floatingMenu.classList.add("opacity-0", "pointer-events-none");
					}
					link.classList.remove("hover-active");
					if (cleanup) {
						cleanup();
						cleanup = null;
					}
				}, 100);
			}

			link.addEventListener("mouseenter", showFloating);
			link.addEventListener("click", (e) => {
				e.stopPropagation();
				showFloating();
			});
			itemWrapper.addEventListener("mouseleave", hideFloating);
		}

		container.appendChild(itemWrapper);
	});
}

export { renderMenu };
