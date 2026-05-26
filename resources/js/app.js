import { menuConfig } from "./config.js";
import { renderMenu } from "./menu.js";

// =============================================================================
// VIBE SYSTEM UI CORE
// Organized using Clean, Namespaced, and Modular Object Literals.
// Principles applied: SOLID, DRY, KISS, SoC, YAGNI.
// Pure CSS-Driven Design Architecture.
// =============================================================================

// =============================================================================
// SECTION 1: VibeState — Configuration & Palette Definitions
// =============================================================================
const VibeState = {
	VIBE_CONFIG: {},
	defaultSettings: {
		"color-theme": "light",
		"dark-palette": "classic-slate",
		"accent-color": "indigo",
		"bg-preset": "Neutral",
		"canvas-style": "Full",
		"zoom-level": "Standard",
		"nav-mode": "sidebar",
		"sidebar-theme": "expanded",
	},
	accentColors: [
		{ name: "Vibrant Rose", value: "vibrant-rose" },
		{ name: "Electric Orange", value: "electric-orange" },
		{ name: "Solar Yellow", value: "solar-yellow" },
		{ name: "Growth Green", value: "growth-green" },
		{ name: "Sky Cyan", value: "sky-cyan" },
		{ name: "Royal Blue", value: "royal-blue" },
		{ name: "Deep Purple", value: "deep-purple" },
		{ name: "Pink Fuchsia", value: "pink-fuchsia" },
	],
	backgroundPresets: [{ name: "Neutral" }, { name: "Solid" }, { name: "Matching" }],
	darkPalettes: [
		{ name: "Classic Slate", value: "classic-slate", color: "11, 17, 32", border: "51, 65, 85" },
		{ name: "Midnight Onyx", value: "midnight-onyx", color: "10, 10, 10", border: "51, 51, 51" },
		{ name: "Charcoal Elegance", value: "charcoal-elegance", color: "28, 33, 40", border: "68, 76, 86" },
		{ name: "Oceanic Deep", value: "oceanic-deep", color: "9, 14, 23", border: "55, 65, 81" },
	],
	lightPalettes: [
		{ name: "Pure White", value: "pure-white", color: "255, 255, 255", border: "241, 245, 249" },
		{ name: "Soft Slate", value: "soft-slate", color: "248, 250, 252", border: "226, 232, 240" },
		{ name: "Modern Gray", value: "modern-gray", color: "243, 244, 246", border: "209, 213, 219" },
		{ name: "Oceanic Light", value: "oceanic-light", color: "240, 249, 255", border: "186, 230, 253" },
	],
	canvasStyles: [
		{ name: "Full", icon: "bx-window-alt", desc: "Standard expansive layout" },
		{ name: "Boxed", icon: "bx-dock-top", desc: "Structured max-width container" },
		{ name: "Soft", icon: "bx-layout", desc: "Distance and rounded clarity" },
		{ name: "Floating", icon: "bx-layer", desc: "Equal margins and full rounding" },
	],
	zoomLevels: [
		{ name: "Compact", icon: "bx-zoom-out", desc: "Dense info" },
		{ name: "Standard", icon: "bx-screenshot", desc: "Default view" },
		{ name: "Zoom", icon: "bx-zoom-in", desc: "High visibility" },
	],
	navModes: [
		{ name: "Sidebar", value: "sidebar", icon: "bx-dock-left", desc: "Traditional vertical menu" },
		{ name: "Top Nav", value: "top", icon: "bx-dock-top", desc: "Modern horizontal links" },
	],
	toastTypeMap: {
		success: {
			icon: "bx-check-circle",
			accent: "text-emerald-500",
			bg: "bg-emerald-100 dark:bg-emerald-950/90",
			border: "border border-emerald-100 dark:border-emerald-800",
		},
		danger: {
			icon: "bx-error",
			accent: "text-rose-500",
			bg: "bg-rose-100 dark:bg-rose-950/90",
			border: "border border-rose-100 dark:border-rose-800",
		},
		warning: {
			icon: "bx-error-circle",
			accent: "text-amber-500",
			bg: "bg-amber-100 dark:bg-amber-950/90",
			border: "border border-amber-100 dark:border-amber-800",
		},
		info: {
			icon: "bx-info-circle",
			accent: "text-sky-500",
			bg: "bg-sky-100 dark:bg-sky-950/90",
			border: "border border-sky-100 dark:border-sky-800",
		},
	},
};

