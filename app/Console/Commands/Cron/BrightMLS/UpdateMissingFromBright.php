<?php

namespace App\Console\Commands\Cron\BrightMLS;

use Illuminate\Console\Command;
use App\Jobs\Cron\BrightMLS\UpdateMissingFromBrightJob;

class UpdateMissingFromBright extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bright_mls:update_missing_from_bright {temp?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        UpdateMissingFromBrightJob::dispatch();
    }
}
