<?php

namespace App\Console\Commands\Cron\BrightMLS;

use Illuminate\Console\Command;
use App\Console\Commands\Cron\BrightMLS\RemoveAgents;

class RemoveAgents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bright_mls:remove_agents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate agents from Bright MLS';

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
        RemoveAgents::dispatch();
    }
}
