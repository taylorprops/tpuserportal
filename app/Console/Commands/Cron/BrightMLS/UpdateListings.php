<?php

namespace App\Console\Commands\Cron\BrightMLS;

use Illuminate\Console\Command;

class UpdateListings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bright_mls:update_listings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Bright Listings';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        UpdateListingsJob::dispatch();
    }
}
