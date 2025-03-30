// Live Search Filter
function filterUsers() {
    let input = document.getElementById("search").value.toLowerCase();
    let adminCards = document.querySelectorAll(".admin-info");

    adminCards.forEach(card => {
        let name = card.querySelector(".admin-details p:nth-child(1)").innerText.toLowerCase();
        let email = card.querySelector(".admin-details p:nth-child(2)").innerText.toLowerCase();
        card.style.display = (name.includes(input) || email.includes(input)) ? "flex" : "none";
    });
}

// Ensure the DOM is fully loaded
document.addEventListener("DOMContentLoaded", function () {
    let modal = document.getElementById("addAdminModal");
    let openModalBtn = document.getElementById("openModalBtn");
    let closeModalBtn = document.querySelector(".close-btn");

    // Open Modal
    window.openModal = function () {
        modal.style.display = "flex";
        document.body.classList.add("modal-active");
        resetForm();
    };

    // Close Modal
    window.closeModal = function () {
        modal.style.display = "none";
        document.body.classList.remove("modal-active");
        resetForm();
    };

    // Reset Form Function
    function resetForm() {
        let form = document.getElementById("addAdminForm");
        if (form) form.reset();
    }

    // Ensure modal is hidden initially
    modal.style.display = "none";

    // Attach event listeners
    if (openModalBtn) openModalBtn.addEventListener("click", openModal);
    if (closeModalBtn) closeModalBtn.addEventListener("click", closeModal);

    // Close modal when navigating
    document.querySelectorAll(".nav-link").forEach(link => {
        link.addEventListener("click", closeModal);
    });

    // Toggle Password Visibility
    document.querySelectorAll(".toggle-password").forEach(icon => {
        icon.addEventListener("click", function () {
            let targetId = this.getAttribute("data-target");
            let passwordField = document.getElementById(targetId);
                
            if (passwordField.type === "password") {
                passwordField.type = "text";
                this.classList.replace("ri-eye-off-line", "ri-eye-line");
            } else {
                passwordField.type = "password";
                this.classList.replace("ri-eye-line", "ri-eye-off-line");
            }
        });
    });
});

// Add User Form Submission
function submitAdmin() {
    let form = document.getElementById("addAdminForm");
    let formData = new FormData(form);
    let responseMessage = document.createElement("p");
    responseMessage.style.marginTop = "10px";

    fetch("Users/addUser.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        responseMessage.innerHTML = data.includes("successfully") 
            ? `<span style="color: green;">${data}</span>` 
            : `<span style="color: red;">${data}</span>`;

        form.appendChild(responseMessage);

        if (data.includes("successfully")) {
            setTimeout(() => {
                location.reload();
            }, 1000);
        }
    })
    .catch(error => console.error("Error:", error));
}
