import './bootstrap';
import './global-search';
import './system-status';

document.addEventListener('DOMContentLoaded', () => {

    // --- HELPER: Loading State Animation ---
    const showLoading = (btn) => {
        // Simpan konten asli tombol
        btn.dataset.originalContent = btn.innerHTML;

        // Disable tombol
        btn.disabled = true;
        btn.classList.add('opacity-75', 'cursor-not-allowed');

        // Ganti konten dengan Spinner
        btn.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-current inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Mohon Tunggu...</span>
        `;
    };

    // --- 1. LOGIKA LOGIN ---
    const loginForm = document.querySelector('form input[name="email"]')?.closest('form');
    if (loginForm) {
        loginForm.addEventListener('submit', function () {
            const btn = this.querySelector('button[type="submit"]');
            if (btn) showLoading(btn);
        });
    }

    // --- 2. LOGIKA LOGOUT (DIPERBAIKI) ---
    // Selector ini akan mencari semua form dengan class 'form-logout'
    // Pastikan di dashboard.blade.php form logout Anda memiliki class="w-full form-logout"
    const logoutForms = document.querySelectorAll('.form-logout');

    logoutForms.forEach(form => {
        form.addEventListener('submit', function (e) {
            // Cegah submit default agar kita bisa tampilkan confirm dialog
            e.preventDefault();

            if (confirm('Apakah Anda yakin ingin keluar dari aplikasi?')) {
                const btn = this.querySelector('button[type="submit"]');
                if (btn) showLoading(btn);

                // Lanjutkan submit form secara manual setelah loading muncul
                this.submit();
            }
        });
    });

    // --- 3. FITUR TOGGLE PASSWORD ---
    const toggleButtons = document.querySelectorAll('.toggle-password');
    toggleButtons.forEach(button => {
        button.addEventListener('click', function () {
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

        if (sidebar.classList.contains('-translate-x-full')) {
            // BUKA: Slide Kanan
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');

            if (overlay) {
                overlay.classList.remove('hidden');
                void overlay.offsetWidth;
                overlay.classList.remove('opacity-0');
            }
        } else {
            // TUTUP: Slide Kiri
            sidebar.classList.remove('translate-x-0');
            sidebar.classList.add('-translate-x-full');

            if (overlay) {
                overlay.classList.add('opacity-0');
                setTimeout(() => {
                    overlay.classList.add('hidden');
                }, 300);
            }
        }
    }

    if (openSidebarBtn) openSidebarBtn.addEventListener('click', toggleSidebar);
    if (closeSidebarBtn) closeSidebarBtn.addEventListener('click', toggleSidebar);
    if (overlay) overlay.addEventListener('click', toggleSidebar);
});

// --- 5. LOGIKA MODAL GLOBAL ---
window.openModal = function (modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    const panel = modal.querySelector('div[class*="relative transform"]');

    // Hapus class hidden jika ada (untuk safety)
    modal.classList.remove('hidden');
    modal.classList.remove('pointer-events-none');

    // Trigger reflow agar browser sadar state awal sebelum transisi
    void modal.offsetWidth;

    modal.classList.remove('opacity-0');

    if (panel) {
        panel.classList.remove('scale-95');
        panel.classList.add('scale-100');
    }
};

window.closeModal = function (modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    const panel = modal.querySelector('div[class*="relative transform"]');

    modal.classList.add('opacity-0');

    if (panel) {
        panel.classList.remove('scale-100');
        panel.classList.add('scale-95');
    }

    // Tunggu transisi selesai baru set pointer-events-none
    setTimeout(() => {
        modal.classList.add('pointer-events-none');
    }, 300);
};

// --- 6. LOGIKA EDIT KATEGORI ---
window.openEditKategori = function (id) {
    // Ganti URL action form
    const form = document.getElementById('editForm');
    form.action = `/kategori/${id}`;

    // Fetch data kategori
    fetch(`/kategori/${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('edit_nama').value = data.nama_kategori;
            document.getElementById('edit_deskripsi').value = data.deskripsi || '';
            window.openModal('editModal');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal mengambil data kategori.');
        });
};

// --- 8. LOGIKA EDIT BUKU ---
window.openEditBuku = function (id) {
    const form = document.getElementById('editForm');
    form.action = `/buku/${id}`;

    fetch(`/buku/${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('edit_judul').value = data.judul;

            // Select Kategori
            const katSelect = document.getElementById('edit_kategori');
            if (katSelect) katSelect.value = data.id_kategori;

            document.getElementById('edit_penulis').value = data.penulis;
            document.getElementById('edit_penerbit').value = data.penerbit || '';
            document.getElementById('edit_tahun').value = data.tahun_terbit;
            document.getElementById('edit_stok').value = data.stok_total;
            document.getElementById('edit_isbn').value = data.isbn || '';
            document.getElementById('edit_dewey').value = data.kode_dewey || '';
            document.getElementById('edit_deskripsi').value = data.deskripsi || '';

            // Select Status
            const statusSelect = document.getElementById('edit_status');
            if (statusSelect) statusSelect.value = data.status;

            window.openModal('editModal');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal mengambil data buku.');
        });
};