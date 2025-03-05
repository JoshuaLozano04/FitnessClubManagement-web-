function filterMembers() {
    let input = document.querySelector('.search-input').value.toLowerCase();
    let statusFilter = document.querySelector('.filter-input').value.toLowerCase();
    let table = document.querySelector("table tbody");
    let rows = table.getElementsByTagName("tr");

    for (let i = 0; i < rows.length; i++) {
        let name = rows[i].getElementsByTagName("td")[1].textContent.toLowerCase(); // Name column
        let status = rows[i].getElementsByTagName("td")[5].textContent.toLowerCase(); // Status column

        if (name.includes(input) && (statusFilter === "" || status === statusFilter)) {
            rows[i].style.display = "";
        } else {
            rows[i].style.display = "none";
        }
    }
}

// Attach event listeners for live filtering
document.querySelector('.search-input').addEventListener('keyup', filterMembers);
document.querySelector('.filter-input').addEventListener('change', filterMembers);