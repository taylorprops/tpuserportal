<?php

namespace App\Console\Commands\Cron\BrightMLS;

use Illuminate\Console\Command;

class UpdateOffices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bright_mls:update_offices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update BrightMLS Offices';

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
        UpdateOfficesJob::dispatch();
    }
}
