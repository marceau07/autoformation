/* Search Icon */
var searchInput = document.getElementById("searchInput");
var searchBox = document.getElementById("searchBox");
var searchIcon = document.getElementById("searchIcon");
var containerSearchResultsh = document.getElementById("containerSearchResults");

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

function handleLeavingElement() {
    containerSearchResultsh.classList.add("d-none", "fade", "show");
}

document.addEventListener("click", handleLeavingElement);
searchIcon.addEventListener("click", handleSearchIconClick);
searchIcon.addEventListener("onmouseenter", handleSearchIconClick);
searchIcon.addEventListener("touchstart", handleSearchIconTouch);
/* Fin search Icon */