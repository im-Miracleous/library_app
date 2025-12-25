
// Live Search Logic using API
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.querySelector('tbody');
    let timeout = null;

    if (!searchInput || !tableBody) return;

    searchInput.addEventListener('input', function (e) {
        // Hapus timeout sebelumnya (Debounce)
        clearTimeout(timeout);

        // Tampilkan indikator loading jika mau (opsional)
        tableBody.style.opacity = '0.5';

        // Tunggu 500ms setelah user berhenti mengetik
        timeout = setTimeout(() => {
            const query = e.target.value;
            fetchBooks(query);
        }, 300);
    });

    async function fetchBooks(query) {
        try {
            // Panggil API Buku
            // Kita pakai URL search dari API yang baru kita update
            const response = await fetch(`/api/buku?search=${encodeURIComponent(query)}`);
            const json = await response.json();

            if (json.status === 'success') {
                renderTable(json.data);
            }
        } catch (error) {
            console.error('Error fetching books:', error);
        } finally {
            tableBody.style.opacity = '1';
        }
    }

    function highlightText(text, query) {
        if (!query) return text;

        // Escape karakter khusus regex agar aman
        const safeQuery = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');

        // Cari query (case insensitive 'gi')
        const regex = new RegExp(`(${safeQuery})`, 'gi');

        // Ganti dengan versi yang di-highlight (warna kuning + teks tebal)
        return text.toString().replace(regex, '<span class="bg-yellow-200 dark:bg-yellow-600/50 text-slate-900 dark:text-white rounded px-0.5">$1</span>');
    }

    function renderTable(books) {
        tableBody.innerHTML = '';
        const searchQuery = searchInput.value.trim(); // Ambil query saat ini

        if (books.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="7" class="p-8 text-center text-slate-500 dark:text-white/40">
                        Tidak ada buku yang ditemukan.
                    </td>
                </tr>
            `;
            return;
        }

        books.forEach(book => {
            // Terapkan highlight pada Judul dan Penulis
            const judulHighlighted = highlightText(book.judul, searchQuery);
            const penulisHighlighted = highlightText(book.penulis, searchQuery);

            const row = `
                <tr class="hover:bg-primary/5 dark:hover:bg-white/5 transition-colors group animate-enter">
                    <td class="p-4 pl-6 font-mono text-primary dark:text-accent text-xs font-bold">
                        ${book.id_buku}
                    </td>
                    <td class="p-4">
                        <div class="font-bold text-slate-800 dark:text-white group-hover:text-primary dark:group-hover:text-accent">
                            ${judulHighlighted}
                        </div>
                        <div class="text-xs text-slate-500 dark:text-white/40">
                            ${book.isbn || 'No ISBN'}
                        </div>
                    </td>
                    <td class="p-4">
                        <span class="px-2 py-1 bg-primary/10 dark:bg-white/5 rounded text-xs font-semibold text-primary-dark dark:text-white/80">
                            ${book.kategori ? book.kategori.nama_kategori : '-'}
                        </span>
                    </td>
                    <td class="p-4">${penulisHighlighted}</td>
                    <td class="p-4 text-center">
                        <span class="font-bold ${book.stok_tersedia > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'}">
                            ${book.stok_tersedia}
                        </span>
                        <span class="text-slate-400 dark:text-white/30 text-xs">/${book.stok_total}</span>
                    </td>
                    <td class="p-4">
                        <span class="px-2 py-1 rounded text-xs font-bold uppercase ${book.status === 'tersedia'
                    ? 'text-green-600 dark:text-green-500 bg-green-50 dark:bg-green-500/10'
                    : 'text-red-600 dark:text-red-500 bg-red-50 dark:bg-red-500/10'
                }">
                            ${book.status}
                        </span>
                    </td>
                    <td class="p-4 pr-6 text-right flex justify-end gap-2">
                         <button onclick="openEditBuku('${book.id_buku}')"
                            class="p-2 rounded-lg hover:bg-blue-500/20 text-blue-400 transition-colors"
                            title="Edit"><span class="material-symbols-outlined text-lg">edit</span></button>
                        
                        <!-- Form Delete manual karena JS tidak bisa render Blade langsung dengan mudah (perlu CSRF) -->
                         <button onclick="deleteBukuViaApi('${book.id_buku}')"
                             class="p-2 rounded-lg hover:bg-red-500/20 text-red-400 transition-colors"
                             title="Hapus"><span class="material-symbols-outlined text-lg">delete</span></button>
                    </td>
                </tr>
            `;
            tableBody.innerHTML += row;
        });
    }

    // Fungsi helper global agar tombol delete baru bisa bekerja
    window.deleteBukuViaApi = async function (id) {
        if (!confirm('Yakin hapus buku ini?')) return;

        // Perlu Token untuk Delete? 
        // Karena ini di halaman Web Dashboard (yang login via Session), 
        // kita sebenarnya bisa pakai Route Web DELETE biasa.
        // Tapi jika mau pakai API DELETE, kita butuh Token.
        // Untuk amannya di demo ini, saya arahkan submit form manual saja.

        // Cara JS Submit form Delete standard Laravel:
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/buku/${id}`; // Mengandalkan Route Web Resource

        const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';
        const hiddenCsrf = document.createElement('input');
        hiddenCsrf.type = 'hidden';
        hiddenCsrf.name = '_token';
        hiddenCsrf.value = csrfToken;

        const hiddenMethod = document.createElement('input');
        hiddenMethod.type = 'hidden';
        hiddenMethod.name = '_method';
        hiddenMethod.value = 'DELETE';

        form.appendChild(hiddenCsrf);
        form.appendChild(hiddenMethod);
        document.body.appendChild(form);
        form.submit();
    };
});
