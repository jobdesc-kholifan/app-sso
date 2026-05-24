import $ from "jquery";

// Register custom pagination renderers for Vibe UI (ul, li, a structure)
$.fn.dataTable.ext.renderer.pagingButton.vibe = function (
  settings,
  buttonType,
  content,
  active,
  disabled,
) {
  var liClasses = "page-item";
  if (active) {
    liClasses += " active";
  }
  if (disabled) {
    liClasses += " disabled";
  }

  var li = $("<li>").addClass(liClasses);

  var aClasses = "page-link";

  var a = $("<a>", {
    class: aClasses,
    href: "#",
  })
    .html(content)
    .appendTo(li);

  return { display: li, clicker: a };
};

$.fn.dataTable.ext.renderer.pagingContainer.vibe = function (
  settings,
  buttonEls,
) {
  return $("<ul/>").addClass("pagination pagination-sm").append(buttonEls);
};

// Set 'vibe' as the default renderer for all DataTables instances
$.extend(true, $.fn.dataTable.defaults, {
  renderer: "vibe",
});

// Override default classes for table, search input, length select, and other components
$.extend(true, $.fn.dataTable.ext.classes, {
  table: "table table-sm table-hover table-striped !mb-0",
  search: {
    container: "dt-search text-xs",
    input: "form-control form-control-sm !inline !w-auto !rounded-[.5rem]",
  },
  length: {
    container: "dt-length text-xs",
    select: "form-select form-select-sm !inline !w-auto",
  },
  info: {
    container: "dt-info text-xs text-slate-500 dark:text-slate-400",
  },
});
