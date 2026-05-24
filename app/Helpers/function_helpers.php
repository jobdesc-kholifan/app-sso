<?php

if (!function_exists('vite_asset')) {
    /**
     * Vite integration for CodeIgniter 4
     * Automatically loads from Vite dev server during development
     * and from build manifest during production.
     */
    function vite_asset(string $entry = 'main.js', $dynamicResolve = true): string
    {
        $viteHost = env('vite.host', '127.0.0.1');
        $vitePort = env('vite.port', '5173');
        $devServerUrl = "http://{$viteHost}:{$vitePort}";
        $manifestPath = FCPATH . 'build/.vite/manifest.json';
        
        if($dynamicResolve) {
            // Dynamically resolve entry path based on where this function is called
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
            if (isset($trace[0]['file'])) {
                $callerDir = dirname($trace[0]['file']);
                // Convert absolute path to relative path from project root
                $relativePath = str_replace(rtrim(ROOTPATH, '/'), '', $callerDir);
                $relativePath = ltrim($relativePath, '/'); // e.g., 'app/Views'
                
                // Only prepend if entry doesn't already have the path
                if (!str_starts_with($entry, $relativePath)) {
                    $entry = $relativePath . '/' . ltrim($entry, '/');
                }
            }
        }

        // Try to determine if dev server is running
        $isDev = false;
        
        if (ENVIRONMENT === 'development') {
            // Cek port dengan timeout cepat (0.5s)
            $connection = @fsockopen($viteHost, (int) $vitePort, $errno, $errstr, 0.5);
            if ($connection !== false) {
                $isDev = true;
                fclose($connection);
            }
        }

        if ($isDev) {
            return '<script type="module" src="' . $devServerUrl . '/@vite/client"></script>' . "\n" .
                   '<script type="module" src="' . $devServerUrl . '/' . $entry . '"></script>';
        }

        if (!is_file($manifestPath)) {
            return '<!-- Vite manifest not found at ' . $manifestPath . ' -->';
        }

        $manifest = json_decode(file_get_contents($manifestPath), true);

        if (!isset($manifest[$entry])) {
            return '<!-- Vite entry ' . $entry . ' not found in manifest -->';
        }

        $tags = '';
        $file = $manifest[$entry]['file'];
        $tags .= '<script type="module" src="' . base_url('build/' . $file) . '"></script>' . "\n";

        if (isset($manifest[$entry]['css'])) {
            foreach ($manifest[$entry]['css'] as $css) {
                $tags .= '<link rel="stylesheet" href="' . base_url('build/' . $css) . '">' . "\n";
            }
        }

        return $tags;
    }
}
