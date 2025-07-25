function toggleTheme() {
    var body = document.body;
    body.classList.toggle('dark');
    if (body.classList.contains('dark')) {
        localStorage.setItem('theme', 'dark');
    } else {
        localStorage.setItem('theme', 'light');
    }
}
window.onload = function() {
    var theme = localStorage.getItem('theme');
    if (theme === 'dark') document.body.classList.add('dark');
};