document.addEventListener('DOMContentLoaded', function () {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('action') === 'create') {
        openModal('createModal');
        const newUrl = window.location.pathname + window.location.search.replace(/[\?&]action=create/, '') + window.location.hash;
        window.history.replaceState({}, '', newUrl);
    }

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
    const editCancelBtn = document.getElementById('edit_cancel_btn'); // "Hapus Foto" for new draft
    const editDeleteBtn = document.getElementById('edit_delete_btn'); // "Hapus Foto" for existing
    const editRestoreBtn = document.getElementById('edit_restore_btn'); // "Batal Hapus"
    const removeInput = document.getElementById('edit_remove_foto_profil');

    // Handle New File Selection
    if (editInput) {
        editInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    editPreviewImg.src = e.target.result;
                    editPreviewContainer.classList.remove('hidden');

                    // Show "Hapus Foto" (Cancel New)
                    if (editCancelBtn) {
                        editCancelBtn.classList.remove('hidden');
                        editCancelBtn.style.display = 'inline-flex';
                    }
                    // Hide "Hapus Foto" (Delete Existing)
                    if (editDeleteBtn) {
                        editDeleteBtn.classList.add('hidden');
                        editDeleteBtn.style.display = 'none';
                    }
                    // Hide "Batal Hapus"
                    if (editRestoreBtn) {
                        editRestoreBtn.classList.add('hidden');
                        editRestoreBtn.style.display = 'none';
                    }
                }
                reader.readAsDataURL(file);
            }
        });
    }

    // Handle Cancel New Selection
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
                // Restore state regarding existing image
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

    // Handle Delete Existing (Draft)
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
            if (editInput) editInput.value = ''; // Clear any dropped file just in case
        });
    }

    // Handle Restore Existing
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

window.openEditAnggota = async function (id) {
    try {
        const response = await fetch(`/anggota/${id}`);
        if (!response.ok) {
            throw new Error('Gagal mengambil data');
        }
        const user = await response.json();

        document.getElementById('edit_nama').value = user.nama;
        document.getElementById('edit_email').value = user.email;
        document.getElementById('edit_telepon').value = user.telepon || '';
        document.getElementById('edit_alamat').value = user.alamat || '';
        document.getElementById('edit_status').value = user.status;

        // Handle Profile Photo Preview
        const editPreviewImg = document.getElementById('edit_preview_img');
        const editPreviewContainer = document.getElementById('edit_preview_container');
        const editDeleteBtn = document.getElementById('edit_delete_btn');
        const editRestoreBtn = document.getElementById('edit_restore_btn');
        const editCancelBtn = document.getElementById('edit_cancel_btn');
        const removeInput = document.getElementById('edit_remove_foto_profil');

        // Reset states
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

        // PROTEKSI DIRI: Disable Status jika edit diri sendiri
        const currentUserId = window.currentUserId;
        const statusSelect = document.getElementById('edit_status');

        // Ensure stricter string comparison just in case
        if (String(user.id_pengguna) === String(currentUserId)) {
            statusSelect.disabled = true;
            statusSelect.classList.add('bg-slate-100', 'dark:bg-slate-800/50', 'cursor-not-allowed');
        } else {
            statusSelect.disabled = false;
            statusSelect.classList.remove('bg-slate-100', 'dark:bg-slate-800/50', 'cursor-not-allowed');
        }

        // UNLOCK ACCOUNT UI
        const unlockContainer = document.getElementById('unlockContainer');
        if (user.is_locked) {
            unlockContainer.classList.remove('hidden');
        } else {
            unlockContainer.classList.add('hidden');
            document.getElementById('unlock_account').checked = false;
        }

        // Set action url
        document.getElementById('editForm').action = `/anggota/${user.id_pengguna}`;

        openModal('editModal');
    } catch (error) {
        console.error('Error:', error);
        alert('Gagal mengambil data anggota: ' + error.message);
    }
}
