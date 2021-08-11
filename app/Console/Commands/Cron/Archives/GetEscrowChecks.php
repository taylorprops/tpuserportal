<?php

namespace App\Console\Commands\Cron\Archives;

use Illuminate\Console\Command;
use App\Jobs\Cron\Archives\GetEscrowChecksJob;

class GetEscrowChecks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'archives:get_escrow_checks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get escrow checks from old server';

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
        GetEscrowChecksJob::dispatch();
    }
}
