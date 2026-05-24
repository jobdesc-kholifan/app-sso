const route = {
  ajaxList: "/master/users/datatable",
  store: "/master/users/store",
  update: "/master/users/update/",
  edit: "/master/users/edit/",
  delete: "/master/users/delete/",
};

let table;

$(document).ready(function () {
  // Initialize DataTables
  table = $("#users-table").DataTable({
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
      { data: "full_name", name: "full_name" },
      { data: "username", name: "username" },
      { data: "role", name: "role" },
      { data: "status", name: "status", orderable: false, searchable: false },
      { data: "last_login", name: "last_login" },
      {
        data: "action",
        name: "action",
        orderable: false,
        searchable: false,
        className: "text-right",
      },
    ],
    // Tailwind classes integration can be customized via DataTables options or using datatables.net-tailwindcss
    dom: '<"flex flex-col sm:flex-row justify-between items-center mb-4"<"w-full sm:w-auto"l><"w-full sm:w-auto mt-4 sm:mt-0"f>><"overflow-hidden border rounded-md"rt><"flex flex-col sm:flex-row justify-between items-center mt-4"<"w-full sm:w-auto"i><"w-full sm:w-auto mt-4 sm:mt-0"p>>',
  });

  // Form Submit
  $("#userForm").on("submit", function (e) {
    e.preventDefault();

    let id = $("#userId").val();
    let url = id ? route.update + id : route.store;

    window.api.post(url, this).then(function (response) {
      if (response.success) {
        closeModal("userModal");
        $("#userId").val(""); // Clear ID
        $("#userForm")[0].reset(); // Reset form fields
        table.ajax.reload(null, false);
        vibeToast.success(response.message);
      } else {
        vibeToast.error("Failed to save data.");
      }
    });
  });

  // Edit Button
  $("#users-table").on("click", ".edit-btn", function () {
    let id = $(this).data("id");
    window.api.get(route.edit + id).then(function (response) {
      if (response.success) {
        let data = response.data;
        $("#userId").val(data.id);
        $("#full_name").val(data.full_name);
        $("#username").val(data.username);
        $("#role").val(data.role);
        $("#status").val(data.status);
        $("#user_password").val("");

        $("#modalTitle").text("Edit User");
        window.openUserModal(true);
      }
    });
  });

  // Delete Button
  $("#users-table").on("click", ".delete-btn", function () {
    if (confirm("Are you sure you want to delete this user?")) {
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

window.openUserModal = function (isEdit = false) {
  if (!isEdit) {
    $("#userId").val("");
    $("#userForm")[0].reset();
    $("#modalTitle").text("Add User");
  }

  openModal("userModal");
};
