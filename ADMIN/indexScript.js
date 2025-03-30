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

    let messageBox = document.createElement("div");
    messageBox.id = "changePasswordMessage";
    form.appendChild(messageBox);

    console.log("JavaScript Loaded");

    // Open modal
    if (openModalBtn) {
        openModalBtn.addEventListener("click", function (event) {
            event.preventDefault();
            modal.style.display = "flex";
            document.body.classList.add("modal-active");
        });
    }

    // Close modal
    function closeChangePasswordModal() {
        modal.style.display = "none";
        document.body.classList.remove("modal-active");
        form.reset();
        messageBox.textContent = "";
    }

    if (closeModalBtn) {
        closeModalBtn.addEventListener("click", closeChangePasswordModal);
    }

    // Close modal when clicking outside
    window.addEventListener("click", function (event) {
        if (event.target === modal) {
            closeChangePasswordModal();
        }
    });

    // Toggle Password Visibility
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

    // Handle form submission via AJAX
    form.addEventListener("submit", function (event) {
        event.preventDefault();

        let formData = new FormData(form);

        fetch("change_password.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            messageBox.textContent = data.message;
            messageBox.style.color = data.success ? "green" : "red";

            if (data.success) {
                setTimeout(closeChangePasswordModal, 2000);
            }
        })
        .catch(error => {
            console.error("Error:", error);
        });
    });
});

// NOTIFICATIONS
function toggleNotifications() {
    const notificationsWindow = document.getElementById('notificationsWindow');
    notificationsWindow.style.display = notificationsWindow.style.display === 'none' ? 'block' : 'none';

    if (notificationsWindow.style.display === 'block') {
        // Send an AJAX request to mark notifications as read
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'mark_notifications_read.php', true);
        xhr.onload = function() {
            if (xhr.status === 200 && xhr.responseText === 'success') {
                // Hide the notification count
                const notificationCount = document.querySelector('.notification-count');
                if (notificationCount) {
                    notificationCount.style.display = 'none';
                }
            }
        };
        xhr.send();

        // Add event listener to close notifications window when clicking outside
        document.addEventListener('click', closeNotificationsOnClickOutside);
    } else {
        // Remove event listener when notifications window is closed
        document.removeEventListener('click', closeNotificationsOnClickOutside);
    }
}

function closeNotificationsOnClickOutside(event) {
    const notificationsWindow = document.getElementById('notificationsWindow');
    const notificationBtn = document.querySelector('.notification-btn');
    if (!notificationsWindow.contains(event.target) && !notificationBtn.contains(event.target)) {
        notificationsWindow.style.display = 'none';
        document.removeEventListener('click', closeNotificationsOnClickOutside);
    }
}

function deleteNotification(notificationId) {
    // Send an AJAX request to delete the notification
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'delete_notification.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200 && xhr.responseText === 'success') {
            // Remove the notification from the list
            const notificationElement = document.querySelector(`button[onclick="deleteNotification(${notificationId})"]`).parentElement;
            notificationElement.remove();
        }
    };
    xhr.send(`id=${notificationId}`);
}