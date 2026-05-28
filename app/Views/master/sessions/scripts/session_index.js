/**
 * Sessions Management Module
 * Designed with SOLID, KISS, DRY, and SoC principles.
 */

// 1. State Management
const State = {
	table: null,
	counters: {
		active: 0,
		expired: 0,
		revoked: 0,
	},

	updateCounters(data) {
		this.counters.active = 0;
		this.counters.expired = 0;
		this.counters.revoked = 0;

		data.forEach((row) => {
			if (row.status.includes("Active")) this.counters.active++;
			else if (row.status.includes("Expired")) this.counters.expired++;
			else if (row.status.includes("Revoked")) this.counters.revoked++;
		});
	},
};

// 2. DOM Elements / Selectors
const DOM = {
	tableEl: $("#sessions-table"),
	countActive: document.getElementById("count-active"),
	countExpired: document.getElementById("count-expired"),
	countRevoked: document.getElementById("count-revoked"),
	btnRefresh: document.getElementById("btn-refresh"),
};

// 3. UI/DOM Manipulators
const UIManager = {
	renderCounters() {
		if (DOM.countActive) DOM.countActive.textContent = State.counters.active;
		if (DOM.countExpired) DOM.countExpired.textContent = State.counters.expired;
		if (DOM.countRevoked) DOM.countRevoked.textContent = State.counters.revoked;
	},

	reloadTable(resetPaging = false) {
		if (State.table) {
			State.table.ajax.reload(null, resetPaging);
		}
	},
};

// 4. Core Business Actions
const Actions = {
	initializeTable() {
		State.table = DOM.tableEl.DataTable({
			processing: true,
			serverSide: true,
			ajax: {
				url: baseUrl("/oauth/sessions/datatable"),
				type: "GET",
				dataSrc: function (json) {
					State.updateCounters(json.data);
					UIManager.renderCounters();
					return json.data;
				},
			},
			columns: [
				{ data: "full_name" },
				{ data: "username" },
				{ data: "client_name" },
				{ data: "scopes" },
				{ data: "status" },
				{ data: "created_at" },
				{ data: "expires_at" },
				{
					data: "action",
					orderable: false,
					className: "text-right",
				},
			],
			order: [[5, "desc"]],
			pageLength: 25,
		});
	},

	revokeToken(id) {
		if (!confirm("Apakah Anda yakin ingin merevoke token ini? Pengguna akan dipaksa login ulang.")) return;

		fetch(baseUrl("/oauth/sessions/revoke/" + id), {
			method: "POST",
			headers: {
				"X-Requested-With": "XMLHttpRequest",
			},
		})
			.then((r) => r.json())
			.then((data) => {
				if (data.success) {
					UIManager.reloadTable(false);
				} else {
					alert("Gagal: " + data.message);
				}
			})
			.catch((err) => {
				console.error("Error revoking token:", err);
				alert("Terjadi kesalahan saat merevoke token.");
			});
	},
};

// 5. Event Binders & Initializer
document.addEventListener("DOMContentLoaded", function () {
	Actions.initializeTable();

	// Refresh Button Event
	if (DOM.btnRefresh) {
		DOM.btnRefresh.addEventListener("click", () => UIManager.reloadTable(true));
	}

	// Revoke Action Delegation (DataTable)
	DOM.tableEl.on("click", ".revoke-btn", function () {
		const id = this.dataset.id;
		Actions.revokeToken(id);
	});
});
