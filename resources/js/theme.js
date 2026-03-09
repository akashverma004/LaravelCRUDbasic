// Theme switcher logic - executes immediately
function initializeTheme() {
    const html = document.documentElement;

    // Get stored theme or use system preference
    const storedTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const initialTheme = storedTheme || (prefersDark ? 'dark' : 'light');

    // Set initial theme
    setTheme(initialTheme);

    // Setup toggle button - use delegation to handle dynamically added buttons
    document.addEventListener('click', (e) => {
        const themeToggle = e.target.closest('#theme-toggle') || e.target.closest('#theme-toggle-topbar');
        if (themeToggle) {
            const isDark = html.classList.contains('dark');
            setTheme(isDark ? 'light' : 'dark');
        }
    });
}

function setTheme(theme) {
    const html = document.documentElement;

    if (theme === 'dark') {
        html.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        html.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }

    updateThemeToggleIcon();
}

function updateThemeToggleIcon() {
    const html = document.documentElement;
    const toggleButtons = document.querySelectorAll('#theme-toggle');
    const isDark = html.classList.contains('dark');
    const sunIcon = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.536l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.828-2.828a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414l.707.707zm.707 5.657a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 1.414l-.707.707zM9 17a1 1 0 011-1h1a1 1 0 110 2h-1a1 1 0 01-1-1zm-4-3.464a1 1 0 00-1.414 1.414l.707.707a1 1 0 001.414-1.414l-.707-.707zM2.05 13.464a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM3 9a1 1 0 110-2H2a1 1 0 010 2h1z" clip-rule="evenodd"></path></svg>';
    const moonIcon = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>';

    toggleButtons.forEach((button) => {
        button.innerHTML = isDark ? sunIcon : moonIcon;
    });
}

// Initialize theme immediately
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeTheme);
} else {
    initializeTheme();
}
