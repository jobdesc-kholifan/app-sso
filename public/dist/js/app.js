// --- Configuration System ---
let VIBE_CONFIG = {};

const defaultSettings = {
  "color-theme": "light",
  "dark-palette": "Antigravity IDE",
  "accent-color": "79, 70, 229",
  "bg-preset": "Neutral",
  "canvas-style": "Full",
  "zoom-level": "Standard",
  "nav-mode": "sidebar",
  "sidebar-theme": "expanded",
};

/**
 * Helper to determine the relative path prefix to the project root
 */
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

/**
 * Loads the default configuration from JSON
 */
async function loadVibeConfig() {
  try {
    const rootPrefix = getRelativePrefix();
    const response = await fetch(rootPrefix + "themes.json");
    if (response.ok) {
      VIBE_CONFIG = await response.json();
    } else {
      VIBE_CONFIG = { ...defaultSettings };
    }
  } catch (e) {
    VIBE_CONFIG = { ...defaultSettings };
  }
}

/**
 * Gets a setting value, prioritized: localStorage > JSON Config > Hardcoded Default
 */
function getVibeConfig(key) {
  const stored = localStorage.getItem(`vibe-template.${key}`);
  if (stored !== null) return stored;
  return VIBE_CONFIG[key] !== undefined
    ? VIBE_CONFIG[key]
    : defaultSettings[key];
}

// --- Core App Logic ---
// Destructure Floating UI functions for cleaner usage
const { computePosition, autoUpdate, offset, flip, shift } =
  window.FloatingUIDOM || {};

document.body.classList.add("preload");

const sidebar = document.getElementById("sidebar");
const overlay = document.getElementById("sidebar-overlay");
const mobileToggle = document.getElementById("mobile-sidebar-toggle");
const desktopToggle = document.getElementById("desktop-sidebar-toggle");
const toggleIcon = document.getElementById("toggle-icon");
const logoutBtn = document.getElementById("logout-btn");

function setSidebarState(isMini) {
  const profileContainer = document.getElementById("profile-container");
  const sideAvatar = document.getElementById("side-avatar");

  const isMobile = window.innerWidth < 768;
  const miniState = isMobile ? false : isMini;

  if (typeof window.clearAllFloatingMenus === "function") {
    window.clearAllFloatingMenus();
  }

  if (!sidebar) return; // Guard clause: cannot set sidebar state if no sidebar exists

  if (miniState) {
    sidebar.classList.add("sidebar-mini");
    sidebar.classList.remove("w-64");
    sidebar.classList.add("w-20");

    if (toggleIcon)
      toggleIcon.classList.replace("bx-chevron-left", "bx-chevron-right");

    document
      .querySelectorAll(".sidebar-hide")
      .forEach((el) => el.classList.add("hidden"));
    document
      .querySelectorAll(".sidebar-mini-show")
      .forEach((el) => el.classList.remove("hidden"));
    document.querySelectorAll(".sidebar-nav-link").forEach((el) => {
      el.classList.replace("justify-start", "justify-center");
      el.style.paddingLeft = "";
    });
    document
      .querySelectorAll(".sidebar-icon")
      .forEach((el) => el.classList.remove("lg:mr-3"));

    document
      .querySelectorAll(".submenu-container")
      .forEach((el) => el.classList.remove("open"));
    document
      .querySelectorAll(".chevron-rotate")
      .forEach((el) => el.classList.remove("rotate"));

    if (logoutBtn)
      logoutBtn.classList.replace("justify-start", "justify-center");

    if (profileContainer) {
      profileContainer.classList.replace("py-8", "py-6");
      profileContainer.classList.replace("px-6", "px-0");
      profileContainer.classList.add("justify-center");
    }
    if (sideAvatar) {
      sideAvatar.classList.replace("w-12", "w-10");
      sideAvatar.classList.replace("h-12", "h-10");
    }

    if (!isMobile) localStorage.setItem("vibe-template.sidebar-theme", "mini");
  } else {
    sidebar.classList.remove("sidebar-mini");
    sidebar.classList.remove("w-20");
    sidebar.classList.add("w-64");

    if (toggleIcon)
      toggleIcon.classList.replace("bx-chevron-right", "bx-chevron-left");

    document
      .querySelectorAll(".sidebar-hide")
      .forEach((el) => el.classList.remove("hidden"));
    document
      .querySelectorAll(".sidebar-mini-show")
      .forEach((el) => el.classList.add("hidden"));
    document.querySelectorAll(".sidebar-nav-link").forEach((el) => {
      el.classList.replace("justify-center", "justify-start");
      const depth = el.getAttribute("data-depth");
      if (depth > 0) el.style.paddingLeft = `${depth * 1 + 1}rem`;
    });
    document
      .querySelectorAll(".sidebar-icon")
      .forEach((el) => el.classList.add("lg:mr-3"));

    if (logoutBtn)
      logoutBtn.classList.replace("justify-center", "justify-start");

    if (profileContainer) {
      profileContainer.classList.replace("py-6", "py-8");
      profileContainer.classList.replace("px-0", "px-6");
      profileContainer.classList.remove("justify-center");
    }
    if (sideAvatar) {
      sideAvatar.classList.replace("w-10", "w-12");
      sideAvatar.classList.replace("h-10", "h-12");
    }

    if (!isMobile)
      localStorage.setItem("vibe-template.sidebar-theme", "expanded");
  }
}

if (mobileToggle) {
  mobileToggle.addEventListener("click", () => {
    const isOpen = !sidebar.classList.contains("-translate-x-full");
    if (isOpen) {
      // Close sidebar
      sidebar.classList.add("-translate-x-full");
      overlay.classList.remove("opacity-100");
      overlay.classList.add("opacity-0");
      setTimeout(() => overlay.classList.add("hidden"), 300);
    } else {
      // Open sidebar
      overlay.classList.remove("hidden");
      setTimeout(() => {
        overlay.classList.remove("opacity-0");
        overlay.classList.add("opacity-100");
      }, 10);
      sidebar.classList.remove("-translate-x-full");
    }
  });
}

if (overlay) {
  overlay.addEventListener("click", () => {
    sidebar.classList.add("-translate-x-full");
    overlay.classList.remove("opacity-100");
    overlay.classList.add("opacity-0");
    setTimeout(() => overlay.classList.add("hidden"), 300);
  });
}

