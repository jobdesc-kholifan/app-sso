<?php

if (!function_exists('is_vite_dev')) {
    /**
     * Checks if the Vite dev server is running and caches the result
     */
    function is_vite_dev(): bool
    {
        static $isDev = null;
        if ($isDev !== null) {
            return $isDev;
        }

        // Cek keberadaan file 'hot' di public/hot yang dibuat oleh Vite dev server
        if (is_file(FCPATH . 'hot')) {
            return $isDev = true;
        }

        $isDev = false;
        if (ENVIRONMENT === 'development') {
            $viteHost = env('vite.host', '127.0.0.1');
            $vitePort = env('vite.port', '5173');
            $connection = @fsockopen($viteHost, (int) $vitePort, $errno, $errstr, 0.2);
            if ($connection !== false) {
                $isDev = true;
                fclose($connection);
            }
        }

        return $isDev;
    }
}

if (!function_exists('vite_dev_url')) {
    /**
     * Gets the Vite dev server URL dynamically
     */
    function vite_dev_url(): string
    {
        static $url = null;
        if ($url !== null) {
            return $url;
        }

        $hotFile = FCPATH . 'hot';
        if (is_file($hotFile)) {
            $url = trim(file_get_contents($hotFile));
            // Ubah 0.0.0.0 menjadi localhost agar kompatibel di semua browser & OS host machine
            $url = str_replace('0.0.0.0', 'localhost', $url);
        }

        if (empty($url)) {
            $viteHost = env('vite.host', '127.0.0.1');
            $vitePort = env('vite.port', '5173');
            if ($viteHost === '0.0.0.0') {
                $viteHost = 'localhost';
            }
            $url = "http://{$viteHost}:{$vitePort}";
        }

        return rtrim($url, '/');
    }
}

if (!function_exists('vite_client')) {
    /**
     * Prints the Vite HMR client script in development mode
     */
    function vite_client(): string
    {
        static $printed = false;
        if ($printed) {
            return '';
        }

        if (is_vite_dev()) {
            $printed = true;
            return '<script type="module" src="' . vite_dev_url() . '/@vite/client"></script>' . "\n";
        }

        return '';
    }
}

if (!function_exists('vite_asset')) {
    /**
     * Vite integration for CodeIgniter 4
     * Automatically loads from Vite dev server during development
     * and from build manifest during production.
     */
    function vite_asset(string $entry = 'main.js', $dynamicResolve = false): string
    {
        $manifestPath = FCPATH . 'build/.vite/manifest.json';

        if ($dynamicResolve) {
            // Dynamically resolve entry path based on where this function is called
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
            if (isset($trace[0]['file'])) {
                $callerDir = dirname($trace[0]['file']);
                // Convert absolute path to relative path from project root
                $relativePath = str_replace(rtrim(ROOTPATH, '/'), '', $callerDir);
                $relativePath = ltrim($relativePath, '/'); // e.g., 'app/Views'

                // Only prepend if entry doesn't already have the path
                if (!str_starts_with($entry, $relativePath)) {
                    $entry = str_replace('\\', '/', $relativePath) . '/' . ltrim($entry, '/');
                }
            }
        }

        // Try to determine if dev server is running
        $isDev = is_vite_dev();

        if ($isDev) {
            return '<script type="module" src="' . vite_dev_url() . '/' . $entry . '"></script>';
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

        $isCssEntry = preg_match('/\.(css|scss|sass|less|styl|postcss)$/i', $entry);

        if ($isCssEntry) {
            // CSS entry: only load the CSS files, skip empty JS stub loader
            if (isset($manifest[$entry]['css'])) {
                foreach ($manifest[$entry]['css'] as $css) {
                    $tags .= '<link rel="stylesheet" href="' . base_url('build/' . $css) . '">' . "\n";
                }
            } else if (preg_match('/\.(css|scss|sass|less|styl|postcss)$/i', $file)) {
                $tags .= '<link rel="stylesheet" href="' . base_url('build/' . $file) . '">' . "\n";
            }
        } else {
            // JS entry
            $tags .= '<script type="module" src="' . base_url('build/' . $file) . '"></script>' . "\n";
            if (isset($manifest[$entry]['css'])) {
                foreach ($manifest[$entry]['css'] as $css) {
                    $tags .= '<link rel="stylesheet" href="' . base_url('build/' . $css) . '">' . "\n";
                }
            }
        }

        return $tags;
    }
}
