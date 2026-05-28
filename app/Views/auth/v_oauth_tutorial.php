<?= $this->extend('layouts/main') ?>

<?= $this->section('style_head') ?>
<link rel="stylesheet" href="<?= base_url('dist/css/docs.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Main Developer Console Card -->
<div id="tutorial-container" class="card card-lg shadow-xl overflow-hidden p-0" data-base-url="<?= base_url() ?>">
    <!-- Header Section with Harmonious Gradient Accent (Integrated) -->
    <div class="card-header">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span
                        class="px-2.5 py-1 text-xs font-semibold bg-indigo-500/20 text-indigo-700 rounded-full border border-indigo-500/30">
                        OIDC / OAuth 2.0
                    </span>
                    <span
                        class="px-2.5 py-1 text-xs font-semibold bg-emerald-500/20 text-emerald-700 rounded-full border border-emerald-500/30">
                        Developer Tool
                    </span>
                </div>
                <h2 class="text-3xl font-extrabold tracking-tight">OAuth / SSO Integration & Testing Center</h2>
                <p class=" text-sm mt-2 max-w-xl">
                    Gunakan dashboard interaktif ini untuk memvalidasi integrasi Single Sign-On (SSO) dari aplikasi
                    klien Anda ke server pusat ini.
                </p>
            </div>
            <div class="shrink-0">
                <div
                    class="flex items-center gap-3 bg-white/5 backdrop-blur-md px-4 py-3 rounded-xl border border-white/10">
                    <i class="bx bx-shield-quarter text-indigo-400 text-3xl"></i>
                    <div>
                        <span class="block text-[10px] text-indigo-300 uppercase font-bold tracking-wider">SSO
                            Status</span>
                        <span class="block text-sm font-bold text-emerald-400">Ready to Authenticate</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Decorative background glow -->
        <div class="absolute right-0 bottom-0 w-80 h-80 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none">
        </div>
    </div>

    <div class="card-body">
        <!-- Vibe UI Pill Tabs Navigation -->
        <div class="nav-pills mb-6">
            <div class="nav-link active" data-tab-target="#tab-workflow">
                <i class="bx bx-git-repo-forked text-lg"></i>
                <span>1. SSO Workflow</span>
            </div>
            <div class="nav-link" data-tab-target="#tab-authorize">
                <i class="bx bx-key text-lg"></i>
                <span>2. Authorize Code Flow</span>
            </div>
            <div class="nav-link" data-tab-target="#tab-token">
                <i class="bx bx-refresh text-lg"></i>
                <span>3. Exchange Token</span>
            </div>
            <div class="nav-link" data-tab-target="#tab-userinfo">
                <i class="bx bx-user-circle text-lg"></i>
                <span>4. Get User Info</span>
            </div>
        </div>

        <!-- Content Container for Native Tab Panels -->
        <div class="tab-content">

            <!-- TAB 1: WORKFLOW DIAGRAM -->
            <div id="tab-workflow" class="tab-pane">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Flow Cards -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="card p-6">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                                <i class="bx bx-compass text-indigo-500 text-xl"></i>
                                Alur Integrasi SSO Berbasis OAuth 2.0 / OIDC
                            </h3>
                            <div
                                class="relative pl-8 space-y-6 before:absolute before:left-3 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200 dark:before:bg-slate-700">

                                <!-- Step 1 -->
                                <div class="relative">
                                    <div style="left: -29px; top: 0;"
                                        class="absolute w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 border-2 border-indigo-600 dark:border-indigo-400 flex items-center justify-center font-bold text-xs">
                                        1</div>
                                    <h4 class="font-bold text-slate-800 dark:text-white text-sm">Pengalihan Otorisasi
                                        (Redirect to SSO)</h4>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                                        Aplikasi Klien Anda mengarahkan browser pengguna ke URL otorisasi SSO Pusat
                                        dengan parameter `client_id`, `redirect_uri`, `response_type=code`, dan `scope`.
                                    </p>
                                </div>

                                <!-- Step 2 -->
                                <div class="relative">
                                    <div style="left: -29px; top: 0;"
                                        class="absolute w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 border-2 border-indigo-600 dark:border-indigo-400 flex items-center justify-center font-bold text-xs">
                                        2</div>
                                    <h4 class="font-bold text-slate-800 dark:text-white text-sm">Autentikasi & Consent
                                    </h4>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                                        Pengguna login menggunakan akun pusat mereka di server SSO. Jika berhasil, layar
                                        persetujuan (Consent Screen) akan muncul meminta izin untuk membagikan data
                                        kepada aplikasi klien.
                                    </p>
                                </div>

                                <!-- Step 3 -->
                                <div class="relative">
                                    <div style="left: -29px; top: 0;"
                                        class="absolute w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 border-2 border-indigo-600 dark:border-indigo-400 flex items-center justify-center font-bold text-xs">
                                        3</div>
                                    <h4 class="font-bold text-slate-800 dark:text-white text-sm">Pengiriman
                                        Authorization Code</h4>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                                        SSO mengarahkan kembali pengguna ke `redirect_uri` Klien dengan menyertakan kode
                                        rahasia sementara (`code`) di query parameter URL.
                                    </p>
                                </div>

                                <!-- Step 4 -->
                                <div class="relative">
                                    <div style="left: -29px; top: 0;"
                                        class="absolute w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 border-2 border-indigo-600 dark:border-indigo-400 flex items-center justify-center font-bold text-xs">
                                        4</div>
                                    <h4 class="font-bold text-slate-800 dark:text-white text-sm">Penukaran Access Token
                                        (Back-channel)</h4>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                                        Aplikasi Klien Anda secara rahasia mengirimkan `code` tersebut dari
                                        server-ke-server menggunakan metode `POST` ke `/oauth/token` bersama dengan
                                        `client_secret` untuk ditukar dengan `access_token` yang sah.
                                    </p>
                                </div>

                                <!-- Step 5 -->
                                <div class="relative">
                                    <div style="left: -29px; top: 0;"
                                        class="absolute w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 border-2 border-indigo-600 dark:border-indigo-400 flex items-center justify-center font-bold text-xs">
                                        5</div>
                                    <h4 class="font-bold text-slate-800 dark:text-white text-sm">Pengambilan Profil
                                        Pengguna</h4>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                                        Aplikasi Klien memanggil endpoint `/oauth/userinfo` dengan menyertakan header
                                        `Authorization: Bearer <access_token>` untuk mendapatkan nama, email, dan detail
                                            profil pengguna.
                                    </p>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Info — Merged Card -->
                    <div>
                        <div class="card p-6 bg-linear-to-b from-indigo-50/50 to-emerald-50/30 dark:from-indigo-950/20 dark:to-emerald-950/10 border-indigo-100 dark:border-indigo-900">

                            <!-- Seeded Test Credentials -->
                            <h3 class="text-sm font-bold text-indigo-900 dark:text-indigo-300 uppercase tracking-wider mb-3">
                                Seeded Test Credentials
                            </h3>
                            <p class="text-xs text-slate-500 mb-4">Secara default, database telah dikonfigurasi dengan kredensial uji coba berikut:</p>
                            <div class="space-y-3">
                                <div class="p-3 bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm">
                                    <span class="block text-[10px] text-slate-400 uppercase font-semibold">Client ID (Identifier)</span>
                                    <code class="text-xs font-bold text-indigo-600 dark:text-indigo-400 select-all">testclient</code>
                                </div>
                                <div class="p-3 bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm">
                                    <span class="block text-[10px] text-slate-400 uppercase font-semibold">Client Secret</span>
                                    <code class="text-xs font-bold text-indigo-600 dark:text-indigo-400 select-all">testsecret</code>
                                </div>
                                <div class="p-3 bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm">
                                    <span class="block text-[10px] text-slate-400 uppercase font-semibold">Valid Redirect URI</span>
                                    <code class="text-xs font-bold text-indigo-600 dark:text-indigo-400 select-all">http://localhost:8080/callback</code>
                                </div>
                            </div>

                            <!-- Divider -->
                            <div class="my-5 mb-3 border-t border-slate-200 dark:border-slate-700"></div>

                            <!-- Client App Demo -->
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4 leading-relaxed">
                                Jalankan aplikasi klien demo di terminal Anda, lalu buka melalui tombol di bawah.
                            </p>

                            <!-- Terminal Command -->
                            <div class="rounded-xl overflow-hidden border border-slate-800 mb-4">
                                <div class="flex items-center justify-between bg-slate-800 px-3 py-2">
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-2.5 h-2.5 rounded-full bg-rose-500"></div>
                                        <div class="w-2.5 h-2.5 rounded-full bg-amber-400"></div>
                                        <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                                    </div>
                                    <span class="text-[10px] text-slate-400 font-mono">Terminal</span>
                                    <button onclick="copyClientCommand(this)"
                                        class="text-[10px] text-slate-400 hover:text-white transition flex items-center gap-1 font-mono bg-slate-700 hover:bg-slate-600 px-2 py-0.5 rounded">
                                        <i class="bx bx-copy text-xs"></i> Copy
                                    </button>
                                </div>
                                <div class="bg-slate-900 px-4 py-3">
                                    <code id="client-cmd" class="text-xs font-mono text-emerald-400 select-all block">php -S 0.0.0.0:8080 client-app/index.php</code>
                                </div>
                            </div>

                            <!-- Open Button -->
                            <button onclick="openClientApp()"
                                class="w-full btn btn-success flex items-center justify-center gap-2 py-2 text-xs font-bold">
                                <i class="bx bx-link-external"></i> Buka Client App
                            </button>
                            <p class="text-[10px] text-slate-400 text-center mt-2">Pastikan server sudah berjalan di port <code class="font-mono">8080</code></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: INTERACTIVE AUTHORIZATION -->
            <div id="tab-authorize" class="tab-pane hidden">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Form inputs -->
                    <div class="lg:col-span-1 card p-6">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">1. Konfigurasi Parameter</h3>

                        <div class="space-y-4">
                            <div>
                                <label
                                    class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">OAuth
                                    Endpoint</label>
                                <input type="text" id="auth-base"
                                    class="form-control text-xs bg-slate-50 dark:bg-slate-800" readonly
                                    value="<?= base_url('oauth/authorize') ?>">
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Client
                                    ID</label>
                                <input type="text" id="auth-client-id" class="form-control" value="testclient"
                                    oninput="generateAuthUrl()">
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Redirect
                                    URI</label>
                                <input type="text" id="auth-redirect" class="form-control"
                                    value="http://localhost:8080/callback" oninput="generateAuthUrl()">
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Scopes</label>
                                <div class="space-y-2 mt-1">
                                    <label class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-400">
                                        <input type="checkbox" id="scope-openid" value="openid" checked
                                            class="rounded text-indigo-600 focus:ring-indigo-500 border-slate-300"
                                            onchange="generateAuthUrl()">
                                        <span>openid (OIDC standard)</span>
                                    </label>
                                    <label class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-400">
                                        <input type="checkbox" id="scope-profile" value="profile" checked
                                            class="rounded text-indigo-600 focus:ring-indigo-500 border-slate-300"
                                            onchange="generateAuthUrl()">
                                        <span>profile (Akses Nama & Avatar)</span>
                                    </label>
                                    <label class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-400">
                                        <input type="checkbox" id="scope-email" value="email" checked
                                            class="rounded text-indigo-600 focus:ring-indigo-500 border-slate-300"
                                            onchange="generateAuthUrl()">
                                        <span>email (Akses data email)</span>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">State
                                    (CSRF Token)</label>
                                <div class="flex gap-2">
                                    <input type="text" id="auth-state" class="form-control" value="xyz123"
                                        oninput="generateAuthUrl()">
                                    <button onclick="randomState()"
                                        class="btn btn-secondary px-3 py-2 text-xs flex items-center justify-center"
                                        title="Generate Random State">
                                        <i class="bx bx-dice-5 text-base"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Code Generator & Dynamic URL Output -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Vibe UI Native Code Container for Auth URL -->
                        <div class="card p-6">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2 flex items-center gap-2">
                                <i class="bx bx-link-external text-indigo-500 text-xl"></i>
                                2. Generated Authorization URL
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">
                                Klien Anda mengalihkan browser pengguna ke alamat ini untuk memulai autentikasi.
                            </p>

                            <div class="code-container mb-6">
                                <div class="code-header">
                                    <span class="code-lang">URL Otorisasi (SSO Redirect)</span>
                                    <button class="copy-btn"><i class="bx bx-copy"></i> Copy</button>
                                </div>
                                <div class="code-content">
                                    <code id="url-output" class="whitespace-normal break-all">Generating...</code>
                                </div>
                            </div>

                            <div class="flex flex-col md:flex-row gap-3">
                                <a id="btn-redirect-test" href="#" target="_blank"
                                    class="btn btn-primary flex-1 py-3 text-sm font-semibold flex items-center justify-center gap-2 shadow-lg shadow-indigo-600/10">
                                    Redirect & Mulai Login Otorisasi <i class="bx bx-right-arrow-alt text-lg"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Tips & Troubleshooting -->
                        <div class="card p-6 bg-slate-50 dark:bg-slate-800/40">
                            <h4 class="font-bold text-slate-800 dark:text-white text-sm mb-3 flex items-center gap-2">
                                <i class="bx bx-info-circle text-indigo-500 text-lg"></i>
                                Tips Pengujian:
                            </h4>
                            <ul
                                class="list-disc list-inside text-xs text-slate-500 dark:text-slate-400 space-y-2 leading-relaxed">
                                <li>Setelah klik tombol di atas, Anda akan dialihkan ke formulir login SSO pusat.</li>
                                <li>Selesai login, sistem akan menanyakan persetujuan data (Consent Screen).</li>
                                <li>Jika disetujui, SSO akan mengalihkan kembali ke browser Anda ke URL
                                    `http://localhost:8080/callback?code=CODE_RAHASIA&state=xyz123`.</li>
                                <li><strong>Catatan:</strong> Jika port 8080 di lokal Anda tidak ada aplikasi yang
                                    menyala,
                                    browser akan menampilkan halaman *not found/error connection*. **Ini wajar!** Cukup
                                    salin
                                    nilai `code` dari URL bar untuk digunakan pada langkah selanjutnya.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 3: EXCHANGE TOKEN -->
            <div id="tab-token" class="tab-pane hidden">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Configuration/Form Input -->
                    <div class="lg:col-span-1 card p-6 flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">1. Masukkan Auth Code</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4 leading-relaxed">
                                Masukkan kode sementara (`code`) yang Anda dapatkan di URL bar browser Anda dari langkah
                                sebelumnya.
                            </p>

                            <div class="space-y-4">
                                <div>
                                    <label
                                        class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Token
                                        Endpoint</label>
                                    <input type="text" class="form-control text-xs bg-slate-50 dark:bg-slate-800"
                                        readonly value="<?= base_url('oauth/token') ?>">
                                </div>

                                <div>
                                    <label
                                        class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Authorization
                                        Code</label>
                                    <input type="text" id="token-code"
                                        class="form-control font-mono text-xs placeholder:font-sans"
                                        placeholder="Paste 'code' parameter value here" oninput="generateTokenCmd()">
                                </div>

                                <div>
                                    <label
                                        class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Client
                                        Secret</label>
                                    <input type="text" id="token-secret" class="form-control" value="testsecret"
                                        oninput="generateTokenCmd()">
                                </div>
                            </div>
                        </div>

                        <div
                            class="p-3 bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900 rounded-lg text-[11px] text-amber-800 dark:text-amber-300 leading-relaxed mt-6">
                            <i class="bx bx-warning text-xs mr-1"></i>
                            Pemberian token hanya bisa dilakukan sekali saja untuk satu kode otorisasi demi menjaga
                            integritas
                            keamanan.
                        </div>
                    </div>

                    <!-- Code Generator Outputs -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="card p-6">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2 flex items-center gap-2">
                                <i class="bx bx-code-block text-indigo-500 text-xl"></i>
                                2. Backend Exchange Command
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">
                                Gunakan CURL (atau kode backend/HTTP client Anda) untuk mengirimkan data ini secara
                                terenkripsi:
                            </p>

                            <!-- Vibe UI Native Code Container with Automatic Clipboard Copying -->
                            <div class="code-container mb-6">
                                <div class="code-header">
                                    <span class="code-lang">Bash (cURL)</span>
                                    <button class="copy-btn"><i class="bx bx-copy"></i> Copy</button>
                                </div>
                                <div class="code-content">
                                    <code id="token-cmd-output">Loading...</code>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                <div>
                                    <h4 class="font-bold text-slate-800 dark:text-white text-xs mb-3 uppercase tracking-wider">
                                        Format Respons JSON Sukses:
                                    </h4>
                                    <div class="code-container mb-4">
                                        <div class="code-header">
                                            <span class="code-lang">JSON</span>
                                            <button class="copy-btn"><i class="bx bx-copy"></i> Copy</button>
                                        </div>
                                        <div class="code-content">
                                            <code>{
                                                "token_type": "Bearer",
                                                "expires_in": 3600,
                                                "access_token": "eyJhbGciOiJSUzI1...",
                                                "refresh_token": "def502008f51a...",
                                                "id_token": "eyJhbGciOiJSUzI1..." // (OIDC JWT)
                                                }</code>
                                        </div>
                                    </div>
                                    <div class="space-y-2 text-xs text-slate-500 dark:text-slate-400">
                                        <div><strong>token_type:</strong> Tipe token otentikasi (selalu <code>Bearer</code>).</div>
                                        <div><strong>expires_in:</strong> Masa aktif access token dalam detik (3600 detik = 1 jam).</div>
                                        <div><strong>access_token:</strong> Token JWT rahasia untuk mengakses API resource server.</div>
                                        <div><strong>refresh_token:</strong> Token khusus untuk memperbarui access token tanpa login ulang.</div>
                                        <div><strong>id_token:</strong> Token JWT khusus berstandar OpenID Connect (OIDC) yang berisi klaim data identitas user terenkripsi.</div>
                                    </div>
                                </div>

                                <div>
                                    <h4 class="font-bold text-rose-600 dark:text-rose-400 text-xs mb-3 uppercase tracking-wider">
                                        Format Respons JSON Error:
                                    </h4>
                                    <div class="code-container mb-4">
                                        <div class="code-header">
                                            <span class="code-lang">JSON (Error)</span>
                                            <button class="copy-btn"><i class="bx bx-copy"></i> Copy</button>
                                        </div>
                                        <div class="code-content">
                                            <code>{
                                                "error": "invalid_grant",
                                                "error_description": "Authorization code is invalid or expired"
                                                }</code>
                                        </div>
                                    </div>
                                    <div class="space-y-3 text-xs text-slate-500 dark:text-slate-400">
                                        <div class="p-2 bg-rose-50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900 rounded-lg">
                                            <span class="font-bold text-rose-600 dark:text-rose-400 block">invalid_client</span>
                                            Kunci <code>client_secret</code> salah, client tidak terdaftar, atau otentikasi client gagal.
                                        </div>
                                        <div class="p-2 bg-rose-50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900 rounded-lg">
                                            <span class="font-bold text-rose-600 dark:text-rose-400 block">invalid_grant</span>
                                            Kode otorisasi (<code>code</code>) sudah kedaluwarsa atau sudah pernah digunakan sebelumnya (proteksi sekali pakai).
                                        </div>
                                        <div class="p-2 bg-rose-50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900 rounded-lg">
                                            <span class="font-bold text-rose-600 dark:text-rose-400 block">invalid_request</span>
                                            Permintaan tidak lengkap (misal parameter <code>grant_type</code> atau <code>code</code> terlewat).
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 4: GET USERINFO -->
            <div id="tab-userinfo" class="tab-pane hidden">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Configuration / Token Input -->
                    <div class="lg:col-span-1 card p-6">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">1. Masukkan Access Token</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-4 leading-relaxed">
                            Tempel nilai `access_token` yang Anda dapatkan dari hasil respons HTTP POST `/oauth/token`
                            pada
                            langkah sebelumnya.
                        </p>

                        <div class="space-y-4">
                            <div>
                                <label
                                    class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">UserInfo
                                    Endpoint</label>
                                <input type="text" class="form-control text-xs bg-slate-50 dark:bg-slate-800" readonly
                                    value="<?= base_url('oauth/userinfo') ?>">
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Access
                                    Token</label>
                                <textarea id="userinfo-token" rows="6"
                                    class="form-control font-mono text-xs placeholder:font-sans"
                                    placeholder="Paste access_token here..." oninput="generateUserInfoCmd()"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Code Generator Outputs -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="card p-6">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2 flex items-center gap-2">
                                <i class="bx bx-terminal text-indigo-500 text-xl"></i>
                                2. UserInfo Request Command
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">
                                Kirimkan HTTP GET ke endpoint `/oauth/userinfo` menggunakan header otentikasi standar
                                `Bearer`:
                            </p>

                            <!-- Vibe UI Native Code Container with Automatic Clipboard Copying -->
                            <div class="code-container mb-6">
                                <div class="code-header">
                                    <span class="code-lang">Bash (cURL)</span>
                                    <button class="copy-btn"><i class="bx bx-copy"></i> Copy</button>
                                </div>
                                <div class="code-content">
                                    <code id="userinfo-cmd-output">Loading...</code>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                <div>
                                    <h4 class="font-bold text-slate-800 dark:text-white text-xs mb-3 uppercase tracking-wider">
                                        Format Respons JSON Profil Pengguna:
                                    </h4>
                                    <div class="code-container mb-4">
                                        <div class="code-header">
                                            <span class="code-lang">JSON</span>
                                            <button class="copy-btn"><i class="bx bx-copy"></i> Copy</button>
                                        </div>
                                        <div class="code-content">
                                            <code>{
                                                "sub": "1", // Unique User ID
                                                "name": "Administrator",
                                                "nickname": "Admin",
                                                "preferred_username": "admin",
                                                "email": "admin@appsso.id",
                                                "email_verified": true,
                                                "role": "superadmin"
                                                }</code>
                                        </div>
                                    </div>
                                    <div class="space-y-2 text-xs text-slate-500 dark:text-slate-400">
                                        <div><strong>sub:</strong> <i>Subject</i>, ID unik global pengguna (OIDC Standard). Jangan gunakan email/username sebagai key di database Anda, gunakan kolom <code>sub</code> ini.</div>
                                        <div><strong>name:</strong> Nama lengkap pengguna untuk display UI.</div>
                                        <div><strong>email:</strong> Alamat email utama yang terdaftar di SSO.</div>
                                        <div><strong>email_verified:</strong> Status verifikasi email (true/false).</div>
                                        <div><strong>role:</strong> Peran/otoritas pengguna (Custom Claim untuk RBAC).</div>
                                    </div>
                                </div>

                                <div>
                                    <h4 class="font-bold text-rose-600 dark:text-rose-400 text-xs mb-3 uppercase tracking-wider">
                                        Format Respons JSON Error:
                                    </h4>
                                    <div class="code-container mb-4">
                                        <div class="code-header">
                                            <span class="code-lang">JSON (Error 401)</span>
                                            <button class="copy-btn"><i class="bx bx-copy"></i> Copy</button>
                                        </div>
                                        <div class="code-content">
                                            <code>{
                                                "error": "access_denied",
                                                "message": "The access token expired or is invalid"
                                                }</code>
                                        </div>
                                    </div>
                                    <div class="space-y-3 text-xs text-slate-500 dark:text-slate-400">
                                        <div class="p-2 bg-rose-50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900 rounded-lg">
                                            <span class="font-bold text-rose-600 dark:text-rose-400 block">HTTP 401 Unauthorized</span>
                                            Dilemparkan jika header <code>Authorization</code> tidak disertakan, format salah, atau token tidak valid.
                                        </div>
                                        <div class="p-2 bg-rose-50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900 rounded-lg">
                                            <span class="font-bold text-rose-600 dark:text-rose-400 block">Access Token Expired</span>
                                            Header respons menyertakan:<br>
                                            <code class="text-[10px] block bg-slate-900 text-slate-300 p-1 mt-1 rounded">WWW-Authenticate: Bearer error="invalid_token", error_description="The access token expired"</code>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div> <!-- Closing card-body -->
    </div> <!-- Closing card -->
</div>
<?= $this->endSection() ?>

<?= $this->section('script_foot') ?>
<?= vite_asset('scripts/oauth_tutorial.js', true) ?>
<script>
    function copyClientCommand(btn) {
        const cmd = document.getElementById('client-cmd').textContent;
        navigator.clipboard.writeText(cmd).then(() => {
            const original = btn.innerHTML;
            btn.innerHTML = '<i class="bx bx-check text-xs"></i> Copied!';
            btn.classList.add('text-emerald-400');
            setTimeout(() => {
                btn.innerHTML = original;
                btn.classList.remove('text-emerald-400');
            }, 2000);
        });
    }

    function openClientApp() {
        const width = 1280;
        const height = 800;
        const left = Math.round((screen.width - width) / 2);
        const top = Math.round((screen.height - height) / 2);
        window.open(
            'http://localhost:8080',
            'client_app_window',
            `width=${width},height=${height},top=${top},left=${left},resizable=yes,scrollbars=yes,status=no`
        );
    }
</script>
<?= $this->endSection() ?>