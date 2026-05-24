# Vibe UI - Premium Aesthetic Component Library

Vibe UI is a high-performance, utility-first UI templating system designed for building stunning, themeable admin dashboards and web applications. Built with **Vanilla JS** and **Tailwind CSS**, it offers a "Rich Aesthetics" experience with glassmorphism effects, smooth animations, and a powerful real-time theming engine.

![Vibe UI Preview](https://img.shields.io/badge/Design-Premium-blueviolet?style=for-the-badge)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.4+-38bdf8?style=for-the-badge&logo=tailwind-css)
![Vanilla JS](https://img.shields.io/badge/JS-Vanilla-f7df1e?style=for-the-badge&logo=javascript)

---

## ✨ Key Features

- 🎨 **Dynamic Theming Engine**: Real-time accent color switching, deep dark mode support, and multiple background presets.
- 📐 **Adaptive Canvas Layouts**: Four distinct layout modes: **Full**, **Boxed**, **Soft**, and **Floating**.
- 🚀 **Performance Optimized**: Component logic is centralized in a custom Tailwind CSS plugin using `@apply` for minimal CSS footprint.
- 🧭 **Intelligent Navigation**: Sidebar with recursive active-state detection, automated sub-menu expansion, and persistent mini/expanded states.
- 💎 **Premium Component Library**: 
    - **Buttons**: Soft, Gradient, and Outline variants with morphing loading states.
    - **Badges**: Floating and inline badges for flexible notifications.
    - **Dropdowns**: Viewport-aware positioning powered by **Floating UI**.
    - **Input Groups**: Seamless integration between inputs and UI buttons.
- 📝 **Interactive Documentation**: Developer-ready pages with live code snippets and copy-to-clipboard functionality.

---

## 🛠️ Technology Stack

- **Core**: HTML5, Vanilla JavaScript (ES6+)
- **Styling**: [Tailwind CSS](https://tailwindcss.com/) (Custom Plugin Architecture)
- **Icons**: [Boxicons](https://boxicons.com/)
- **Floating Logic**: [Floating UI](https://floating-ui.com/)
- **Fonts**: [Inter](https://fonts.google.com/specimen/Inter) (via Google Fonts)

---

## ⚙️ Configuration & Theming

Vibe UI uses a centralized configuration system powered by `themes.json` located in the root directory. This file defines the initial defaults for the application.

### `themes.json` Keys

| Key | Type | Description | Available Values |
| :--- | :--- | :--- | :--- |
| `color-theme` | `string` | Sets the default theme mode on first load. | `"light"`, `"dark"` |
| `light-palette` | `string` | Selection of professional color schemes for Light Mode. | `"Pure White"`, `"Soft Slate"`, `"Modern Gray"`, `"Oceanic Light"`, `"Professional Cream"` |
| `dark-palette` | `string` | Selection of professional color schemes for Dark Mode. | `"Classic Slate"`, `"Midnight Onyx"`, `"Charcoal Elegance"`, `"Oceanic Deep"`, `"Antigravity IDE"`, `"VS Code Modern"` |
| `accent-color` | `string` | The primary brand/accent color in RGB format. | Any RGB string (e.g., `"79, 70, 229"`) |
| `bg-preset` | `string` | The background and surface color strategy. | `"Neutral"`, `"Solid"`, `"Matching"` |
| `canvas-style` | `string` | The outer layout container style. | `"Full"`, `"Boxed"`, `"Soft"`, `"Floating"` |
| `zoom-level` | `string` | Controls the base font size and UI scaling. | `"Compact"`, `"Standard"`, `"Zoom"` |
| `nav-mode` | `string` | Placement of the main navigation menu. | `"sidebar"`, `"top"` |
| `sidebar-theme` | `string` | The initial width state of the sidebar. | `"expanded"`, `"mini"` |

---

### Detailed Settings Information

#### 🎨 Light Palettes
- **Pure White**: Brilliant, high-contrast minimalist white.
- **Soft Slate**: Subtle blue-gray professional aesthetic.
- **Modern Gray**: Warm-neutral industrial gray.
- **Oceanic Light**: Refreshing blue-tinted brightness.
- **Professional Cream**: Classic, warm executive appeal.

#### 🌙 Dark Palettes
- **Classic Slate**: Deep blue-gray professional look.
- **Midnight Onyx**: High-contrast, near-black aesthetics.
- **Charcoal Elegance**: Soft, warm dark gray focus.
- **Antigravity IDE**: Matte dark gray inspired by modern IDEs.

#### 📐 Layout Styles (Canvas)
- **Full**: Edge-to-edge layout.
- **Boxed**: Centered layout with a maximum width of 1280px.
- **Soft**: Highly rounded corners with gap margins.
- **Floating**: Floating containers with equal margins from the viewport edges.

#### 🏗️ LocalStorage Persistence
To prevent data collisions, all preference keys are namespaced with `vibe-template.`:
- `vibe-template.color-theme`
- `vibe-template.accent-color`
- `vibe-template.sidebar-theme`
- `vibe-template.bg-preset`
- `vibe-template.canvas-style`

---

## 🚀 Getting Started

### 1. Installation
Clone the repository and open `index.html` in your browser.

```bash
git clone https://github.com/your-repo/vibe-templating.git
cd vibe-templating
```

### 2. Customization
Edit `themes.json` in the root directory to set your desired initial state. Modifying this file will change the starting theme, accent color, and layout for all new users.


---

## 📜 Documentation
Check out the `buttons.html` page for a complete reference of the button system, including code snippets for every variant.

---

## ⚖️ License
Distributed under the MIT License. See `LICENSE` for more information.

---

Designed with ❤️ by **Vibe UI Team**
