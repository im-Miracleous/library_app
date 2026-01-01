<div id="imagePreviewModal" class="fixed inset-0 z-[60] transition-all duration-300 opacity-0 pointer-events-none"
    aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div
                class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-[#1A1410] text-left shadow-2xl transition-all sm:w-full sm:max-w-2xl border border-primary/20 dark:border-[#36271F]">
                <div
                    class="px-6 py-4 border-b border-primary/20 dark:border-[#36271F] flex justify-between items-center bg-surface dark:bg-[#1A1410]">
                    <h3 class="text-lg font-bold text-primary-dark dark:text-white flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary dark:text-accent">visibility</span>
                        Pratinjau Gambar
                    </h3>
                    <button type="button" id="closePreviewBtn"
                        class="text-slate-500 dark:text-white/60 hover:text-slate-700 dark:hover:text-white transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="p-6">
                    <!-- Simple Image Preview -->
                    <div
                        class="w-full h-[400px] bg-black/5 dark:bg-black/20 rounded-lg overflow-hidden relative mb-4 flex items-center justify-center border-2 border-dashed border-slate-200 dark:border-white/10">
                        <img id="imagePreviewContent" src="" alt="Preview" class="max-w-full max-h-full object-contain">
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-primary/20 dark:border-[#36271F]">
                        <button type="button" id="cancelPreviewBtn"
                            class="px-4 py-2 rounded-lg border border-slate-200 dark:border-[#36271F] text-slate-600 dark:text-white/70 hover:bg-slate-50 dark:hover:bg-white/5 text-sm font-bold">
                            Batal
                        </button>
                        <button type="button" id="confirmPreviewBtn"
                            class="px-4 py-2 rounded-lg bg-primary dark:bg-accent text-white dark:text-primary-dark text-sm font-bold hover:brightness-110 transition-all shadow-sm">
                            Konfirmasi & Gunakan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>