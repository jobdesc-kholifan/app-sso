<?php $this->layout('layout', ['title' => 'Demo Client App - SSO Tester Suite']) ?>

<div class="bg-surface border border-slate-200 dark:border-slate-800 rounded-2xl p-6 md:p-8 shadow-sm max-w-4xl mx-auto transition-colors duration-300">
    <?php if (isset($user_profile)): ?>
        <!-- LOGGED IN VIEW -->
        <div class="flex items-center gap-4 mb-6">
            <?php if (($flow_type ?? '') === 'confidential'): ?>
                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20">
                    Alur Teruji: Backend PHP (Confidential)
                </span>
            <?php else: ?>
                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20">
                    Alur Teruji: Frontend SPA (Public PKCE)
                </span>
            <?php endif; ?>
        </div>

        <h2 class="text-2xl font-extrabold mb-2">Login Berhasil!</h2>
        <p class="text-muted text-sm mb-6">Selamat datang kembali, <strong class="text-slate-900 dark:text-white"><?= htmlspecialchars($user_profile['full_name'] ?? 'User') ?></strong>.</p>

        <div class="p-4 rounded-xl mb-6 bg-emerald-500/10 border border-emerald-500/20 text-emerald-800 dark:text-emerald-400 text-sm">
            <strong class="font-bold flex items-center gap-1.5 mb-1"><i class="bx bxs-check-circle text-lg"></i> Info Sesi Otentikasi:</strong>
            Proses penukaran token dan pengambilan UserInfo berhasil disimulasikan secara aman tanpa adanya error database.
        </div>

        <div class="space-y-4">
            <div>
                <h3 class="text-sm font-bold text-muted uppercase tracking-wider mb-2">Data UserInfo dari SSO Server:</h3>
                <pre class="bg-slate-900 text-sky-400 p-4 rounded-xl overflow-x-auto font-mono text-xs border border-slate-800"><?= htmlspecialchars(print_r($user_profile, true)) ?></pre>
            </div>

            <div>
                <h3 class="text-sm font-bold text-muted uppercase tracking-wider mb-2">Access Token:</h3>
                <pre class="bg-slate-900 text-purple-400 p-4 rounded-xl overflow-x-auto font-mono text-xs border border-slate-800"><?= htmlspecialchars($access_token) ?></pre>
            </div>
        </div>

        <div class="mt-8">
            <a class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-bold bg-rose-600 text-white rounded-xl shadow-md hover:bg-rose-700 transition" href="/logout">
                <i class="bx bx-log-out"></i> Logout & Hapus Sesi
            </a>
        </div>

    <?php else: ?>
        <!-- TABS FOR LOGIN CHOICE -->
        <div class="flex gap-2 p-1.5 rounded-xl mb-8" style="background-color: rgb(var(--color-bg-alt))">
            <button class="flex-1 py-2.5 text-sm font-semibold rounded-lg transition-all bg-primary-600 text-white shadow-md shadow-primary-600/20" id="tab-php" onclick="switchTab('php-flow', this)">1. Backend PHP</button>
            <button class="flex-1 py-2.5 text-sm font-semibold rounded-lg transition-all text-muted hover:text-slate-900 dark:hover:text-white" id="tab-spa" onclick="switchTab('spa-flow', this)">2. Frontend SPA (PKCE)</button>
            <button class="flex-1 py-2.5 text-sm font-semibold rounded-lg transition-all text-muted hover:text-slate-900 dark:hover:text-white" id="tab-errors" onclick="switchTab('error-simulator', this)">3. Error Simulator</button>
        </div>

        <!-- CONFIGURATION PANEL (DYNAMIC PARAMETERS) -->
        <div class="p-6 rounded-2xl mb-6 border border-slate-200 dark:border-slate-800" style="background-color: rgb(var(--color-bg-alt))">
            <div class="text-xs font-bold text-muted uppercase tracking-wider mb-4">Parameter Scopes & Custom Testing</div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <label class="flex items-center gap-3 text-sm font-medium cursor-pointer">
                    <input type="checkbox" id="scope-openid" value="openid" checked class="w-4 h-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                    <span>openid (OIDC Core)</span>
                </label>
                <label class="flex items-center gap-3 text-sm font-medium cursor-pointer">
                    <input type="checkbox" id="scope-profile" value="profile" checked class="w-4 h-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                    <span>profile (Nama & Info)</span>
                </label>
                <label class="flex items-center gap-3 text-sm font-medium cursor-pointer">
                    <input type="checkbox" id="scope-email" value="email" checked class="w-4 h-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                    <span>email (Alamat Surel)</span>
                </label>
                <label class="flex items-center gap-3 text-sm font-medium cursor-pointer text-rose-500">
                    <input type="checkbox" id="scope-invalid" value="unsupported_scope" class="w-4 h-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                    <span>unsupported_scope (Simulasi Gagal)</span>
                </label>
            </div>
        </div>

        <!-- TAB 1: BACKEND PHP -->
        <div id="php-flow" class="tab-content block">
            <div class="p-4 rounded-xl mb-6 border-l-4 border-primary-600 text-sm leading-relaxed" style="background-color: rgb(var(--color-bg-alt))">
                <strong class="font-bold block mb-1">Arsitektur Confidential Client:</strong>
                Alur backend server-side tradisional. Kredensial Klien (<code class="font-mono bg-slate-200 dark:bg-slate-800 px-1 py-0.5 rounded">client_secret</code>) disimpan dengan sangat rahasia di dalam file konfigurasi PHP Anda dan dikirimkan secara aman dari Server-ke-Server.
            </div>
            <button class="w-full flex items-center justify-center gap-2 py-3.5 bg-primary-600 text-white rounded-xl font-bold hover:bg-primary-700 transition shadow-lg shadow-primary-600/20" onclick="startPhpLoginFlow()">
                Mulai Tes dengan Backend PHP <i class="bx bx-right-arrow-alt text-lg"></i>
            </button>
        </div>

        <!-- TAB 2: FRONTEND SPA -->
        <div id="spa-flow" class="tab-content hidden">
            <div class="p-4 rounded-xl mb-6 border-l-4 border-sky-500 text-sm leading-relaxed" style="background-color: rgb(var(--color-bg-alt))">
                <strong class="font-bold block mb-1">Arsitektur Public SPA (PKCE):</strong>
                Didesain untuk aplikasi web murni frontend (React/Vue/JS). <strong class="text-sky-500">Tanpa menggunakan client_secret</strong>. Keamanan pertukaran token diamankan di browser menggunakan hashing dinamis <strong class="font-semibold">PKCE (Code Verifier & Challenge)</strong>.
            </div>
            <button class="w-full flex items-center justify-center gap-2 py-3.5 bg-sky-500 text-white rounded-xl font-bold hover:bg-sky-600 transition shadow-lg shadow-sky-500/20" onclick="startSpaLoginFlow(false)">
                Mulai Tes dengan SPA (PKCE) <i class="bx bx-zap text-lg"></i>
            </button>
        </div>

        <!-- TAB 3: NEGATIVE TESTING & ERROR SIMULATOR -->
        <div id="error-simulator" class="tab-content hidden">
            <div class="p-4 rounded-xl mb-6 border-l-4 border-rose-500 text-sm leading-relaxed" style="background-color: rgb(var(--color-bg-alt))">
                <strong class="font-bold block mb-1">Integrasi & Pengetesan Robustness:</strong>
                Gunakan skenario simulasi error berikut untuk memastikan OAuth Server menampilkan layar error yang elegan serta memvalidasi sistem error catcher Anda.
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Skenario 1: Client ID Palsu -->
                <div class="p-5 rounded-2xl border border-slate-200 dark:border-slate-800" style="background-color: rgb(var(--color-bg-alt))">
                    <div class="font-bold text-sm mb-1">Client ID Tidak Valid</div>
                    <div class="text-xs text-muted mb-4">Mengirim parameter 'client_id' salah untuk memicu error <strong class="font-semibold">invalid_client</strong>.</div>
                    <button class="w-full py-2.5 bg-amber-500 text-white rounded-lg font-bold hover:bg-amber-600 transition text-sm flex items-center justify-center gap-1.5" onclick="triggerErrorClient()">
                        Tes Client Palsu <i class="bx bx-error-alt text-base"></i>
                    </button>
                </div>

                <!-- Skenario 2: Redirect URI Salah -->
                <div class="p-5 rounded-2xl border border-slate-200 dark:border-slate-800" style="background-color: rgb(var(--color-bg-alt))">
                    <div class="font-bold text-sm mb-1">Redirect URI Mismatch</div>
                    <div class="text-xs text-muted mb-4">Mengirim parameter redirect URI yang tidak terdaftar di database klien SSO.</div>
                    <button class="w-full py-2.5 bg-amber-500 text-white rounded-lg font-bold hover:bg-amber-600 transition text-sm flex items-center justify-center gap-1.5" onclick="triggerErrorRedirect()">
                        Tes Redirect Mismatch <i class="bx bx-link-external text-base"></i>
                    </button>
                </div>

                <!-- Skenario 3: PKCE Mismatch (Frontend Callback) -->
                <div class="p-5 rounded-2xl border border-slate-200 dark:border-slate-800" style="background-color: rgb(var(--color-bg-alt))">
                    <div class="font-bold text-sm mb-1">PKCE Verifier Corrupt</div>
                    <div class="text-xs text-muted mb-4">Mengacak nilai verifier di browser saat callback untuk memicu token exchange mismatch.</div>
                    <button class="w-full py-2.5 bg-rose-600 text-white rounded-lg font-bold hover:bg-rose-700 transition text-sm flex items-center justify-center gap-1.5" onclick="startSpaLoginFlow(true)">
                        Tes PKCE Mismatch <i class="bx bx-lock-open-alt text-base"></i>
                    </button>
                </div>

                <!-- Skenario 4: Invalid Client Secret -->
                <div class="p-5 rounded-2xl border border-slate-200 dark:border-slate-800" style="background-color: rgb(var(--color-bg-alt))">
                    <div class="font-bold text-sm mb-1">Client Secret Salah</div>
                    <div class="text-xs text-muted mb-4">Mengirim client secret yang salah dari backend saat pertukaran auth code.</div>
                    <button class="w-full py-2.5 bg-rose-600 text-white rounded-lg font-bold hover:bg-rose-700 transition text-sm flex items-center justify-center gap-1.5" onclick="triggerErrorSecret()">
                        Tes Secret Salah <i class="bx bx-key text-base"></i>
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php $this->start('scripts') ?>
<script>
    function getSelectedScopes() {
        const scopes = [];
        if (document.getElementById('scope-openid').checked) scopes.push('openid');
        if (document.getElementById('scope-profile').checked) scopes.push('profile');
        if (document.getElementById('scope-email').checked) scopes.push('email');
        if (document.getElementById('scope-invalid').checked) scopes.push('unsupported_scope');
        return scopes.join(' ');
    }

    function switchTab(tabId, btn) {
        // Sembunyikan semua tab content
        document.querySelectorAll('.tab-content').forEach(c => {
            c.classList.add('hidden');
            c.classList.remove('block');
        });
        // Tampilkan tab active
        document.getElementById(tabId).classList.remove('hidden');
        document.getElementById(tabId).classList.add('block');

        // Reset class semua tombol tab
        document.querySelectorAll('[id^="tab-"]').forEach(b => {
            b.className = "flex-1 py-2.5 text-sm font-semibold rounded-lg transition-all text-muted hover:text-slate-900 dark:hover:text-white";
        });

        // Set style aktif untuk tombol tab
        if (tabId === 'error-simulator') {
            btn.className = "flex-1 py-2.5 text-sm font-semibold rounded-lg transition-all bg-rose-600 text-white shadow-md shadow-rose-600/20";
        } else if (tabId === 'spa-flow') {
            btn.className = "flex-1 py-2.5 text-sm font-semibold rounded-lg transition-all bg-sky-500 text-white shadow-md shadow-sky-500/20";
        } else {
            btn.className = "flex-1 py-2.5 text-sm font-semibold rounded-lg transition-all bg-primary-600 text-white shadow-md shadow-primary-600/20";
        }
    }

    // --- POPUP HELPER ---
    function openPopup(url) {
        const width = 680;
        const height = 780;
        const left = (screen.width - width) / 2;
        const top = (screen.height - height) / 2;
        return window.open(url, 'sso_testing_popup', `width=${width},height=${height},top=${top},left=${left},status=no,resizable=yes,scrollbars=yes`);
    }

    // --- REDIRECT FLOW INITIATORS ---
    function startPhpLoginFlow() {
        const scopes = encodeURIComponent(getSelectedScopes());
        openPopup(`/start-php-flow?test_scopes=${scopes}`);
    }

    // --- ERROR FLOW TRIGGERS ---
    function triggerErrorClient() {
        const ssoAuthorizeUrl = 'http://localhost/app-sso/public/oauth/authorize';
        openPopup(`${ssoAuthorizeUrl}?response_type=code&client_id=fake_client_123&redirect_uri=${encodeURIComponent('http://localhost:8080/callback')}&scope=openid`);
    }

    function triggerErrorRedirect() {
        const ssoAuthorizeUrl = 'http://localhost/app-sso/public/oauth/authorize';
        openPopup(`${ssoAuthorizeUrl}?response_type=code&client_id=testclient&redirect_uri=${encodeURIComponent('http://localhost:8080/unregistered-callback-route')}&scope=openid`);
    }

    function triggerErrorSecret() {
        const scopes = encodeURIComponent(getSelectedScopes());
        openPopup(`/start-php-flow?test_scopes=${scopes}&test_client_secret=wrong_secret_key_abc`);
    }

    // --- PKCE FRONTEND GENERATOR ---
    function dec2hex(dec) {
        return ('0' + dec.toString(16)).slice(-2);
    }

    function generateCodeVerifier() {
        var array = new Uint32Array(56 / 2);
        window.crypto.getRandomValues(array);
        return Array.from(array, dec2hex).join('');
    }

    function sha256(plain) {
        const encoder = new TextEncoder();
        const data = encoder.encode(plain);
        return window.crypto.subtle.digest('SHA-256', data);
    }

    function base64urlencode(a) {
        var str = "";
        var bytes = new Uint8Array(a);
        var len = bytes.byteLength;
        for (var i = 0; i < len; i++) {
            str += String.fromCharCode(bytes[i]);
        }
        return btoa(str)
            .replace(/\+/g, "-")
            .replace(/\//g, "_")
            .replace(/=+$/, "");
    }

    async function generateCodeChallenge(v) {
        var hashed = await sha256(v);
        return base64urlencode(hashed);
    }

    // --- SPA LOGIN INITIATION ---
    async function startSpaLoginFlow(corruptVerifier = false) {
        const verifier = generateCodeVerifier();
        const challenge = await generateCodeChallenge(verifier);

        sessionStorage.setItem('spa_code_verifier', verifier);

        if (corruptVerifier) {
            sessionStorage.setItem('spa_corrupt_verifier', 'true');
        } else {
            sessionStorage.removeItem('spa_corrupt_verifier');
        }

        const ssoAuthorizeUrl = 'http://localhost/app-sso/public/oauth/authorize';
        const clientId = 'testspa';
        const redirectUri = 'http://localhost:8080/callback-spa';
        const scope = getSelectedScopes();
        const state = generateCodeVerifier().substring(0, 16);

        const authUrl = `${ssoAuthorizeUrl}?response_type=code` +
            `&client_id=${encodeURIComponent(clientId)}` +
            `&redirect_uri=${encodeURIComponent(redirectUri)}` +
            `&scope=${encodeURIComponent(scope)}` +
            `&state=${encodeURIComponent(state)}` +
            `&code_challenge=${encodeURIComponent(challenge)}` +
            `&code_challenge_method=S256`;

        openPopup(authUrl);
    }
</script>
<?php $this->end() ?>
