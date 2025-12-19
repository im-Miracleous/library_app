window.toggleTheme = function () {
    // Aktifkan animasi transisi background
    document.body.classList.add('theme-animating');

    const html = document.documentElement;

    if (html.classList.contains('dark')) {
        html.classList.remove('dark');
        localStorage.theme = 'light';
    } else {
        html.classList.add('dark');
        localStorage.theme = 'dark';
    }

    updateThemeIcon();

    // Hapus kelas animasi setelah transisi selesai (500ms sesuai CSS)
    setTimeout(() => {
        document.body.classList.remove('theme-animating');
    }, 500);
};

function updateThemeIcon() {
    const iconIds = ['theme-icon', 'theme-icon-page'];

    iconIds.forEach(id => {
        const icon = document.getElementById(id);
        if (icon) {
            // Animasi Fade Out & Rotate sedikit
            icon.style.transition = 'transform 0.2s ease, opacity 0.2s ease';
            icon.style.opacity = '0';
            icon.style.transform = 'rotate(90deg) scale(0.5)';

            setTimeout(() => {
                // Ganti Icon saat invisible
                if (document.documentElement.classList.contains('dark')) {
                    icon.textContent = 'light_mode';
                } else {
                    icon.textContent = 'dark_mode';
                }

                // Animasi Fade In & Reset Rotate
                icon.style.opacity = '1';
                icon.style.transform = 'rotate(0deg) scale(1)';
            }, 200);
        }
    });
}

document.addEventListener('DOMContentLoaded', updateThemeIcon);