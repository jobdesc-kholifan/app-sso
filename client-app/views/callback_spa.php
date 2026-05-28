<?php $this->layout('layout', ['title' => 'SPA Callback (PKCE) - Client App']) ?>

<div class="bg-surface border border-slate-200 dark:border-slate-800 rounded-2xl p-6 md:p-8 shadow-sm max-w-md mx-auto text-center transition-colors duration-300">
    <div id="loader">
        <div class="w-12 h-12 border-4 border-slate-200 dark:border-slate-700 border-t-primary-600 rounded-full animate-spin mx-auto my-6"></div>
        <h2 class="text-xl font-extrabold mb-2">Exchanging PKCE Token</h2>
        <p class="text-muted text-sm leading-relaxed">Menukarkan kode otorisasi sementara dengan Access Token menggunakan PKCE di sisi frontend...</p>
    </div>
    <div id="error-box" class="hidden">
        <h2 class="text-xl font-extrabold text-rose-500 mb-2">Pertukaran Token Gagal!</h2>
        <p id="error-msg" class="break-all bg-slate-900 text-rose-400 p-4 rounded-xl font-mono text-xs border border-slate-800 my-4 text-left"></p>
        <a href="/" class="w-full inline-flex items-center justify-center gap-2 py-2.5 bg-primary-600 text-white rounded-xl font-bold hover:bg-primary-700 transition shadow-lg shadow-primary-600/20 mt-4">
            Kembali ke Beranda
        </a>
    </div>
</div>

<?php $this->start('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const urlParams = new URLSearchParams(window.location.search);
        const code = urlParams.get('code');
        const state = urlParams.get('state');

        if (!code) {
            showError("Parameter 'code' tidak ditemukan di URL callback.");
            return;
        }

        // Ambil code_verifier yang disimpan di sessionStorage
        let verifier = sessionStorage.getItem('spa_code_verifier');

        // Mismatch Simulator check
        const corruptFlag = sessionStorage.getItem('spa_corrupt_verifier');
        if (corruptFlag === 'true') {
            verifier = 'invalid_verifier_mismatch_12345';
            sessionStorage.removeItem('spa_corrupt_verifier');
        }

        if (!verifier) {
            showError("PKCE 'code_verifier' hilang dari session browser. Silakan coba login kembali.");
            return;
        }

        try {
            // 1. Post Exchange Token ke SSO Server (TANPA client_secret)
            const tokenResponse = await fetch('http://localhost/app-sso/public/oauth/token', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    grant_type: 'authorization_code',
                    client_id: 'testspa',
                    redirect_uri: 'http://localhost:8080/callback-spa',
                    code_verifier: verifier,
                    code: code
                })
            });

            const tokenData = await tokenResponse.json();

            if (!tokenResponse.ok || tokenData.error) {
                showError(tokenData.error_description || tokenData.message || "Gagal mendapatkan access token.");
                return;
            }

            // 2. Ambil UserInfo dengan Access Token baru
            const userinfoResponse = await fetch('http://localhost/app-sso/public/oauth/userinfo', {
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + tokenData.access_token
                }
            });

            const userData = await userinfoResponse.json();

            if (!userinfoResponse.ok) {
                showError("Gagal mengambil data profil pengguna dari UserInfo endpoint.");
                return;
            }

            // 3. Simpan profil ke session SPA di frontend
            sessionStorage.setItem('spa_profile', JSON.stringify(userData));
            sessionStorage.setItem('spa_token', tokenData.access_token);

            // Simpan status agar backend tahu kita sedang login tipe SPA
            const saveSession = await fetch('/save-spa-session', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    profile: userData,
                    token: tokenData.access_token
                })
            });

            // Redirect ke dashboard
            if (window.opener && !window.opener.closed) {
                window.opener.location.reload();
                window.close();
            } else {
                window.location.href = '/';
            }

        } catch (err) {
            console.error("Error during SPA Exchange Flow:", err);
            showError("Koneksi gagal atau CORS error ke Server SSO. Periksa kembali console log browser.");
        }
    });

    function showError(msg) {
        document.getElementById('loader').classList.add('hidden');
        document.getElementById('error-box').classList.remove('hidden');
        document.getElementById('error-msg').textContent = msg;
    }
</script>
<?php $this->end() ?>
