/* Search Icon */
const searchInput = document.getElementById("searchInput");
const searchBox = document.getElementById("searchBox");
const searchIcon = document.getElementById("searchIcon");

searchIcon.onclick = function () {
    searchBox.classList.toggle("active");
    searchInput.focus();
};

searchIcon.onmouseenter = function () {
    searchBox.classList.add("active");
    searchInput.focus();
};

searchInput.onblur = function () {
    searchBox.classList.remove("active");
};

function handleSearchIconClick() {
    searchBox.classList.toggle("active");
    searchInput.focus();
};

function handleSearchIconTouch(event) {
    event.preventDefault(); // Empêcher le comportement tactile par défaut
    handleSearchIconClick();
};

searchIcon.addEventListener("click", handleSearchIconClick);
searchIcon.addEventListener("touchstart", handleSearchIconTouch);
/* Fin search Icon */