if (desktopToggle) {
  desktopToggle.addEventListener("click", () => {
    const isMini = sidebar.classList.contains("sidebar-mini");
    setSidebarState(!isMini);
  });
}

// --- Theme Toggle ---
const themeToggleBtn = document.getElementById("theme-toggle");
const themeToggleDarkIcon = document.getElementById("theme-toggle-dark-icon");
const themeToggleLightIcon = document.getElementById("theme-toggle-light-icon");

function updateThemeIcons() {
  if (document.documentElement.classList.contains("dark")) {
    if (themeToggleDarkIcon) themeToggleDarkIcon.classList.add("!hidden");
    if (themeToggleLightIcon) themeToggleLightIcon.classList.remove("!hidden");
  } else {
    if (themeToggleDarkIcon) themeToggleDarkIcon.classList.remove("!hidden");
    if (themeToggleLightIcon) themeToggleLightIcon.classList.add("!hidden");
  }
}

if (themeToggleBtn) {
  themeToggleBtn.addEventListener("click", () => {
    document.documentElement.classList.toggle("dark");
    const isDark = document.documentElement.classList.contains("dark");
    localStorage.setItem(
      "vibe-template.color-theme",
      isDark ? "dark" : "light",
    );
    updateThemeIcons();

    // Re-apply background preset to update colors for dark/light switch
    const currentBg = getVibeConfig("bg-preset");
    updateBackgroundPreset(currentBg);
    refreshActiveSwatches();
  });
}

// --- Initial Theme Load (Synchronous for speed to prevent flickering) ---
const initialTheme = getVibeConfig("color-theme");

if (initialTheme === "dark") {
  document.documentElement.classList.add("dark");
} else {
  document.documentElement.classList.remove("dark");
}
updateThemeIcons();

// --- Settings Panel Implementation ---
const accentColors = [
  { name: "Vibrant Rose", value: "225, 29, 72" },
  { name: "Electric Orange", value: "234, 88, 12" },
  { name: "Solar Yellow", value: "202, 138, 4" },
  { name: "Growth Green", value: "22, 163, 74" },
  { name: "Sky Cyan", value: "8, 145, 178" },
  { name: "Royal Blue", value: "37, 99, 235" },
  { name: "Deep Purple", value: "124, 58, 237" },
  { name: "Pink Fuchsia", value: "219, 39, 119" },
];

const backgroundPresets = [
  {
    name: "Neutral",
    light: "241, 245, 249",
    surface: "255, 255, 255",
    alt: "241, 245, 249",
    border: "226, 232, 240",
    muted: "100, 116, 139",
    sidebar: "71, 85, 105",
  },
  {
    name: "Solid",
    light: "241, 245, 249",
    surface: "255, 255, 255",
    border: "226, 232, 240",
    sidebar: "71, 85, 105",
  },
  {
    name: "Matching",
    light: "241, 245, 249",
    surface: "255, 255, 255",
    sidebar: "71, 85, 105",
  },
];

const darkPalettes = [
  {
    name: "Classic Slate",
    body: "11, 17, 32",
    surface: "15, 23, 42",
    alt: "30, 41, 59",
    border: "51, 65, 85",
    muted: "148, 163, 184",
  },
  {
    name: "Midnight Onyx",
    body: "10, 10, 10",
    surface: "23, 23, 23",
    alt: "38, 38, 38",
    border: "51, 51, 51",
    muted: "163, 163, 163",
  },
  {
    name: "Charcoal Elegance",
    body: "28, 33, 40",
    surface: "34, 39, 46",
    alt: "45, 51, 59",
    border: "68, 76, 86",
    muted: "133, 142, 153",
  },
  {
    name: "Oceanic Deep",
    body: "9, 14, 23",
    surface: "17, 24, 39",
    alt: "31, 41, 55",
    border: "55, 65, 81",
    muted: "110, 120, 133",
  },
];

const lightPalettes = [
  {
    name: "Pure White",
    body: "255, 255, 255",
    surface: "255, 255, 255",
    alt: "248, 250, 252",
    border: "241, 245, 249",
    muted: "100, 116, 139",
  },
  {
    name: "Soft Slate",
    body: "248, 250, 252",
    surface: "255, 255, 255",
    alt: "241, 245, 249",
    border: "226, 232, 240",
    muted: "100, 116, 139",
  },
  {
    name: "Modern Gray",
    body: "243, 244, 246",
    surface: "255, 255, 255",
    alt: "229, 231, 235",
    border: "209, 213, 219",
    muted: "75, 85, 101",
  },
  {
    name: "Oceanic Light",
    body: "240, 249, 255",
    surface: "255, 255, 255",
    alt: "224, 242, 254",
    border: "186, 230, 253",
    muted: "51, 65, 85",
  },
];

const canvasStyles = [
  {
    name: "Full",
    appClass: "h-screen w-full bg-body",
    mainClass: "",
    canvasClass: "p-6 pt-8 pb-8",
    headerClass: "bg-surface",
    sidebarClass: "bg-surface h-full",
    icon: "bx-window-alt",
    desc: "Standard expansive layout",
  },
  {
    name: "Boxed",
    appClass: "h-screen max-w-[1280px] mx-auto shadow-2xl border-x bg-body",
    mainClass: "",
    canvasClass: "p-6 pt-8 pb-8",
    headerClass: "bg-surface",
    sidebarClass: "bg-surface h-full",
    icon: "bx-dock-top",
    desc: "Structured max-width container",
  },
  {
    name: "Soft",
    appClass: "h-screen w-screen py-0 px-4 gap-4 bg-body",
    mainClass: "mb-4",
    canvasClass:
      "p-4 rounded-[1.5rem] bg-surface/50 backdrop-blur-xl shadow-xl",
    headerClass: "rounded-[1.5rem] border my-4 shadow-sm bg-surface",
    sidebarClass: "rounded-[1.5rem] border my-4 h-[calc(100%-2rem)] bg-surface",
    icon: "bx-layout",
    desc: "Distance and rounded clarity",
  },
  {
    name: "Floating",
    appClass:
      "h-[calc(100vh-40px)] m-[20px] rounded-[1.5rem] border bg-body overflow-hidden shadow-2xl",
    mainClass: "mr-4",
    canvasClass: "px-6 py-8",
    headerClass: "bg-surface border-b",
    sidebarClass: "bg-surface h-full border-r",
    icon: "bx-layer",
    desc: "Equal margins and full rounding",
  },
];

