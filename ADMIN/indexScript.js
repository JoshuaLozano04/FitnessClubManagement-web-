//DROPDOWN MENU ON CLICK
const showDropdown = (dropdownId) => {
    const dropdown = document.getElementById(dropdownId);

    dropdown.addEventListener('click', () =>{
        dropdown.classList.toggle('show-dropdown');
    })
}
showDropdown('dropdown');

//LOGOUT
function confirmLogout() {
    return confirm("Are you sure you want to log out?");
}

// CHANGE PASSWORD MODAL
document.addEventListener("DOMContentLoaded", function () {
    let modal = document.getElementById("changePasswordModal");
    let openModalBtn = document.getElementById("changePasswordLink");
    let closeModalBtn = document.getElementById("closeChangePassword");
    let form = document.getElementById("changePasswordForm");

    // Show modal when the button is clicked
    openModalBtn.addEventListener("click", function (event) {
        event.preventDefault(); 
        modal.style.display = "flex"; 
        document.body.classList.add("modal-active");
    });

    // Function to close the modal and reset the form
    function closeChangePasswordModal() {
        modal.style.display = "none"; 
        document.body.classList.remove("modal-active");
        form.reset(); 
    }

    // Hide modal when close button is clicked
    closeModalBtn.addEventListener("click", closeChangePasswordModal);

    // Hide modal when clicking outside the modal content
    window.addEventListener("click", function (event) {
        if (event.target === modal) {
            closeChangePasswordModal();
        }
    });

    // Ensure modal is hidden when the page loads
    modal.style.display = "none";

    // Toggle Password visibility
    document.querySelectorAll(".change-password-toggle").forEach(icon => {
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
