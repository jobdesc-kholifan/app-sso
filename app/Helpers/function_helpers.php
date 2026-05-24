<?php

/**
 * Vite Helper untuk CodeIgniter 4
 * Membantu me-load asset dari Vite Dev Server saat development,
 * atau dari manifest.json saat sudah di-build (production).
 */

if (!function_exists('vite_asset')) {
    /**
     * Meng-generate tag <script> dan <link> untuk Vite
     *
     * @param string $entryPoint Path entry point, misal 'public/dist/js/app.js' atau 'src/main.js'
     * @return string HTML script/link tags
     */
    function vite_asset(string $entryPoint): string
    {
        $isDev = env('CI_ENVIRONMENT') === 'development';
        $devServerUrl = env('VITE_DEV_SERVER', 'http://localhost:5173/');

        // Jika mode development, load langsung dari Vite Dev Server
        if ($isDev) {
            $html  = '<script type="module" src="' . $devServerUrl . '@vite/client"></script>' . "\n";
            $html .= '<script type="module" src="' . $devServerUrl . ltrim($entryPoint, '/') . '"></script>';
            return $html;
        }

        // Jika mode production, baca dari manifest.json
        $manifestPath = FCPATH . 'dist/.vite/manifest.json'; // Path bawaan Vite 5+
        if (!file_exists($manifestPath)) {
            $manifestPath = FCPATH . 'dist/manifest.json'; // Path bawaan Vite 4
        }

        // Fallback jika tidak ada manifest
        if (!file_exists($manifestPath)) {
            return '<script type="module" src="' . base_url('dist/' . ltrim($entryPoint, '/')) . '"></script>';
        }

        $manifest = json_decode(file_get_contents($manifestPath), true);

        if (!isset($manifest[$entryPoint])) {
            return '<!-- Vite Entry Not Found: ' . $entryPoint . ' -->';
        }

        $html = '';
        $file = $manifest[$entryPoint]['file'];

        // Load file CSS yang berelasi dengan JS entry point ini (jika ada)
        if (isset($manifest[$entryPoint]['css'])) {
            foreach ($manifest[$entryPoint]['css'] as $css) {
                $html .= '<link rel="stylesheet" href="' . base_url('dist/' . $css) . '"/>' . "\n";
            }
        }

        // Load JS file
        $html .= '<script type="module" src="' . base_url('dist/' . $file) . '"></script>';

        return $html;
    }
}

if (!function_exists('vite')) {
    /**
     * Me-load single asset URL dari Vite (berguna untuk gambar, dsb)
     *
     * @param string $path Path file di dalam folder source
     * @return string URL ke file tersebut
     */
    function vite(string $path): string
    {
        $isDev = env('CI_ENVIRONMENT') === 'development';
        $devServerUrl = env('VITE_DEV_SERVER', 'http://localhost:5173/');

        if ($isDev) {
            return $devServerUrl . ltrim($path, '/');
        }

        $manifestPath = FCPATH . 'dist/.vite/manifest.json';
        if (!file_exists($manifestPath)) {
            $manifestPath = FCPATH . 'dist/manifest.json';
        }

        if (!file_exists($manifestPath)) {
            return base_url('dist/' . ltrim($path, '/'));
        }

        $manifest = json_decode(file_get_contents($manifestPath), true);

        if (isset($manifest[$path])) {
            return base_url('dist/' . $manifest[$path]['file']);
        }

        return base_url('dist/' . ltrim($path, '/'));
    }
}
