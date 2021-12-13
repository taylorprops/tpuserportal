<?php

namespace App\Console\Commands\Cron\BrightMLS;

use Illuminate\Console\Command;
use App\Jobs\Cron\BrightMLS\UpdateAgentsAndOfficesJob;

class UpdateAgentsAndOffices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bright_mls:update_agents_and_offices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Bright Agents and Offices';

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
        UpdateAgentsAndOfficesJob::dispatch();
    }
}