const zoomLevels = [
  { name: "Compact", size: "12px", icon: "bx-zoom-out", desc: "Dense info" },
  {
    name: "Standard",
    size: "14px",
    icon: "bx-screenshot",
    desc: "Default view",
  },
  { name: "Zoom", size: "18px", icon: "bx-zoom-in", desc: "High visibility" },
];

const navModes = [
  {
    name: "Sidebar",
    value: "sidebar",
    icon: "bx-dock-left",
    desc: "Traditional vertical menu",
  },
  {
    name: "Top Nav",
    value: "top",
    icon: "bx-dock-top",
    desc: "Modern horizontal links",
  },
];

// Create Settings Panel UI
const settingsPanel = document.createElement("div");
settingsPanel.id = "settings-panel";
settingsPanel.className =
  "bg-surface border-l shadow-2xl flex flex-col transition-all duration-300";
settingsPanel.innerHTML = `
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

const settingsBackdrop = document.createElement("div");
settingsBackdrop.id = "settings-backdrop";
document.body.appendChild(settingsBackdrop);
document.body.appendChild(settingsPanel);

function updateAccentColor(rgbString) {
  if (typeof clearAllFloatingMenus === "function") clearAllFloatingMenus();
  document.documentElement.style.setProperty("--color-primary", rgbString);
  localStorage.setItem("vibe-template.accent-color", rgbString);

  const avatar = document.getElementById("side-avatar");
  if (avatar) {
    const rgb = rgbString.split(",").map((c) => parseInt(c.trim()));
    const hex = rgb.map((c) => c.toString(16).padStart(2, "0")).join("");
    avatar.src = `https://ui-avatars.com/api/?name=Admin&background=${hex}&color=fff`;
  }

  renderBackgroundPresets();
  const currentBg = getVibeConfig("bg-preset");
  updateBackgroundPreset(currentBg);
  refreshActiveSwatches();
}

