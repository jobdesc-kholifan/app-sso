<?php
require __DIR__ . '/vendor/autoload.php';

session_start();

// Use the standard league/oauth2-client generic provider
$provider = new \League\OAuth2\Client\Provider\GenericProvider([
    'clientId'                => 'testclient',    // The client ID assigned to you by the provider
    'clientSecret'            => 'testsecret',   // The client password assigned to you by the provider
    'redirectUri'             => 'http://localhost:8080/callback',
    'urlAuthorize'            => 'http://localhost/app-sso/public/oauth/authorize',
    'urlAccessToken'          => 'http://localhost/app-sso/public/oauth/token',
    'urlResourceOwnerDetails' => 'http://localhost/app-sso/public/oauth/userinfo',
    'scopes'                  => 'openid profile email'
]);

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($path === '/callback') {
    // CALLBACK ROUTE
    if (!isset($_GET['code'])) {
        // If we don't have an authorization code then get one
        echo "Error: No code provided.";
        exit;
    } elseif (empty($_GET['state']) || (isset($_SESSION['oauth2state']) && $_GET['state'] !== $_SESSION['oauth2state'])) {
        // State is invalid, possible CSRF attack in progress
        $expected = $_SESSION['oauth2state'] ?? 'NOT SET';
        $received = $_GET['state'] ?? 'NOT SET';
        unset($_SESSION['oauth2state']);
        exit('Invalid state. Expected: ' . $expected . ', Received: ' . $received);
    } else {
        // Try to get an access token using the authorization code grant.
        try {
            $accessToken = $provider->getAccessToken('authorization_code', [
                'code' => $_GET['code']
            ]);

            $resourceOwner = $provider->getResourceOwner($accessToken);

            $_SESSION['user_profile'] = $resourceOwner->toArray();
            $_SESSION['access_token'] = $accessToken->getToken();

            header('Location: /');
            exit;
        } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
            // Error resmi dari server OAuth (misal: client_id salah, code expired)
            exit('OAuth Provider Error: ' . $e->getMessage());
        } catch (\UnexpectedValueException $e) {
            // 💡 TANGKAP ERROR JSON DI SINI
            echo "<h3>Gagal me-parse JSON dari Server SSO!</h3>";
            echo "<strong>Pesan Error:</strong> " . $e->getMessage() . "<br><br>";

            // Kita coba intip trace-nya untuk melihat string apa yang sebenarnya diterima
            echo "<strong>Kemungkinan besar SSO Server mengirimkan HTML Error atau PHP Warning. Check log Server SSO Anda.</strong>";
            print_r($e->getTraceAsString());
        } catch (\Throwable $e) {
            // Jaga-jaga jika ada error lain (PHP 7/8 kompatibel)
            exit('General Error: ' . $e->getMessage());
        }
    }
} elseif ($path === '/logout') {
    // LOGOUT ROUTE
    session_destroy();
    // Redirect ke server SSO dengan membawa post_logout_redirect_uri
    // header('Location: http://localhost:8080/');
    header('Location: http://localhost/app-sso/public/oauth/logout?post_logout_redirect_uri=' . urlencode('http://localhost:8080/'));
    exit;
} elseif ($path === '/') {
    // HOME ROUTE / DASHBOARD
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Demo Client App</title>
        <style>
            body {
                font-family: sans-serif;
                text-align: center;
                margin-top: 50px;
            }

            .btn {
                display: inline-block;
                padding: 10px 20px;
                background: #0ea5e9;
                color: white;
                text-decoration: none;
                border-radius: 8px;
                font-weight: bold;
                font-size: 16px;
                margin: 10px;
            }

            .btn-danger {
                background: #ef4444;
            }

            .btn:hover {
                opacity: 0.9;
            }

            .card {
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                padding: 20px;
                max-width: 600px;
                margin: 0 auto;
                text-align: left;
            }

            pre {
                background: #1e293b;
                color: #a5b4fc;
                padding: 15px;
                border-radius: 8px;
                overflow-x: auto;
            }
        </style>
    </head>

    <body>
        <?php if (isset($_SESSION['user_profile'])): ?>
            <h1>Dashboard Klien SSO</h1>
            <p>Selamat datang, <strong><?= htmlspecialchars($_SESSION['user_profile']['full_name'] ?? 'User') ?></strong>!</p>

            <div class="card">
                <h3>Data Profil dari SSO:</h3>
                <pre><?= htmlspecialchars(print_r($_SESSION['user_profile'], true)) ?></pre>

                <h3>Access Token:</h3>
                <pre><?= htmlspecialchars($_SESSION['access_token']) ?></pre>
            </div>
            <br>
            <a class="btn btn-danger" href="/logout">Logout dari Klien</a>
        <?php else: ?>
            <h1>Welcome to Demo Client App</h1>
            <p>This is a testing application to verify your SSO implementation.</p>
            <br>
            <?php
            // Generate auth URL and state
            $authorizationUrl = $provider->getAuthorizationUrl();
            $_SESSION['oauth2state'] = $provider->getState();
            ?>
            <a class="btn" href="<?= htmlspecialchars($authorizationUrl) ?>">Login with SSO</a>
        <?php endif; ?>
    </body>

    </html>
<?php
} else {
    // Ignore other requests like favicon.ico
    http_response_code(404);
    echo "Not Found";
}
