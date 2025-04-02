// Order Form Modal
function openModal() {
    document.getElementById('orderForm').style.display = 'flex';
    }
function closeModal() {
    document.getElementById('orderForm').style.display = 'none';
    resetForm(); // Clear form data when closing
}

function resetForm() {
    let form = document.querySelector("#orderForm form");
    form.reset(); // Resets all input fields to their default values
}


// Search Filter for Orders
document.addEventListener("DOMContentLoaded", function () {
    let searchInput = document.getElementById("orderSearch");

    if (searchInput) {
        searchInput.addEventListener("keyup", filterOrders);
    }
});

function filterOrders() {
    let input = document.getElementById("orderSearch").value.toLowerCase();
    let table = document.querySelector("table tbody");

    if (!table) {
        return;
    }

    let rows = table.getElementsByTagName("tr");

    for (let i = 0; i < rows.length; i++) {
        let customerNameCell = rows[i].getElementsByTagName("td")[0];
        let productNameCell = rows[i].getElementsByTagName("td")[2];
        let statusCell = rows[i].getElementsByTagName("td")[5];

        if (customerNameCell && productNameCell && statusCell) {
            let customerName = customerNameCell.textContent.toLowerCase();
            let productName = productNameCell.textContent.toLowerCase();
            let status = statusCell.textContent.toLowerCase();

            if (customerName.includes(input) || productName.includes(input) || status.includes(input)) {
                rows[i].style.display = "";
            } else {
                rows[i].style.display = "none";
            }
        } else {
            rows[i].style.display = "none";
        }
    }
}

// Update price when product is selected
function updatePrice() {
    let productSelect = document.getElementById("product_id");
    let selectedOption = productSelect.options[productSelect.selectedIndex];
    let priceInput = document.getElementById("price");

    if (selectedOption.value !== "") {
        priceInput.value = selectedOption.getAttribute("data-price");
    } else {
        priceInput.value = "";
    }
}