// =============================================================================
// SECTION 2: VibeUtils — Helper & Utility Logic
// =============================================================================
const VibeUtils = {
	/**
	 * Helper to determine the relative path prefix to the project root
	 */
	getRelativePrefix() {
		const scripts = document.getElementsByTagName("script");
		for (const s of scripts) {
			const src = s.getAttribute("src");
			if (src && src.includes("dist/js/app.js")) {
				return src.split("dist/js/app.js")[0] || "./";
			}
		}
		return "./";
	},

	/**
	 * Gets a setting value, prioritized: localStorage (for sidebar-theme) > DOM Attribute > Hardcoded Default
	 */
	getVibeConfig(key) {
		if (key === "sidebar-theme") {
			const stored = localStorage.getItem("vibe-template.sidebar-theme");
			if (stored !== null) return stored;
		}

		let val = null;
		if (key === "color-theme") {
			val =
				document.documentElement.getAttribute("data-theme") ||
				(document.documentElement.classList.contains("dark") ? "dark" : "light");
		} else if (key === "sidebar-theme") {
			val = document.documentElement.getAttribute("data-sidebar-theme");
		} else if (key === "accent-color") {
			val = document.documentElement.getAttribute("data-accent");
		} else if (key === "bg-preset") {
			val = document.documentElement.getAttribute("data-bg-preset");
		} else if (key === "canvas-style") {
			val = document.documentElement.getAttribute("data-canvas-style");
		} else if (key === "zoom-level") {
			val = document.documentElement.getAttribute("data-zoom-level");
		} else if (key === "nav-mode") {
			val = document.documentElement.getAttribute("data-nav-mode");
		} else if (key === "light-palette") {
			val = document.documentElement.getAttribute("data-light-palette");
		} else if (key === "dark-palette") {
			val = document.documentElement.getAttribute("data-dark-palette");
		}

		if (val) return val;
		return VibeState.defaultSettings[key];
	},

	/**
	 * Loads default configurations (unused in pure CSS-driven design)
	 */
	async loadVibeConfig() {
		// No-op: theme states are loaded directly from DOM attributes
	},

	/**
	 * Shorthand for document.documentElement.style.setProperty
	 */
	cssVar(prop, value) {
		document.documentElement.style.setProperty(prop, value);
	},

	/**
	 * Shorthand for document.documentElement.style.removeProperty
	 */
	cssVarRemove(prop) {
		document.documentElement.style.removeProperty(prop);
	},

	/**
	 * Safely call clearAllFloatingMenus if it exists in the global namespace.
	 */
	safeClearMenus() {
		if (typeof clearAllFloatingMenus === "function") clearAllFloatingMenus();
	},

	/**
	 * Parses an RGB string "r, g, b" to an array [r, g, b]
	 */
	parseRgb(rgbString) {
		return rgbString.split(",").map((c) => parseInt(c.trim()));
	},

	/**
	 * Applies classes from a space-separated string, filtering empty components
	 */
	addClassesFromString(el, classString) {
		if (el && classString) {
			el.classList.add(...classString.split(" ").filter((c) => c));
		}
	},

	/**
	 * Toggles the active class on a group of buttons based on data-name/data-value
	 */
	toggleActiveClass(selector, currentValue, activeClasses, allClasses, attrName = "data-name") {
		document.querySelectorAll(selector).forEach((el) => {
			el.classList.remove(...allClasses);
			if (el.getAttribute(attrName) === currentValue) {
				el.classList.add(...activeClasses);
			}
		});
	},

	/**
	 * Toggles swatch ring and appends check icon when active
	 */
	toggleSwatchRing(selector, currentValue, attrName = "data-name") {
		document.querySelectorAll(selector).forEach((sw) => {
			const isActive = sw.getAttribute(attrName) === currentValue;
			sw.classList.toggle("ring-4", isActive);
			sw.classList.toggle("ring-primary-500/30", isActive);
			sw.classList.toggle("border-white", isActive);
			sw.innerHTML = isActive ? '<i class="bx bx-check text-white text-xl"></i>' : "";
		});
	},

	/**
	 * Renders color palette swatch lists
	 */
	renderPaletteSwatches(containerId, palettes, swatchClass, onClickFn) {
		const container = document.getElementById(containerId);
		if (!container) return;
		container.innerHTML = "";
		palettes.forEach((palette) => {
			const swatch = document.createElement("div");
			swatch.className = `${swatchClass} w-10 h-10 rounded-full cursor-pointer transition-all hover:scale-110 flex items-center justify-center border-2 border-transparent`;
			swatch.setAttribute("data-name", palette.value);
			swatch.style.backgroundColor = `rgb(${palette.color})`;
			swatch.style.borderColor = `rgb(${palette.border})`;
			swatch.onclick = () => onClickFn(palette.value);
			container.appendChild(swatch);
		});
	},
};

