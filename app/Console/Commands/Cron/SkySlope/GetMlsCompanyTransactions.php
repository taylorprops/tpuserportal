<?php

namespace App\Console\Commands\Cron\SkySlope;

use Illuminate\Console\Command;
use App\Jobs\Cron\SkySlope\GetMlsCompanyTransactionsJob;

class GetMlsCompanyTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'skyslope:add_mls_company_transactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add old transactions and docs from mls_company to skyslope db';

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
        GetMlsCompanyTransactionsJob::dispatch();
    }
}
