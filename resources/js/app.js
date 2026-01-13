import './bootstrap';
import './global-search';
import './system-status';
import {
    Title,
    Chart,
    LineController,
    LineElement,
    PointElement,
    LinearScale,
    CategoryScale,
    BarController,
    BarElement,
    Tooltip,
    Legend,
    Filler
} from 'chart.js';

Chart.register(
    LineController,
    LineElement,
    PointElement,
    LinearScale,
    CategoryScale,
    BarController,
    BarElement,
    Tooltip,
    Legend,
    Filler
)

window.Chart = Chart;


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

    // Reset all file inputs within the modal to clear selected filenames
    const fileInputs = modal.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.value = '';
    });

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
            document.getElementById('edit_rusak').value = data.stok_rusak || 0;
            document.getElementById('edit_hilang').value = data.stok_hilang || 0;
            document.getElementById('edit_isbn').value = data.isbn || '';
            document.getElementById('edit_dewey').value = data.kode_dewey || '';
            document.getElementById('edit_deskripsi').value = data.deskripsi || '';

            // Select Status
            const statusSelect = document.getElementById('edit_status');
            if (statusSelect) statusSelect.value = data.status;

            // Handle Image Preview & Draft Delete
            const previewContainer = document.getElementById('edit_preview_container');
            const previewImg = document.getElementById('edit_preview_img');
            const removeInput = document.getElementById('edit_remove_gambar_sampul');
            const deleteBtn = document.getElementById('edit_cover_delete_btn');
            const restoreBtn = document.getElementById('edit_cover_restore_btn');
            const cancelBtn = document.getElementById('edit_cover_cancel_btn');

            if (removeInput) removeInput.value = '0';

            // Reset Buttons State
            if (deleteBtn) {
                deleteBtn.classList.add('hidden');
                deleteBtn.style.display = 'none';
            }
            if (restoreBtn) {
                restoreBtn.classList.add('hidden');
                restoreBtn.style.display = 'none';
            }
            if (cancelBtn) {
                cancelBtn.classList.add('hidden');
                cancelBtn.style.display = 'none';
            }

            if (previewContainer && previewImg) {
                if (data.gambar_sampul) {
                    previewImg.src = `/storage/${data.gambar_sampul}`;
                    previewImg.dataset.initialSrc = `/storage/${data.gambar_sampul}`;
                    previewContainer.classList.remove('hidden');
                    if (deleteBtn) {
                        deleteBtn.classList.remove('hidden');
                        deleteBtn.style.display = 'inline-flex';
                    }
                } else {
                    previewImg.src = '';
                    previewImg.dataset.initialSrc = '';
                    previewContainer.classList.add('hidden');
                }
            }

            window.openModal('editModal');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal mengambil data buku.');
        });
};

// --- 9. LOGIKA PRATINJAU GAMBAR (IMAGE PREVIEW) ---
let currentTargetInput = null;
let currentPreviewImage = null;

window.initImagePreview = function (inputSelector, previewSelector) {
    const input = document.querySelector(inputSelector);
    if (!input) return;

    input.addEventListener('change', function (e) {
        if (this.files && this.files[0]) {
            const file = this.files[0];

            // Validate image
            if (!file.type.startsWith('image/')) {
                alert('Silakan pilih file gambar.');
                return;
            }

            currentTargetInput = this;
            currentPreviewImage = document.querySelector(previewSelector);

            const reader = new FileReader();
            reader.onload = function (evt) {
                const modal = document.getElementById('imagePreviewModal');
                const previewImgModal = document.getElementById('imagePreviewContent');

                if (modal && previewImgModal) {
                    // Set src for modal preview
                    previewImgModal.src = evt.target.result;

                    // Show Modal
                    modal.classList.remove('hidden', 'pointer-events-none');
                    // Force reflow
                    void modal.offsetWidth;

                    modal.classList.remove('opacity-0');
                    modal.querySelector('.transform').classList.remove('scale-95');
                    modal.querySelector('.transform').classList.add('scale-100');
                }
            };
            reader.readAsDataURL(file);
        }
    });
}

// Global initialization for controls
document.addEventListener('DOMContentLoaded', function () {
    // Tombol close & cancel
    const closeBtns = document.querySelectorAll('#closePreviewBtn, #cancelPreviewBtn');
    closeBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const modal = document.getElementById('imagePreviewModal');
            if (modal) {
                modal.classList.add('opacity-0');
                modal.querySelector('.transform').classList.add('scale-95');
                modal.querySelector('.transform').classList.remove('scale-100');
                setTimeout(() => {
                    modal.classList.add('hidden', 'pointer-events-none');

                    // Reset Logic
                    const previewImgModal = document.getElementById('imagePreviewContent');
                    if (previewImgModal) previewImgModal.src = '';

                    // Reset input if cancelled
                    if (currentTargetInput && !currentTargetInput.hasAttribute('confirmed')) {
                        currentTargetInput.value = '';
                    }
                    currentTargetInput?.removeAttribute('confirmed');
                }, 300);
            }
        });
    });

    // Confirm Upload Image
    const confirmBtn = document.getElementById('confirmPreviewBtn');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function () {
            // Mark as confirmed so it's not reset by the close button logic
            if (currentTargetInput) {
                currentTargetInput.setAttribute('confirmed', 'true');

                // The FileReader already loaded the image for us in initImagePreview
                // and set the preview image modal. 
                // We just need to update the page preview.
                if (currentPreviewImage) {
                    const previewImgModal = document.getElementById('imagePreviewContent');
                    if (previewImgModal) {
                        currentPreviewImage.src = previewImgModal.src;
                        if (currentPreviewImage.parentElement.classList.contains('hidden')) {
                            currentPreviewImage.parentElement.classList.remove('hidden');
                        }
                    }
                }
            }

            // Close Modal
            const closeBtn = document.getElementById('closePreviewBtn');
            if (closeBtn) closeBtn.click();
        });
    }
});
