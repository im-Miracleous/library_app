document.addEventListener('DOMContentLoaded', () => {
    // Selectors
    const dbStatusDot = document.getElementById('sidebar-db-dot');
    const dbStatusText = document.getElementById('sidebar-db-text');
    const serverStatusDot = document.getElementById('sidebar-server-dot');
    const serverStatusText = document.getElementById('sidebar-server-text');

    if (!dbStatusDot || !serverStatusDot) return; // Exit if elements not found

    // Helper: Update UI
    function updateStatusUI(type, isOnline) {
        const dot = type === 'db' ? dbStatusDot : serverStatusDot;
        const text = type === 'db' ? dbStatusText : serverStatusText;

        // Dot Classes
        const activeClass = 'bg-emerald-500';
        const errorClass = 'bg-red-500';
        const animateClass = 'animate-pulse';

        // Text Content
        const onlineText = type === 'db' ? 'DB Connected' : 'Server Online';
        const offlineText = type === 'db' ? 'DB Error' : 'Server Down';

        // Initial/Neutral Classes to Remove
        const neutralDotClass = 'bg-slate-300';
        const neutralTextClass = 'text-slate-400';

        if (isOnline) {
            dot.classList.remove(errorClass, animateClass, neutralDotClass);
            dot.classList.add(activeClass);
            if (text) {
                text.textContent = onlineText;
                text.classList.remove('text-red-500', neutralTextClass);
                text.classList.add('text-emerald-500');
            }
        } else {
            dot.classList.remove(activeClass, neutralDotClass);
            dot.classList.add(errorClass, animateClass);
            if (text) {
                text.textContent = offlineText;
                text.classList.remove('text-emerald-500', neutralTextClass);
                text.classList.add('text-red-500');
            }
        }
    }

    // Polling Function
    function checkSystemStatus() {
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 10000); // Increase to 10s

        fetch('/api/system-status', { signal: controller.signal })
            .then(response => {
                clearTimeout(timeoutId);
                if (!response.ok) throw new Error('Network error');
                return response.json();
            })
            .then(data => {
                updateStatusUI('db', data.db_status);
                updateStatusUI('server', true);
            })
            .catch(error => {
                // Only mark as offline if it's truly a failure, not just a slow response sometimes
                console.warn('System check failed:', error.message);
                if (error.name === 'AbortError') {
                    // If it just timed out, maybe don't immediately turn it red
                    // to avoid "flickering" on slow local dev environments
                    return;
                }
                updateStatusUI('db', false);
                updateStatusUI('server', false);
            });
    }

    // Initial Check & Interval
    checkSystemStatus();
    setInterval(checkSystemStatus, 15000); // Check every 15 seconds instead of 10
});
