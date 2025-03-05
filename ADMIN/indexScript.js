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
