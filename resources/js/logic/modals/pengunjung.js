window.openEditPengunjung = function (item) {
    const namaInput = document.getElementById('edit_nama');
    const jenisSelect = document.getElementById('edit_jenis');
    const keperluanInput = document.getElementById('edit_keperluan');
    const form = document.getElementById('editForm');

    if (namaInput) namaInput.value = item.nama_pengunjung;
    if (jenisSelect) jenisSelect.value = item.jenis_pengunjung;
    if (keperluanInput) keperluanInput.value = item.keperluan || '';

    if (form) {
        form.action = `/pengunjung/${item.id_pengunjung}`;
    }

    if (window.openModal) {
        window.openModal('editModal');
    } else {
        const modal = document.getElementById('editModal');
        if (modal) modal.classList.remove('opacity-0', 'pointer-events-none');
    }
};
