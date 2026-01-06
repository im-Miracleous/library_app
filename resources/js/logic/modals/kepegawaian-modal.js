document.addEventListener('DOMContentLoaded', function () {
    // --- CREATE MODAL LOGIC ---
    const createInput = document.getElementById('create_foto_profil');
    const createPreviewImg = document.getElementById('create_preview_img');
    const createPreviewContainer = document.getElementById('create_preview_container');
    const createCancelBtn = document.getElementById('create_cancel_btn');

    if (createInput) {
        createInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    createPreviewImg.src = e.target.result;
                    createPreviewContainer.classList.remove('hidden');
                    if (createCancelBtn) {
                        createCancelBtn.classList.remove('hidden');
                        createCancelBtn.style.display = 'inline-flex';
                    }
                }
                reader.readAsDataURL(file);
            }
        });
    }

    if (createCancelBtn) {
        createCancelBtn.addEventListener('click', function () {
            createInput.value = '';
            createPreviewImg.src = '';
            createPreviewContainer.classList.add('hidden');
            this.classList.add('hidden');
            this.style.display = 'none';
        });
    }

    // --- EDIT MODAL LOGIC ---
    const editInput = document.getElementById('edit_foto_profil');
    const editPreviewImg = document.getElementById('edit_preview_img');
    const editPreviewContainer = document.getElementById('edit_preview_container');
    const editCancelBtn = document.getElementById('edit_cancel_btn');
    const editDeleteBtn = document.getElementById('edit_delete_btn');
    const editRestoreBtn = document.getElementById('edit_restore_btn');
    const removeInput = document.getElementById('edit_remove_foto_profil');

    if (editInput) {
        editInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    editPreviewImg.src = e.target.result;
                    editPreviewContainer.classList.remove('hidden');

                    if (editCancelBtn) {
                        editCancelBtn.classList.remove('hidden');
                        editCancelBtn.style.display = 'inline-flex';
                    }
                    if (editDeleteBtn) {
                        editDeleteBtn.classList.add('hidden');
                        editDeleteBtn.style.display = 'none';
                    }
                    if (editRestoreBtn) {
                        editRestoreBtn.classList.add('hidden');
                        editRestoreBtn.style.display = 'none';
                    }
                }
                reader.readAsDataURL(file);
            }
        });
    }

    if (editCancelBtn) {
        editCancelBtn.addEventListener('click', function () {
            editInput.value = '';
            const initialSrc = editPreviewImg.dataset.initialSrc || '';
            editPreviewImg.src = initialSrc;

            this.classList.add('hidden');
            this.style.display = 'none';

            if (!initialSrc) {
                editPreviewContainer.classList.add('hidden');
            } else {
                const isRemoved = removeInput.value === '1';
                if (!isRemoved) {
                    if (editDeleteBtn) {
                        editDeleteBtn.classList.remove('hidden');
                        editDeleteBtn.style.display = 'inline-flex';
                    }
                } else {
                    if (editRestoreBtn) {
                        editRestoreBtn.classList.remove('hidden');
                        editRestoreBtn.style.display = 'inline-flex';
                    }
                }
            }
        });
    }

    if (editDeleteBtn) {
        editDeleteBtn.addEventListener('click', function () {
            editPreviewContainer.classList.add('hidden');
            this.classList.add('hidden');
            this.style.display = 'none';

            if (editRestoreBtn) {
                editRestoreBtn.classList.remove('hidden');
                editRestoreBtn.style.display = 'inline-flex';
            }
            if (removeInput) removeInput.value = '1';
            if (editInput) editInput.value = '';
        });
    }

    if (editRestoreBtn) {
        editRestoreBtn.addEventListener('click', function () {
            const initialSrc = editPreviewImg.dataset.initialSrc;
            if (initialSrc) {
                editPreviewImg.src = initialSrc;
                editPreviewContainer.classList.remove('hidden');
            }

            this.classList.add('hidden');
            this.style.display = 'none';

            if (editDeleteBtn) {
                editDeleteBtn.classList.remove('hidden');
                editDeleteBtn.style.display = 'inline-flex';
            }
            if (removeInput) removeInput.value = '0';
        });
    }
});

