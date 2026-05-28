<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Enterprise-Grade CORS Filter for OAuth/OIDC Endpoints
 *
 * Security model:
 * - Only allows origins whose redirect_uri is registered in the oauth.clients table.
 * - No wildcard (*) is used; the exact origin is echoed back for matched clients.
 * - Adds Vary: Origin to prevent proxy/CDN caching issues.
 * - Caches preflight for 24 hours via Access-Control-Max-Age.
 *
 * Registered in Filters.php $filters array (path-specific),
 * NOT in $globals, so it is scoped only to OAuth API endpoints.
 */
class CorsFilter implements FilterInterface
{
    /**
     * Allowed OAuth endpoints that require cross-origin access.
     * Uses strpos matching to support subfolder deployments.
     */
    private array $corsEndpoints = [
        'oauth/token',
        'oauth/userinfo',
    ];

    /**
     * BEFORE filter: handles preflight OPTIONS and validates origin.
     * For OPTIONS requests, returns 200 immediately without hitting the controller.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! $this->isOAuthEndpoint($request)) {
            return;
        }

        $origin = $request->getHeaderLine('Origin');

        // No Origin header = not a cross-origin request, skip CORS
        if (empty($origin)) {
            return;
        }

        $allowedOrigin = $this->resolveAllowedOrigin($origin);

        // Immediately handle browser OPTIONS preflight
        if (strtoupper($request->getMethod()) === 'OPTIONS') {
            $response = service('response');

            if ($allowedOrigin) {
                $response->setHeader('Access-Control-Allow-Origin', $allowedOrigin);
                $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
                $response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
                $response->setHeader('Access-Control-Max-Age', '86400');
                $response->setHeader('Vary', 'Origin');
            }

            // Always return 200 for OPTIONS (even if origin is blocked,
            // the browser will handle the denial based on missing ACAO header)
            $response->setStatusCode(200);
            $response->setBody('');
            return $response;
        }
    }

    /**
     * AFTER filter: injects CORS headers into the final controller response.
     * This is the critical step — the controller may create a new response object
     * (e.g., via convertPsr7Response()), so headers must be added here to survive.
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        if (! $this->isOAuthEndpoint($request)) {
            return $response;
        }

        $origin = $request->getHeaderLine('Origin');

        if (empty($origin)) {
            return $response;
        }

        $allowedOrigin = $this->resolveAllowedOrigin($origin);

        if ($allowedOrigin) {
            $response->setHeader('Access-Control-Allow-Origin', $allowedOrigin);
            $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
            $response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
            $response->setHeader('Access-Control-Max-Age', '86400');
            $response->setHeader('Vary', 'Origin');
        }

        return $response;
    }

    /**
     * Checks if the current request path is a CORS-enabled OAuth endpoint.
     */
    private function isOAuthEndpoint(RequestInterface $request): bool
    {
        $path = $request->getUri()->getPath();

        foreach ($this->corsEndpoints as $endpoint) {
            if (strpos($path, $endpoint) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validates the incoming Origin against all registered client redirect_uris.
     * Extracts scheme://host:port from each redirect_uri and compares to origin.
     *
     * @return string|null The validated origin to reflect, or null if not allowed.
     */
    private function resolveAllowedOrigin(string $origin): ?string
    {
        try {
            $cache = \Config\Services::cache();
            $cacheKey = 'oauth_allowed_origins';
            $allowedOrigins = $cache->get($cacheKey);

            if ($allowedOrigins === null) {
                $db = db_connect();
                $clients = $db->table('oauth.clients')
                    ->select('redirect_uri')
                    ->get()
                    ->getResultArray();

                $allowedOrigins = [];
                foreach ($clients as $client) {
                    $redirectUris = explode(',', $client['redirect_uri']);

                    foreach ($redirectUris as $uri) {
                        $uri = trim($uri);
                        $parsed = parse_url($uri);

                        if (! $parsed || empty($parsed['scheme']) || empty($parsed['host'])) {
                            continue;
                        }

                        // Build origin from redirect_uri: scheme://host[:port]
                        $clientOrigin = $parsed['scheme'] . '://' . $parsed['host'];
                        if (! empty($parsed['port'])) {
                            $clientOrigin .= ':' . $parsed['port'];
                        }

                        $allowedOrigins[] = $clientOrigin;
                    }
                }
                
                $allowedOrigins = array_unique($allowedOrigins);
                // Cache for 24 hours (86400 seconds) to avoid querying on every request
                $cache->save($cacheKey, $allowedOrigins, 86400);
            }

            if (in_array(rtrim($origin, '/'), $allowedOrigins, true)) {
                return rtrim($origin, '/');
            }

        } catch (\Exception $e) {
            log_message('error', '[CorsFilter] Failed to resolve allowed origin: ' . $e->getMessage());
        }

        return null;
    }
}
