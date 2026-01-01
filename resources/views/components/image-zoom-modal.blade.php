<!-- IMAGE ZOOM MODAL COMPONENT -->
<div id="imageZoomModal" class="fixed inset-0 z-[100] transition-all duration-300 opacity-0 pointer-events-none"
    aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/90 backdrop-blur-sm transition-opacity duration-300" onclick="closeImageZoom()">
    </div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-6 sm:p-12">
            <div class="relative transform transition-all duration-300 scale-95 p-2">
                <button onclick="closeImageZoom()"
                    class="absolute -top-14 right-0 sm:-right-10 text-white/70 hover:text-white transition-colors bg-white/10 hover:bg-white/20 rounded-full p-2.5 backdrop-blur-md cursor-pointer flex items-center justify-center shadow-lg hover:scale-110 active:scale-95 group/close">
                    <span
                        class="material-symbols-outlined text-2xl group-hover/close:rotate-90 transition-transform duration-300">close</span>
                </button>
                <img id="imageZoomContent" src="" alt="Zoomed Content"
                    class="max-w-full max-h-[60vh] rounded-lg shadow-2xl object-contain ring-1 ring-white/20 transition-transform duration-300">
                <p id="imageZoomCaption"
                    class="text-center text-white/80 mt-6 text-lg font-medium tracking-wide drop-shadow-md"></p>
            </div>
        </div>
    </div>
</div>

<script>
    /**
     * openImageZoom
     * Global function to trigger the zoom modal
     * @param {string} src - Source of the image
     * @param {string} caption - Optional caption to display
     */
    window.openImageZoom = function (src, caption) {
        const modal = document.getElementById('imageZoomModal');
        const img = document.getElementById('imageZoomContent');
        const cap = document.getElementById('imageZoomCaption');

        if (modal && img) {
            img.src = src;
            if (cap) cap.innerText = caption || '';

            modal.classList.remove('hidden');
            // Small delay for animation
            setTimeout(() => {
                modal.classList.remove('opacity-0', 'pointer-events-none');
                const inner = modal.querySelector('.scale-95');
                if (inner) {
                    inner.classList.remove('scale-95');
                    inner.classList.add('scale-100');
                }
            }, 10);
        }
    }

    /**
     * closeImageZoom
     * Global function to close the zoom modal
     */
    window.closeImageZoom = function () {
        const modal = document.getElementById('imageZoomModal');
        const inner = modal?.querySelector('.scale-100');

        if (modal) {
            modal.classList.add('opacity-0', 'pointer-events-none');
            if (inner) {
                inner.classList.remove('scale-100');
                inner.classList.add('scale-95');
            }
            // Wait for transition before hiding completely
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
    }

    // Support for the previous function name for compatibility during migration
    window.openImageModal = window.openImageZoom;
    window.closeImageModal = window.closeImageZoom;
    window.openZoom = function () {
        // Find suitable preview img if called without params like in profile/settings
        const target = document.getElementById('profile_preview_img') || document.getElementById('logo_preview_img');
        if (target && target.src && !target.classList.contains('hidden')) {
            window.openImageZoom(target.src, '');
        }
    };
    window.closeZoom = window.closeImageZoom;
</script>