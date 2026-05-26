<?php
require __DIR__ . '/vendor/autoload.php';

date_default_timezone_set('Asia/Jakarta');

session_start();

// Use the standard league/oauth2-client generic provider
$provider = new \League\OAuth2\Client\Provider\GenericProvider([
    'clientId' => 'testclient',    // The client ID assigned to you by the provider
    'clientSecret' => 'testsecret',   // The client password assigned to you by the provider
    'redirectUri' => 'http://localhost:8080/callback',
    'urlAuthorize' => 'http://localhost/oauth/authorize',
    'urlAccessToken' => 'http://appsso_web/oauth/token',
    'urlResourceOwnerDetails' => 'http://appsso_web/oauth/userinfo',
    'scopes' => 'openid profile email'
]);

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($path === '/callback') {
    // CALLBACK ROUTE
    if (!isset($_GET['code'])) {
        echo "Error: No code provided.";
        exit;
    } elseif (empty($_GET['state']) || (isset($_SESSION['oauth2state']) && $_GET['state'] !== $_SESSION['oauth2state'])) {
        $expected = $_SESSION['oauth2state'] ?? 'NOT SET';
        $received = $_GET['state'] ?? 'NOT SET';
        unset($_SESSION['oauth2state']);
        exit('Invalid state. Expected: ' . $expected . ', Received: ' . $received);
    } else {
        try {
            $accessToken = $provider->getAccessToken('authorization_code', [
                'code' => $_GET['code']
            ]);

            $resourceOwner = $provider->getResourceOwner($accessToken);

            $_SESSION['user_profile'] = $resourceOwner->toArray();
            $_SESSION['access_token'] = $accessToken->getToken();

?>
            <!DOCTYPE html>
            <html lang="en">

            <head>
                <meta charset="UTF-8">
                <title>Otentikasi Berhasil | SSO Pusat</title>
                <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600&display=swap" rel="stylesheet">
                <style>
                    body {
                        background-color: #090d16;
                        color: #f3f4f6;
                        font-family: 'Plus Jakarta Sans', sans-serif;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: center;
                        height: 100vh;
                        margin: 0;
                        overflow: hidden;
                        text-align: center;
                    }

                    .spinner {
                        width: 50px;
                        height: 50px;
                        border: 3px solid rgba(99, 102, 241, 0.1);
                        border-radius: 50%;
                        border-top-color: #6366f1;
                        animation: spin 1s ease-in-out infinite;
                        margin-bottom: 1.5rem;
                    }

                    @keyframes spin {
                        to {
                            transform: rotate(360deg);
                        }
                    }

                    h2 {
                        font-size: 1.25rem;
                        font-weight: 600;
                        margin-bottom: 0.5rem;
                    }

                    p {
                        color: #9ca3af;
                        font-size: 0.875rem;
                    }
                </style>
                <script>
                    if (window.opener && typeof window.opener.setTimelineStep === 'function') {
                        // 1. Immediately transition parent page timeline to Step 2 (Dapatkan Authorization Code & Consent)
                        window.opener.setTimelineStep(2);
                        window.opener.sso_success = true;

                        // 2. After 1.2s, transition to Step 3 (Backend Token Exchange & UserInfo)
                        setTimeout(() => {
                            window.opener.setTimelineStep(3);
                            document.getElementById('popup-status').innerText = 'Menukarkan Code dengan Access Token...';

                            // 3. After another 1.2s, reload parent window to show finished dashboard
                            setTimeout(() => {
                                document.getElementById('popup-status').innerText = 'Koneksi sukses! Menutup jendela...';
                                window.opener.location.href = '/';
                            }, 1200);
                        }, 1200);
                    }
                    setTimeout(() => {
                        window.close();
                    }, 2400);
                </script>
            </head>

            <body>
                <div class="spinner"></div>
                <h2>Otentikasi Berhasil!</h2>
                <p id="popup-status">Menerima Authorization Code...</p>
            </body>

            </html>
    <?php
            exit;
        } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
            exit('OAuth Provider Error: ' . $e->getMessage());
        } catch (\UnexpectedValueException $e) {
            echo "<h3>Gagal me-parse JSON dari Server SSO!</h3>";
            echo "<strong>Pesan Error:</strong> " . $e->getMessage() . "<br><br>";
            echo "<strong>Kemungkinan besar SSO Server mengirimkan HTML Error atau PHP Warning. Check log Server SSO Anda.</strong>";
            print_r($e->getTraceAsString());
            exit;
        } catch (\Throwable $e) {
            exit('General Error: ' . $e->getMessage());
        }
    }
} elseif ($path === '/logout') {
    // LOGOUT ROUTE
    session_destroy();
    header('Location: http://localhost:8080');
    // header('Location: http://localhost:9300/oauth/logout?post_logout_redirect_uri=http://localhost:8080');
    exit;
} elseif ($path === '/') {
    // HOME ROUTE / DASHBOARD
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Demo Client App | Developer Console</title>
        <!-- Google Fonts & Boxicons -->
        <link
            href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap"
            rel="stylesheet">
        <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

        <style>
            :root {
                --bg-dark: #090d16;
                --bg-surface: rgba(17, 24, 39, 0.7);
                --bg-card: #151c2c;
                --text-main: #f3f4f6;
                --text-muted: #9ca3af;
                --primary: #6366f1;
                --primary-hover: #4f46e5;
                --primary-light: rgba(99, 102, 241, 0.1);
                --accent-emerald: #10b981;
                --accent-glow: rgba(99, 102, 241, 0.15);
                --border-color: rgba(255, 255, 255, 0.05);
                --font-main: 'Plus Jakarta Sans', sans-serif;
                --font-mono: 'JetBrains Mono', monospace;
            }

            * {
                box-sizing: border-box;
                margin: 0;
                padding: 0;
            }

            body {
                font-family: var(--font-main);
                background-color: var(--bg-dark);
                color: var(--text-main);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 2rem 1.5rem;
                overflow-x: hidden;
                position: relative;
            }

            /* Decorative Background Orbs */
            .bg-orb {
                position: absolute;
                border-radius: 50%;
                filter: blur(140px);
                z-index: 0;
                pointer-events: none;
                opacity: 0.4;
            }

            .orb-1 {
                width: 400px;
                height: 400px;
                background: radial-gradient(circle, var(--primary) 0%, transparent 70%);
                top: -100px;
                left: -100px;
            }

            .orb-2 {
                width: 500px;
                height: 500px;
                background: radial-gradient(circle, #0ea5e9 0%, transparent 70%);
                bottom: -150px;
                right: -100px;
            }

            /* Main Container */
            .app-container {
                position: relative;
                z-index: 10;
                width: 100%;
                max-width: 680px;
            }

            /* Premium Glassmorphic Card */
            .card {
                background: var(--bg-card);
                border: 1px solid var(--border-color);
                border-radius: 24px;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
                overflow: hidden;
            }

            /* Header Gradient Banner */
            .card-header-banner {
                position: relative;
                background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%);
                padding: 2.5rem;
                border-bottom: 1px solid var(--border-color);
                overflow: hidden;
            }

            .card-header-banner::before {
                content: '';
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                height: 1px;
                background: linear-gradient(90deg, transparent, var(--primary), transparent);
            }

            .badge-container {
                display: flex;
                gap: 0.5rem;
                margin-bottom: 1rem;
            }

            .badge {
                font-size: 0.7rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                padding: 0.3rem 0.8rem;
                border-radius: 9999px;
                display: inline-flex;
                align-items: center;
                gap: 0.3rem;
            }

            .badge-primary {
                background-color: rgba(99, 102, 241, 0.2);
                color: #a5b4fc;
                border: 1px solid rgba(99, 102, 241, 0.3);
            }

            .badge-success {
                background-color: rgba(16, 185, 129, 0.2);
                color: #a7f3d0;
                border: 1px solid rgba(16, 185, 129, 0.3);
            }

            .badge-danger {
                background-color: rgba(239, 68, 68, 0.2);
                color: #fca5a5;
                border: 1px solid rgba(239, 68, 68, 0.3);
            }

            .title {
                font-size: 1.75rem;
                font-weight: 800;
                letter-spacing: -0.025em;
                line-height: 1.2;
                margin-bottom: 0.5rem;
                background: linear-gradient(to right, #ffffff, #c7d2fe);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }

            .subtitle {
                font-size: 0.9rem;
                color: var(--text-muted);
                line-height: 1.5;
            }

            /* Card Body */
            .card-body {
                padding: 2.5rem;
            }

            .intro-text {
                font-size: 0.95rem;
                color: var(--text-muted);
                line-height: 1.6;
                margin-bottom: 2rem;
            }

            /* Connection Console Grid */
            .console-box {
                background-color: rgba(9, 13, 22, 0.5);
                border: 1px solid rgba(255, 255, 255, 0.04);
                border-radius: 16px;
                padding: 1.5rem;
                margin-bottom: 2rem;
            }

            .console-title {
                font-size: 0.75rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                color: var(--primary);
                margin-bottom: 1rem;
                display: flex;
                align-items: center;
                gap: 0.4rem;
            }

            .console-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0.6rem 0;
                border-bottom: 1px solid rgba(255, 255, 255, 0.03);
                font-size: 0.85rem;
            }

            .console-row:last-child {
                border-bottom: none;
                padding-bottom: 0;
            }

            .console-label {
                color: var(--text-muted);
                font-weight: 500;
            }

            .console-value {
                font-family: var(--font-mono);
                color: #e2e8f0;
                font-weight: 600;
                background-color: rgba(255, 255, 255, 0.03);
                padding: 0.2rem 0.5rem;
                border-radius: 6px;
                border: 1px solid rgba(255, 255, 255, 0.02);
                font-size: 0.75rem;
                max-width: 250px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            /* Timeline Guide */
            .timeline {
                margin-bottom: 2.5rem;
                position: relative;
                padding-left: 1.75rem;
            }

            .timeline::before {
                content: '';
                position: absolute;
                left: 7px;
                top: 6px;
                bottom: 6px;
                width: 2px;
                background-color: rgba(255, 255, 255, 0.05);
            }

            .timeline-item {
                position: relative;
                margin-bottom: 1.25rem;
            }

            .timeline-item:last-child {
                margin-bottom: 0;
            }

            .timeline-badge {
                position: absolute;
                left: -1.75rem;
                top: 2px;
                width: 16px;
                height: 16px;
                border-radius: 50%;
                background-color: var(--bg-card);
                border: 2px solid var(--primary);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 2;
            }

            .timeline-item.active .timeline-badge {
                background-color: var(--primary);
                box-shadow: 0 0 10px var(--primary);
            }

            .timeline-content h4 {
                font-size: 0.85rem;
                font-weight: 600;
                color: #f3f4f6;
                margin-bottom: 0.2rem;
            }

            .timeline-content p {
                font-size: 0.75rem;
                color: var(--text-muted);
                line-height: 1.4;
            }

            /* Styled code block */
            pre {
                background: #090d16;
                color: #a5b4fc;
                padding: 1.25rem;
                border-radius: 12px;
                overflow-x: auto;
                font-family: var(--font-mono);
                font-size: 0.75rem;
                border: 1px solid rgba(255, 255, 255, 0.04);
                line-height: 1.5;
                margin-top: 0.5rem;
            }

            .dashboard-section-title {
                font-size: 0.85rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                color: #818cf8;
                margin-top: 1.5rem;
                margin-bottom: 0.5rem;
                display: flex;
                align-items: center;
                gap: 0.4rem;
            }

            /* Action Buttons */
            .btn-action {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0.75rem;
                width: 100%;
                padding: 1.1rem 2rem;
                background: linear-gradient(135deg, var(--primary) 0%, var(--primary-hover) 100%);
                color: #ffffff;
                border: none;
                border-radius: 14px;
                font-size: 0.95rem;
                font-weight: 700;
                cursor: pointer;
                text-decoration: none;
                transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.4);
                position: relative;
                overflow: hidden;
            }

            .btn-action::after {
                content: '';
                position: absolute;
                inset: 0;
                background: linear-gradient(90deg, rgba(255, 255, 255, 0), rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0));
                transform: translateX(-100%);
                transition: transform 0.5s ease;
            }

            .btn-action:hover {
                transform: translateY(-2px);
                box-shadow: 0 15px 30px -5px rgba(99, 102, 241, 0.6);
            }

            .btn-action:hover::after {
                transform: translateX(100%);
                transition: transform 0.8s ease;
            }

            .btn-action:active {
                transform: translateY(1px);
            }

            .btn-danger-gradient {
                background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
                box-shadow: 0 10px 25px -5px rgba(239, 68, 68, 0.3);
            }

            .btn-danger-gradient:hover {
                box-shadow: 0 15px 30px -5px rgba(239, 68, 68, 0.5);
            }

            .footer-note {
                text-align: center;
                font-size: 0.75rem;
                color: var(--text-muted);
                margin-top: 1.5rem;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0.3rem;
            }

            .footer-note i {
                color: var(--accent-emerald);
            }
        </style>
    </head>

    <body>

        <!-- Background Orbs -->
        <div class="bg-orb orb-1"></div>
        <div class="bg-orb orb-2"></div>

        <div class="app-container">
            <div class="card">

                <?php if (isset($_SESSION['user_profile'])): ?>
                    <!-- HEADER SECTION - LOGGED IN -->
                    <div class="card-header-banner">
                        <div class="badge-container">
                            <span class="badge badge-success">
                                <i class="bx bx-shield-quarter"></i> OIDC Connected
                            </span>
                            <span class="badge badge-primary">
                                <i class="bx bx-user-check"></i> Authenticated
                            </span>
                        </div>
                        <h1 class="title">Developer Testing Console</h1>
                        <p class="subtitle">Token berhasil ditukarkan! Di bawah ini adalah profil claims dan access token Anda.
                        </p>
                    </div>

                    <!-- CARD BODY - LOGGED IN -->
                    <div class="card-body">
                        <div class="intro-text" style="margin-bottom: 1.5rem;">
                            Selamat datang, <strong
                                style="color: #ffffff; font-size: 1.1rem;"><?= htmlspecialchars($_SESSION['user_profile']['name'] ?? $_SESSION['user_profile']['full_name'] ?? 'Developer User') ?></strong>!
                            Handshake OAuth 2.0 / OpenID Connect dengan server pusat telah berjalan dengan sukses secara
                            backend.
                        </div>

                        <!-- User Profile Information -->
                        <div class="dashboard-section-title">
                            <i class="bx bx-id-card text-lg"></i>
                            <span>Decoded User Profile (OIDC Claims)</span>
                        </div>
                        <pre><?= htmlspecialchars(print_r($_SESSION['user_profile'], true)) ?></pre>

                        <!-- Access Token Details -->
                        <div class="dashboard-section-title" style="margin-top: 2rem;">
                            <i class="bx bx-key text-lg"></i>
                            <span>Active Access Token</span>
                        </div>
                        <pre
                            style="color: #34d399; word-break: break-all; white-space: pre-wrap;"><?= htmlspecialchars($_SESSION['access_token']) ?></pre>

                        <div style="margin-top: 2.5rem;">
                            <a class="btn-action btn-danger-gradient" href="/logout">
                                <i class="bx bx-log-out text-lg"></i>
                                <span>Logout dari Klien</span>
                            </a>
                        </div>

                        <div class="footer-note">
                            <i class="bx bxs-check-shield"></i>
                            <span>Sesi lokal klien berhasil dihapus saat menekan tombol Logout</span>
                        </div>
                    </div>

                <?php else: ?>
                    <!-- HEADER SECTION - LOGGED OUT -->
                    <div class="card-header-banner">
                        <div class="badge-container">
                            <span class="badge badge-primary">
                                <i class="bx bx-code-block"></i> SSO Client Simulator
                            </span>
                            <span class="badge badge-success">
                                <i class="bx bx-wifi"></i> Ready to Handshake
                            </span>
                        </div>
                        <h1 class="title">SSO Client Integration Tester</h1>
                        <p class="subtitle">Aplikasi klien pengujian mandiri untuk memvalidasi alur Single Sign-On (SSO) pusat.
                        </p>
                    </div>

                    <!-- CARD BODY - LOGGED OUT -->
                    <div class="card-body">
                        <p class="intro-text">
                            Aplikasi simulasi ini terintegrasi menggunakan library standar PHP `league/oauth2-client` untuk
                            menguji pertukaran kode otorisasi dan profil OIDC claims secara aman.
                        </p>

                        <!-- Console Specs -->
                        <div class="console-box">
                            <div class="console-title">
                                <i class="bx bx-terminal"></i>
                                <span>Client Handshake Profile</span>
                            </div>
                            <div class="console-row">
                                <span class="console-label">Client ID</span>
                                <span class="console-value">testclient</span>
                            </div>
                            <div class="console-row">
                                <span class="console-label">SSO Base URL</span>
                                <span class="console-value">http://localhost:9300</span>
                            </div>
                            <div class="console-row">
                                <span class="console-label">Callback URI</span>
                                <span class="console-value">http://localhost:8080/callback</span>
                            </div>
                            <div class="console-row">
                                <span class="console-label">Scope Requested</span>
                                <span class="console-value">openid profile email</span>
                            </div>
                        </div>

                        <!-- Step Timeline -->
                        <div class="timeline">
                            <div id="step-1" class="timeline-item active">
                                <div class="timeline-badge"></div>
                                <div class="timeline-content">
                                    <h4>Langkah 1: Otorisasi & Login Pengguna</h4>
                                    <p>Mengalihkan browser Anda ke URL otorisasi SSO Pusat dengan membawa parameter aman.</p>
                                </div>
                            </div>
                            <div id="step-2" class="timeline-item">
                                <div class="timeline-badge"></div>
                                <div class="timeline-content">
                                    <h4>Langkah 2: Dapatkan Authorization Code & Consent</h4>
                                    <p>SSO Pusat mengalihkan kembali ke Callback URI klien dengan membawa parameter kode
                                        sementara (`code`).</p>
                                </div>
                            </div>
                            <div id="step-3" class="timeline-item">
                                <div class="timeline-badge"></div>
                                <div class="timeline-content">
                                    <h4>Langkah 3: Backend Token Exchange & UserInfo</h4>
                                    <p>Aplikasi klien menukar kode secara backend (`http://appsso_web`) untuk mengambil UserInfo
                                        & Access Token.</p>
                                </div>
                            </div>
                        </div>

                        <?php
                        // Generate dynamic authorization URL & session state
                        $authorizationUrl = $provider->getAuthorizationUrl();
                        $_SESSION['oauth2state'] = $provider->getState();
                        ?>

                        <a href="#" onclick="openSSOPopup('<?= htmlspecialchars($authorizationUrl) ?>'); return false;"
                            class="btn-action">
                            <span>Connect with SSO Pusat</span>
                            <i class="bx bx-log-in-circle text-xl"></i>
                        </a>

                        <div class="footer-note">
                            <i class="bx bxs-lock-alt"></i>
                            <span>Koneksi diamankan oleh token CSRF state:
                                <?= htmlspecialchars($_SESSION['oauth2state']) ?></span>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>

        <script>
            window.sso_success = false;

            function setTimelineStep(stepNumber) {
                // Reset all steps
                document.querySelectorAll('.timeline-item').forEach(item => {
                    item.classList.remove('active');
                });
                // Set active step
                const activeItem = document.getElementById(`step-${stepNumber}`);
                if (activeItem) {
                    activeItem.classList.add('active');
                }
            }

            function openSSOPopup(url) {
                // When popup opens, user is still on the login page (Step 1 is active)
                setTimelineStep(1);
                window.sso_success = false;

                const width = 600;
                const height = 750;
                const left = (screen.width - width) / 2;
                const top = (screen.height - height) / 2;
                const popup = window.open(url, 'sso_handshake_popup', `width=${width},height=${height},top=${top},left=${left},status=no,resizable=yes,scrollbars=yes`);

                // Monitor popup close event (fallback if user manually closes it before completing auth)
                const checkPopupClosed = setInterval(() => {
                    if (popup.closed) {
                        clearInterval(checkPopupClosed);
                        setTimeout(() => {
                            // If closed without success, revert back to Step 1
                            if (!window.sso_success) {
                                setTimelineStep(1);
                            }
                        }, 1000);
                    }
                }, 500);
            }
        </script>
    </body>

    </html>
<?php
} else {
    http_response_code(404);
    echo "Not Found";
}