// =============================================================================
// SECTION 3: VibeTheme — Sidebar, Theme Toggle, & Customizer Updates
// =============================================================================
const VibeTheme = {
	// Elements retrieved dynamically to prevent null reference issues
	get sidebar() {
		return document.getElementById("sidebar");
	},
	get overlay() {
		return document.getElementById("sidebar-overlay");
	},
	get mobileToggle() {
		return document.getElementById("mobile-sidebar-toggle");
	},
	get desktopToggle() {
		return document.getElementById("desktop-sidebar-toggle");
	},
	get toggleIcon() {
		return document.getElementById("toggle-icon");
	},
	get logoutBtn() {
		return document.getElementById("logout-btn");
	},

	/**
	 * Manages the sidebar expanded/minimized layout state natively in CSS
	 */
	setSidebarState(isMini) {
		const isMobile = window.innerWidth < 768;
		const themeValue = (isMobile ? false : isMini) ? "mini" : "expanded";

		VibeUtils.safeClearMenus();

		const sidebar = this.sidebar;
		if (!sidebar) return;

		document.documentElement.setAttribute("data-sidebar-theme", themeValue);

		// Maintain sidebar-mini class on the sidebar for legacy CSS layout selectors
		if (themeValue === "mini") {
			sidebar.classList.add("sidebar-mini");
			sidebar.classList.remove("w-64");
			sidebar.classList.add("w-20");
		} else {
			sidebar.classList.remove("sidebar-mini");
			sidebar.classList.remove("w-20");
			sidebar.classList.add("w-64");
		}

		const toggleIcon = this.toggleIcon;
		if (toggleIcon) {
			if (themeValue === "mini") {
				toggleIcon.classList.replace("bx-chevron-left", "bx-chevron-right");
			} else {
				toggleIcon.classList.replace("bx-chevron-right", "bx-chevron-left");
			}
		}

		// Adjust link depths in non-mini modes dynamically (submenus support)
		document.querySelectorAll(".sidebar-nav-link").forEach((el) => {
			const depth = el.getAttribute("data-depth");
			if (themeValue === "mini") {
				el.style.paddingLeft = "";
			} else if (depth > 0) {
				el.style.paddingLeft = `${depth * 1 + 1}rem`;
			} else {
				el.style.paddingLeft = "";
			}
		});

		if (!isMobile) {
			localStorage.setItem("vibe-template.sidebar-theme", themeValue);
		}
	},

	/**
	 * Synchronizes theme toggler icons with current body class (dark/light)
	 */
	updateThemeIcons() {
		const themeToggleDarkIcon = document.getElementById("theme-toggle-dark-icon");
		const themeToggleLightIcon = document.getElementById("theme-toggle-light-icon");
		if (document.documentElement.classList.contains("dark")) {
			console.log(console.log(themeToggleDarkIcon));
			if (themeToggleDarkIcon) themeToggleDarkIcon.classList.add("hidden!");
			if (themeToggleLightIcon) themeToggleLightIcon.classList.remove("hidden!");
		} else {
			if (themeToggleDarkIcon) themeToggleDarkIcon.classList.remove("hidden!");
			if (themeToggleLightIcon) themeToggleLightIcon.classList.add("hidden!");
		}
	},

	/**
	 * Updates accent colors throughout the page by binding a semantic Root Attribute
	 */
	updateAccentColor(colorName) {
		VibeUtils.safeClearMenus();
		document.documentElement.setAttribute("data-accent", colorName);

		const avatar = document.getElementById("side-avatar");
		if (avatar) {
			const avatarHexMap = {
				indigo: "4f46e5",
				"vibrant-rose": "e11d48",
				"electric-orange": "ea580c",
				"solar-yellow": "ca8a04",
				"growth-green": "16a34a",
				"sky-cyan": "0891b2",
				"royal-blue": "2563eb",
				"deep-purple": "7c3aed",
				"pink-fuchsia": "db2677",
			};
			const hex = avatarHexMap[colorName] || "4f46e5";
			avatar.src = `https://ui-avatars.com/api/?name=Admin&background=${hex}&color=fff`;
		}

		VibeRender.renderBackgroundPresets();
		this.updateBackgroundPreset(VibeUtils.getVibeConfig("bg-preset"));
		VibeRender.refreshActiveSwatches();
	},

	/**
	 * Changes the theme's background preset layout style via Root Attributes
	 */
	updateBackgroundPreset(presetName) {
		VibeUtils.safeClearMenus();
		document.documentElement.setAttribute("data-bg-preset", presetName);

		const elSidebar = this.sidebar;

		if (presetName === "Solid") {
			if (elSidebar) elSidebar.classList.remove("bg-surface", "bg-body", "bg-alt");
		} else {
			const currentCanvasStyle = VibeUtils.getVibeConfig("canvas-style");
			const canvasToken = VibeState.canvasStyles.find((s) => s.name === currentCanvasStyle);
			if (elSidebar && canvasToken && canvasToken.sidebarClass) {
				VibeUtils.addClassesFromString(elSidebar, canvasToken.sidebarClass);
			}
		}

		const navMode = VibeUtils.getVibeConfig("nav-mode");
		const header = document.getElementById("app-header");
		if (header) {
			const headerControls = header.querySelectorAll(
				'#theme-toggle, #settings-toggle, .flex.p-1.rounded-lg[style*="rgb(var(--color-bg-alt))"]'
			);

			if (presetName === "Solid" && navMode === "top") {
				header.classList.add("header-solid");
				header.classList.remove("bg-surface", "bg-body", "bg-alt");
				header.style.backgroundColor = "";
				headerControls.forEach((ctrl) => {
					ctrl.style.backgroundColor = "";
					ctrl.style.borderColor = "";
				});
			} else {
				header.classList.remove("header-solid");
				header.style.backgroundColor = "";
				headerControls.forEach((ctrl) => {
					ctrl.style.backgroundColor = "rgb(var(--color-bg-alt))";
					ctrl.style.borderColor = "rgb(var(--color-border))";
				});
			}
		}

		VibeRender.refreshActiveSwatches();
	},

	/**
	 * Switches canvas wrapping styles (Full, Boxed, Soft, Floating) in a pure CSS manner
	 */
	updateCanvasStyle(styleName) {
		VibeUtils.safeClearMenus();
		document.documentElement.setAttribute("data-canvas-style", styleName);

		const isMini = VibeUtils.getVibeConfig("sidebar-theme") === "mini";
		this.setSidebarState(isMini);

		const bgPreset = VibeUtils.getVibeConfig("bg-preset");
		this.updateBackgroundPreset(bgPreset);
		VibeRender.refreshActiveSwatches();
	},

	/**
	 * Sets zoom level metrics on the root element
	 */
	updateZoomLevel(levelName) {
		VibeUtils.safeClearMenus();
		const loader = document.getElementById("app-loader");
		const isInitial = !document.body.classList.contains("preload") === false;

		if (loader && !isInitial) {
			let loaderText = loader.querySelector(".loader-text");
			if (!loaderText) {
				loaderText = document.createElement("div");
				loaderText.className = "loader-text";
				loader.appendChild(loaderText);
			}
			loaderText.textContent = "Rescaling Layout...";
			loader.classList.remove("fade-out");

			setTimeout(() => {
				document.documentElement.setAttribute("data-zoom-level", levelName);
				setTimeout(() => {
					loader.classList.add("fade-out");
				}, 500);
			}, 300);
		} else {
			document.documentElement.setAttribute("data-zoom-level", levelName);
		}

		VibeRender.refreshActiveSwatches();
	},

	/**
	 * Switches navigation mode between Top Nav and Sidebar layout
	 */
	updateNavMode(mode) {
		VibeUtils.safeClearMenus();
		const isMobile = window.innerWidth < 768;

		document.documentElement.setAttribute("data-nav-mode", mode);

		const layout = document.getElementById("app-layout");
		const sidebar = this.sidebar;
		const header = document.getElementById("app-header");

		if (!layout || !header) return;

		if (mode === "top") {
			layout.classList.add("layout-top-nav");
			if (sidebar) {
				sidebar.classList.add("md:hidden");
				sidebar.classList.remove("hidden");
			}

			let topNav = document.getElementById("top-navigation-bar");
			const headerTitle = header.querySelector("h1");

			if (!topNav) {
				topNav = document.createElement("div");
				topNav.id = "top-navigation-bar";
				topNav.className = "hidden md:flex flex-1 overflow-x-auto no-scrollbar";
				if (headerTitle) {
					headerTitle.after(topNav);
				}
			} else {
				topNav.classList.remove("hidden");
			}

			if (headerTitle) {
				headerTitle.classList.add("hidden");
				headerTitle.parentElement.classList.add("flex-1");
			}

			if (typeof renderTopMenu === "function" && typeof menuConfig !== "undefined") {
				renderTopMenu(menuConfig, topNav);
			}
		} else {
			layout.classList.remove("layout-top-nav");
			if (sidebar) {
				sidebar.classList.remove("md:hidden");
				sidebar.classList.remove("hidden");
			}

			const topNav = document.getElementById("top-navigation-bar");
			if (topNav) topNav.classList.add("hidden");

			const headerTitle = header.querySelector("h1");
			if (headerTitle) {
				headerTitle.classList.remove("hidden");
				headerTitle.parentElement.classList.remove("flex-1");
			}
		}

		this.updateBackgroundPreset(VibeUtils.getVibeConfig("bg-preset"));
		VibeRender.refreshActiveSwatches();
	},

	/**
	 * Updates dark theme palette choice
	 */
	updateDarkPalette(paletteName) {
		VibeUtils.safeClearMenus();
		document.documentElement.setAttribute("data-dark-palette", paletteName);
		this.updateBackgroundPreset(VibeUtils.getVibeConfig("bg-preset"));
		VibeRender.renderDarkPalettes();
		VibeRender.refreshActiveSwatches();
	},

	/**
	 * Updates light theme palette choice
	 */
	updateLightPalette(paletteName) {
		VibeUtils.safeClearMenus();
		document.documentElement.setAttribute("data-light-palette", paletteName);
		this.updateBackgroundPreset(VibeUtils.getVibeConfig("bg-preset"));
		VibeRender.renderLightPalettes();
		VibeRender.refreshActiveSwatches();
	},
};

