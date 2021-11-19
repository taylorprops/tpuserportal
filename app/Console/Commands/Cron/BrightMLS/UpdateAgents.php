<?php

namespace App\Console\Commands\Cron\BrightMLS;

use Illuminate\Console\Command;
use App\Jobs\Cron\BrightMLS\UpdateAgents as UpdateAgentsJob;

class UpdateAgents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bright_mls:update_agents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update BrightMLS Agents';

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
        UpdateAgentsJob::dispatch();
    }
}
