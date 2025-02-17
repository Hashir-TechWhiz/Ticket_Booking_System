document.addEventListener("DOMContentLoaded", function () {
    const loginButton = document.getElementById("loginButton");
    const loginPopup = document.getElementById("loginPopup");

    // Toggle popup visibility when button is clicked
    loginButton.addEventListener("click", function (event) {
        event.stopPropagation();
        loginPopup.classList.toggle("hidden");
    });

    // Close popup when clicking outside
    document.addEventListener("click", function (event) {
        if (!loginPopup.contains(event.target) && event.target !== loginButton) {
            loginPopup.classList.add("hidden");
        }
    });
});