// =============================================================================
// SECTION 4: VibeRender — Renderers & UI Sync Logic
// =============================================================================
const VibeRender = {
	/**
	 * Renders the Background Style panel selector buttons
	 */
	renderBackgroundPresets() {
		const bgList = document.getElementById("bg-presets-list");
		if (!bgList) return;
		bgList.innerHTML = "";
		VibeState.backgroundPresets.forEach((preset) => {
			const btn = document.createElement("button");
			btn.className = `bg-preset-btn text-left px-4 py-3 rounded-xl border transition-all hover:border-primary-600`;
			btn.setAttribute("data-name", preset.name);

			const previewHtml =
				preset.name === "Solid"
					? `<div class="w-4 h-4 rounded-full border shadow-sm bg-primary" style="background-color: rgb(var(--color-primary))"></div>`
					: `<div class="w-4 h-4 rounded-full border bg-white"></div>
           <div class="w-4 h-4 rounded-full border bg-slate-900"></div>`;

			btn.innerHTML = `
        <span class="block text-sm font-bold">${preset.name}</span>
        <div class="flex gap-1 mt-1.5">${previewHtml}</div>
      `;
			btn.onclick = () => VibeTheme.updateBackgroundPreset(preset.name);
			bgList.appendChild(btn);
		});
	},

	/**
	 * Renders the Canvas Wrapping Style panel selector buttons
	 */
	renderCanvasStyles() {
		const canvasList = document.getElementById("canvas-styles-list");
		if (!canvasList) return;
		canvasList.innerHTML = "";
		VibeState.canvasStyles.forEach((style) => {
			const btn = document.createElement("button");
			btn.className = `canvas-style-btn text-left p-2.5 rounded-lg border transition-all hover:border-primary-600 flex flex-col gap-1.5`;
			btn.setAttribute("data-name", style.name);
			btn.innerHTML = `
        <div class="w-7 h-7 rounded-md bg-primary-50 flex items-center justify-center">
          <i class="bx ${style.icon} text-lg"></i>
        </div>
        <div>
          <span class="block text-[10px] font-bold uppercase tracking-tight">${style.name}</span>
          <span class="block text-[8px] leading-tight mt-0.5">${style.desc}</span>
        </div>
      `;
			btn.onclick = () => VibeTheme.updateCanvasStyle(style.name);
			canvasList.appendChild(btn);
		});
	},

	/**
	 * Renders the Layout Zoom level panel selector buttons
	 */
	renderZoomLevels() {
		const zoomList = document.getElementById("zoom-levels-list");
		if (!zoomList) return;
		zoomList.innerHTML = "";
		VibeState.zoomLevels.forEach((level) => {
			const btn = document.createElement("button");
			btn.className = `zoom-level-btn text-center p-2 rounded-lg border transition-all hover:border-primary-600 flex flex-col items-center gap-1`;
			btn.setAttribute("data-name", level.name);
			btn.innerHTML = `
        <i class="bx ${level.icon} text-lg mb-0.5"></i>
        <span class="block text-[9px] font-bold uppercase tracking-tighter">${level.name}</span>
      `;
			btn.onclick = () => VibeTheme.updateZoomLevel(level.name);
			zoomList.appendChild(btn);
		});
	},

	/**
	 * Renders the Navigation Mode panel selector buttons
	 */
	renderNavModes() {
		const navList = document.getElementById("nav-mode-list");
		if (!navList) return;
		navList.innerHTML = "";
		VibeState.navModes.forEach((mode) => {
			const btn = document.createElement("button");
			btn.className = `nav-mode-btn text-left p-2.5 rounded-lg border transition-all hover:border-primary-600 flex flex-col gap-1.5`;
			btn.setAttribute("data-name", mode.value);
			btn.innerHTML = `
        <div class="w-7 h-7 rounded-md bg-primary-50 flex items-center justify-center">
          <i class="bx ${mode.icon} text-lg"></i>
        </div>
        <div>
          <span class="block text-[10px] font-bold uppercase tracking-tight">${mode.name}</span>
          <span class="block text-[8px] leading-tight mt-0.5">${mode.desc}</span>
        </div>
      `;
			btn.onclick = () => VibeTheme.updateNavMode(mode.value);
			navList.appendChild(btn);
		});
	},

	/**
	 * Renders the dark color palettes swatches list
	 */
	renderDarkPalettes() {
		VibeUtils.renderPaletteSwatches("dark-palettes-list", VibeState.darkPalettes, "dark-palette-swatch", (value) =>
			VibeTheme.updateDarkPalette(value)
		);
	},

	/**
	 * Renders the light color palettes swatches list
	 */
	renderLightPalettes() {
		VibeUtils.renderPaletteSwatches(
			"light-palettes-list",
			VibeState.lightPalettes,
			"light-palette-swatch",
			(value) => VibeTheme.updateLightPalette(value)
		);
	},

	/**
	 * Re-evaluates active selector items and applies rings/accent color classes
	 */
	refreshActiveSwatches() {
		const currentAccent = VibeUtils.getVibeConfig("accent-color");
		const isDark = document.documentElement.classList.contains("dark");

		const nameLabel = document.getElementById("selected-accent-name");
		if (nameLabel) {
			const selectedAccent =
				VibeState.accentColors.find((c) => c.value === currentAccent) || VibeState.accentColors[0];
			nameLabel.textContent = selectedAccent.name;
		}

		VibeUtils.toggleSwatchRing(".color-swatch", currentAccent, "data-value");

		const activeClasses = isDark
			? ["bg-white", "text-slate-900", "border-white"]
			: ["bg-primary-600", "text-white", "border-primary-600"];
		const allPossibleActiveClasses = [
			"bg-white",
			"text-slate-900",
			"border-white",
			"bg-primary-600",
			"text-white",
			"border-primary-600",
		];

		VibeUtils.toggleActiveClass(
			".bg-preset-btn",
			VibeUtils.getVibeConfig("bg-preset"),
			activeClasses,
			allPossibleActiveClasses
		);
		VibeUtils.toggleActiveClass(
			".canvas-style-btn",
			VibeUtils.getVibeConfig("canvas-style"),
			activeClasses,
			allPossibleActiveClasses
		);
		VibeUtils.toggleActiveClass(
			".zoom-level-btn",
			VibeUtils.getVibeConfig("zoom-level"),
			activeClasses,
			allPossibleActiveClasses
		);
		VibeUtils.toggleActiveClass(
			".nav-mode-btn",
			VibeUtils.getVibeConfig("nav-mode"),
			activeClasses,
			allPossibleActiveClasses
		);

		const currentPalette = VibeUtils.getVibeConfig("dark-palette");
		const darkNameLabel = document.getElementById("selected-dark-palette-name");
		if (darkNameLabel) {
			const selectedPalette =
				VibeState.darkPalettes.find((p) => p.value === currentPalette) || VibeState.darkPalettes[0];
			darkNameLabel.textContent = selectedPalette.name;
		}
		VibeUtils.toggleSwatchRing(".dark-palette-swatch", currentPalette);

		const currentLightPalette = VibeUtils.getVibeConfig("light-palette");
		const lightNameLabel = document.getElementById("selected-light-palette-name");
		if (lightNameLabel) {
			const selectedPalette =
				VibeState.lightPalettes.find((p) => p.value === currentLightPalette) || VibeState.lightPalettes[1];
			lightNameLabel.textContent = selectedPalette.name;
		}
		VibeUtils.toggleSwatchRing(".light-palette-swatch", currentLightPalette);
	},
};

