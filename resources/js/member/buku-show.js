window.toggleBookmark = async function (id) {
    const btn = document.getElementById('btn-bookmark');
    const icon = btn.querySelector('span');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    try {
        const response = await fetch(`/member/buku/${id}/bookmark`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });
        const data = await response.json();

        if (data.status === 'added') {
            icon.classList.add('filled');
            icon.style.fontVariationSettings = "'FILL' 1";
            btn.className = "size-12 rounded-full flex items-center justify-center transition-all duration-300 bg-pink-100 dark:bg-pink-500/20 text-pink-600 dark:text-pink-400 hover:bg-pink-200 dark:hover:bg-pink-500/30";
        } else {
            icon.classList.remove('filled');
            icon.style.fontVariationSettings = "'FILL' 0";
            btn.className = "size-12 rounded-full flex items-center justify-center transition-all duration-300 bg-slate-100 dark:bg-white/10 text-slate-500 dark:text-white/50 hover:bg-slate-200 dark:hover:bg-white/20";
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Gagal mengubah status koleksi');
    }
}

window.addToCart = async function (id, isQuickLoan = false) {
    const btn = isQuickLoan ? document.getElementById('btn-loan-now') : document.getElementById('btn-add-cart');
    const originalText = isQuickLoan ? 'Ajukan Peminjaman' : 'Tambah Ke Keranjang';
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    if (btn.classList.contains('loading')) return;

    // Start Loading
    btn.classList.add('loading');
    btn.disabled = true;
    btn.querySelector('.btn-text').textContent = 'Memproses...';

    try {
        const response = await fetch("/member/keranjang", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ id_buku: id })
        });
        const data = await response.json();

        if (data.status === 'success') {
            if (!isQuickLoan) {
                alert('Berhasil: ' + data.message);
                // Redirect to Catalog instead of Cart
                window.location.href = "/member/buku";
            }
            return true;
        } else {
            alert('Gagal: ' + data.message);
            return false;
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menambahkan ke keranjang');
        return false;
    } finally {
        // Stop Loading
        btn.classList.remove('loading');
        btn.disabled = false;
        btn.querySelector('.btn-text').textContent = originalText;
    }
}

window.loanNow = async function (id) {
    const success = await window.addToCart(id, true);
    if (success) {
        window.location.href = "/member/keranjang";
    }
}
