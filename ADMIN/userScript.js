// Live Searching Filter Section
function filterUsers() {
    let input = document.getElementById("search").value.toLowerCase();
    let adminCards = document.querySelectorAll(".admin-info");

    adminCards.forEach(card => {
        let name = card.querySelector(".admin-details p:nth-child(1)").innerText.toLowerCase();
        let email = card.querySelector(".admin-details p:nth-child(2)").innerText.toLowerCase();
        if (name.includes(input) || email.includes(input)) {
            card.style.display = "flex";
        } else {
            card.style.display = "none";
        }
    });
}

// Add User Section
document.addEventListener("DOMContentLoaded", function () {
    let modal = document.getElementById("addAdminModal");
    let openModalBtn = document.getElementById("openModalBtn");
    let closeModalBtn = document.querySelector(".close-btn");

    if (openModalBtn) {
        openModalBtn.addEventListener("click", function () {
            modal.style.display = "flex";
            document.body.classList.add("modal-active");
        });
    }

    if (closeModalBtn) {
        closeModalBtn.addEventListener("click", function () {
            modal.style.display = "none";
            document.body.classList.remove("modal-active");
            resetForm();
        });
    }

    modal.style.display = "none";

    // Function to show the modal
    window.openModal = function () {
        modal.style.display = "flex";
        document.body.classList.add("modal-active");
    };

    // Function to hide the modal
    window.closeModal = function () {
        modal.style.display = "none";
        document.body.classList.remove("modal-active");
        resetForm(); 
    };
    
    // Function to reset the form
    function resetForm() {
        let form = document.getElementById("addAdminForm");
        if (form) {
            form.reset();
        }
    }
    // Attach event listeners
    if (openModalBtn) openModalBtn.addEventListener("click", openModal);
    if (closeModalBtn) closeModalBtn.addEventListener("click", closeModal); 

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