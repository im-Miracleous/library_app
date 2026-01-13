<div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b border-primary/10 dark:border-white/10">
                <th class="p-4 pl-6 font-medium text-slate-500 dark:text-white/60">Peringkat</th>
                <th class="p-4 font-medium text-slate-500 dark:text-white/60">Anggota</th>
                <th class="p-4 font-medium text-slate-500 dark:text-white/60">Email</th>
                <th class="p-4 pr-6 font-medium text-right text-slate-500 dark:text-white/60">Total Transaksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-primary/5 dark:divide-white/5">
            @include('admin.laporan.partials.rows-anggota_top', ['data' => $data])
        </tbody>
    </table>
</div>
