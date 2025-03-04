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

    openModalBtn.addEventListener("click", function () {
        modal.style.display = "flex";
        document.body.classList.add("modal-active");
    });

    closeModalBtn.addEventListener("click", function () {
        modal.style.display = "none";
        document.body.classList.remove("modal-active");
    });

    modal.style.display = "none";
});

// Function to show the modal
function openModal() {
    document.getElementById("addAdminModal").style.display = "flex";
    document.body.classList.add("modal-active");
}

// Function to hide the modal
function closeModal() {
    document.getElementById("addAdminModal").style.display = "none";
    document.body.classList.remove("modal-active"); 
}

// Hide modal when the page loads
window.onload = function() {
    document.getElementById("addAdminModal").style.display = "none";
};

// Attach event listeners to the buttons
document.getElementById("openModalBtn").addEventListener("click", openModal);
document.querySelector(".close-btn").addEventListener("click", closeModal);