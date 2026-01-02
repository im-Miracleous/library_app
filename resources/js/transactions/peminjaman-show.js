window.showApproveModal = function () {
    if (window.openModal) {
        window.openModal('approveModal');
    } else {
        const modal = document.getElementById('approveModal');
        if (modal) modal.classList.remove('hidden');
    }
}

window.closeApproveModal = function () {
    if (window.closeModal) {
        window.closeModal('approveModal');
    } else {
        const modal = document.getElementById('approveModal');
        if (modal) modal.classList.add('hidden');
    }
}

window.submitApprove = function () {
    const btn = document.getElementById('btnApproveConfirm');
    const text = document.getElementById('approveText');
    const spinner = document.getElementById('approveSpinner');

    if (btn) {
        btn.disabled = true;
        btn.classList.add('opacity-75', 'cursor-not-allowed');
    }
    if (text) text.innerText = 'Memproses...';
    if (spinner) spinner.classList.remove('hidden');

    const form = document.getElementById('approveForm');
    if (form) form.submit();
}

window.showRejectModal = function () {
    if (window.openModal) {
        window.openModal('rejectModal');
    } else {
        const modal = document.getElementById('rejectModal');
        if (modal) modal.classList.remove('hidden');
    }
}

window.closeRejectModal = function () {
    if (window.closeModal) {
        window.closeModal('rejectModal');
    } else {
        const modal = document.getElementById('rejectModal');
        if (modal) modal.classList.add('hidden');
    }
}

window.submitReject = function () {
    const btn = document.getElementById('btnRejectConfirm');
    const text = document.getElementById('rejectText');
    const spinner = document.getElementById('rejectSpinner');

    if (btn) {
        btn.disabled = true;
        btn.classList.add('opacity-75', 'cursor-not-allowed');
    }
    if (text) text.innerText = 'Memproses...';
    if (spinner) spinner.classList.remove('hidden');

    const form = document.getElementById('rejectForm');
    if (form) form.submit();
}
