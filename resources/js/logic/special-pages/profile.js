document.addEventListener('DOMContentLoaded', () => {
    // Init Image Preview if available globally
    if (typeof initImagePreview === 'function') {
        initImagePreview('#foto_profil', '#profile_preview_img');
    }

    // Elements
    const targetImg = document.getElementById('profile_preview_img');
    const initials = document.getElementById('profile_initials');
    const zoomOverlay = document.getElementById('profile_zoom_overlay');
    const cancelBtn = document.getElementById('profile_cancel_btn');
    const deleteBtn = document.getElementById('profile_delete_btn');
    const restoreBtn = document.getElementById('profile_restore_btn');
    const removeInput = document.getElementById('remove_foto_profil');
    const input = document.getElementById('foto_profil');

    // Update Cancel Button Visibility based on changes
    const updateCancelBtnVisibility = () => {
        if (!cancelBtn) return;

        // FAIL-SAFE: If no input or no files selected
        if (!input || !input.files || input.files.length === 0) {
            cancelBtn.classList.add('hidden');
            cancelBtn.style.display = 'none';
            // Logic continues to check if we are in "removed" state or standard state
        }

        if (!targetImg) return;

        const newSrc = targetImg.getAttribute('src') || '';
        const initialSrc = targetImg.dataset.initialSrc || '';
        const isRemoved = removeInput?.value === '1';

        // A "new draft" is when we HAVE a data/blob URL 
        // and it's DIFFERENT from the initial one.
        const isNewDraft = newSrc &&
            newSrc.length > 0 &&
            newSrc !== initialSrc &&
            (newSrc.startsWith('data:') || newSrc.startsWith('blob:'));

        if (isNewDraft) {
            // User selected a new file
            if (initials) initials.classList.add('hidden');
            if (zoomOverlay) zoomOverlay.classList.remove('hidden');
            if (targetImg) targetImg.classList.remove('hidden');

            cancelBtn.classList.remove('hidden');
            cancelBtn.style.display = 'inline-flex';

            if (deleteBtn) {
                deleteBtn.classList.add('hidden');
                deleteBtn.style.display = 'none';
            }
            if (restoreBtn) {
                restoreBtn.classList.add('hidden');
                restoreBtn.style.display = 'none';
            }
        } else {
            // No new file draft
            cancelBtn.classList.add('hidden');
            cancelBtn.style.display = 'none';

            if (initialSrc && initialSrc.length > 0) {
                if (isRemoved) {
                    // Image was removed
                    if (deleteBtn) {
                        deleteBtn.classList.add('hidden');
                        deleteBtn.style.display = 'none';
                    }
                    if (restoreBtn) {
                        restoreBtn.classList.remove('hidden');
                        restoreBtn.style.display = 'inline-flex';
                    }
                    if (targetImg) targetImg.classList.add('hidden');
                    if (zoomOverlay) zoomOverlay.classList.add('hidden');
                    if (initials) initials.classList.remove('hidden');
                } else {
                    // Standard state (image exists)
                    if (deleteBtn) {
                        deleteBtn.classList.remove('hidden');
                        deleteBtn.style.display = 'inline-flex';
                    }
                    if (restoreBtn) {
                        restoreBtn.classList.add('hidden');
                        restoreBtn.style.display = 'none';
                    }
                    if (targetImg) targetImg.classList.remove('hidden');
                    if (zoomOverlay) zoomOverlay.classList.remove('hidden');
                    if (initials) initials.classList.add('hidden');
                }
            } else {
                // No initial image
                if (targetImg) targetImg.classList.add('hidden');
                if (zoomOverlay) zoomOverlay.classList.add('hidden');
                if (initials) initials.classList.remove('hidden');
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

    // Observer to watch for src changes (triggered by initImagePreview or other scripts)
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.type === 'attributes' && mutation.attributeName === 'src') {
                updateCancelBtnVisibility();
            }
        });
    });

    if (targetImg) {
        observer.observe(targetImg, { attributes: true });
        // Initial check
        updateCancelBtnVisibility();
    }

    // Event Listeners
    if (deleteBtn && removeInput) {
        deleteBtn.addEventListener('click', function () {
            targetImg.classList.add('hidden');
            initials?.classList.remove('hidden');
            zoomOverlay?.classList.add('hidden');

            deleteBtn.classList.add('hidden');
            deleteBtn.style.display = 'none';
            if (restoreBtn) {
                restoreBtn.classList.remove('hidden');
                restoreBtn.style.display = 'inline-flex';
            }
            removeInput.value = '1';

            // Clear file input if any
            if (input) input.value = '';
            if (cancelBtn) {
                cancelBtn.classList.add('hidden');
                cancelBtn.style.display = 'none';
            }
        });
    }

    if (restoreBtn) {
        restoreBtn.addEventListener('click', function () {
            const initialSrc = targetImg.dataset.initialSrc;

            if (initialSrc) {
                targetImg.src = initialSrc;
                targetImg.classList.remove('hidden');
                initials?.classList.add('hidden');
                zoomOverlay?.classList.remove('hidden');
            }

            if (deleteBtn) {
                deleteBtn.classList.remove('hidden');
                deleteBtn.style.display = 'inline-flex';
            }
            if (restoreBtn) {
                restoreBtn.classList.add('hidden');
                restoreBtn.style.display = 'none';
            }
            removeInput.value = '0';
        });
    }

    // Cancel Selection Logic (Revert to initial or removed state)
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function () {
            const initialSrc = targetImg.dataset.initialSrc || '';

            input.value = '';
            targetImg.src = initialSrc; // This triggers observer -> updateCancelBtnVisibility

            // Force immediate update if observer is slow, though observer handles it
            // checking state manually:
            if (!initialSrc || initialSrc === '') {
                targetImg.classList.add('hidden');
                initials?.classList.remove('hidden');
                zoomOverlay?.classList.add('hidden');
            } else {
                // If we reverted to an existing image, make sure delete btn is visible
                // provided we haven't marked it as removed
                if (removeInput.value === '0') {
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
});