window.openEditPegawai = function (user) {
    // 1. Validasi & Set Action URL (Prioritas Utama)
    if (!user.id_pengguna) {
        alert('Error System: ID Pengguna tidak ditemukan pada data yang dipilih.');
        console.error('Data User:', user);
        return;
    }

    const form = document.getElementById('editForm');
    if (form) {
        form.action = `/kepegawaian/${user.id_pengguna}`;
    } else {
        console.error('Form editForm tidak ditemukan!');
        return;
    }

    document.getElementById('edit_nama').value = user.nama;
    document.getElementById('edit_email').value = user.email;
    document.getElementById('edit_telepon').value = user.telepon;
    document.getElementById('edit_alamat').value = user.alamat;
    document.getElementById('edit_status').value = user.status;

    // Set action URL
    document.getElementById('editForm').action = `/kepegawaian/${user.id_pengguna}`;

    // Set role value manually (for safety, though value mapping usually works)
    const peranSelect = document.getElementById('edit_peran');
    if (peranSelect) {
        // Try to select even if disabled
        peranSelect.value = user.peran;
    }

    // UNLOCK ACCOUNT UI
    const unlockContainer = document.getElementById('unlockContainer');
    // Ensure boolean check (handles 1, "1", true)
    if (user.is_locked == 1 || user.is_locked === true) {
        unlockContainer.classList.remove('hidden');
    } else {
        unlockContainer.classList.add('hidden');
        document.getElementById('unlock_account').checked = false;
    }

    // Handle Profile Photo Preview
    const editPreviewImg = document.getElementById('edit_preview_img');
    const editPreviewContainer = document.getElementById('edit_preview_container');
    const editDeleteBtn = document.getElementById('edit_delete_btn');
    const editRestoreBtn = document.getElementById('edit_restore_btn');
    const editCancelBtn = document.getElementById('edit_cancel_btn');
    const removeInput = document.getElementById('edit_remove_foto_profil');

    if (removeInput) removeInput.value = '0';
    document.getElementById('edit_foto_profil').value = '';
    if (editCancelBtn) {
        editCancelBtn.classList.add('hidden');
        editCancelBtn.style.display = 'none';
    }
    if (editRestoreBtn) {
        editRestoreBtn.classList.add('hidden');
        editRestoreBtn.style.display = 'none';
    }

    if (user.foto_profil) {
        const src = `/storage/${user.foto_profil}`;
        editPreviewImg.src = src;
        editPreviewImg.dataset.initialSrc = src;
        editPreviewContainer.classList.remove('hidden');

        if (editDeleteBtn) {
            editDeleteBtn.classList.remove('hidden');
            editDeleteBtn.style.display = 'inline-flex';
        }
    } else {
        editPreviewImg.src = '';
        editPreviewImg.dataset.initialSrc = '';
        editPreviewContainer.classList.add('hidden');
        if (editDeleteBtn) {
            editDeleteBtn.classList.add('hidden');
            editDeleteBtn.style.display = 'none';
        }
    }

    // PROTEKSI ROLE & STATUS
    // Need to access current user ID from Blade to JS (passed via window variable in Blade view)
    const currentUserId = window.currentUserId;
    const currentUserRole = window.currentUserRole;

    // Elements to control
    const inputsToControl = [
        'edit_nama', 'edit_email', 'edit_telepon', 'edit_alamat',
        'edit_foto_profil', 'edit_status', 'edit_peran'
    ];
    const passwordInputs = document.querySelectorAll('#editForm input[type="password"]');

    // Restore missing element references
    const peranReadonly = document.getElementById('edit_peran_readonly');
    const statusSelect = document.getElementById('edit_status');

    // Helper to set disabled state
    const setDisabled = (isLocked) => {
        inputsToControl.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.disabled = isLocked;
                if (isLocked) {
                    el.classList.add('bg-slate-100', 'dark:bg-slate-800/50', 'cursor-not-allowed', 'opacity-70');
                } else {
                    el.classList.remove('bg-slate-100', 'dark:bg-slate-800/50', 'cursor-not-allowed', 'opacity-70');
                }
            }
        });
        passwordInputs.forEach(el => {
            el.disabled = isLocked;
            if (isLocked) {
                el.classList.add('bg-slate-100', 'dark:bg-slate-800/50', 'cursor-not-allowed', 'opacity-70');
            } else {
                el.classList.remove('bg-slate-100', 'dark:bg-slate-800/50', 'cursor-not-allowed', 'opacity-70');
            }
        });
    };

    // Logic Proteksi
    const isOwner = user.peran === 'owner';
    const isSelf = String(user.id_pengguna) === String(currentUserId);
    const isAdminEditingAdmin = currentUserRole === 'admin' && user.peran === 'admin' && !isSelf;

    if (isAdminEditingAdmin) {
        // ADMIN EDITING OTHER ADMIN -> LOCK ALL EXCEPT UNLOCK CHECKBOX
        setDisabled(true);

        // Ensure role and status are visually locked even if handled by setDisabled
        if (peranReadonly) peranReadonly.classList.add('hidden'); // Or show it? Maybe better to show the select but disabled
        peranSelect.classList.remove('hidden');
    } else {
        // RESET DEFAULT STATE (Enable first, then apply specific restrictions)
        setDisabled(false);

        if (isOwner) {
            // Owner Editing: Role & Status Protected
            peranSelect.classList.add('hidden');
            peranSelect.disabled = true;

            if (peranReadonly) peranReadonly.classList.remove('hidden');

            statusSelect.disabled = true;
            statusSelect.classList.add('bg-slate-100', 'dark:bg-slate-800/50', 'cursor-not-allowed');
        } else if (isSelf) {
            // Self Editing: Role & Status Protected
            peranSelect.classList.remove('hidden');
            peranSelect.disabled = true; // Cannot demote self

            if (peranReadonly) peranReadonly.classList.add('hidden');
            peranSelect.classList.add('bg-slate-100', 'dark:bg-slate-800/50', 'cursor-not-allowed');

            statusSelect.disabled = true; // Cannot deactivate self
            statusSelect.classList.add('bg-slate-100', 'dark:bg-slate-800/50', 'cursor-not-allowed');
        } else {
            // Normal Editing
            peranSelect.classList.remove('hidden');
            peranSelect.disabled = false;

            if (peranReadonly) peranReadonly.classList.add('hidden');
            peranSelect.classList.remove('bg-slate-100', 'dark:bg-slate-800/50', 'cursor-not-allowed');

            statusSelect.disabled = false;
            statusSelect.classList.remove('bg-slate-100', 'dark:bg-slate-800/50', 'cursor-not-allowed');
        }
    }

    openModal('editModal');
}
