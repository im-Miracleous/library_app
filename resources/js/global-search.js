document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('global-search-input');
    const resultsContainer = document.getElementById('global-search-results');
    let debounceTimer;

    if (!searchInput || !resultsContainer) return;

    searchInput.addEventListener('input', function (e) {
        const query = e.target.value.trim();

        clearTimeout(debounceTimer);

        if (query.length < 2) {
            resultsContainer.classList.add('hidden');
            resultsContainer.innerHTML = '';
            return;
        }

        debounceTimer = setTimeout(() => {
            fetchSearchResults(query);
        }, 300);
    });

    // Close results when clicking outside
    document.addEventListener('click', function (e) {
        if (!searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
            resultsContainer.classList.add('hidden');
        }
    });

    // Re-open results if input is focused and has content
    searchInput.addEventListener('focus', function () {
        if (searchInput.value.trim().length >= 2 && resultsContainer.innerHTML !== '') {
            resultsContainer.classList.remove('hidden');
        }
    });

    function fetchSearchResults(query) {
        // Show loading state if needed, or just wait
        // Using sanctum auth - assumes browser session cookie handles it or header needs token?
        // Since this is blade served, default axios/fetch often needs X-CSRF-TOKEN or just relies on session cookies if same domain.
        // Let's try basic fetch with credentials included.

        // Fetch to web route which uses session cookies
        fetch(`/global-search?query=${encodeURIComponent(query)}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.json())
            .then(data => {
                renderResults(data);
            })
            .catch(error => {
                console.error('Error fetching search results:', error);
            });
    }

    function renderResults(data) {
        let html = '';
        const hasBuku = data.buku && data.buku.length > 0;
        const hasAnggota = data.anggota && data.anggota.length > 0;

        if (!hasBuku && !hasAnggota) {
            html = `
                <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                    <p class="text-sm">Tidak ada hasil ditemukan.</p>
                </div>
            `;
        } else {
            // Section Buku
            if (hasBuku) {
                html += `
                    <div class="px-4 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50 dark:bg-gray-800/50">
                        Buku
                    </div>
                `;
                data.buku.forEach(item => {
                    // Assuming route exists or just linking to generic edit/show
                    // We need to know the base URL or named route. For now, hardcode pattern: /buku/{id}/edit or similar.
                    // Or maybe just /buku?search=... 
                    // Let's try to link to specific detail page if possible. 
                    // Based on web.php: Route::resource('buku', ...); -> /buku/{id}
                    const url = `/buku/${item.id_buku}/edit`; // Usually admin wants to edit/view details

                    html += `
                        <a href="${url}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors border-b last:border-0 border-gray-100 dark:border-gray-700">
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-gray-800 dark:text-gray-200">${item.judul}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">ISBN: ${item.isbn || '-'} • ${item.penulis || '-'}</span>
                            </div>
                        </a>
                    `;
                });
            }

            // Section Anggota
            if (hasAnggota) {
                html += `
                    <div class="px-4 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50 dark:bg-gray-800/50 ${hasBuku ? 'mt-2' : ''}">
                        Anggota
                    </div>
                `;
                data.anggota.forEach(item => {
                    // Based on web.php: Route::resource('anggota', ...); -> /anggota/{id}
                    // But actually admin uses modal usually? Or maybe index page with search?
                    // Let's link to index with search param if possible, or edit page.
                    // Route::resource('anggota', ...) creates /anggota/{id}/edit
                    const url = `/anggota?search=${encodeURIComponent(item.nama)}`; // Sending to index with search might be safer if detail page isn't straightforward

                    html += `
                        <a href="${url}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors border-b last:border-0 border-gray-100 dark:border-gray-700">
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-gray-800 dark:text-gray-200">${item.nama}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">${item.email}</span>
                            </div>
                        </a>
                    `;
                });
            }
        }

        resultsContainer.innerHTML = html;
        resultsContainer.classList.remove('hidden');
    }
});