// =============================================================================
// SECTION 5: VibeComponents — Modals, Clipboard, & Toast Systems
// =============================================================================
const VibeComponents = {
	/**
	 * Opens modal elements and handles body locking and backdrop transitions
	 */
	openModal(modalId) {
		const modal = document.getElementById(modalId);
		if (!modal) {
			console.warn(`Modal with ID "${modalId}" not found.`);
			return;
		}

		let backdrop = document.querySelector(".vibe-modal-backdrop");
		if (!backdrop) {
			backdrop = document.createElement("div");
			backdrop.className = "vibe-modal-backdrop";
			document.body.appendChild(backdrop);
		}

		modal.style.display = "flex";
		backdrop.style.display = "block";

		void modal.offsetHeight;
		void backdrop.offsetHeight;

		modal.classList.add("show");
		backdrop.classList.add("show");
		document.body.style.overflow = "hidden";
	},

	/**
	 * Closes active modal elements smoothly
	 */
	closeModal(modalId) {
		const modal = document.getElementById(modalId);
		const backdrop = document.querySelector(".vibe-modal-backdrop");
		if (!modal) return;

		modal.classList.remove("show");
		if (backdrop) backdrop.classList.remove("show");

		setTimeout(() => {
			if (!modal.classList.contains("show")) {
				modal.style.display = "none";
			}
			if (!document.querySelector(".vibe-modal.show")) {
				if (backdrop) backdrop.style.display = "none";
				document.body.style.overflow = "";
			}
		}, 300);
	},

	/**
	 * Handles dynamic toast and notification creation
	 */
	vibeToast: {
		containers: {},

		getContainer(position = "bottom-right") {
			const id = `vibe-toast-container-${position}`;
			if (this.containers[id]) return this.containers[id];

			let container = document.getElementById(id);
			if (!container) {
				container = document.createElement("div");
				container.id = id;
				container.className = `vibe-toast-container vibe-toast-${position}`;
				document.body.appendChild(container);
			}
			this.containers[id] = container;
			return container;
		},

		show({ title = "Notification", message = "", type = "info", position = "bottom-right", duration = 5000 }) {
			const container = this.getContainer(position);
			const toast = document.createElement("div");

			const typeStyle = VibeState.toastTypeMap[type] || VibeState.toastTypeMap.info;
			const { icon, accent: accentColor, bg: bgClass, border: borderClass } = typeStyle;

			let animIn = "toast-animate-in-right";
			if (position.includes("left")) animIn = "toast-animate-in-left";
			if (position.includes("center"))
				animIn = position.includes("top") ? "toast-animate-in-top" : "toast-animate-in-bottom";

			toast.className = `flex items-center p-4 ${bgClass} ${borderClass} rounded-xl shadow-2xl transition-all duration-300 ${animIn} max-w-sm`;
			toast.innerHTML = `
        <i class="bx ${icon} ${accentColor} text-2xl mr-3"></i>
        <div class="flex-1">
          <h6 class="text-sm font-bold leading-none mb-1">${title}</h6>
          <p class="text-xs text-muted">${message}</p>
        </div>
        <button class="text-muted hover:text-rose-500 ml-4 transition-colors" onclick="this.parentElement.remove()"><i class="bx bx-x"></i></button>
      `;

			if (position.startsWith("top")) {
				container.prepend(toast);
			} else {
				container.appendChild(toast);
			}

			requestAnimationFrame(() => {
				setTimeout(() => {
					toast.classList.remove(
						"toast-animate-in-top",
						"toast-animate-in-bottom",
						"toast-animate-in-left",
						"toast-animate-in-right",
						"opacity-0"
					);
				}, 10);
			});

			if (duration > 0) {
				setTimeout(() => {
					toast.classList.add("opacity-0");
					if (position.includes("top")) toast.classList.add("toast-animate-in-top");
					else toast.classList.add("toast-animate-in-bottom");

					setTimeout(() => toast.remove(), 300);
				}, duration);
			}
		},
	},
};

