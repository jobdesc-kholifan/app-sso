/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: 'class',
    content: [
        "./app/Views/**/*.php",
        "./resources/**/*.js",
        "./resources/**/*.css"
    ],
    theme: {
        extend: {
            fontFamily: { sans: ['Inter', 'sans-serif'] },
            colors: {
                primary: {
                    50: 'rgba(var(--color-primary), 0.1)',
                    100: 'rgba(var(--color-primary), 0.2)',
                    200: 'rgba(var(--color-primary), 0.4)',
                    600: 'rgb(var(--color-primary))',
                    700: 'rgba(var(--color-primary), 0.8)',
                },
                accent: 'rgb(var(--color-primary))',
                surface: 'rgb(var(--color-bg-surface))',
                body: 'rgb(var(--color-bg-body))',
                muted: 'rgb(var(--color-text-muted))',
                alt: {
                    50: 'rgba(var(--color-bg-alt), 0.1)',
                    100: 'rgba(var(--color-bg-alt), 0.2)',
                    200: 'rgba(var(--color-bg-alt), 0.4)',
                    600: 'rgb(var(--color-bg-alt))',
                    700: 'rgba(var(--color-bg-alt), 0.8)',
                },
            }
        }
    },
    plugins: [],
}
