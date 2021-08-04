<?php

namespace App\Console\Commands\Cron\Archives;

use Illuminate\Console\Command;
use App\Jobs\Cron\Archives\GetTransactionsJob;

class GetTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'archives:get_transactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get all transactions from skyslope';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        GetTransactionsJob::dispatch();
    }
}
