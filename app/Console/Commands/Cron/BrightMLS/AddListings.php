<?php

namespace App\Console\Commands\Cron\BrightMLS;

use Illuminate\Console\Command;
use App\Jobs\Cron\BrightMLS\AddListingsJob;

class AddListings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bright_mls:add_listings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add Bright Listings';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        AddListingsJob::dispatch();
    }
}
