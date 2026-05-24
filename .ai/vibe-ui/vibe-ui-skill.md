# Vibe UI Component Knowledge Base

<instructions>
You are an AI assistant integrating the "Vibe UI" design system into a CodeIgniter 4 (or similar) project. 
Vibe UI is a heavily customized Tailwind CSS template with custom classes (`btn`, `card`, `form-input`, etc.) and specific HTML structures.

To ensure **100% accuracy**, you MUST NOT guess the HTML structure or CSS classes for Vibe UI components. Instead, you have access to a Model Context Protocol (MCP) tool named `get_vibe_ui_component`.

Whenever the user asks you to build or modify a UI element (e.g., "Add a primary button", "Create a data table", "Add an accordion"), you MUST:
1. Identify the relevant component from the **Available Components Directory** below.
2. Call the `get_vibe_ui_component` MCP tool with the exact `category` and `filename`.
3. Use the exact HTML structure, wrapper classes, and Boxicons (`bx bx-...`) returned by the tool.
</instructions>

## Available Components Directory

### UI Kit (`category: "ui-kit"`)
- **accordion**: Vertically collapsing displays (`.accordion`, `.accordion-item`, `.accordion-flush`).
- **alerts**: Contextual feedback messages (`.alert`, `.alert-primary`, `.alert-danger`, etc.).
- **avatars**: User images and initials (`.avatar`, sizes, shapes).
- **badges**: Small count and labeling indicators (`.badge`, `.badge-primary`, `.badge-soft`).
- **breadcrumbs**: Navigation paths (`.breadcrumb`).
- **buttons**: Clickable elements (`.btn`, `.btn-primary`, `.btn-default`, `.btn-outline`, `.btn-icon`).
- **cards**: Flexible content containers (`.card`, `.card-header`, `.card-body`, `.card-footer`).
- **dropdowns**: Toggleable contextual overlays (`.dropdown`, `.dropdown-menu`, `.dropdown-item`).
- **lists**: Styled list groups (`.list-group`, `.list-group-item`).
- **modals**: Dialog prompts (`.modal`, `.modal-dialog`, `.modal-content`).
- **notifications**: Toast notifications and snackbars.
- **pagination**: Page navigation UI (`.pagination`, `.page-item`).
- **popovers**: Rich tooltips/popovers requiring floating-ui.
- **progress-bars**: Visual progression indicators (`.progress`, `.progress-bar`).
- **ribbons**: Corner ribbons for cards.
- **spinners**: Loading indicators (`.spinner-border`).
- **tables**: Styled data tables (`.table`, `.table-hover`, `.table-striped`).
- **tabs**: Navigational tabs (`.nav-tabs`, `.tab-content`, `.tab-pane`).
- **tooltips**: Small hover labels.
- **typography**: Headings, paragraphs, muted text (`text-muted`, `font-bold`).

### Forms (`category: "forms"`)
- **form-elements**: Basic inputs, selects, textareas, checkboxes, radios, switches (`.form-label`, `.form-control`, `.form-check`, `.form-switch`).
- **form-layouts**: Grid layouts, horizontal forms, vertical forms.

## Usage Rules
- **Icons**: Vibe UI uses Boxicons. Always use `<i class="bx bx-[icon-name]"></i>`.
- **Assets**: Always wrap static assets (images, js, css) with `<?= base_url('path') ?>` when building views.
- **DO NOT HALLUCINATE**: If the MCP tool returns a specific structure (e.g., a card requires `<div class="card"><div class="card-body">`), you must use it exactly. Do not use standard Tailwind utilities (like `bg-white shadow-md p-4 rounded`) if a custom Vibe UI class (`card`) exists.
