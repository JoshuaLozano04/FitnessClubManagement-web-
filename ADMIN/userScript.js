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
