
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchKategoriInput');
    const tableBody = document.querySelector('tbody');
    let timeout = null;

    if (!searchInput || !tableBody) return;

    searchInput.addEventListener('input', function (e) {
        clearTimeout(timeout);
        tableBody.style.opacity = '0.5';

        timeout = setTimeout(() => {
            const query = e.target.value;
            fetchKategori(query);
        }, 300);
    });

    async function fetchKategori(query) {
        try {
            const response = await fetch(`/api/kategori?search=${encodeURIComponent(query)}`);
            const json = await response.json();

            if (json.status === 'success') {
                renderTable(json.data);
            }
        } catch (error) {
            console.error('Error fetching categories:', error);
        } finally {
            tableBody.style.opacity = '1';
        }
    }

    function highlightText(text, query) {
        if (!query || !text) return text;
        const safeQuery = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        const regex = new RegExp(`(${safeQuery})`, 'gi');
        return text.toString().replace(regex, '<span class="bg-yellow-200 dark:bg-yellow-600/50 text-slate-900 dark:text-white rounded px-0.5">$1</span>');
    }

    function renderTable(categories) {
        tableBody.innerHTML = '';
        const searchQuery = searchInput.value.trim();

        if (categories.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="4" class="p-8 text-center text-slate-400 dark:text-white/40">
                         Kategori tidak ditemukan.
                    </td>
                </tr>
            `;
            return;
        }

        categories.forEach(item => {
            const namaHighlighted = highlightText(item.nama_kategori, searchQuery);

            // Asumsi struktur tabel di index.blade.php Kategori
            const row = `
                <tr class="hover:bg-primary/5 dark:hover:bg-white/5 transition-colors group animate-enter">
                    <td class="p-4 pl-6 font-mono text-primary dark:text-accent font-bold text-xs">
                        ${item.id_kategori}
                    </td>
                    <td class="p-4 font-bold text-slate-800 dark:text-white group-hover:text-primary dark:group-hover:text-accent transition-colors">
                        ${namaHighlighted}
                    </td>
                    <td class="p-4 text-slate-600 dark:text-white/70 text-sm">
                        ${item.deskripsi || '-'}
                    </td>
                    <td class="p-4 pr-6 text-right flex justify-end gap-2">
                        <button onclick="openEditKategori('${item.id_kategori}', '${item.nama_kategori}', '${item.deskripsi || ''}')" 
                            class="p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-500/20 text-blue-600 dark:text-blue-400 transition-colors"
                            title="Edit">
                            <span class="material-symbols-outlined text-lg">edit</span>
                        </button>
                        <form action="/kategori/${item.id_kategori}" method="POST" onsubmit="return confirm('Yakin hapus kategori ini?');" class="inline">
                            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" 
                                class="p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-500/20 text-red-600 dark:text-red-400 transition-colors"
                                title="Hapus">
                                <span class="material-symbols-outlined text-lg">delete</span>
                            </button>
                        </form>
                    </td>
                </tr>
            `;
            tableBody.innerHTML += row;
        });
    }
});
