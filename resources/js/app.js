import './bootstrap';

// Logika Custom Aplikasi
document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Fitur Toggle Password
    const toggleButtons = document.querySelectorAll('.toggle-password');
    
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const input = this.closest('.relative').querySelector('input');
            const icon = this.querySelector('.material-symbols-outlined'); // Perbaikan selektor (sebelumnya span)
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.textContent = 'visibility_off'; 
            } else {
                input.type = 'password';
                icon.textContent = 'visibility';
            }
        });
    });

    // 2. Fitur Konfirmasi Logout
    const logoutForms = document.querySelectorAll('.form-logout');
    
    logoutForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); 
            if (confirm('Apakah Anda yakin ingin keluar dari aplikasi?')) {
                this.submit(); 
            }
        });
    });

    // 3. LOGIKA SIDEBAR MOBILE
    const sidebar = document.getElementById('sidebar');
    const openSidebarBtn = document.getElementById('open-sidebar');
    const closeSidebarBtn = document.getElementById('close-sidebar');
    const overlay = document.getElementById('mobile-overlay');

    function toggleSidebar() {
        if (!sidebar) return; // Guard clause jika elemen tidak ada (misal di halaman login)

        // Toggle Class untuk geser sidebar
        sidebar.classList.toggle('-translate-x-full');
        
        // Toggle Overlay
        if (overlay) {
            if (overlay.classList.contains('hidden')) {
                overlay.classList.remove('hidden');
                setTimeout(() => overlay.classList.remove('opacity-0'), 10);
            } else {
                overlay.classList.add('opacity-0');
                setTimeout(() => overlay.classList.add('hidden'), 300);
            }
        }
    }

    // Event Listeners dengan pengecekan null
    if (openSidebarBtn) openSidebarBtn.addEventListener('click', toggleSidebar);
    if (closeSidebarBtn) closeSidebarBtn.addEventListener('click', toggleSidebar);
    if (overlay) overlay.addEventListener('click', toggleSidebar);
});