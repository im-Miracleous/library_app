window.openEditModal = function (item) {
    // Populate Data
    const jumlahInput = document.getElementById('edit_jumlah');
    const keteranganInput = document.getElementById('edit_keterangan');
    const statusSelect = document.getElementById('edit_status_bayar');
    const form = document.getElementById('editForm');

    if (jumlahInput) jumlahInput.value = item.jumlah_denda;
    if (keteranganInput) keteranganInput.value = item.keterangan || '';
    if (statusSelect) statusSelect.value = item.status_bayar;

    // Set Action URL
    if (form) {
        form.action = `/denda/${item.id_denda}`;
    }

    // Use Global Helper for animation
    if (window.openModal) {
        window.openModal('editModal');
    } else {
        // Fallback
        const modal = document.getElementById('editModal');
        if (modal) modal.classList.remove('opacity-0', 'pointer-events-none');
    }
}
