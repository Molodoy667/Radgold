// Перемикання теми та градієнтів
const THEME_KEY = 'site_theme';
const GRADIENT_KEY = 'site_gradient';

document.addEventListener('DOMContentLoaded', function() {
    // Встановити тему при завантаженні
    setTheme(getTheme());
    setGradient(getGradient());

    // Перемикач теми
    document.querySelectorAll('.theme-toggle').forEach(btn => {
        btn.addEventListener('click', function() {
            const newTheme = getTheme() === 'dark' ? 'light' : 'dark';
            setTheme(newTheme);
        });
    });

    // Вибір градієнта
    document.querySelectorAll('.gradient-select').forEach(btn => {
        btn.addEventListener('click', function() {
            setGradient(this.dataset.gradient);
        });
    });
});

function setTheme(theme) {
    document.body.setAttribute('data-theme', theme);
    localStorage.setItem(THEME_KEY, theme);
}
function getTheme() {
    return localStorage.getItem(THEME_KEY) || 'light';
}
function setGradient(gradient) {
    document.body.classList.remove(...Array.from(document.body.classList).filter(c => c.startsWith('gradient-')));
    if (gradient) {
        document.body.classList.add(gradient);
        localStorage.setItem(GRADIENT_KEY, gradient);
    }
}
function getGradient() {
    return localStorage.getItem(GRADIENT_KEY) || '';
}