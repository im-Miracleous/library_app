document.addEventListener('DOMContentLoaded', () => {
    // --- 1. MEMBER SEARCH LOGIC ---
    const searchAnggotaInput = document.getElementById('searchAnggota');
    const anggotaResults = document.getElementById('anggotaResults');
    const selectedAnggotaDiv = document.getElementById('selectedAnggota');
    const idPenggunaInput = document.getElementById('id_pengguna_input');
    const removeAnggotaBtn = document.getElementById('removeAnggotaBtn');

    let anggotaTimeout = null;

    if (searchAnggotaInput) {
        searchAnggotaInput.addEventListener('input', (e) => {
            clearTimeout(anggotaTimeout);
            const query = e.target.value.trim();

            if (query.length < 1) {
                anggotaResults.classList.add('hidden');
                return;
            }

            anggotaTimeout = setTimeout(async () => {
                try {
                    const response = await fetch(`/pengguna?search=${encodeURIComponent(query)}&status=aktif`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const json = await response.json();

                    renderAnggotaResults(json.data);
                } catch (error) {
                    console.error('Error fetching members:', error);
                }
            }, 300);
        });

        // Hide dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!searchAnggotaInput.contains(e.target) && !anggotaResults.contains(e.target)) {
                anggotaResults.classList.add('hidden');
            }
        });
    }

    function renderAnggotaResults(users) {
        anggotaResults.innerHTML = '';
        if (users.length === 0) {
            anggotaResults.innerHTML = `<div class="p-3 text-sm text-slate-500 text-center">Tidak ada anggota ditemukan.</div>`;
        } else {
            users.forEach(user => {
                const div = document.createElement('div');
                div.className = 'p-3 hover:bg-slate-50 dark:hover:bg-white/5 cursor-pointer border-b border-slate-100 dark:border-white/5 last:border-0 flex items-center justify-between group';
                div.innerHTML = `
                    <div>
                        <div class="font-bold text-slate-800 dark:text-white text-sm">${user.nama}</div>
                        <div class="text-xs text-slate-500 dark:text-white/60">${user.email}</div>
                    </div>
                    <span class="text-xs bg-green-100 dark:bg-green-500/20 text-green-700 dark:text-green-400 px-2 py-0.5 rounded-full">${user.id_pengguna}</span>
                `;
                div.onclick = () => selectAnggota(user);
                anggotaResults.appendChild(div);
            });
        }
        anggotaResults.classList.remove('hidden');
    }

    function selectAnggota(user) {
        // Set values
        idPenggunaInput.value = user.id_pengguna;
        document.getElementById('selectedAnggotaInitial').textContent = user.nama.charAt(0).toUpperCase();
        document.getElementById('selectedAnggotaName').textContent = user.nama;
        document.getElementById('selectedAnggotaEmail').textContent = user.email;

        // Show selected, hide search
        searchAnggotaInput.parentElement.classList.add('hidden');
        selectedAnggotaDiv.classList.remove('hidden');
        selectedAnggotaDiv.classList.add('flex');
        anggotaResults.classList.add('hidden');
    }

    if (removeAnggotaBtn) {
        removeAnggotaBtn.onclick = () => {
            idPenggunaInput.value = '';
            searchAnggotaInput.value = ''; // Clear search text
            searchAnggotaInput.parentElement.classList.remove('hidden');
            selectedAnggotaDiv.classList.add('hidden');
            selectedAnggotaDiv.classList.remove('flex');
        };
    }


    // --- 2. BOOK SEARCH LOGIC ---
    const searchBukuInput = document.getElementById('searchBuku');
    const bukuResults = document.getElementById('bukuResults');
    const selectedBukuParams = document.getElementById('selectedBukuParams');
    const emptyBukuRow = document.getElementById('emptyBukuRow');

    let bukuTimeout = null;
    let selectedBooks = [];

    // Validasi form sebelum submit
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', (e) => {
            if (selectedBooks.length === 0) {
                e.preventDefault();
                alert('Pilih minimal satu buku untuk dipinjam.');
            }
        });
    }

    if (searchBukuInput) {
        searchBukuInput.addEventListener('input', (e) => {
            clearTimeout(bukuTimeout);
            const query = e.target.value.trim();

            if (query.length < 1) {
                bukuResults.classList.add('hidden');
                return;
            }

            bukuTimeout = setTimeout(async () => {
                try {
                    const response = await fetch(`/buku?search=${encodeURIComponent(query)}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const json = await response.json();

                    renderBukuResults(json.data);
                } catch (error) {
                    console.error('Error fetching books:', error);
                }
            }, 300);
        });

        document.addEventListener('click', (e) => {
            if (!searchBukuInput.contains(e.target) && !bukuResults.contains(e.target)) {
                bukuResults.classList.add('hidden');
            }
        });
    }

    function renderBukuResults(books) {
        bukuResults.innerHTML = '';
        if (books.length === 0) {
            bukuResults.innerHTML = `<div class="p-3 text-sm text-slate-500 text-center">Tidak ada buku ditemukan.</div>`;
        } else {
            books.forEach(book => {
                // Check if already selected
                const isSelected = selectedBooks.some(b => b.id_buku === book.id_buku);
                if (isSelected) return; // Skip if already added

                // Check stock
                const hasStock = book.stok_tersedia > 0;

                const div = document.createElement('div');
                div.className = `p-3 border-b border-slate-100 dark:border-white/5 last:border-0 flex items-center justify-between group ${hasStock ? 'hover:bg-slate-50 dark:hover:bg-white/5 cursor-pointer' : 'opacity-50 cursor-not-allowed bg-slate-50 dark:bg-white/5'}`;
                div.innerHTML = `
                    <div class="flex items-center gap-3">
                        <div class="bg-primary/10 dark:bg-white/10 size-10 flex items-center justify-center rounded text-xs font-bold text-primary dark:text-white">
                            ${book.judul.substring(0, 2).toUpperCase()}
                        </div>
                        <div>
                            <div class="font-bold text-slate-800 dark:text-white text-sm line-clamp-1">${book.judul}</div>
                            <div class="text-xs text-slate-500 dark:text-white/60">${book.penulis}</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-bold ${hasStock ? 'text-green-600' : 'text-red-500'}">Stok: ${book.stok_tersedia}</span>
                    </div>
                `;

                if (hasStock) {
                    div.onclick = () => selectBuku(book);
                }

                bukuResults.appendChild(div);
            });

            // If all results filtered out
            if (bukuResults.children.length === 0) {
                bukuResults.innerHTML = `<div class="p-3 text-sm text-slate-500 text-center">Buku sudah dipilih atau tidak ada stok.</div>`;
            }
        }
        bukuResults.classList.remove('hidden');
    }

    function selectBuku(book) {
        selectedBooks.push(book);
        renderSelectedBooks();
        searchBukuInput.value = '';
        bukuResults.classList.add('hidden');
    }

    window.removeBuku = function (id_buku) {
        selectedBooks = selectedBooks.filter(b => b.id_buku !== id_buku);
        renderSelectedBooks();
    }

    function renderSelectedBooks() {
        if (selectedBooks.length === 0) {
            selectedBukuParams.innerHTML = '';
            selectedBukuParams.appendChild(emptyBukuRow);
            return;
        }

        selectedBukuParams.innerHTML = '';
        selectedBooks.forEach((book, index) => {
            const tr = document.createElement('tr');
            tr.className = 'group hover:bg-slate-50 dark:hover:bg-white/5';
            tr.innerHTML = `
                <td class="p-3 pl-4">
                    <div class="font-bold text-slate-800 dark:text-white text-sm">${book.judul}</div>
                    <div class="text-xs text-slate-500 dark:text-white/60">${book.penulis}</div>
                    <input type="hidden" name="buku[${index}][id_buku]" value="${book.id_buku}">
                </td>
                <td class="p-3 text-sm text-slate-600 dark:text-white/80">${book.stok_tersedia}</td>
                <td class="p-3 text-right pr-4">
                    <button type="button" onclick="removeBuku('${book.id_buku}')" class="text-red-500 hover:text-red-700 p-1 rounded hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                        <span class="material-symbols-outlined text-lg">delete</span>
                    </button>
                </td>
            `;
            selectedBukuParams.appendChild(tr);
        });
    }
});
