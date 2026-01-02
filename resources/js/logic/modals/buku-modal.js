document.addEventListener('DOMContentLoaded', function () {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('action') === 'create') {
        openModal('createModal');

        // Clean URL without reloading
        const newUrl = window.location.pathname + window.location.search.replace(/[\?&]action=create/, '') + window.location.hash;
        window.history.replaceState({}, '', newUrl);
    }

    // Init Image Preview
    if (typeof initImagePreview === 'function') {
        initImagePreview('#create_gambar_sampul', '#create_preview_img');
        initImagePreview('#edit_gambar_sampul', '#edit_preview_img');
    }

    const editPreviewImg = document.getElementById('edit_preview_img');

    // Book Management Draft Delete & Preview Logic
    const updateCancelBtnVisibility = () => {
        const img = document.getElementById('edit_preview_img');
        const cancelBtn = document.getElementById('edit_cover_cancel_btn');
        const deleteBtn = document.getElementById('edit_cover_delete_btn');
        const restoreBtn = document.getElementById('edit_cover_restore_btn');
        const removeInput = document.getElementById('edit_remove_gambar_sampul');
        const previewContainer = document.getElementById('edit_preview_container');

        if (!img || !cancelBtn) return;

        const newSrc = img.getAttribute('src') || '';
        const initialSrc = img.dataset.initialSrc || '';
        const isRemoved = removeInput?.value === '1';

        const isNewDraft = newSrc &&
            newSrc.length > 0 &&
            newSrc !== initialSrc &&
            (newSrc.startsWith('data:') || newSrc.startsWith('blob:'));

        if (isNewDraft) {
            cancelBtn.classList.remove('hidden');
            cancelBtn.style.display = 'inline-flex';
            if (previewContainer) previewContainer.classList.remove('hidden');

            if (deleteBtn) {
                deleteBtn.classList.add('hidden');
                deleteBtn.style.display = 'none';
            }
            if (restoreBtn) {
                restoreBtn.classList.add('hidden');
                restoreBtn.style.display = 'none';
            }
        } else {
            cancelBtn.classList.add('hidden');
            cancelBtn.style.display = 'none';

            if (initialSrc && initialSrc.length > 0) {
                if (isRemoved) {
                    if (deleteBtn) {
                        deleteBtn.classList.add('hidden');
                        deleteBtn.style.display = 'none';
                    }
                    if (restoreBtn) {
                        restoreBtn.classList.remove('hidden');
                        restoreBtn.style.display = 'inline-flex';
                    }
                    if (previewContainer) previewContainer.classList.add('hidden');
                } else {
                    if (deleteBtn) {
                        deleteBtn.classList.remove('hidden');
                        deleteBtn.style.display = 'inline-flex';
                    }
                    if (restoreBtn) {
                        restoreBtn.classList.add('hidden');
                        restoreBtn.style.display = 'none';
                    }
                    if (previewContainer) previewContainer.classList.remove('hidden');
                }
            } else {
                if (previewContainer) previewContainer.classList.add('hidden');
                if (deleteBtn) {
                    deleteBtn.classList.add('hidden');
                    deleteBtn.style.display = 'none';
                }
                if (restoreBtn) {
                    restoreBtn.classList.add('hidden');
                    restoreBtn.style.display = 'none';
                }
            }
        }
    };

    const editPreviewObserver = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.type === 'attributes' && mutation.attributeName === 'src') {
                updateCancelBtnVisibility();
            }
        });
    });

    if (editPreviewImg) {
        editPreviewObserver.observe(editPreviewImg, { attributes: true });
        // Initial check
        updateCancelBtnVisibility();
    }

    // Edit Cover Delete draft logic
    const editDeleteBtn = document.getElementById('edit_cover_delete_btn');
    const editRestoreBtn = document.getElementById('edit_cover_restore_btn');
    const editRemoveInput = document.getElementById('edit_remove_gambar_sampul');

    if (editDeleteBtn) {
        editDeleteBtn.addEventListener('click', function () {
            const previewContainer = document.getElementById('edit_preview_container');
            const restoreBtn = document.getElementById('edit_cover_restore_btn');
            const removeInput = document.getElementById('edit_remove_gambar_sampul');
            const cancelBtn = document.getElementById('edit_cover_cancel_btn');

            if (previewContainer) previewContainer.classList.add('hidden');

            this.classList.add('hidden');
            this.style.display = 'none';

            if (restoreBtn) {
                restoreBtn.classList.remove('hidden');
                restoreBtn.style.display = 'inline-flex';
            }
            if (removeInput) removeInput.value = '1';

            // Clear file input if any
            const input = document.getElementById('edit_gambar_sampul');
            if (input) input.value = '';

            if (cancelBtn) {
                cancelBtn.classList.add('hidden');
                cancelBtn.style.display = 'none';
            }
        });
    }

    if (editRestoreBtn) {
        editRestoreBtn.addEventListener('click', function () {
            const previewContainer = document.getElementById('edit_preview_container');
            const previewImg = document.getElementById('edit_preview_img');
            const deleteBtn = document.getElementById('edit_cover_delete_btn');
            const removeInput = document.getElementById('edit_remove_gambar_sampul');

            if (previewContainer && previewImg && previewImg.dataset.initialSrc) {
                previewImg.src = previewImg.dataset.initialSrc;
                previewContainer.classList.remove('hidden');
            }

            if (deleteBtn) {
                deleteBtn.classList.remove('hidden');
                deleteBtn.style.display = 'inline-flex';
            }

            this.classList.add('hidden');
            this.style.display = 'none';

            if (removeInput) removeInput.value = '0';
        });
    }

    // Edit Cover Cancel selection
    const editCancelBtn = document.getElementById('edit_cover_cancel_btn');
    if (editCancelBtn) {
        editCancelBtn.addEventListener('click', function () {
            const input = document.getElementById('edit_gambar_sampul');
            const previewImg = document.getElementById('edit_preview_img');
            const previewContainer = document.getElementById('edit_preview_container');
            const initialSrc = previewImg.dataset.initialSrc || '';

            input.value = '';
            previewImg.src = initialSrc;

            if (!initialSrc || initialSrc === '') {
                if (previewContainer) previewContainer.classList.add('hidden');
            } else {
                if (previewContainer) previewContainer.classList.remove('hidden');
                // If we reverted to an existing image, make sure delete btn is visible
                const removeInput = document.getElementById('edit_remove_gambar_sampul');
                const deleteBtn = document.getElementById('edit_cover_delete_btn');
                const restoreBtn = document.getElementById('edit_cover_restore_btn');
                if (removeInput?.value === '0') {
                    if (deleteBtn) {
                        deleteBtn.classList.remove('hidden');
                        deleteBtn.style.display = 'inline-flex';
                    }
                    if (restoreBtn) {
                        restoreBtn.classList.add('hidden');
                        restoreBtn.style.display = 'none';
                    }
                }
            }
            this.classList.add('hidden');
            this.style.display = 'none';
        });
    }
    // Create Modal Cover Logic
    const createPreviewImg = document.getElementById('create_preview_img');
    const createCancelBtn = document.getElementById('create_cover_cancel_btn');

    if (createPreviewImg) {
        const createObserver = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'attributes' && mutation.attributeName === 'src') {
                    const src = createPreviewImg.getAttribute('src');
                    if (src && src !== '') {
                        createCancelBtn.classList.remove('hidden');
                        createCancelBtn.style.display = 'inline-flex';
                    } else {
                        createCancelBtn.classList.add('hidden');
                        createCancelBtn.style.display = 'none';
                    }
                }
            });
        });
        createObserver.observe(createPreviewImg, { attributes: true });
    }

    if (createCancelBtn) {
        createCancelBtn.addEventListener('click', function () {
            const input = document.getElementById('create_gambar_sampul');
            const previewContainer = document.getElementById('create_preview_container');
            const previewImg = document.getElementById('create_preview_img');

            if (input) input.value = '';
            if (previewImg) previewImg.src = '';
            if (previewContainer) previewContainer.classList.add('hidden');

            this.classList.add('hidden');
            this.style.display = 'none';
        });
    }
});
