<?php

namespace App\Console\Commands\Cron\BrightMLS;

use Illuminate\Console\Command;
use App\Jobs\Cron\BrightMLS\ListingsAddFieldsJob;

class ListingsAddFields extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bright_mls:add_fields';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add missing fields to bright listings';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        ListingsAddFieldsJob::dispatch();
    }
}
