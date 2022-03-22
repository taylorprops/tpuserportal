<?php

namespace App\Console\Commands\Backups;

use Illuminate\Console\Command;
use App\Jobs\Backups\TransferToOffsiteJob;

class TransferToOffsite extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backups:transfer_to_offsite';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfer all backups to staging server';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        TransferToOffsiteJob::dispatch();
    }
}
