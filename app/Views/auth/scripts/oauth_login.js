/**
 * OAuth Login Module
 * Designed with SOLID, KISS, DRY, and SoC principles.
 */

// 1. State Management
const State = {
	accounts: JSON.parse(localStorage.getItem("sso_accounts") || "[]"),
	currentState: "standard", // 'chooser', 'password', 'standard'

	saveAccounts() {
		localStorage.setItem("sso_accounts", JSON.stringify(this.accounts));
	},

	addAccount(user) {
		const exists = this.accounts.some((acc) => acc.username === user.username);
		if (!exists) {
			this.accounts.push(user);
			this.saveAccounts();
		}
	},

	removeAccountAtIndex(index) {
		this.accounts.splice(index, 1);
		this.saveAccounts();
	},
};

// 2. DOM Elements / Selectors
const DOM = {
	authCard: document.getElementById("auth-card"),
	chooserSection: document.getElementById("account-chooser-section"),
	loginForm: document.getElementById("login-form"),
	defaultHeader: document.getElementById("default-header"),
	chooserHeader: document.getElementById("account-chooser-header"),
	selectedHeader: document.getElementById("selected-account-header"),
	emailFieldContainer: document.getElementById("email-field-container"),
	emailVisibleInput: document.getElementById("email-visible"),
	hiddenEmailInput: document.getElementById("hidden-email"),
	btnBackToChooser: document.getElementById("btn-back-to-chooser"),
	btnCancel: document.getElementById("btn-cancel"),
	btnUseAnother: document.getElementById("btn-use-another"),
	accountsList: document.getElementById("accounts-list"),
	errorAlert: document.getElementById("error-alert"),
	errorMessage: document.getElementById("error-message"),
	selectedAccountAvatar: document.getElementById("selected-account-avatar"),
	selectedAccountName: document.getElementById("selected-account-name"),
	selectedAccountEmail: document.getElementById("selected-account-email"),

	submitBtn() {
		return this.loginForm.querySelector('button[type="submit"]');
	},
};

// 3. UI/DOM Manipulators
const UIManager = {
	renderAccounts(onSelect, onRemove) {
		DOM.accountsList.innerHTML = "";
		State.accounts.forEach((acc, index) => {
			const item = document.createElement("div");
			item.className =
				"flex items-center gap-3 p-3 rounded-xl border border-slate-100 dark:border-primary-800/80 bg-primary hover:bg-slate-50 dark:hover:bg-slate-800/40 hover:border-sky-500/30 transition-all cursor-pointer relative group";

			const contentWrap = document.createElement("div");
			contentWrap.className = "flex items-center gap-3 flex-1 min-w-0";
			contentWrap.onclick = () => onSelect(acc.username, acc.full_name, acc.avatar);
			contentWrap.innerHTML = `
                <div class="w-10 h-10 rounded-full overflow-hidden border border-slate-200/50 dark:border-slate-700/50 shrink-0">
                    <img src="${acc.avatar}" class="w-full h-full object-cover">
                </div>
                <div class="grow min-w-0">
                    <h4 class="font-bold text-slate-800 dark:text-slate-200 text-sm truncate leading-tight">${acc.full_name}</h4>
                    <p class="text-xs text-slate-500 truncate mt-0.5">${acc.username}</p>
                </div>
            `;

			const deleteBtn = document.createElement("button");
			deleteBtn.type = "button";
			deleteBtn.className =
				"w-8 h-8 rounded-full flex items-center justify-center text-rose-500 dark:text-rose-400 bg-rose-500/10 border border-rose-500 hover:bg-rose-500/20 hover:border-rose-500/40 transition-all z-20 flex-shrink-0";
			deleteBtn.innerHTML = `<i class="bx bx-x text-lg"></i>`;
			deleteBtn.onclick = (e) => {
				e.stopPropagation();
				onRemove(index);
			};

			item.appendChild(contentWrap);
			item.appendChild(deleteBtn);
			DOM.accountsList.appendChild(item);
		});
	},

	showState(state) {
		State.currentState = state;

		// Hide all initially
		DOM.defaultHeader.classList.add("hidden");
		DOM.chooserHeader.classList.add("hidden");
		DOM.selectedHeader.classList.add("hidden");
		DOM.chooserSection.classList.add("hidden");
		DOM.loginForm.classList.add("hidden");
		DOM.emailFieldContainer.classList.add("hidden");
		DOM.btnBackToChooser.classList.add("hidden");
		DOM.btnCancel.classList.remove("hidden");

		if (state === "chooser") {
			DOM.chooserHeader.classList.remove("hidden");
			DOM.chooserSection.classList.remove("hidden");
			this.renderAccounts(
				(username, fullName, avatar) => Actions.selectAccount(username, fullName, avatar),
				(index) => Actions.removeAccount(index)
			);
		} else if (state === "password") {
			DOM.selectedHeader.classList.remove("hidden");
			DOM.loginForm.classList.remove("hidden");
			DOM.btnBackToChooser.classList.remove("hidden");
			DOM.btnCancel.classList.add("hidden");
		} else {
			DOM.defaultHeader.classList.remove("hidden");
			DOM.loginForm.classList.remove("hidden");
			DOM.emailFieldContainer.classList.remove("hidden");
			DOM.hiddenEmailInput.value = "";
		}

		DOM.authCard.classList.remove("hidden");
	},

	showError(message) {
		DOM.errorMessage.textContent = message;
		DOM.errorAlert.classList.remove("hidden");
	},

	clearError() {
		DOM.errorAlert.classList.add("hidden");
	},

	setLoading(isLoading, originalText = "Sign In & Continue") {
		const btn = DOM.submitBtn();
		if (isLoading) {
			btn.disabled = true;
			btn.innerHTML = `<i class="bx bx-loader-alt bx-spin mr-2"></i> Signing In...`;
		} else {
			btn.disabled = false;
			btn.innerHTML = originalText;
		}
	},
};

