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
    const dendaRusak = parseInt(scriptTag.dataset.dendaRusak || 0);
    const dendaHilang = parseInt(scriptTag.dataset.dendaHilang || 0);
    const dendaPerHari = 1000;

    function calculateTotals() {
        let checkedCount = 0;
        let totalConditionDenda = 0;

        checkboxes.forEach(cb => {
            if (cb.checked) {
                checkedCount++;

                // Find associated select for condition
                const row = cb.closest('tr');
                const select = row.querySelector('select'); // Assumes select is in the same row
                if (select) {
                    if (select.value === 'rusak') totalConditionDenda += dendaRusak;
                    if (select.value === 'hilang') totalConditionDenda += dendaHilang;
                }
            }
        });

        if (countDisplay) {
            countDisplay.textContent = checkedCount;
        }

        // Hitung Denda Keterlambatan
        let totalDenda = 0;
        if (terlambatHari > 0) {
            totalDenda = checkedCount * dendaPerHari * terlambatHari;
        }

        // Tambah Denda Kondisi
        totalDenda += totalConditionDenda;

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

    // Listen for select changes
    const selects = document.querySelectorAll('select[name^="kondisi"]');
    selects.forEach(select => {
        select.addEventListener('change', () => {
            // Check the checkbox if condition changes (UX: usually if you select condition you imply return)
            const row = select.closest('tr');
            const cb = row.querySelector('.book-checkbox');
            if (cb && !cb.checked) cb.checked = true;

            calculateTotals();
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
