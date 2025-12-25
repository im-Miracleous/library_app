
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchAnggotaInput');
    const tableBody = document.querySelector('tbody');
    let timeout = null;

    if (!searchInput || !tableBody) return;

    searchInput.addEventListener('input', function (e) {
        clearTimeout(timeout);
        tableBody.style.opacity = '0.5';

        timeout = setTimeout(() => {
            const query = e.target.value;
            fetchUsers(query);
        }, 300);
    });

    async function fetchUsers(query) {
        try {
            // Tambahkan filter &peran=anggota secara eksplisit
            const response = await fetch(`/api/pengguna?search=${encodeURIComponent(query)}&peran=anggota`);
            const json = await response.json();

            if (json.status === 'success') {
                renderTable(json.data);
            }
        } catch (error) {
            console.error('Error fetching users:', error);
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

    function renderTable(users) {
        tableBody.innerHTML = '';
        const searchQuery = searchInput.value.trim();

        if (users.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="p-8 text-center text-slate-400 dark:text-white/40">
                        Tidak ada data anggota.
                    </td>
                </tr>
            `;
            return;
        }

        users.forEach(user => {
            const namaHighlighted = highlightText(user.nama, searchQuery);
            const emailHighlighted = highlightText(user.email, searchQuery);
            const idHighlighted = highlightText(user.id_pengguna, searchQuery);

            const statusClass = user.status === 'aktif'
                ? 'bg-green-100 dark:bg-green-500/10 text-green-700 dark:text-green-500'
                : 'bg-red-100 dark:bg-red-500/10 text-red-700 dark:text-red-500';

            // Template Row disesuaikan persis dengan index.blade.php
            const row = `
                <tr class="hover:bg-primary/5 dark:hover:bg-white/5 transition-colors group animate-enter">
                    <td class="p-4 pl-6 font-mono text-primary dark:text-accent font-bold">
                        ${idHighlighted}
                    </td>
                    <td class="p-4 font-bold text-slate-800 dark:text-white">
                        ${namaHighlighted}
                    </td>
                    <td class="p-4">
                        ${emailHighlighted}
                    </td>
                    <td class="p-4">
                        ${user.telepon || '-'}
                    </td>
                    <td class="p-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold capitalize ${statusClass}">
                            ${user.status}
                        </span>
                    </td>
                    <td class="p-4 pr-6 text-right flex justify-end gap-2">
                        <button onclick="openEditPengguna('${user.id_pengguna}')" 
                            class="p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-500/20 text-blue-600 dark:text-blue-400 transition-colors"
                            title="Edit">
                            <span class="material-symbols-outlined text-lg">edit</span>
                        </button>
                        <form action="/pengguna/${user.id_pengguna}" method="POST" onsubmit="return confirm('Yakin hapus?');">
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
