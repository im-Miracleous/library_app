import './bootstrap';

// Logika Custom Aplikasi
document.addEventListener('DOMContentLoaded', () => {
    
    // --- HELPER: Loading State Animation ---
    const showLoading = (btn) => {
        // 1. Disable tombol
        btn.disabled = true;
        btn.classList.add('opacity-75', 'cursor-not-allowed');
        
        // 2. Ganti konten dengan Spinner
        btn.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Mohon Tunggu...</span>
        `;
    };

    // --- 1. LOGIKA HALAMAN LOGIN ---
    // Cari form yang memiliki input email (indikator form login)
    const loginForm = document.querySelector('form input[name="email"]')?.closest('form');
    
    if (loginForm) {
        loginForm.addEventListener('submit', function() {
            const btn = this.querySelector('button[type="submit"]');
            if (btn) showLoading(btn);
        });
    }

    // --- 2. LOGIKA LOGOUT ---
    const logoutForms = document.querySelectorAll('.form-logout');
    
    logoutForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); 
            if (confirm('Apakah Anda yakin ingin keluar dari aplikasi?')) {
                const btn = this.querySelector('button[type="submit"]');
                if (btn) showLoading(btn);
                this.submit(); 
            }
        });
    });

    // --- 3. FITUR TOGGLE PASSWORD ---
    const toggleButtons = document.querySelectorAll('.toggle-password');
    
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const input = this.closest('.relative').querySelector('input');
            const icon = this.querySelector('.material-symbols-outlined');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.textContent = 'visibility_off'; 
            } else {
                input.type = 'password';
                icon.textContent = 'visibility';
            }
        });
    });

    // --- 4. LOGIKA SIDEBAR MOBILE ---
    const sidebar = document.getElementById('sidebar');
    const openSidebarBtn = document.getElementById('open-sidebar');
    const closeSidebarBtn = document.getElementById('close-sidebar');
    const overlay = document.getElementById('mobile-overlay');

    function toggleSidebar() {
        if (!sidebar) return; 

        sidebar.classList.toggle('-translate-x-full');
        
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

    if (openSidebarBtn) openSidebarBtn.addEventListener('click', toggleSidebar);
    if (closeSidebarBtn) closeSidebarBtn.addEventListener('click', toggleSidebar);
    if (overlay) overlay.addEventListener('click', toggleSidebar);

    // --- 5. LOGIKA MODAL (User Management) ---
    window.openModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;
        
        const panel = modal.querySelector('div[class*="relative transform"]');
        
        modal.classList.remove('pointer-events-none');
        setTimeout(() => modal.classList.remove('opacity-0'), 10);
        setTimeout(() => {
            panel.classList.remove('scale-95');
            panel.classList.add('scale-100');
        }, 10);
    }

    window.closeModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        const panel = modal.querySelector('div[class*="relative transform"]');

        modal.classList.add('opacity-0');
        panel.classList.remove('scale-100');
        panel.classList.add('scale-95');

        setTimeout(() => modal.classList.add('pointer-events-none'), 300);
    }

    // Logic Fetch Data Edit User
    window.openEditModal = function(userId) {
        window.openModal('editModal');
        const form = document.getElementById('editForm');
        // Reset form opacity
        form.style.opacity = '0.5';
        
        fetch(`/pengguna/${userId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('edit_nama').value = data.nama;
                document.getElementById('edit_email').value = data.email;
                document.getElementById('edit_telepon').value = data.telepon;
                document.getElementById('edit_alamat').value = data.alamat;
                document.getElementById('edit_status').value = data.status;
                document.getElementById('editForm').action = `/pengguna/${userId}`;
                form.style.opacity = '1';
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal mengambil data user.');
                window.closeModal('editModal');
            });
    }
});