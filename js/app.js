if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    document.documentElement.classList.add('dark');
} else {
    document.documentElement.classList.remove('dark');
}


document.addEventListener('DOMContentLoaded', () => {
    const themeToggleBtn = document.getElementById('theme-toggle');

    themeToggleBtn.addEventListener('click', function () {
        const html = document.querySelector('html');

        let isDark = document.documentElement.classList.contains('dark');

        isDark ? html.classList.remove('dark') : html.classList.add('dark');
        localStorage.theme = isDark ? 'light' : 'dark';
    });
});