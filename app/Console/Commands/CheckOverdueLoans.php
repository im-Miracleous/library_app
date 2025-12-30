<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckOverdueLoans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loans:check-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the database cursor to check for overdue loans and generate notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for overdue loans...');

        $overdueLoans = \App\Models\Peminjaman::where('status_transaksi', 'berjalan')
            ->where('tanggal_jatuh_tempo', '<', now()->toDateString())
            ->with('pengguna')
            ->get();

        $count = 0;
        foreach ($overdueLoans as $loan) {
            if ($loan->pengguna) {
                // Check if already notified recently to avoid spam (Simple check: looked at unread notifications)
                // For now, we will just notify. In prod, maybe check if notification already exists for today.

                $loan->pengguna->notify(new \App\Notifications\PeminjamanJatuhTempo($loan));
                $count++;
            }
        }

        $this->info("Success! Sent {$count} notifications for overdue loans.");
    }
}
