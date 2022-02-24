<?php

namespace App\Console\Commands\OldDB\SkySlope;

use App\Jobs\OldDB\SkySlope\AddSkySlopeListingsJob;
use Illuminate\Console\Command;

class AddSkySlopeListings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'old_db:add_skyslope_listings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update skyslope listings on old server';

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
        AddSkySlopeListingsJob::dispatch();
    }
}
