function filterProducts() {
    let input = document.getElementById("search").value.toLowerCase();
    let table = document.querySelector("table tbody");
    let rows = table.getElementsByTagName("tr");

    for (let i = 0; i < rows.length; i++) {
        let productName = rows[i].getElementsByTagName("td")[0]?.textContent.toLowerCase(); // Product Name column

        if (productName.includes(input)) {
            rows[i].style.display = "";
        } else {
            rows[i].style.display = "none";
        }
    }
}

// Attach event listener for live filtering on inventory page
document.getElementById("search").addEventListener("keyup", filterProducts);
