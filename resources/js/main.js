import '../css/main.css';

import $ from 'jquery';
window.$ = window.jQuery = $;

import 'datatables.net';
import 'datatables.net-dt/css/dataTables.dataTables.min.css';
import './datatables-config.js';

import './api.js';

// Global helper for Vibe UI Toast Notification System
$(document).ready(function () {
  if (window.vibeToast) {
    window.vibeToast.success = function (message, title = "Success") {
      const options = typeof message === 'object' ? message : { message, title };
      window.vibeToast.show({
        title: options.title || "Success",
        message: options.message || "",
        type: "success",
        position: options.position || "top-right",
      });
    };

    window.vibeToast.error = function (message, title = "Error") {
      const options = typeof message === 'object' ? message : { message, title };
      window.vibeToast.show({
        title: options.title || "Error",
        message: options.message || "",
        type: "danger",
        position: options.position || "top-right",
      });
    };
  }
});