// =============================================================================
// SECTION 6: VibeEvents — Initialization & Interactive Event Handlers
// =============================================================================
const VibeEvents = {
	settingsPanel: null,
	settingsBackdrop: null,

	/**
	 * Bootstraps theme configurations and settings panels
	 */
	initSettingsUI() {
		const accentList = document.getElementById("accent-colors-list");
		if (accentList) {
			accentList.innerHTML = "";
			const accentColorPreviewMap = {
				indigo: "79, 70, 229",
				"vibrant-rose": "225, 29, 72",
				"electric-orange": "234, 88, 12",
				"solar-yellow": "202, 138, 4",
				"growth-green": "22, 163, 74",
				"sky-cyan": "8, 145, 178",
				"royal-blue": "37, 99, 235",
				"deep-purple": "124, 58, 237",
				"pink-fuchsia": "219, 39, 119",
			};
			VibeState.accentColors.forEach((color) => {
				const swatch = document.createElement("div");
				swatch.className = `color-swatch w-10 h-10 rounded-full cursor-pointer transition-all hover:scale-110 flex items-center justify-center border-2 border-transparent`;
				swatch.setAttribute("data-value", color.value);
				const rgbVal = accentColorPreviewMap[color.value] || "79, 70, 229";
				swatch.style.backgroundColor = `rgb(${rgbVal})`;
				swatch.onclick = () => VibeTheme.updateAccentColor(color.value);
				accentList.appendChild(swatch);
			});
		}

		VibeRender.renderBackgroundPresets();
		VibeRender.renderCanvasStyles();
		VibeRender.renderZoomLevels();
		VibeRender.renderNavModes();
		VibeRender.renderDarkPalettes();
		VibeRender.renderLightPalettes();

		const savedAccent = VibeUtils.getVibeConfig("accent-color");
		if (savedAccent) VibeTheme.updateAccentColor(savedAccent);

		const savedBg = VibeUtils.getVibeConfig("bg-preset");
		if (savedBg) VibeTheme.updateBackgroundPreset(savedBg);

		VibeTheme.updateCanvasStyle(VibeUtils.getVibeConfig("canvas-style") || "Full");
		VibeTheme.updateZoomLevel(VibeUtils.getVibeConfig("zoom-level") || "Standard");
		VibeTheme.updateNavMode(VibeUtils.getVibeConfig("nav-mode") || "sidebar");

		const savedLightPalette = VibeUtils.getVibeConfig("light-palette");
		if (savedLightPalette) VibeTheme.updateLightPalette(savedLightPalette);
	},

	/**
	 * Initializes tooltips and dropdown menus using Floating UI
	 */
	initFloatingComponents() {
		if (typeof FloatingUIDOM === "undefined") {
			console.warn(
				"Floating UI library (FloatingUIDOM) not found. Tooltips and dropdowns will not be initialized."
			);
			return;
		}
		const { computePosition, autoUpdate, flip, shift, offset } = FloatingUIDOM;

		document.querySelectorAll(".dropdown-toggle").forEach((toggle) => {
			const dropdown = toggle.closest(".dropdown");
			const menu = dropdown ? dropdown.querySelector(".dropdown-menu") : null;
			if (!menu) return;

			function updatePosition() {
				const placement = toggle.getAttribute("data-placement") || "bottom-start";
				computePosition(toggle, menu, {
					placement: placement,
					middleware: [offset(8), flip(), shift({ padding: 5 })],
				}).then(({ x, y }) => {
					Object.assign(menu.style, { left: `${x}px`, top: `${y}px` });
					menu.setAttribute("data-placement", placement);
				});
			}

			function toggleMenu(show) {
				if (show) {
					document.querySelectorAll(".dropdown-menu.show").forEach((m) => {
						if (m !== menu) {
							m.classList.remove("show");
							const t = m.closest(".dropdown")?.querySelector(".dropdown-toggle");
							if (t && t._floatingCleanup) {
								t._floatingCleanup();
								t._floatingCleanup = null;
							}
						}
					});

					menu.classList.add("show");
					toggle._floatingCleanup = autoUpdate(toggle, menu, updatePosition);
				} else {
					menu.classList.remove("show");
					if (toggle._floatingCleanup) {
						toggle._floatingCleanup();
						toggle._floatingCleanup = null;
					}
				}
			}

			const trigger = toggle.getAttribute("data-trigger") || "click";

			if (trigger.includes("click")) {
				toggle.addEventListener("click", (e) => {
					e.stopPropagation();
					toggleMenu(!menu.classList.contains("show"));
				});
			}

			if (trigger.includes("mouseenter")) {
				let hideTimeout = null;

				const handleEnter = () => {
					if (hideTimeout) {
						clearTimeout(hideTimeout);
						hideTimeout = null;
					}
					toggleMenu(true);
				};

				const handleLeave = () => {
					if (hideTimeout) clearTimeout(hideTimeout);
					hideTimeout = setTimeout(() => {
						toggleMenu(false);
					}, 150);
				};

				toggle.addEventListener("mouseenter", handleEnter);
				dropdown.addEventListener("mouseleave", handleLeave);
				menu.addEventListener("mouseenter", handleEnter);
				menu.addEventListener("mouseleave", handleLeave);
			}
		});

		document.querySelectorAll("[data-vibe-tooltip], [data-tippy-content]").forEach((ref) => {
			const content = ref.getAttribute("data-vibe-tooltip") || ref.getAttribute("data-tippy-content");
			if (!content) return;

			const tooltip = document.createElement("div");
			tooltip.className =
				"vibe-tooltip fixed bg-slate-800 text-white text-[11px] font-bold px-2 py-1.5 rounded-lg shadow-xl z-[2000] opacity-0 pointer-events-none transition-opacity duration-200";
			tooltip.style.whiteSpace = "pre-line";
			tooltip.style.maxWidth = "250px";
			tooltip.textContent = content.replace(/\\n/g, "\n");
			document.body.appendChild(tooltip);

			let cleanup;

			function update() {
				const placement = ref.getAttribute("data-placement") || "top";
				computePosition(ref, tooltip, {
					placement: placement,
					middleware: [offset(8), flip(), shift({ padding: 5 })],
				}).then(({ x, y }) => {
					Object.assign(tooltip.style, { left: `${x}px`, top: `${y}px` });
				});
			}

			ref.addEventListener("mouseenter", () => {
				tooltip.classList.remove("opacity-0");
				cleanup = autoUpdate(ref, tooltip, update);
			});

			ref.addEventListener("mouseleave", () => {
				tooltip.classList.add("opacity-0");
				if (cleanup) cleanup();
			});
		});

		document.addEventListener("click", (e) => {
			if (!e.target.closest(".dropdown")) {
				document.querySelectorAll(".dropdown-menu.show").forEach((menu) => {
					menu.classList.remove("show");
					const toggle = menu.closest(".dropdown")?.querySelector(".dropdown-toggle");
					if (toggle && toggle._floatingCleanup) {
						toggle._floatingCleanup();
						toggle._floatingCleanup = null;
					}
				});
			}
		});
	},

	/**
	 * Generates the Theme Settings customization side panel dynamically
	 */
	createSettingsPanel() {
		this.settingsPanel = document.createElement("div");
		this.settingsPanel.id = "settings-panel";
		this.settingsPanel.className = "bg-surface border-l shadow-2xl flex flex-col transition-all duration-300";
		this.settingsPanel.innerHTML = `
        <div class="flex items-center justify-between p-6">
            <h2 class="text-lg font-bold">Theme Settings</h2>
            <button id="close-settings" class="hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition">
                <i class="bx bx-x text-2xl"></i>
            </button>
        </div>
        <div class="space-y-8 flex-1 overflow-y-auto p-6">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <label class="text-sm font-semibold text-muted uppercase tracking-wider">Accent Color</label>
                    <span id="selected-accent-name" class="text-xs font-bold text-primary-600 px-2 py-0.5 rounded-full bg-primary-50">Indigo</span>
                </div>
                <div class="grid grid-cols-4 gap-4" id="accent-colors-list"></div>
            </div>
            <div class="dark:block hidden">
                <div class="flex items-center justify-between mb-4">
                    <label class="text-sm font-semibold text-muted uppercase tracking-wider">Dark Palette</label>
                    <span id="selected-dark-palette-name" class="text-xs font-bold text-slate-400 px-2 py-0.5 rounded-full bg-slate-800">Classic Slate</span>
                </div>
                <div class="grid grid-cols-4 gap-3" id="dark-palettes-list"></div>
            </div>
            <div class="dark:hidden block">
                <div class="flex items-center justify-between mb-4">
                    <label class="text-sm font-semibold text-muted uppercase tracking-wider">Light Palette</label>
                    <span id="selected-light-palette-name" class="text-xs font-bold text-primary-600 px-2 py-0.5 rounded-full bg-primary-50">Soft Slate</span>
                </div>
                <div class="grid grid-cols-4 gap-3" id="light-palettes-list"></div>
            </div>
            <div>
                <label class="text-sm font-semibold text-muted uppercase tracking-wider block mb-4">Background Style</label>
                <div class="grid grid-cols-3 gap-2" id="bg-presets-list"></div>
            </div>
            <div>
                <label class="text-sm font-semibold text-muted uppercase tracking-wider block mb-4">Canvas Style</label>
                <div class="grid grid-cols-2 gap-3" id="canvas-styles-list"></div>
            </div>
            <div>
                <label class="text-sm font-semibold text-muted uppercase tracking-wider block mb-4">Layout Zoom</label>
                <div class="grid grid-cols-3 gap-2" id="zoom-levels-list"></div>
            </div>
            <div>
                <label class="text-sm font-semibold text-muted uppercase tracking-wider block mb-4">Navigation Mode</label>
                <div class="grid grid-cols-2 gap-2" id="nav-mode-list"></div>
            </div>
        </div>
        <div class="border-t" style="border-color: rgb(var(--color-border))">
            <button onclick="localStorage.clear(); location.reload();" class="w-full py-4 text-xs font-bold text-muted hover:text-rose-500 transition uppercase tracking-widest">Reset to Default</button>
        </div>
    `;

		this.settingsBackdrop = document.createElement("div");
		this.settingsBackdrop.id = "settings-backdrop";
		document.body.appendChild(this.settingsBackdrop);
		document.body.appendChild(this.settingsPanel);
	},

	/**
	 * Binds layout toggles, alerts, tabs, accordions, and clipboard copying
	 */
	initEventListeners() {
		const mobileToggle = document.getElementById("mobile-sidebar-toggle");
		if (mobileToggle) {
			mobileToggle.addEventListener("click", () => {
				const sidebar = VibeTheme.sidebar;
				const overlay = VibeTheme.overlay;
				if (!sidebar) return;
				const isOpen = !sidebar.classList.contains("-translate-x-full");
				if (isOpen) {
					sidebar.classList.add("-translate-x-full");
					if (overlay) {
						overlay.classList.remove("opacity-100");
						overlay.classList.add("opacity-0");
						setTimeout(() => overlay.classList.add("hidden"), 300);
					}
				} else {
					if (overlay) {
						overlay.classList.remove("hidden");
						setTimeout(() => {
							overlay.classList.remove("opacity-0");
							overlay.classList.add("opacity-100");
						}, 10);
					}
					sidebar.classList.remove("-translate-x-full");
				}
			});
		}

		const overlay = VibeTheme.overlay;
		if (overlay) {
			overlay.addEventListener("click", () => {
				const sidebar = VibeTheme.sidebar;
				if (sidebar) sidebar.classList.add("-translate-x-full");
				overlay.classList.remove("opacity-100");
				overlay.classList.add("opacity-0");
				setTimeout(() => overlay.classList.add("hidden"), 300);
			});
		}

		const desktopToggle = VibeTheme.desktopToggle;
		if (desktopToggle) {
			desktopToggle.addEventListener("click", () => {
				const sidebar = VibeTheme.sidebar;
				if (!sidebar) return;
				const isMini = sidebar.classList.contains("sidebar-mini");
				VibeTheme.setSidebarState(!isMini);
			});
		}

		const themeToggleBtn = document.getElementById("theme-toggle");
		if (themeToggleBtn) {
			themeToggleBtn.addEventListener("click", () => {
				document.documentElement.classList.toggle("dark");
				const isDark = document.documentElement.classList.contains("dark");
				document.documentElement.setAttribute("data-theme", isDark ? "dark" : "light");
				VibeTheme.updateThemeIcons();

				VibeTheme.updateBackgroundPreset(VibeUtils.getVibeConfig("bg-preset"));
				VibeRender.refreshActiveSwatches();
			});
		}

		const settingsToggle = document.getElementById("settings-toggle");
		if (settingsToggle) {
			settingsToggle.onclick = () => {
				if (this.settingsPanel) this.settingsPanel.classList.add("open");
				if (this.settingsBackdrop) this.settingsBackdrop.classList.add("open");
				VibeRender.refreshActiveSwatches();
			};
		}

		const closeBtn = document.getElementById("close-settings");
		if (closeBtn) {
			closeBtn.onclick = () => {
				if (this.settingsPanel) this.settingsPanel.classList.remove("open");
				if (this.settingsBackdrop) this.settingsBackdrop.classList.remove("open");
			};
		}

		if (this.settingsBackdrop) {
			this.settingsBackdrop.onclick = () => {
				if (this.settingsPanel) this.settingsPanel.classList.remove("open");
				if (this.settingsBackdrop) this.settingsBackdrop.classList.remove("open");
			};
		}

		document.addEventListener("click", (e) => {
			const tabLink = e.target.closest(".nav-link[data-tab-target]");
			if (!tabLink) return;

			const targetId = tabLink.getAttribute("data-tab-target");
			const targetPane = document.querySelector(targetId);
			if (!targetPane) return;

			const nav = tabLink.closest(".nav-tabs, .nav-pills");
			if (!nav) return;

			nav.querySelectorAll(".nav-link").forEach((link) => link.classList.remove("active"));
			tabLink.classList.add("active");

			const contentContainer = targetPane.parentElement;
			contentContainer.querySelectorAll(".tab-pane").forEach((pane) => {
				pane.classList.add("hidden");
			});

			targetPane.classList.remove("hidden");
		});

		document.addEventListener("click", (e) => {
			const header = e.target.closest(".accordion-header");
			if (!header) return;

			const item = header.closest(".accordion-item");
			const accordion = header.closest(".accordion");

			if (accordion && accordion.classList.contains("accordion-single")) {
				const isActive = item.classList.contains("active");
				accordion.querySelectorAll(".accordion-item").forEach((i) => i.classList.remove("active"));
				if (!isActive) item.classList.add("active");
			} else {
				item.classList.toggle("active");
			}
		});

		document.addEventListener("click", (e) => {
			if (e.target.classList.contains("vibe-modal")) {
				VibeComponents.closeModal(e.target.id);
			}
		});

		document.addEventListener("keydown", (e) => {
			if (e.key === "Escape") {
				const activeModal = document.querySelector(".vibe-modal.show");
				if (activeModal) {
					VibeComponents.closeModal(activeModal.id);
				}
			}
		});

		document.addEventListener("click", (e) => {
			const closeBtn = e.target.closest(".alert-close");
			if (!closeBtn) return;

			const alert = closeBtn.closest(".alert");
			if (!alert) return;

			alert.style.transition = "opacity 0.3s, transform 0.3s";
			alert.style.opacity = "0";
			alert.style.transform = "translateY(-10px)";

			setTimeout(() => {
				alert.remove();
			}, 300);
		});

		document.addEventListener("click", (e) => {
			const copyBtn = e.target.closest(".copy-btn");
			if (!copyBtn) return;

			const codeContainer = copyBtn.closest(".code-container");
			if (!codeContainer) return;

			const code = codeContainer.querySelector("code");
			if (!code) return;

			const textContent = code.innerText;
			navigator.clipboard.writeText(textContent).then(() => {
				const originalHTML = copyBtn.innerHTML;
				copyBtn.innerHTML = '<i class="bx bx-check"></i> Copied!';
				copyBtn.classList.add("bg-emerald-500", "text-white");

				setTimeout(() => {
					copyBtn.innerHTML = originalHTML;
					copyBtn.classList.remove("bg-emerald-500", "text-white");
				}, 2000);
			});
		});
	},

	/**
	 * Restores starting sidebar mode (expanded or mini) from settings
	 */
	initSidebarState() {
		const sidebarTheme = VibeUtils.getVibeConfig("sidebar-theme");
		if (sidebarTheme === "mini") {
			VibeTheme.setSidebarState(true);
		}
	},

	/**
	 * Dismisses loading screen and transition blockades
	 */
	hideAppLoader() {
		const loader = document.getElementById("app-loader");
		if (loader && !loader.classList.contains("fade-out")) {
			loader.classList.add("fade-out");
			setTimeout(() => {
				document.body.classList.remove("preload");
			}, 600);
		}
	},
};

