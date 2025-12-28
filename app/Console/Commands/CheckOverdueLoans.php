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
        $this->info('Running Stored Procedure: sp_cek_keterlambatan...');

        try {
            DB::statement("CALL sp_cek_keterlambatan()");
            $this->info('Success! Notifications generated for overdue loans.');
        } catch (\Exception $e) {
            $this->error('Failed: ' . $e->getMessage());
        }
    }
}
