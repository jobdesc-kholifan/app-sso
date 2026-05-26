const route = {
  ajaxList: "/master/clients/datatable",
  store: "/master/clients/store",
  update: "/master/clients/update/",
  edit: "/master/clients/edit/",
  delete: "/master/clients/delete/",
};

let table;

$(document).ready(function () {
  // Initialize DataTables
  table = $("#clients-table").DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: baseUrl(route.ajaxList),
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

  // Form Submit
  $("#clientForm").on("submit", function (e) {
    e.preventDefault();

    let id = $("#clientId").val();
    let url = id ? route.update + id : route.store;

    window.api.post(url, this).then(function (response) {
      if (response.success) {
        closeModal("clientModal");
        $("#clientId").val(""); // Clear ID
        $("#clientForm")[0].reset(); // Reset form fields
        table.ajax.reload(null, false);
        vibeToast.success(response.message);
      } else {
        if (response.errors && response.errors.client_identifier) {
          vibeToast.error(response.errors.client_identifier);
        } else {
          vibeToast.error("Failed to save data.");
        }
      }
    });
  });

  // Edit Button
  $("#clients-table").on("click", ".edit-btn", function () {
    let id = $(this).data("id");
    window.api.get(route.edit + id).then(function (response) {
      if (response.success) {
        let data = response.data;
        $("#clientId").val(data.id);
        $("#client_identifier").val(data.client_identifier);
        $("#client_secret").val(""); // Leave empty for security/edit placeholder
        $("#name").val(data.name);
        $("#redirect_uri").val(data.redirect_uri);
        $("#is_confidential").val(data.is_confidential);

        $("#modalTitle").text("Edit Client");
        window.openClientModal(true);
      }
    });
  });

  // Delete Button
  $("#clients-table").on("click", ".delete-btn", function () {
    if (confirm("Are you sure you want to delete this client?")) {
      let id = $(this).data("id");

      window.api.delete(route.delete + id).then(function (response) {
        if (response.success) {
          table.ajax.reload(null, false);
          vibeToast.success(response.message, "Deleted");
        }
      });
    }
  });
});

window.openClientModal = function (isEdit = false) {
  if (!isEdit) {
    $("#clientId").val("");
    $("#clientForm")[0].reset();
    $("#modalTitle").text("Add Client");
    $("#client_secret").attr("required", true); // required for new records
  } else {
    $("#client_secret").removeAttr("required"); // optional for edits
  }

  openModal("clientModal");
};