function updateBackgroundPreset(presetName) {
  if (typeof clearAllFloatingMenus === "function") clearAllFloatingMenus();
  const currentAccent = getVibeConfig("accent-color");
  const isDark = document.documentElement.classList.contains("dark");

  const rgb = currentAccent.split(",").map((c) => parseInt(c.trim()));
  const lightMatch = rgb
    .map((c) => Math.round(c + (255 - c) * 0.82))
    .join(", ");
  const lightMatchAlt = rgb
    .map((c) => Math.round(c + (255 - c) * 0.75))
    .join(", ");
  const lightMatchBorder = rgb
    .map((c) => Math.round(c + (255 - c) * 0.6))
    .join(", ");
  const darkMatchSurface = rgb.map((c) => Math.round(5 + c * 0.12)).join(", ");
  const darkMatchAlt = rgb.map((c) => Math.round(15 + c * 0.2)).join(", ");
  const darkMatchBorder = rgb.map((c) => Math.round(20 + c * 0.25)).join(", ");
  const darkMatchBody = rgb.map((c) => Math.round(2 + c * 0.04)).join(", ");

  const presetData =
    backgroundPresets.find((p) => p.name === presetName) ||
    backgroundPresets[0];
  const elSidebar = document.getElementById("sidebar");
  const elHeader = document.getElementById("app-header");
  const elLayout = document.getElementById("app-layout");

  // Sidebar specifically for Solid
  if (presetName === "Solid") {
    const darkSolidSidebar = rgb
      .map((c) => Math.round(15 + c * 0.25))
      .join(", ");

    document.documentElement.style.setProperty(
      "--sidebar-bg",
      isDark ? darkSolidSidebar : currentAccent,
    );
    document.documentElement.style.setProperty(
      "--sidebar-text",
      "219, 234, 254",
    );
    document.documentElement.style.setProperty(
      "--sidebar-text-muted",
      "219, 234, 254, 0.65",
    );
    document.documentElement.style.setProperty(
      "--sidebar-link-active-bg",
      "255, 255, 255, 0.25",
    );
    document.documentElement.style.setProperty(
      "--sidebar-link-active-text",
      "255, 255, 255",
    );
    document.documentElement.style.setProperty(
      "--sidebar-border",
      isDark ? "51, 65, 85" : "255, 255, 255, 0.1",
    );

    // Sidebar item active/hover — Solid mode uses translucent white
    document.documentElement.style.setProperty(
      "--sidebar-item-text",
      "rgba(219, 234, 254, 0.85)",
    );
    document.documentElement.style.setProperty(
      "--sidebar-item-active-bg",
      "rgba(255, 255, 255, 0.25)",
    );
    document.documentElement.style.setProperty(
      "--sidebar-item-active-text",
      "rgb(255, 255, 255)",
    );
    document.documentElement.style.setProperty(
      "--sidebar-item-hover-bg",
      "rgba(255, 255, 255, 0.15)",
    );
    document.documentElement.style.setProperty(
      "--sidebar-item-hover-text",
      "rgb(255, 255, 255)",
    );

    // Floating menu solid sync
    document.documentElement.style.setProperty(
      "--floating-bg",
      isDark ? darkSolidSidebar : currentAccent,
    );
    document.documentElement.style.setProperty(
      "--floating-text",
      "219, 234, 254",
    );
    document.documentElement.style.setProperty(
      "--floating-border",
      isDark ? "51, 65, 85" : "255, 255, 255, 0.1",
    );
    document.documentElement.style.setProperty(
      "--floating-hover-bg",
      "rgba(255, 255, 255, 0.2)",
    );
    document.documentElement.style.setProperty(
      "--floating-hover-text",
      "#ffffff",
    );
    document.documentElement.style.setProperty(
      "--floating-active-bg",
      "rgba(255, 255, 255, 0.25)",
    );
    document.documentElement.style.setProperty(
      "--floating-active-text",
      "#ffffff",
    );

    // Cleanup background classes from sidebar
    if (elSidebar)
      elSidebar.classList.remove("bg-surface", "bg-body", "bg-alt");
  } else {
    // For Neutral and Matching, ensure sidebar matches surface
    document.documentElement.style.setProperty(
      "--sidebar-bg",
      "var(--color-bg-surface)",
    );
    document.documentElement.style.setProperty(
      "--sidebar-text",
      isDark ? "203, 213, 225" : "71, 85, 105",
    );
    document.documentElement.style.removeProperty("--sidebar-text-muted");
    document.documentElement.style.removeProperty("--sidebar-link-active-bg");
    document.documentElement.style.removeProperty("--sidebar-link-active-text");

    // Reset sidebar borders and interactive states to CSS defaults (using accent)
    document.documentElement.style.removeProperty("--sidebar-border");
    document.documentElement.style.removeProperty("--sidebar-item-text");
    document.documentElement.style.removeProperty("--sidebar-item-active-bg");
    document.documentElement.style.removeProperty("--sidebar-item-active-text");
    document.documentElement.style.removeProperty("--sidebar-item-hover-bg");
    document.documentElement.style.removeProperty("--sidebar-item-hover-text");

    document.documentElement.style.setProperty(
      "--floating-bg",
      "var(--color-bg-surface)",
    );
    document.documentElement.style.setProperty(
      "--floating-text",
      isDark ? "203, 213, 225" : "71, 85, 105",
    );
    document.documentElement.style.removeProperty("--floating-border");
    document.documentElement.style.removeProperty("--floating-hover-bg");
    document.documentElement.style.removeProperty("--floating-hover-text");
    document.documentElement.style.removeProperty("--floating-active-bg");
    document.documentElement.style.removeProperty("--floating-active-text");

    // Restore sidebar background from canvas if not solid
    const currentCanvasStyle = getVibeConfig("canvas-style");
    const canvasToken = canvasStyles.find((s) => s.name === currentCanvasStyle);
    if (elSidebar && canvasToken && canvasToken.sidebarClass) {
      elSidebar.classList.add(
        ...canvasToken.sidebarClass.split(" ").filter((c) => c),
      );
    }
  }
  const paletteName = isDark
    ? getVibeConfig("dark-palette")
    : getVibeConfig("light-palette");
  const activePalette = isDark
    ? darkPalettes.find((p) => p.name === paletteName) || darkPalettes[0]
    : lightPalettes.find((p) => p.name === paletteName) || lightPalettes[1]; // Soft Slate default

  if (presetName === "Matching") {
    document.documentElement.style.setProperty(
      "--color-bg-body",
      isDark ? darkMatchBody : lightMatch,
    );
    document.documentElement.style.setProperty(
      "--color-bg-surface",
      isDark ? darkMatchSurface : "255, 255, 255",
    );
    document.documentElement.style.setProperty(
      "--color-bg-alt",
      isDark ? darkMatchAlt : lightMatchAlt,
    );
    document.documentElement.style.setProperty(
      "--color-border",
      isDark ? darkMatchBorder : lightMatchBorder,
    );
    document.documentElement.style.setProperty(
      "--bg-gradient",
      isDark
        ? `rgb(${darkMatchBody})`
        : `linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%)`,
    );
  } else if (presetName === "Solid") {
    // Blending logic for Solid: Neutral surfaces but with tinted borders and optimized body
    const solidBorder = isDark
      ? activePalette.border
      : rgb.map((c) => Math.round(c + (255 - c) * 0.88)).join(", ");

    document.documentElement.style.setProperty(
      "--color-bg-body",
      activePalette.body,
    );
    document.documentElement.style.setProperty(
      "--color-bg-surface",
      activePalette.surface,
    );
    document.documentElement.style.setProperty(
      "--color-bg-alt",
      activePalette.alt,
    );
    document.documentElement.style.setProperty("--color-border", solidBorder);
    document.documentElement.style.setProperty(
      "--bg-gradient",
      isDark
        ? `rgb(${activePalette.body
            .split(",")
            .map((c) => Math.max(0, parseInt(c) - 5))
            .join(",")})`
        : `linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%)`,
    );
  } else {
    document.documentElement.style.setProperty(
      "--color-bg-body",
      activePalette.body,
    );
    document.documentElement.style.setProperty(
      "--color-bg-surface",
      activePalette.surface,
    );
    document.documentElement.style.setProperty(
      "--color-bg-alt",
      activePalette.alt,
    );
    document.documentElement.style.setProperty(
      "--color-border",
      activePalette.border,
    );
    document.documentElement.style.setProperty(
      "--bg-gradient",
      isDark
        ? `rgb(${activePalette.body})`
        : `linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%)`,
    );
  }

  // Sync Text Muted
  document.documentElement.style.setProperty(
    "--color-text-muted",
    activePalette.muted,
  );

  localStorage.setItem("vibe-template.bg-preset", presetName);

  // Sync Top Nav Header if in Solid mode
  const navMode = getVibeConfig("nav-mode");
  const header = document.getElementById("app-header");
  if (header) {
    const headerControls = header.querySelectorAll(
      '#theme-toggle, #settings-toggle, .flex.p-1.rounded-lg[style*="rgb(var(--color-bg-alt))"]',
    );

    if (presetName === "Solid" && navMode === "top" && header) {
      header.classList.add("header-solid");
      header.classList.remove("bg-surface", "bg-body", "bg-alt");
      header.style.backgroundColor = "";

      // Remove inline styles that clash with solid mode
      headerControls.forEach((ctrl) => {
        ctrl.style.backgroundColor = "";
        ctrl.style.borderColor = "";
      });
    } else {
      header.classList.remove("header-solid");
      header.style.backgroundColor = "";

      // Restore inline styles (simple way: rely on the fact they are in HTML, or re-set them)
      // Actually, better to just let CSS handle it if we remove them, but they are hardcoded in HTML.
      // Let's re-apply them if NOT in solid mode
      headerControls.forEach((ctrl) => {
        ctrl.style.backgroundColor = "rgb(var(--color-bg-alt))";
        ctrl.style.borderColor = "rgb(var(--color-border))";
      });
    }
  }

  refreshActiveSwatches();
}

