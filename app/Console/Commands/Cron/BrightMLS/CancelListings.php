<?php

namespace App\Console\Commands\Cron\BrightMLS;

use Illuminate\Console\Command;
use App\Jobs\Cron\BrightMLS\CancelListingsJob;

class CancelListings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bright_mls:cancel_listings {temp?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark listings cancelled';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        CancelListingsJob::dispatch();
    }
}
