document.addEventListener('DOMContentLoaded', () => {
    const checkAll = document.getElementById('checkAll');
    const checkboxes = document.querySelectorAll('.book-checkbox');
    const dendaDisplay = document.getElementById('dendaDisplay');
    const countDisplay = document.getElementById('countDisplay');
    const btnConfirm = document.getElementById('btnConfirm');
    const returnForm = document.getElementById('returnForm');

    // Get variables from global window object or data attributes
    // Recommended: use data attributes on the container or script tag
    const scriptTag = document.getElementById('pengembalian-script');
    const terlambatHari = parseInt(scriptTag.dataset.terlambatHari || 0);
    const dendaPerHari = 1000;

    function calculateTotals() {
        let checkedCount = 0;
        checkboxes.forEach(cb => {
            if (cb.checked) checkedCount++;
        });

        if (countDisplay) {
            countDisplay.textContent = checkedCount;
        }

        // Hitung Denda hanya jika terlambat
        let totalDenda = 0;
        if (terlambatHari > 0) {
            totalDenda = checkedCount * dendaPerHari * terlambatHari;
        }

        // Format Currency
        if (dendaDisplay) {
            dendaDisplay.textContent = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(totalDenda);
        }
    }

    if (checkAll) {
        checkAll.addEventListener('change', (e) => {
            checkboxes.forEach(cb => cb.checked = e.target.checked);
            calculateTotals();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', () => {
            calculateTotals();
            // Update header checkbox
            if (checkAll) {
                checkAll.checked = Array.from(checkboxes).every(c => c.checked);
            }
        });
    });

    // Handle Submit Loading
    if (btnConfirm && returnForm) {
        btnConfirm.addEventListener('click', (e) => {
            e.preventDefault();

            // Disable button & Show Loading
            btnConfirm.disabled = true;
            btnConfirm.innerHTML = `
                <span class="animate-spin material-symbols-outlined">progress_activity</span>
                Memproses...
            `;
            btnConfirm.classList.add('opacity-75', 'cursor-not-allowed');

            // Submit form
            returnForm.submit();
        });
    }

    // Init Logic
    calculateTotals();
});
