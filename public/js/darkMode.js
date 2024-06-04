const elToggleTheme = document.querySelector('#form_dark_mode');

elToggleTheme.checked = localStorage.theme === "dark";

elToggleTheme.addEventListener("change", () => {
    const theme = elToggleTheme.checked ? "dark" : "light";
    setTheme(theme);
});