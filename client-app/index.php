<?php
// Jika me-request file statis yang ada di disk, serve secara langsung dengan content-type yang tepat
$filePath = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (is_file($filePath)) {
    $ext = pathinfo($filePath, PATHINFO_EXTENSION);
    $mimeTypes = [
        'css'   => 'text/css',
        'js'    => 'application/javascript',
        'png'   => 'image/png',
        'jpg'   => 'image/jpeg',
        'jpeg'  => 'image/jpeg',
        'gif'   => 'image/gif',
        'svg'   => 'image/svg+xml',
        'woff'  => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf'   => 'font/ttf',
        'otf'   => 'font/otf',
    ];
    $contentType = $mimeTypes[strtolower($ext)] ?? 'application/octet-stream';
    header('Content-Type: ' . $contentType);
    readfile($filePath);
    exit;
}

require __DIR__ . '/vendor/autoload.php';

session_start();

use Bramus\Router\Router;
use League\Plates\Engine;
use League\OAuth2\Client\Provider\GenericProvider;

$router = new Router();
$templates = new Engine(__DIR__ . '/views');

/**
 * Instansiasi GenericProvider berdasarkan parameter testing di session / GET
 */
function getOAuthProvider() {
    $customScopes = $_GET['test_scopes'] ?? $_SESSION['test_scopes'] ?? 'openid profile email';
    $customRedirect = $_GET['test_redirect'] ?? $_SESSION['test_redirect'] ?? 'http://localhost:8080/callback';
    $customClientId = $_GET['test_client_id'] ?? $_SESSION['test_client_id'] ?? 'testclient';
    $customClientSecret = $_GET['test_client_secret'] ?? $_SESSION['test_client_secret'] ?? 'testsecret';

    return new GenericProvider([
        'clientId'                => $customClientId,
        'clientSecret'            => $customClientSecret,
        'redirectUri'             => $customRedirect,
        'urlAuthorize'            => 'http://localhost/app-sso/public/oauth/authorize',
        'urlAccessToken'          => 'http://localhost/app-sso/public/oauth/token',
        'urlResourceOwnerDetails' => 'http://localhost/app-sso/public/oauth/userinfo',
        'scopes'                  => $customScopes
    ]);
}

// 1. Halaman Utama / Dashboard
$router->get('/', function() use ($templates) {
    $data = [];
    if (isset($_SESSION['user_profile'])) {
        $data['user_profile'] = $_SESSION['user_profile'];
        $data['access_token'] = $_SESSION['access_token'] ?? '';
        $data['flow_type'] = $_SESSION['flow_type'] ?? '';
    }
    echo $templates->render('dashboard', $data);
});

// 2. Mulai Alur PHP Oauth Flow
$router->get('/start-php-flow', function() {
    $_SESSION['test_scopes'] = $_GET['test_scopes'] ?? null;
    $_SESSION['test_redirect'] = $_GET['test_redirect'] ?? null;
    $_SESSION['test_client_id'] = $_GET['test_client_id'] ?? null;
    $_SESSION['test_client_secret'] = $_GET['test_client_secret'] ?? null;

    $provider = getOAuthProvider();
    $authorizationUrl = $provider->getAuthorizationUrl();
    $_SESSION['oauth2state'] = $provider->getState();
    header('Location: ' . $authorizationUrl);
    exit;
});

// 3. Callback URL untuk Confidential PHP Flow
$router->get('/callback', function() use ($templates) {
    if (!isset($_GET['code'])) {
        echo $templates->render('error', ['error_message' => 'Parameter code tidak ditemukan di URL callback.']);
        exit;
    }

    if (empty($_GET['state']) || (isset($_SESSION['oauth2state']) && $_GET['state'] !== $_SESSION['oauth2state'])) {
        $expected = $_SESSION['oauth2state'] ?? 'NOT SET';
        $received = $_GET['state'] ?? 'NOT SET';
        unset($_SESSION['oauth2state']);
        echo $templates->render('error', [
            'error_message' => "State tidak valid. Diharapkan: {$expected}, Diterima: {$received}"
        ]);
        exit;
    }

    try {
        $provider = getOAuthProvider();
        $accessToken = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);

        $resourceOwner = $provider->getResourceOwner($accessToken);

        $_SESSION['user_profile'] = $resourceOwner->toArray();
        $_SESSION['access_token'] = $accessToken->getToken();
        $_SESSION['flow_type'] = 'confidential';

        // Bersihkan parameter testing
        unset($_SESSION['test_scopes']);
        unset($_SESSION['test_redirect']);
        unset($_SESSION['test_client_id']);
        unset($_SESSION['test_client_secret']);

        echo $templates->render('callback_success');
        exit;
    } catch (\Exception $e) {
        unset($_SESSION['test_scopes']);
        unset($_SESSION['test_redirect']);
        unset($_SESSION['test_client_id']);
        unset($_SESSION['test_client_secret']);
        echo $templates->render('error', ['error_message' => $e->getMessage()]);
        exit;
    }
});

// 4. Callback URL untuk Frontend SPA Flow (PKCE)
$router->get('/callback-spa', function() use ($templates) {
    echo $templates->render('callback_spa');
});

// 5. Menyimpan Data Sesi SPA Ke PHP Session (API Helper)
$router->post('/save-spa-session', function() {
    $input = json_decode(file_get_contents('php://input'), true);
    header('Content-Type: application/json');
    if ($input) {
        $_SESSION['user_profile'] = $input['profile'];
        $_SESSION['access_token'] = $input['token'];
        $_SESSION['flow_type'] = 'spa_pkce';
        echo json_encode(['success' => true]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Payload tidak valid']);
    }
    exit;
});

// 6. Log Out & Destroy Session
$router->get('/logout', function() {
    session_destroy();
    header('Location: http://localhost/app-sso/public/oauth/logout?post_logout_redirect_uri=' . urlencode('http://localhost:8080/'));
    exit;
});

// Penanganan Error 404
$router->set404(function() use ($templates) {
    http_response_code(404);
    echo $templates->render('error', ['error_message' => 'Halaman yang Anda cari tidak ditemukan (404).']);
});

$router->run();
