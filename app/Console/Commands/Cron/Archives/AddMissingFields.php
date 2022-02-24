<?php

namespace App\Console\Commands\Cron\Archives;

use App\Jobs\Cron\Archives\AddMissingFieldsJob;
use Illuminate\Console\Command;

class AddMissingFields extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'archives:add_missing_fields';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add agent and property details from json columns';

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
        AddMissingFieldsJob::dispatch();
    }
}
