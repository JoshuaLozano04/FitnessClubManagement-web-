const registerButton = document.getElementById("register");
const loginButton = document.getElementById("login");
const container = document.getElementById("container");
const backButton = document.querySelector(".back");

/*Double-Sided Gif*/
registerButton.addEventListener("click", () =>{
    container.classList.add("right-panel-active");
});

loginButton.addEventListener("click", () =>{
    container.classList.remove("right-panel-active");
});

document.querySelectorAll(".togglePassword").forEach((icon) => {
    icon.addEventListener("click", function () {
        const passwordInput = document.getElementById(this.dataset.target);
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            this.classList.replace("ri-eye-off-fill", "ri-eye-fill");
        } else {
            passwordInput.type = "password";
            this.classList.replace("ri-eye-fill", "ri-eye-off-fill");
        }
    });
});