function updateCanvasStyle(styleName) {
  if (typeof clearAllFloatingMenus === "function") clearAllFloatingMenus();
  const elApp = document.getElementById("app-layout");
  const elCanvas = document.getElementById("main-canvas");
  const elMain = document.getElementById("main-content");
  const elSidebar = document.getElementById("sidebar");
  const elHeader = document.getElementById("app-header");

  const styleToken =
    canvasStyles.find((s) => s.name === styleName) || canvasStyles[0];
  const isMini = getVibeConfig("sidebar-theme") === "mini";
  const navMode = getVibeConfig("nav-mode");
  const bgPreset = getVibeConfig("bg-preset");

  if (elApp) {
    elApp.className =
      "flex transition-all duration-500 bg-body overflow-hidden";
    if (navMode === "top") elApp.classList.add("layout-top-nav");
  }
  if (elCanvas) elCanvas.className = "space-y-8 transition-all duration-500";
  if (elMain)
    elMain.className =
      "flex-1 overflow-y-auto w-full space-y-8 transition-all duration-500";
  if (elSidebar) {
    elSidebar.className = `fixed inset-y-0 left-0 z-50 ${isMini ? "w-20 sidebar-mini" : "w-64"} transition-all duration-500 transform -translate-x-full md:translate-x-0 md:relative flex flex-col flex-shrink-0 lg:translate-x-0`;
    if (navMode === "top") elSidebar.classList.add("hidden");
  }
  if (elHeader) {
    elHeader.className =
      "h-16 flex items-center justify-between px-8 sticky top-0 z-30 transition-all duration-500";
    // Note: header-solid will be re-applied by updateBackgroundPreset call at the end
  }

  if (elApp && styleToken.appClass)
    elApp.classList.add(...styleToken.appClass.split(" ").filter((c) => c));
  if (elMain && styleToken.mainClass)
    elMain.classList.add(...styleToken.mainClass.split(" ").filter((c) => c));
  if (elCanvas && styleToken.canvasClass)
    elCanvas.classList.add(
      ...styleToken.canvasClass.split(" ").filter((c) => c),
    );
  if (elSidebar && styleToken.sidebarClass)
    elSidebar.classList.add(
      ...styleToken.sidebarClass.split(" ").filter((c) => c),
    );
  if (elHeader && styleToken.headerClass)
    elHeader.classList.add(
      ...styleToken.headerClass.split(" ").filter((c) => c),
    );

  setSidebarState(isMini);
  localStorage.setItem("vibe-template.canvas-style", styleName);

  // Re-apply background preset to ensure header-solid and proper colors are restored
  updateBackgroundPreset(bgPreset);

  refreshActiveSwatches();
}

function updateZoomLevel(levelName) {
  if (typeof clearAllFloatingMenus === "function") clearAllFloatingMenus();
  const level = zoomLevels.find((z) => z.name === levelName) || zoomLevels[1];
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

    // Wait for loader to be fully visible before changing scale
    setTimeout(() => {
      document.documentElement.style.fontSize = level.size;
      // Small delay to let browser re-calculate layout before fading out
      setTimeout(() => {
        loader.classList.add("fade-out");
      }, 500);
    }, 300);
  } else {
    document.documentElement.style.fontSize = level.size;
  }

  localStorage.setItem("vibe-template.zoom-level", levelName);
  refreshActiveSwatches();
}

function renderBackgroundPresets() {
  const bgList = document.getElementById("bg-presets-list");
  if (!bgList) return;
  const currentAccent = getVibeConfig("accent-color");
  bgList.innerHTML = "";
  backgroundPresets.forEach((preset) => {
    const btn = document.createElement("button");
    btn.className = `bg-preset-btn text-left px-4 py-3 rounded-xl border transition-all hover:border-primary-600`;
    btn.setAttribute("data-name", preset.name);

    let previewHtml = "";
    if (preset.name === "Solid") {
      previewHtml = `<div class="w-4 h-4 rounded-full border shadow-sm" style="background-color: rgb(${currentAccent})"></div>`;
    } else {
      previewHtml = `
                <div class="w-4 h-4 rounded-full border bg-white"></div>
                <div class="w-4 h-4 rounded-full border bg-slate-900"></div>
            `;
    }

    btn.innerHTML = `
            <span class="block text-sm font-bold">${preset.name}</span>
            <div class="flex gap-1 mt-1.5">
                ${previewHtml}
            </div>
        `;
    btn.onclick = () => updateBackgroundPreset(preset.name);
    bgList.appendChild(btn);
  });
}

function renderCanvasStyles() {
  const canvasList = document.getElementById("canvas-styles-list");
  if (!canvasList) return;
  canvasList.innerHTML = "";
  canvasStyles.forEach((style) => {
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
    btn.onclick = () => updateCanvasStyle(style.name);
    canvasList.appendChild(btn);
  });
}

function renderZoomLevels() {
  const zoomList = document.getElementById("zoom-levels-list");
  if (!zoomList) return;
  zoomList.innerHTML = "";
  zoomLevels.forEach((level) => {
    const btn = document.createElement("button");
    btn.className = `zoom-level-btn text-center p-2 rounded-lg border transition-all hover:border-primary-600 flex flex-col items-center gap-1`;
    btn.setAttribute("data-name", level.name);
    btn.innerHTML = `
            <i class="bx ${level.icon} text-lg mb-0.5"></i>
            <span class="block text-[9px] font-bold uppercase tracking-tighter">${level.name}</span>
        `;
    btn.onclick = () => updateZoomLevel(level.name);
    zoomList.appendChild(btn);
  });
}

function renderNavModes() {
  const navList = document.getElementById("nav-mode-list");
  if (!navList) return;
  navList.innerHTML = "";
  navModes.forEach((mode) => {
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
    btn.onclick = () => updateNavMode(mode.value);
    navList.appendChild(btn);
  });
}

function renderDarkPalettes() {
  const paletteList = document.getElementById("dark-palettes-list");
  if (!paletteList) return;
  paletteList.innerHTML = "";
  darkPalettes.forEach((palette) => {
    const swatch = document.createElement("div");
    swatch.className = `dark-palette-swatch w-10 h-10 rounded-full cursor-pointer transition-all hover:scale-110 flex items-center justify-center border-2 border-transparent`;
    swatch.setAttribute("data-name", palette.name);
    swatch.style.backgroundColor = `rgb(${palette.body})`;
    swatch.style.borderColor = `rgb(${palette.border})`;
    swatch.onclick = () => updateDarkPalette(palette.name);
    paletteList.appendChild(swatch);
  });
}

function renderLightPalettes() {
  const paletteList = document.getElementById("light-palettes-list");
  if (!paletteList) return;
  paletteList.innerHTML = "";
  lightPalettes.forEach((palette) => {
    const swatch = document.createElement("div");
    swatch.className = `light-palette-swatch w-10 h-10 rounded-full cursor-pointer transition-all hover:scale-110 flex items-center justify-center border-2 border-transparent`;
    swatch.setAttribute("data-name", palette.name);
    swatch.style.backgroundColor = `rgb(${palette.body})`;
    swatch.style.borderColor = `rgb(${palette.border})`;
    swatch.onclick = () => updateLightPalette(palette.name);
    paletteList.appendChild(swatch);
  });
}