// =============================================================================
// SYNCHRONOUS BOOTSTRAP PROCEDURES
// =============================================================================
const initialTheme = VibeUtils.getVibeConfig("color-theme");
if (initialTheme === "dark") {
	document.documentElement.classList.add("dark");
	document.documentElement.setAttribute("data-theme", "dark");
} else {
	document.documentElement.classList.remove("dark");
	document.documentElement.setAttribute("data-theme", "light");
}

// =============================================================================
// WINDOW ASYNC LIFECYCLE BINDINGS
// =============================================================================
const initApp = async () => {
	await VibeUtils.loadVibeConfig();

	document.body.classList.add("preload");

	VibeEvents.createSettingsPanel();
	VibeEvents.initSettingsUI();
	VibeEvents.initFloatingComponents();
	VibeEvents.initEventListeners();
	VibeTheme.updateThemeIcons();

	const sidebarNav = document.getElementById("sidebar-nav");
	if (sidebarNav && typeof renderMenu === "function" && typeof menuConfig !== "undefined") {
		renderMenu(menuConfig, sidebarNav);
	}

	VibeEvents.initSidebarState();
};

if (document.readyState === "loading") {
	document.addEventListener("DOMContentLoaded", initApp);
} else {
	initApp();
}

window.addEventListener("load", () => {
	VibeEvents.hideAppLoader();
});

if (document.readyState === "complete") {
	VibeEvents.hideAppLoader();
} else {
	setTimeout(() => VibeEvents.hideAppLoader(), 3000);
}

let resizeTimer;
window.addEventListener("resize", () => {
	clearTimeout(resizeTimer);
	resizeTimer = setTimeout(() => {
		const navMode = VibeUtils.getVibeConfig("nav-mode");
		const topNav = document.getElementById("top-navigation-bar");

		if (navMode === "top" && topNav && typeof renderTopMenu === "function" && typeof menuConfig !== "undefined") {
			renderTopMenu(menuConfig, topNav);
			VibeTheme.updateBackgroundPreset(VibeUtils.getVibeConfig("bg-preset"));
		}
	}, 150);
});

// =============================================================================
// GLOBAL EXPOSURES for HTML template compatibility
// =============================================================================
window.openModal = VibeComponents.openModal;
window.closeModal = VibeComponents.closeModal;
window.vibeToast = VibeComponents.vibeToast;
window.getVibeConfig = VibeUtils.getVibeConfig;
window.updateThemeIcons = VibeTheme.updateThemeIcons;
