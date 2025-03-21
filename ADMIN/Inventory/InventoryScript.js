function filterProducts() {
    let input = document.getElementById("search").value.toLowerCase();
    let table = document.querySelector("table tbody");

    if (!table) return;

    let rows = table.getElementsByTagName("tr");

    for (let i = 0; i < rows.length; i++) {
        let productNameCell = rows[i].getElementsByTagName("td")[1];
        
        if (productNameCell) {
            let productName = productNameCell.textContent.toLowerCase();
            
            if (productName.includes(input)) {
                rows[i].style.display = "";
            } else {
                rows[i].style.display = "none";
            }
        } else {
            rows[i].style.display = "none";
        }
    }
}

// Attach event listener for live filtering on inventory page
document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("search").addEventListener("keyup", filterProducts);
});
