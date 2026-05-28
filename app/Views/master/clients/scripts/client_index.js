/**
 * Client Management Module
 * Designed with SOLID, KISS, DRY, and SoC principles.
 */

// 1. Routes & Config
const Routes = {
	ajaxList: "/master/clients/datatable",
	store: "/master/clients/store",
	update: "/master/clients/update/",
	edit: "/master/clients/edit/",
	delete: "/master/clients/delete/",
};

// 2. State Management
const State = {
	table: null,
};

// 3. DOM Elements / Selectors
const DOM = {
	tableEl: $("#clients-table"),
	formEl: $("#clientForm"),
	clientIdInput: $("#clientId"),
	clientIdentifierInput: $("#client_identifier"),
	clientSecretInput: $("#client_secret"),
	clientNameInput: $("#name"),
	redirectUriInput: $("#redirect_uri"),
	isConfidentialInput: $("#is_confidential"),
	modalTitle: $("#modalTitle"),
};

// 4. UI/DOM Manipulators
const UIManager = {
	resetForm() {
		DOM.clientIdInput.val("");
		if (DOM.formEl.length) DOM.formEl[0].reset();
	},

	fillForm(data) {
		DOM.clientIdInput.val(data.id);
		DOM.clientIdentifierInput.val(data.client_identifier);
		DOM.clientSecretInput.val(""); // Leave empty for security/edit placeholder
		DOM.clientNameInput.val(data.name);
		DOM.redirectUriInput.val(data.redirect_uri);
		DOM.isConfidentialInput.val(data.is_confidential);
	},

	configureModal(isEdit = false) {
		if (!isEdit) {
			this.resetForm();
			DOM.modalTitle.text("Add Client");
			DOM.clientSecretInput.attr("required", true); // required for new records
		} else {
			DOM.modalTitle.text("Edit Client");
			DOM.clientSecretInput.removeAttr("required"); // optional for edits
		}
		openModal("clientModal");
	},

	reloadTable() {
		if (State.table) {
			State.table.ajax.reload(null, false);
		}
	},
};

// 5. Core Business Actions
const Actions = {
	initializeTable() {
		State.table = DOM.tableEl.DataTable({
			processing: true,
			serverSide: true,
			ajax: {
				url: baseUrl(Routes.ajaxList),
				type: "POST",
				data: function (d) {
					let csrf = window.api.getCsrfData();
					d[csrf.tokenName] = csrf.tokenHash; // CSRF protection
				},
			},
			columns: [
				{ data: "id", name: "id" },
				{ data: "client_identifier", name: "client_identifier" },
				{ data: "name", name: "name" },
				{ data: "redirect_uri", name: "redirect_uri" },
				{ data: "is_confidential", name: "is_confidential", orderable: false, searchable: false },
				{
					data: "action",
					name: "action",
					orderable: false,
					searchable: false,
					className: "text-right",
				},
			],
			dom: '<"flex flex-col sm:flex-row justify-between items-center mb-4"<"w-full sm:w-auto"l><"w-full sm:w-auto mt-4 sm:mt-0"f>><"overflow-hidden border rounded-md"rt><"flex flex-col sm:flex-row justify-between items-center mt-4"<"w-full sm:w-auto"i><"w-full sm:w-auto mt-4 sm:mt-0"p>>',
		});
	},

	submitForm(e, formElement) {
		e.preventDefault();

		let id = DOM.clientIdInput.val();
		let url = id ? Routes.update + id : Routes.store;

		window.api.post(url, formElement).then(function (response) {
			if (response.success) {
				closeModal("clientModal");
				UIManager.resetForm();
				UIManager.reloadTable();
				vibeToast.success(response.message);
			} else {
				if (response.errors && response.errors.client_identifier) {
					vibeToast.error(response.errors.client_identifier);
				} else {
					vibeToast.error("Failed to save data.");
				}
			}
		});
	},

	editClient(id) {
		window.api.get(Routes.edit + id).then(function (response) {
			if (response.success) {
				UIManager.fillForm(response.data);
				UIManager.configureModal(true);
			}
		});
	},

	deleteClient(id) {
		if (!confirm("Are you sure you want to delete this client?")) return;

		window.api.delete(Routes.delete + id).then(function (response) {
			if (response.success) {
				UIManager.reloadTable();
				vibeToast.success(response.message, "Deleted");
			}
		});
	},
};

// 6. Global Bindings (For trigger components / legacy layout integrations)
window.openClientModal = (isEdit = false) => UIManager.configureModal(isEdit);

// 7. Event Binders & Initializer
$(function () {
	Actions.initializeTable();

	DOM.formEl.on("submit", function (e) {
		Actions.submitForm(e, this);
	});

	DOM.tableEl.on("click", ".edit-btn", function () {
		let id = $(this).data("id");
		Actions.editClient(id);
	});

	DOM.tableEl.on("click", ".delete-btn", function () {
		let id = $(this).data("id");
		Actions.deleteClient(id);
	});
});
