/**
 * OAuth Tutorial Module
 * Designed with SOLID, KISS, DRY, and SoC principles.
 */

// 1. State Management
const State = {
	getBaseUrl() {
		const container = document.getElementById("tutorial-container");
		return container ? container.getAttribute("data-base-url").replace(/\/$/, "") : "";
	},
};

// 2. DOM Elements / Selectors
const DOM = {
	authState: document.getElementById("auth-state"),
	authBase: document.getElementById("auth-base"),
	authClientId: document.getElementById("auth-client-id"),
	authRedirect: document.getElementById("auth-redirect"),
	scopeOpenId: document.getElementById("scope-openid"),
	scopeProfile: document.getElementById("scope-profile"),
	scopeEmail: document.getElementById("scope-email"),
	urlOutput: document.getElementById("url-output"),
	btnRedirectTest: document.getElementById("btn-redirect-test"),
	tokenCode: document.getElementById("token-code"),
	tokenSecret: document.getElementById("token-secret"),
	tokenCmdOutput: document.getElementById("token-cmd-output"),
	userinfoToken: document.getElementById("userinfo-token"),
	userinfoCmdOutput: document.getElementById("userinfo-cmd-output"),
};

// 3. UI/DOM Manipulators & Value Formatters (Separation of Concerns)
const UIManager = {
	updateAuthUrl(url) {
		DOM.urlOutput.innerText = url;
		DOM.btnRedirectTest.href = url;
	},

	updateTokenCommand(cmd) {
		DOM.tokenCmdOutput.innerText = cmd;
	},

	updateUserInfoCommand(cmd) {
		DOM.userinfoCmdOutput.innerText = cmd;
	},
};

// 4. Core Business Actions
const Actions = {
	startSimulation() {
		const targetTab = document.querySelector('.nav-link[data-tab-target="#tab-authorize"]');
		if (targetTab) {
			targetTab.click(); // Triggers Vibe UI's native app.js tab switcher
		}
	},

	randomState() {
		const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
		let result = "";
		for (let i = 0; i < 16; i++) {
			result += chars.charAt(Math.floor(Math.random() * chars.length));
		}
		DOM.authState.value = result;
		this.generateAuthUrl();
	},

	generateAuthUrl() {
		const base = DOM.authBase.value;
		const clientId = DOM.authClientId.value;
		const redirect = DOM.authRedirect.value;
		const state = DOM.authState.value;

		let scopes = [];
		if (DOM.scopeOpenId.checked) scopes.push("openid");
		if (DOM.scopeProfile.checked) scopes.push("profile");
		if (DOM.scopeEmail.checked) scopes.push("email");

		const scopeStr = encodeURIComponent(scopes.join(" "));
		const redirectStr = encodeURIComponent(redirect);

		const finalUrl = `${base}?response_type=code&client_id=${clientId}&redirect_uri=${redirectStr}&scope=${scopeStr}&state=${state}`;
		UIManager.updateAuthUrl(finalUrl);
	},

	generateTokenCmd() {
		const baseUrlVal = State.getBaseUrl();
		const code = DOM.tokenCode.value || "PASTE_YOUR_CODE_HERE";
		const secret = DOM.tokenSecret.value || "testsecret";
		const clientId = DOM.authClientId.value || "testclient";
		const redirect = DOM.authRedirect.value || "http://localhost:8080/callback";

		const cmd = `curl -X POST "${baseUrlVal}/oauth/token" \\
  -H "Content-Type: application/x-www-form-urlencoded" \\
  -d "grant_type=authorization_code" \\
  -d "client_id=${clientId}" \\
  -d "client_secret=${secret}" \\
  -d "redirect_uri=${redirect}" \\
  -d "code=${code}"`;

		UIManager.updateTokenCommand(cmd);
	},

	generateUserInfoCmd() {
		const baseUrlVal = State.getBaseUrl();
		const token = DOM.userinfoToken.value || "PASTE_YOUR_ACCESS_TOKEN_HERE";

		const cmd = `curl -X GET "${baseUrlVal}/oauth/userinfo" \\
  -H "Authorization: Bearer ${token}"`;

		UIManager.updateUserInfoCommand(cmd);
	},
};

// 5. Global Bindings (For inline HTML event handlers / old code compatibility)
window.startSimulation = () => Actions.startSimulation();
window.randomState = () => Actions.randomState();
window.generateAuthUrl = () => Actions.generateAuthUrl();
window.generateTokenCmd = () => Actions.generateTokenCmd();
window.generateUserInfoCmd = () => Actions.generateUserInfoCmd();

// 6. Event Binders & Initializer
const initTutorial = () => {
	// Attach dynamically to forms/inputs to maintain DRY
	if (DOM.authClientId) DOM.authClientId.addEventListener("input", () => Actions.generateAuthUrl());
	if (DOM.authRedirect) DOM.authRedirect.addEventListener("input", () => Actions.generateAuthUrl());
	if (DOM.authState) DOM.authState.addEventListener("input", () => Actions.generateAuthUrl());
	if (DOM.scopeOpenId) DOM.scopeOpenId.addEventListener("change", () => Actions.generateAuthUrl());
	if (DOM.scopeProfile) DOM.scopeProfile.addEventListener("change", () => Actions.generateAuthUrl());
	if (DOM.scopeEmail) DOM.scopeEmail.addEventListener("change", () => Actions.generateAuthUrl());

	if (DOM.tokenCode) DOM.tokenCode.addEventListener("input", () => Actions.generateTokenCmd());
	if (DOM.tokenSecret) DOM.tokenSecret.addEventListener("input", () => Actions.generateTokenCmd());

	if (DOM.userinfoToken) DOM.userinfoToken.addEventListener("input", () => Actions.generateUserInfoCmd());

	// Open authorization in a centered new popup window
	if (DOM.btnRedirectTest) {
		DOM.btnRedirectTest.addEventListener("click", (e) => {
			e.preventDefault();
			const url = DOM.btnRedirectTest.getAttribute("href");
			if (url && url !== "#" && url !== "") {
				const width = 680;
				const height = 780;
				const left = (screen.width - width) / 2;
				const top = (screen.height - height) / 2;
				window.open(url, 'sso_testing_popup', `width=${width},height=${height},top=${top},left=${left},status=no,resizable=yes,scrollbars=yes`);
			}
		});
	}

	// Backup tab switcher in case global handler fails
	document.querySelectorAll(".nav-tabs .nav-link[data-tab-target]").forEach((tabLink) => {
		tabLink.addEventListener("click", (e) => {
			e.preventDefault();
			const targetId = tabLink.getAttribute("data-tab-target");
			const targetPane = document.querySelector(targetId);
			if (!targetPane) return;

			const nav = tabLink.closest(".nav-tabs");
			if (nav) {
				nav.querySelectorAll(".nav-link").forEach((link) => link.classList.remove("active"));
			}
			tabLink.classList.add("active");

			// Hide all tab panes in the same container
			const contentContainer = targetPane.parentElement;
			contentContainer.querySelectorAll(".tab-pane").forEach((pane) => {
				pane.classList.add("hidden");
			});

			targetPane.classList.remove("hidden");
		});
	});

	// Init values
	Actions.randomState();
	Actions.generateAuthUrl();
	Actions.generateTokenCmd();
	Actions.generateUserInfoCmd();
};

if (document.readyState === "loading") {
	document.addEventListener("DOMContentLoaded", initTutorial);
} else {
	initTutorial();
}
