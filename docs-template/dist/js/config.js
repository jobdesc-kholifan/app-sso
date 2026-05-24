const menuConfig = [
    {
        header: "Reporting"
    },
    {
        title: "Sales Dashboard",
        icon: "bxs-dashboard",
        link: "dashboards/index.html"
    },
    {
        title: "Mobile App Demo",
        icon: "bx-mobile-alt",
        link: "dashboards/mobile-app.html",
        badge: { text: "New", color: "bg-emerald-500" }
    },
    {
        header: "Applications"
    },
    {
        title: "File Manager",
        icon: "bx-folder",
        link: "apps/app-file-manager.html"
    },
    {
        title: "Invoices",
        icon: "bx-receipt",
        children: [
            { title: "Invoice List", link: "apps/app-invoice-list.html" },
            { title: "Invoice Detail", link: "apps/app-invoice-detail.html" }
        ]
    },
    {
        header: "Pages"
    },
    {
        title: "User Profile",
        icon: "bx-user-circle",
        link: "pages/page-profile.html"
    },
    {
        title: "Pricing",
        icon: "bx-dollar-circle",
        link: "pages/page-pricing.html"
    },
    {
        title: "FAQs",
        icon: "bx-help-circle",
        link: "pages/page-faq.html"
    },
    {
        title: "Authentication",
        icon: "bx-lock-alt",
        children: [
            {
                title: "Login",
                children: [
                    { title: "Basic (v1)", link: "pages/auth/auth-login-v1.html" },
                    { title: "Split (v2)", link: "pages/auth/auth-login-v2.html" }
                ]
            },
            {
                title: "Register",
                children: [
                    { title: "Basic (v1)", link: "pages/auth/auth-register-v1.html" },
                    { title: "Split (v2)", link: "pages/auth/auth-register-v2.html" }
                ]
            },
            { title: "Forgot Password", link: "pages/auth/auth-forgot-password.html" }
        ]
    },
    {
        title: "Utility",
        icon: "bx-info-circle",
        children: [
            { title: "Blank Page", link: "pages/page-blank.html" },
            { title: "Landing Page", link: "pages/page-landing.html" },
            { title: "404 Error", link: "pages/error/error-404.html" },
            { title: "500 Error", link: "pages/error/error-500.html" },
            { title: "Maintenance", link: "pages/utility-maintenance.html" }
        ]
    },
    {
        header: "Documentation"
    },
    {
        title: "Setup & Installation",
        icon: "bx-book-open",
        link: "pages/docs-setup.html"
    },
    {
        title: "AI Vibe Coding",
        icon: "bx-bot",
        link: "pages/docs-vibe-coding.html",
        badge: { text: "Hot", color: "bg-purple-500" }
    },
    {
        header: "UI Kit"
    },
    {
        title: "Landing Page",
        icon: "bx-rocket",
        link: "ui-kit/landing.html",
        badge: { text: "Pro", color: "bg-primary" }
    },
    {
        title: "UI Components",
        icon: "bx-cube",
        children: [
            { title: "Typography", link: "ui-kit/typography.html" },
            { title: "Accordions", link: "ui-kit/accordion.html" },
            { title: "Avatars", link: "ui-kit/avatars.html" },
            { title: "Buttons", link: "ui-kit/buttons.html" },
            { title: "Dropdowns", link: "ui-kit/dropdowns.html" },
            { title: "Breadcrumbs", link: "ui-kit/breadcrumbs.html" },
            { title: "Cards", link: "ui-kit/cards.html" },
            { title: "Tabs", link: "ui-kit/tabs.html" },
            { title: "Tables", link: "ui-kit/tables.html" },
            { title: "Badges", link: "ui-kit/badges.html" },
            { title: "Modals", link: "ui-kit/modals.html" },
            { title: "Alerts", link: "ui-kit/alerts.html" },
            { title: "Notifications", link: "ui-kit/notifications.html" },
            { title: "Pagination", link: "ui-kit/pagination.html" },
            { title: "Popovers", link: "ui-kit/popovers.html" },
            { title: "Progress Bars", link: "ui-kit/progress-bars.html" },
            { title: "Ribbons", link: "ui-kit/ribbons.html" },
            { title: "Lists", link: "ui-kit/lists.html" },
            { title: "Spinners", link: "ui-kit/spinners.html" },
            { title: "Tooltips", link: "ui-kit/tooltips.html" }
        ]
    },
    {
        title: "Forms & Layouts",
        icon: "bx-spreadsheet",
        children: [
            { title: "Form Elements", link: "forms/form-elements.html" },
            { title: "Form Layouts", link: "forms/form-layouts.html" }
        ]
    },
    {
        title: "Multi-level Menu",
        icon: "bx-git-branch",
        children: [
            {
                title: "Level 2 - A",
                link: "#",
                children: [
                    {
                        title: "Level 3 - A",
                        link: "#",
                        children: [
                            { title: "Level 4 - Final", link: "#" }
                        ]
                    },
                    { title: "Level 3 - B", link: "#" }
                ]
            },
            { title: "Level 2 - B", link: "#" }
        ]
    },

];
