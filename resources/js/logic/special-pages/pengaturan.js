document.addEventListener('DOMContentLoaded', () => {
    // Init Image Preview if available globally
    if (typeof initImagePreview === 'function') {
        initImagePreview('#logo', '#logo_preview_img');
    }

    // Observer to show preview container and zoom overlay for NEW images
    const updateCancelBtnVisibility = () => {
        const img = document.getElementById('logo_preview_img');
        const input = document.getElementById('logo');
        const cancelBtn = document.getElementById('logo_cancel_btn');
        const previewContainer = document.getElementById('logo_preview_container');

        const deleteBtn = document.getElementById('logo_delete_btn');
        const restoreBtn = document.getElementById('logo_restore_btn');
        const removeInput = document.getElementById('remove_logo');
        const isRemoved = removeInput?.value === '1';

        // Helper safely gets src
        const newSrc = img ? img.getAttribute('src') : '';
        const initialSrc = img ? img.dataset.initialSrc : '';

        // A "new draft" is when we HAVE a file selected AND a data/blob URL 
        // AND it's DIFFERENT from the initial one.
        const isNewDraft = newSrc &&
            newSrc.length > 0 &&
            newSrc !== initialSrc &&
            (newSrc.startsWith('data:') || newSrc.startsWith('blob:'));

        if (isNewDraft) {
            if (cancelBtn) {
                cancelBtn.classList.remove('hidden');
                cancelBtn.style.display = 'inline-flex';
            }
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
            if (cancelBtn) {
                cancelBtn.classList.add('hidden');
                cancelBtn.style.display = 'none';
            }

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
                // No initial image and no draft
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

    // Add listener to input change as well, for double safety
    const logoInput = document.getElementById('logo');
    if (logoInput) {
        logoInput.addEventListener('change', updateCancelBtnVisibility);
        logoInput.addEventListener('input', updateCancelBtnVisibility);
    }

    // Observer to show preview container and zoom overlay for NEW images
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.type === 'attributes' && mutation.attributeName === 'src') {
                updateCancelBtnVisibility();
            }
        });
    });

    const targetImg = document.getElementById('logo_preview_img');
    if (targetImg) {
        observer.observe(targetImg, { attributes: true });
        // Initial check on load
        updateCancelBtnVisibility();
    }

    // Delete (Draft) Selection Logic
    const deleteBtn = document.getElementById('logo_delete_btn');
    const restoreBtn = document.getElementById('logo_restore_btn');
    const removeInput = document.getElementById('remove_logo');

    if (deleteBtn && removeInput) {
        deleteBtn.addEventListener('click', function () {
            const preview = document.getElementById('logo_preview_img');
            const previewContainer = document.getElementById('logo_preview_container');
            const cancelBtn = document.getElementById('logo_cancel_btn');

            // Hide current logo elements
            // We need to find the specific container. 
            // In the view, the image is inside a div, which is inside a div with class 'mb-2'
            if (preview) {
                const wrapper = preview.closest('.mb-2');
                if (wrapper) wrapper.classList.add('hidden');
            }

            deleteBtn.classList.add('hidden');
            deleteBtn.style.display = 'none';
            if (restoreBtn) {
                restoreBtn.classList.remove('hidden');
                restoreBtn.style.display = 'inline-flex';
            }
            removeInput.value = '1';

            // Clear file input if any
            if (logoInput) logoInput.value = '';

            if (cancelBtn) cancelBtn.classList.add('hidden');

            if (previewContainer) previewContainer.classList.add('hidden');
        });
    }

    if (restoreBtn) {
        restoreBtn.addEventListener('click', function () {
            const preview = document.getElementById('logo_preview_img');

            if (preview) {
                const wrapper = preview.closest('.mb-2');
                if (wrapper) wrapper.classList.remove('hidden');
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

    // Cancel Button Logic
    const cancelBtn = document.getElementById('logo_cancel_btn');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function () {
            const input = document.getElementById('logo');
            const preview = document.getElementById('logo_preview_img');
            const initialSrc = preview.dataset.initialSrc || '';

            if (input) input.value = '';
            if (preview) preview.src = initialSrc;

            // If we reverted to an existing image, make sure delete btn is visible
            if (removeInput && removeInput.value === '0') {
                if (deleteBtn) {
                    deleteBtn.classList.remove('hidden');
                    deleteBtn.style.display = 'inline-flex';
                }
                if (restoreBtn) {
                    restoreBtn.classList.add('hidden');
                    restoreBtn.style.display = 'none';
                }
            }
            this.classList.add('hidden');
        });
    }
});