// 4. Core Business Actions
const Actions = {
	selectAccount(username, fullName, avatar) {
		DOM.hiddenEmailInput.value = username;
		DOM.selectedAccountAvatar.src = avatar;
		DOM.selectedAccountName.textContent = "Welcome, " + fullName;
		DOM.selectedAccountEmail.querySelector("span").textContent = username;
		UIManager.showState("password");
	},

	removeAccount(index) {
		State.removeAccountAtIndex(index);
		UIManager.renderAccounts(
			(username, fullName, avatar) => this.selectAccount(username, fullName, avatar),
			(idx) => this.removeAccount(idx)
		);

		if (State.accounts.length === 0) {
			UIManager.showState("standard");
		}
	},

	submitLogin(e) {
		e.preventDefault();

		if (State.currentState === "standard") {
			DOM.hiddenEmailInput.value = DOM.emailVisibleInput.value;
		}

		UIManager.clearError();
		const submitBtn = DOM.submitBtn();
		const originalBtnText = submitBtn.innerHTML;
		UIManager.setLoading(true);

		const formData = new FormData(DOM.loginForm);

		fetch(DOM.loginForm.action, {
			method: "POST",
			body: formData,
			headers: {
				"X-Requested-With": "XMLHttpRequest",
			},
		})
			.then((response) => {
				if (!response.ok) throw new Error("Network error during fetch");
				return response.json();
			})
			.then((data) => {
				if (data.status === "success") {
					State.addAccount(data.user);
					window.location.href = data.redirect;
				} else {
					UIManager.showError(data.message);
					UIManager.setLoading(false, originalBtnText);
				}
			})
			.catch((error) => {
				console.error("Error during login:", error);
				UIManager.showError("Something went wrong. Please try again.");
				UIManager.setLoading(false, originalBtnText);
			});
	},
};

// 5. Global Bindings (For template inline onClick calls compatibility)
window.selectAccount = (username, fullName, avatar) => Actions.selectAccount(username, fullName, avatar);
window.removeAccount = (event, index) => {
	event.stopPropagation();
	Actions.removeAccount(index);
};

// 6. Event Binders & Initialization
document.addEventListener("DOMContentLoaded", () => {
	DOM.btnUseAnother.addEventListener("click", () => UIManager.showState("standard"));
	DOM.btnBackToChooser.addEventListener("click", () => UIManager.showState("chooser"));
	DOM.loginForm.addEventListener("submit", (e) => Actions.submitLogin(e));

	// Initial transition
	if (State.accounts.length > 0) {
		UIManager.showState("chooser");
	} else {
		UIManager.showState("standard");
	}
});
