
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchPegawaiInput');
    const tableBody = document.querySelector('tbody');
    let timeout = null;

    if (!searchInput || !tableBody) return;

    searchInput.addEventListener('input', function (e) {
        clearTimeout(timeout);
        tableBody.style.opacity = '0.5';

        timeout = setTimeout(() => {
            const query = e.target.value;
            fetchPegawai(query);
        }, 300);
    });

    async function fetchPegawai(query) {
        try {
            // Menggunakan Endpoint Web yang menerima Request AJAX (sehingga Cookie Auth Session terbawa otomatis)
            const response = await fetch(`/kepegawaian?search=${encodeURIComponent(query)}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const json = await response.json();

            if (json.status === 'success') {
                renderTable(json.data);
            } else if (response.status === 403) {
                console.error('Unauthorized access to Kepegawaian API');
            }
        } catch (error) {
            console.error('Error fetching pegawai:', error);
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
                    <td colspan="7" class="p-8 text-center text-slate-400 dark:text-white/40">
                        Belum ada data pegawai.
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

            const roleClass = user.peran === 'admin'
                ? 'bg-purple-100 text-purple-700'
                : 'bg-orange-100 text-orange-700';

            const row = `
                <tr class="hover:bg-primary/5 dark:hover:bg-white/5 transition-colors group animate-enter">
                    <td class="p-4 pl-6 font-mono text-primary dark:text-accent font-bold">
                        ${idHighlighted}
                    </td>
                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            <div class="size-10 rounded-full bg-primary/20 dark:bg-accent/20 flex items-center justify-center text-primary-dark dark:text-accent font-bold flex-shrink-0">
                                ${user.nama.charAt(0).toUpperCase()}
                            </div>
                            <div class="flex flex-col max-w-[220px]">
                                <span class="font-bold text-slate-800 dark:text-white line-clamp-2 text-sm leading-tight" title="${user.nama}">
                                    ${namaHighlighted}
                                </span>
                                <span class="text-xs text-slate-500 dark:text-white/60 truncate" title="${user.email}">
                                    ${emailHighlighted}
                                </span>
                            </div>
                        </div>
                    </td>
                    <td class="p-4">
                        <span class="px-2 py-1 rounded text-xs font-bold ${roleClass} uppercase">
                            ${user.peran}
                        </span>
                    </td>
                    <td class="p-4">
                        ${user.telepon || '-'}
                    </td>
                    <td class="p-4 max-w-[200px] truncate" title="${user.alamat || '-'}">
                        ${user.alamat || '-'}
                    </td>
                    <td class="p-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold capitalize ${statusClass}">
                            ${user.status}
                        </span>
                    </td>
                    <td class="p-4 pr-6 text-right flex justify-end gap-2">
                        <button onclick='openEditPegawai(${JSON.stringify(user)})' 
                            class="p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-500/20 text-blue-600 dark:text-blue-400 transition-colors"
                            title="Edit">
                            <span class="material-symbols-outlined text-lg">edit</span>
                        </button>
                        <form action="/kepegawaian/${user.id_pengguna}" method="POST" onsubmit="return confirm('Yakin hapus?');">
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
