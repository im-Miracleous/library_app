window.toggleBookmark = async function (event, id) {
    event.stopPropagation();
    const btn = document.getElementById(`bookmark-${id}`);
    const icon = btn.querySelector('span');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    if (!csrfToken) {
        console.error('CSRF token not found');
        return;
    }

    try {
        const response = await fetch(`/member/buku/${id}/bookmark`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.status === 'added') {
            icon.classList.add('filled');
            icon.style.fontVariationSettings = "'FILL' 1";
        } else {
            icon.classList.remove('filled');
            icon.style.fontVariationSettings = "'FILL' 0";

            // If we are on the bookmarks filter page, we might want to hide the card
            if (window.location.search.includes('filter=bookmarks')) {
                const card = btn.closest('.group');
                card.style.opacity = '0';
                card.style.transform = 'scale(0.9)';
                setTimeout(() => card.remove(), 300);
            }
        }
    } catch (error) {
        console.error('Error toggling bookmark:', error);
    }
}
