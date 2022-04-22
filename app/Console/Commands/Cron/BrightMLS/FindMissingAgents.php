<?php

namespace App\Console\Commands\Cron\BrightMLS;

use Illuminate\Console\Command;
use App\Jobs\Cron\BrightMLS\FindMissingAgentsJob;

class FindMissingAgents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bright_mls:find_missing_agents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find missing agents from brightmls';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        FindMissingAgentsJob::dispatch();
    }
}
