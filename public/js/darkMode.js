var elToggleTheme = document.querySelector('#form_dark_mode');

elToggleTheme.checked = localStorage.theme === "dark";

elToggleTheme.addEventListener("click", () => {
    var theme = elToggleTheme.checked ? "dark" : "light";
    setTheme(theme);
});