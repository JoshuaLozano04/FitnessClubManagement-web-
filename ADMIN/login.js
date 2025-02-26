document.getElementById("togglePassword").addEventListener("click", function () {
    let passwordField = document.getElementById("password");
    let icon = this;
    
    if (passwordField.type === "password") {
        passwordField.type = "text";
        icon.classList.remove("ri-eye-off-fill");
        icon.classList.add("ri-eye-fill"); // Open eye when visible
    } else {
        passwordField.type = "password";
        icon.classList.remove("ri-eye-fill");
        icon.classList.add("ri-eye-off-fill"); // Closed eye when hidden
    }
});