function updateDarkPalette(paletteName) {
  if (typeof clearAllFloatingMenus === "function") clearAllFloatingMenus();
  localStorage.setItem("vibe-template.dark-palette", paletteName);
  const currentBg = getVibeConfig("bg-preset");
  updateBackgroundPreset(currentBg);
  renderDarkPalettes();
  refreshActiveSwatches();
}

function updateLightPalette(paletteName) {
  if (typeof clearAllFloatingMenus === "function") clearAllFloatingMenus();
  localStorage.setItem("vibe-template.light-palette", paletteName);
  const currentBg = getVibeConfig("bg-preset");
  updateBackgroundPreset(currentBg);
  renderLightPalettes();
  refreshActiveSwatches();
}

function updateNavMode(mode) {
  if (typeof clearAllFloatingMenus === "function") clearAllFloatingMenus();
  const isMobile = window.innerWidth < 768;
  const activeMode = isMobile ? "sidebar" : mode;
  if (!isMobile) localStorage.setItem("vibe-template.nav-mode", mode);

  const layout = document.getElementById("app-layout");
  const sidebar = document.getElementById("sidebar");
  const header = document.getElementById("app-header");

  if (!layout || !header) return; // Guard clause: cannot change nav mode if dashboard layout is missing

  if (mode === "top") {
    layout.classList.add("layout-top-nav");
    if (sidebar) {
      sidebar.classList.add("md:hidden");
      sidebar.classList.remove("hidden"); // Ensure it's not totally gone
    }

    // Ensure header has container for top menu if needed
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

    // Always hide breadcrumb/title in top nav mode and adjust flex layout
    if (headerTitle) {
      headerTitle.classList.add("hidden");
      headerTitle.parentElement.classList.add("flex-1");
    }

    if (
      typeof renderTopMenu === "function" &&
      typeof menuConfig !== "undefined"
    ) {
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

  // Always re-apply background preset to ensure header/sidebar colors are in sync
  const currentBg = getVibeConfig("bg-preset");
  updateBackgroundPreset(currentBg);

  refreshActiveSwatches();
}

function refreshActiveSwatches() {
  const currentAccent = getVibeConfig("accent-color");
  const currentBg = getVibeConfig("bg-preset");
  const currentCanvas = getVibeConfig("canvas-style");
  const isDark = document.documentElement.classList.contains("dark");

  const nameLabel = document.getElementById("selected-accent-name");
  if (nameLabel) {
    const selectedAccent =
      accentColors.find((c) => c.value === currentAccent) || accentColors[0];
    nameLabel.textContent = selectedAccent.name;
  }

  document.querySelectorAll(".color-swatch").forEach((sw) => {
    const isActive = sw.getAttribute("data-value") === currentAccent;
    sw.classList.toggle("ring-4", isActive);
    sw.classList.toggle("ring-primary-500/30", isActive);
    sw.classList.toggle("border-white", isActive);
    sw.innerHTML = isActive
      ? '<i class="bx bx-check text-white text-xl"></i>'
      : "";
  });

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

  document.querySelectorAll(".bg-preset-btn").forEach((btn) => {
    btn.classList.remove(...allPossibleActiveClasses);
    const isActive = btn.getAttribute("data-name") === currentBg;
    if (isActive) btn.classList.add(...activeClasses);
  });

  document.querySelectorAll(".canvas-style-btn").forEach((btn) => {
    btn.classList.remove(...allPossibleActiveClasses);
    const isActive = btn.getAttribute("data-name") === currentCanvas;
    if (isActive) btn.classList.add(...activeClasses);
  });

  const currentZoom = getVibeConfig("zoom-level");
  document.querySelectorAll(".zoom-level-btn").forEach((btn) => {
    btn.classList.remove(...allPossibleActiveClasses);
    const isActive = btn.getAttribute("data-name") === currentZoom;
    if (isActive) btn.classList.add(...activeClasses);
  });

  const currentNav = getVibeConfig("nav-mode");
  document.querySelectorAll(".nav-mode-btn").forEach((btn) => {
    btn.classList.remove(...allPossibleActiveClasses);
    const isActive = btn.getAttribute("data-name") === currentNav;
    if (isActive) btn.classList.add(...activeClasses);
  });

  const currentPalette = getVibeConfig("dark-palette");
  const darkNameLabel = document.getElementById("selected-dark-palette-name");
  if (darkNameLabel) {
    const selectedPalette =
      darkPalettes.find((p) => p.name === currentPalette) || darkPalettes[0];
    darkNameLabel.textContent = selectedPalette.name;
  }

  document.querySelectorAll(".dark-palette-swatch").forEach((sw) => {
    const isActive = sw.getAttribute("data-name") === currentPalette;
    sw.classList.toggle("ring-4", isActive);
    sw.classList.toggle("ring-primary-500/30", isActive);
    sw.classList.toggle("border-white", isActive);
    sw.innerHTML = isActive
      ? '<i class="bx bx-check text-white text-xl"></i>'
      : "";
  });

  const currentLightPalette = getVibeConfig("light-palette");
  const lightNameLabel = document.getElementById("selected-light-palette-name");
  if (lightNameLabel) {
    const selectedPalette =
      lightPalettes.find((p) => p.name === currentLightPalette) ||
      lightPalettes[1];
    lightNameLabel.textContent = selectedPalette.name;
  }

  document.querySelectorAll(".light-palette-swatch").forEach((sw) => {
    const isActive = sw.getAttribute("data-name") === currentLightPalette;
    sw.classList.toggle("ring-4", isActive);
    sw.classList.toggle("ring-primary-500/30", isActive);
    sw.classList.toggle("border-white", isActive);
    sw.innerHTML = isActive
      ? '<i class="bx bx-check text-white text-xl"></i>'
      : "";
  });
}

// Initialize UI Elements
function initSettingsUI() {
  const accentList = document.getElementById("accent-colors-list");
  if (accentList) {
    accentList.innerHTML = "";
    accentColors.forEach((color) => {
      const swatch = document.createElement("div");
      swatch.className = `color-swatch w-10 h-10 rounded-full cursor-pointer transition-all hover:scale-110 flex items-center justify-center border-2 border-transparent`;
      swatch.setAttribute("data-value", color.value);
      swatch.style.backgroundColor = `rgb(${color.value})`;
      swatch.onclick = () => updateAccentColor(color.value);
      accentList.appendChild(swatch);
    });
  }

  renderBackgroundPresets();
  renderCanvasStyles();
  renderZoomLevels();
  renderNavModes();
  renderDarkPalettes();
  renderLightPalettes();

  const savedAccent = getVibeConfig("accent-color");
  if (savedAccent) updateAccentColor(savedAccent);

  const savedBg = getVibeConfig("bg-preset");
  if (savedBg) updateBackgroundPreset(savedBg);

  const savedCanvas = getVibeConfig("canvas-style");
  updateCanvasStyle(savedCanvas || "Full");

  const savedZoom = getVibeConfig("zoom-level");
  updateZoomLevel(savedZoom || "Standard");

  const savedNav = getVibeConfig("nav-mode");
  updateNavMode(savedNav || "sidebar");

  const savedLightPalette = getVibeConfig("light-palette");
  if (savedLightPalette) updateLightPalette(savedLightPalette);
}

// --- Floating UI Core Integration ---
function initFloatingComponents() {
  if (typeof FloatingUIDOM === "undefined") {
    console.warn(
      "Floating UI library (FloatingUIDOM) not found. Tooltips and dropdowns will not be initialized.",
    );
    return;
  }
  const { computePosition, autoUpdate, flip, shift, offset } = FloatingUIDOM;

  // --- Dropdown Management ---
  const dropdownToggles = document.querySelectorAll(".dropdown-toggle");
  dropdownToggles.forEach((toggle) => {
    const dropdown = toggle.closest(".dropdown");
    const menu = dropdown ? dropdown.querySelector(".dropdown-menu") : null;
    if (!menu) return;

    function updatePosition() {
      const placement = toggle.getAttribute("data-placement") || "bottom-start";
      computePosition(toggle, menu, {
        placement: placement,
        middleware: [offset(8), flip(), shift({ padding: 5 })],
      }).then(({ x, y }) => {
        Object.assign(menu.style, {
          left: `${x}px`,
          top: `${y}px`,
        });
        menu.setAttribute("data-placement", placement);
      });
    }

    function toggleMenu(show) {
      if (show) {
        // Close other open menus
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
        }, 150); // 150ms grace period
      };

      toggle.addEventListener("mouseenter", handleEnter);
      dropdown.addEventListener("mouseleave", handleLeave);

      // Also allow hovering on the menu itself to keep it open
      menu.addEventListener("mouseenter", handleEnter);
      menu.addEventListener("mouseleave", handleLeave);
    }
  });

  // --- Tooltip Management ---
  const tooltips = document.querySelectorAll(
    "[data-vibe-tooltip], [data-tippy-content]",
  );
  tooltips.forEach((ref) => {
    const content =
      ref.getAttribute("data-vibe-tooltip") ||
      ref.getAttribute("data-tippy-content");
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
        Object.assign(tooltip.style, {
          left: `${x}px`,
          top: `${y}px`,
        });
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

  // Global Click-outside to close all menus
  document.addEventListener("click", (e) => {
    if (!e.target.closest(".dropdown")) {
      document.querySelectorAll(".dropdown-menu.show").forEach((menu) => {
        menu.classList.remove("show");
        const toggle = menu
          .closest(".dropdown")
          ?.querySelector(".dropdown-toggle");
        if (toggle && toggle._floatingCleanup) {
          toggle._floatingCleanup();
          toggle._floatingCleanup = null;
        }
      });
    }
  });
}

function initEventListeners() {
  // Settings Toggle
  const settingsToggle = document.getElementById("settings-toggle");
  if (settingsToggle) {
    settingsToggle.onclick = () => {
      settingsPanel.classList.add("open");
      settingsBackdrop.classList.add("open");
      refreshActiveSwatches();
    };
  }

  // Close Settings
  const closeBtn = document.getElementById("close-settings");
  if (closeBtn) {
    closeBtn.onclick = () => {
      settingsPanel.classList.remove("open");
      settingsBackdrop.classList.remove("open");
    };
  }

  // Settings Backdrop
  if (settingsBackdrop) {
    settingsBackdrop.onclick = () => {
      settingsPanel.classList.remove("open");
      settingsBackdrop.classList.remove("open");
    };
  }
}

// Initial Sidebar State
function initSidebarState() {
  const sidebarTheme = getVibeConfig("sidebar-theme");
  if (sidebarTheme === "mini") {
    setSidebarState(true);
  }
}

// Initialize all components
document.addEventListener("DOMContentLoaded", async () => {
  // Load external config first
  await loadVibeConfig();

  initSettingsUI();
  initFloatingComponents();
  initEventListeners();

  // Render Menu if elements and config are available
  const sidebarNav = document.getElementById("sidebar-nav");
  if (
    sidebarNav &&
    typeof renderMenu === "function" &&
    typeof menuConfig !== "undefined"
  ) {
    renderMenu(menuConfig, sidebarNav);
  }

  initSidebarState();
});

// Remove preload class and hide loader after initial load
function hideAppLoader() {
  const loader = document.getElementById("app-loader");
  if (loader && !loader.classList.contains("fade-out")) {
    loader.classList.add("fade-out");
    setTimeout(() => {
      document.body.classList.remove("preload");
    }, 600); // 600ms matches the theme.css transition duration
  }
}

window.addEventListener("load", hideAppLoader);

// Safety fallback: ensure loader hides even if load event was missed or delayed
if (document.readyState === "complete") {
  hideAppLoader();
} else {
  // If loader is still visible after 3 seconds, force hide it
  setTimeout(hideAppLoader, 3000);
}

// --- Component Interactivity Logic ---

// -- Tabs Logic --
document.addEventListener("click", (e) => {
  const tabLink = e.target.closest(".nav-link[data-tab-target]");
  if (!tabLink) return;

  const targetId = tabLink.getAttribute("data-tab-target");
  const targetPane = document.querySelector(targetId);
  if (!targetPane) return;

  // Get the container (nav-tabs or nav-pills)
  const nav = tabLink.closest(".nav-tabs, .nav-pills");
  if (!nav) return;

  // Deactivate all links in this nav
  nav
    .querySelectorAll(".nav-link")
    .forEach((link) => link.classList.remove("active"));
  tabLink.classList.add("active");

  // Hide all sibling panes
  const contentContainer = targetPane.parentElement;
  contentContainer.querySelectorAll(".tab-pane").forEach((pane) => {
    pane.classList.add("hidden");
  });

  // Show target pane
  targetPane.classList.remove("hidden");
});

// -- Accordion Logic --
document.addEventListener("click", (e) => {
  const header = e.target.closest(".accordion-header");
  if (!header) return;

  const item = header.closest(".accordion-item");
  const accordion = header.closest(".accordion");

  if (accordion && accordion.classList.contains("accordion-single")) {
    // Toggle behavior for single-open accordions
    const isActive = item.classList.contains("active");
    accordion
      .querySelectorAll(".accordion-item")
      .forEach((i) => i.classList.remove("active"));
    if (!isActive) item.classList.add("active");
  } else {
    // Multi-open behavior
    item.classList.toggle("active");
  }
});

// -- Modal Logic --
window.openModal = function (modalId) {
  const modal = document.getElementById(modalId);
  if (!modal) {
    console.warn(`Modal with ID "${modalId}" not found.`);
    return;
  }

  // Create backdrop if it doesn't exist
  let backdrop = document.querySelector(".vibe-modal-backdrop");
  if (!backdrop) {
    backdrop = document.createElement("div");
    backdrop.className = "vibe-modal-backdrop";
    document.body.appendChild(backdrop);
  }

  // Reset styles and prepare for entry
  modal.style.display = "flex";
  backdrop.style.display = "block";

  // Forced reflow to ensure transitions trigger
  void modal.offsetHeight;
  void backdrop.offsetHeight;

  modal.classList.add("show");
  backdrop.classList.add("show");
  document.body.style.overflow = "hidden";
};

window.closeModal = function (modalId) {
  const modal = document.getElementById(modalId);
  const backdrop = document.querySelector(".vibe-modal-backdrop");
  if (!modal) return;

  modal.classList.remove("show");
  if (backdrop) backdrop.classList.remove("show");

  // Wait for animation to finish before hiding
  setTimeout(() => {
    if (!modal.classList.contains("show")) {
      modal.style.display = "none";
    }
    // Only hide backdrop if no other modals are show
    if (!document.querySelector(".vibe-modal.show")) {
      if (backdrop) backdrop.style.display = "none";
      document.body.style.overflow = "";
    }
  }, 300);
};

// Global click to close modal via backdrop
document.addEventListener("click", (e) => {
  if (e.target.classList.contains("vibe-modal")) {
    closeModal(e.target.id);
  }
});

// Global keydown to close modal via Escape key
document.addEventListener("keydown", (e) => {
  if (e.key === "Escape") {
    const activeModal = document.querySelector(".vibe-modal.show");
    if (activeModal) {
      closeModal(activeModal.id);
    }
  }
});

// -- Alert Dismissal --
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

// -- Copy to Clipboard Logic --
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

/* --- Toast & Notification System --- */
window.vibeToast = {
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

  show({
    title = "Notification",
    message = "",
    type = "info",
    position = "bottom-right",
    duration = 5000,
  }) {
    const container = this.getContainer(position);
    const toast = document.createElement("div");

    let icon = "bx-info-circle";
    let accentColor = "text-primary-600";
    let bgClass = "bg-white dark:bg-slate-800";
    let borderClass = "border";

    if (type === "success") {
      icon = "bx-check-circle";
      accentColor = "text-emerald-500";
      bgClass = "bg-emerald-100 dark:bg-emerald-950/90";
      borderClass = "border border-emerald-100 dark:border-emerald-800";
    }
    if (type === "danger") {
      icon = "bx-error";
      accentColor = "text-rose-500";
      bgClass = "bg-rose-100 dark:bg-rose-950/90";
      borderClass = "border border-rose-100 dark:border-rose-800";
    }
    if (type === "warning") {
      icon = "bx-error-circle";
      accentColor = "text-amber-500";
      bgClass = "bg-amber-100 dark:bg-amber-950/90";
      borderClass = "border border-amber-100 dark:border-amber-800";
    }
    if (type === "info") {
      icon = "bx-info-circle";
      accentColor = "text-sky-500";
      bgClass = "bg-sky-100 dark:bg-sky-950/90";
      borderClass = "border border-sky-100 dark:border-sky-800";
    }

    // Animation classes based on position
    let animIn = "toast-animate-in-right";
    if (position.includes("left")) animIn = "toast-animate-in-left";
    if (position.includes("center"))
      animIn = position.includes("top")
        ? "toast-animate-in-top"
        : "toast-animate-in-bottom";

    toast.className = `flex items-center p-4 ${bgClass} ${borderClass} rounded-xl shadow-2xl transition-all duration-300 ${animIn} max-w-sm`;
    toast.innerHTML = `
            <i class="bx ${icon} ${accentColor} text-2xl mr-3"></i>
            <div class="flex-1">
                <h6 class="text-sm font-bold leading-none mb-1">${title}</h6>
                <p class="text-xs text-muted">${message}</p>
            </div>
            <button class="text-muted hover:text-rose-500 ml-4 transition-colors" onclick="this.parentElement.remove()"><i class="bx bx-x"></i></button>
        `;

    // Stack order: Top positions prepend, Bottom positions append
    if (position.startsWith("top")) {
      container.prepend(toast);
    } else {
      container.appendChild(toast);
    }

    // Trigger animation
    requestAnimationFrame(() => {
      setTimeout(() => {
        toast.classList.remove(
          "toast-animate-in-top",
          "toast-animate-in-bottom",
          "toast-animate-in-left",
          "toast-animate-in-right",
          "opacity-0",
        );
      }, 10);
    });

    // Auto remove
    if (duration > 0) {
      setTimeout(() => {
        toast.classList.add("opacity-0");
        if (position.includes("top"))
          toast.classList.add("toast-animate-in-top");
        else toast.classList.add("toast-animate-in-bottom");

        setTimeout(() => toast.remove(), 300);
      }, duration);
    }
  },
};

/* --- Responsive Top Nav Overflow Handler --- */
let resizeTimer;
window.addEventListener("resize", () => {
  clearTimeout(resizeTimer);
  resizeTimer = setTimeout(() => {
    const navMode = getVibeConfig("nav-mode");
    const topNav = document.getElementById("top-navigation-bar");

    if (
      navMode === "top" &&
      topNav &&
      typeof renderTopMenu === "function" &&
      typeof menuConfig !== "undefined"
    ) {
      renderTopMenu(menuConfig, topNav);

      // Re-apply background preset to ensure correct colors after re-render
      const currentBg = getVibeConfig("bg-preset");
      updateBackgroundPreset(currentBg);
    }
  }, 150);
});
