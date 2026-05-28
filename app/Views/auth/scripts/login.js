document.addEventListener("DOMContentLoaded", function() {
    // Check theme setup compatibility
    if (typeof updateThemeIcons === 'function') {
        updateThemeIcons();
    }

    const loginForm = document.getElementById("login-form");
    if (!loginForm) return;

    loginForm.addEventListener("submit", function(e) {
        e.preventDefault();

        const errorAlert = document.getElementById("error-alert");
        const errorMessage = document.getElementById("error-message");
        if (errorAlert) errorAlert.classList.add("hidden");

        const submitBtn = loginForm.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;

        // Visual loading feedback
        submitBtn.disabled = true;
        submitBtn.innerHTML = `<i class="bx bx-loader-alt bx-spin mr-2"></i> Signing In...`;

        const formData = new FormData(loginForm);

        fetch(loginForm.action, {
            method: "POST",
            body: formData,
            headers: {
                "X-Requested-With": "XMLHttpRequest"
            }
        })
        .then(response => {
            if (!response.ok) throw new Error("Network error during fetch");
            return response.json();
        })
        .then(data => {
            if (data.status === "success") {
                // Success! Redirect to correct dashboard
                window.location.href = data.redirect;
            } else {
                // Error! Show message and reset button
                if (errorMessage) errorMessage.textContent = data.message;
                if (errorAlert) errorAlert.classList.remove("hidden");
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        })
        .catch(error => {
            console.error("Error during login:", error);
            if (errorMessage) errorMessage.textContent = "Something went wrong. Please try again.";
            if (errorAlert) errorAlert.classList.remove("hidden");
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        });
    });
});
