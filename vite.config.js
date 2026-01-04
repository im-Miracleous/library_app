import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/theme-toggle.js',
                'resources/js/system-status.js',
                'resources/js/global-search.js',

                // Logic (Modals)
                'resources/js/logic/modals/buku-modal.js',
                'resources/js/logic/modals/anggota-modal.js',
                'resources/js/logic/modals/kepegawaian-modal.js',
                'resources/js/logic/modals/pengunjung.js',

                // Logic (Special Pages)
                'resources/js/logic/special-pages/pengaturan.js',
                'resources/js/logic/special-pages/profile.js',

                // Transactions
                'resources/js/transactions/peminjaman-create.js',
                'resources/js/transactions/peminjaman-show.js',
                'resources/js/transactions/pengembalian.js',

                // Reports
                'resources/js/reports/laporan-denda.js',

                // Live Searches
                'resources/js/live-search/live-search-anggota.js',
                'resources/js/live-search/live-search-buku.js',
                'resources/js/live-search/live-search-kategori.js',
                'resources/js/live-search/live-search-kepegawaian.js',
                'resources/js/live-search/live-search-laporan-denda.js',
                'resources/js/live-search/live-search-laporan-transaksi.js',
                'resources/js/live-search/live-search-pengunjung.js',
                'resources/js/live-search/live-search-sirkulasi-peminjaman.js',
                'resources/js/live-search/live-search-sirkulasi-pengembalian.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        hmr: {
            host: 'localhost',
        },
        watch: {
            usePolling: true,
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
