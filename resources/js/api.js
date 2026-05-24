import $ from "jquery";

export const baseUrl = (path = "") => {
  const metaBaseUrl = document.querySelector('meta[name="base-url"]');
  const base = metaBaseUrl ? metaBaseUrl.getAttribute("content") : "";
  path = path.replace(/^\//, ""); // Remove leading slash
  return base + (path ? "/" + path : "");
};

const resolveUrl = (url) => {
  if (
    url.startsWith("http://") ||
    url.startsWith("https://") ||
    url.startsWith("//")
  ) {
    return url;
  }
  return baseUrl(url);
};

const getCsrfData = () => {
  const metaToken = document.querySelector('meta[name="csrf-token"]');
  const metaHash = document.querySelector('meta[name="csrf-hash"]');

  return {
    tokenName: metaToken ? metaToken.getAttribute("content") : "csrf_test_name",
    tokenHash: metaHash ? metaHash.getAttribute("content") : "",
  };
};

export const api = {
  baseUrl: baseUrl,
  get: function (url, data = {}, options = {}) {
    let payload = typeof data === "function" ? data() : data;

    // Convert HTML or jQuery form to serialized string for GET
    if (payload instanceof HTMLFormElement) {
      payload = $(payload).serialize();
    } else if (payload && payload.jquery) {
      payload = payload.serialize();
    }

    return $.ajax({
      url: resolveUrl(url),
      type: "GET",
      data: payload,
      ...options,
    });
  },
  post: function (url, data = {}, options = {}) {
    const csrf = getCsrfData();
    let payload = typeof data === "function" ? data() : data;

    // Convert HTML or jQuery form to FormData
    if (payload instanceof HTMLFormElement) {
      payload = new FormData(payload);
    } else if (
      payload &&
      payload.jquery &&
      payload.length > 0 &&
      payload[0] instanceof HTMLFormElement
    ) {
      payload = new FormData(payload[0]);
    }

    // Attach CSRF dynamically based on data type
    if (typeof payload === "string") {
      payload +=
        (payload.length > 0 ? "&" : "") + `${csrf.tokenName}=${csrf.tokenHash}`;
    } else if (payload instanceof FormData) {
      payload.append(csrf.tokenName, csrf.tokenHash);
      options.processData = false;
      options.contentType = false;
    } else {
      payload = payload || {};
      payload[csrf.tokenName] = csrf.tokenHash;
    }

    return $.ajax({
      url: resolveUrl(url),
      type: "POST",
      data: payload,
      ...options,
    });
  },
  put: function (url, data = {}, options = {}) {
    const csrf = getCsrfData();
    let payload = typeof data === "function" ? data() : data;

    if (payload instanceof HTMLFormElement) {
      payload = new FormData(payload);
    } else if (
      payload &&
      payload.jquery &&
      payload.length > 0 &&
      payload[0] instanceof HTMLFormElement
    ) {
      payload = new FormData(payload[0]);
    }

    if (typeof payload === "string") {
      payload +=
        (payload.length > 0 ? "&" : "") + `${csrf.tokenName}=${csrf.tokenHash}`;
    } else if (payload instanceof FormData) {
      payload.append(csrf.tokenName, csrf.tokenHash);
      options.processData = false;
      options.contentType = false;
    } else {
      payload = payload || {};
      payload[csrf.tokenName] = csrf.tokenHash;
    }

    return $.ajax({
      url: resolveUrl(url),
      type: "PUT",
      data: payload,
      ...options,
    });
  },
  delete: function (url, data = {}, options = {}) {
    const csrf = getCsrfData();
    let payload = typeof data === "function" ? data() : data;

    if (payload instanceof HTMLFormElement) {
      payload = new FormData(payload);
    } else if (
      payload &&
      payload.jquery &&
      payload.length > 0 &&
      payload[0] instanceof HTMLFormElement
    ) {
      payload = new FormData(payload[0]);
    }

    if (typeof payload === "string") {
      payload +=
        (payload.length > 0 ? "&" : "") + `${csrf.tokenName}=${csrf.tokenHash}`;
    } else if (payload instanceof FormData) {
      payload.append(csrf.tokenName, csrf.tokenHash);
      options.processData = false;
      options.contentType = false;
    } else {
      payload = payload || {};
      payload[csrf.tokenName] = csrf.tokenHash;
    }

    return $.ajax({
      url: resolveUrl(url),
      type: "POST", // CI4 commonly simulates DELETE via POST or expects POST for destructive actions if not strictly RESTful
      data: payload,
      ...options,
    });
  },
  upload: function (url, data = {}, options = {}) {
    const csrf = getCsrfData();
    let payload = typeof data === "function" ? data() : data;

    let formData;
    if (payload instanceof FormData) {
      formData = payload;
    } else if (payload instanceof HTMLFormElement) {
      formData = new FormData(payload);
    } else if (
      payload &&
      payload.jquery &&
      payload.length > 0 &&
      payload[0] instanceof HTMLFormElement
    ) {
      formData = new FormData(payload[0]);
    } else {
      formData = new FormData();
      if (typeof payload === "object" && payload !== null) {
        for (let key in payload) {
          if (payload.hasOwnProperty(key)) {
            formData.append(key, payload[key]);
          }
        }
      }
    }

    // Attach CSRF
    formData.append(csrf.tokenName, csrf.tokenHash);

    return $.ajax({
      url: resolveUrl(url),
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      ...options,
    });
  },
  getCsrfData: getCsrfData,
};

// Setup jQuery global AJAX to handle potential CI4 CSRF regeneration in responses
$(document).ajaxSuccess(function (event, xhr, settings, response) {
  if (response && response.csrf_hash) {
    const metaHash = document.querySelector('meta[name="csrf-hash"]');
    if (metaHash) {
      metaHash.setAttribute("content", response.csrf_hash);
    }
  }
});

// Expose globally
window.api = api;
window.baseUrl = baseUrl;

