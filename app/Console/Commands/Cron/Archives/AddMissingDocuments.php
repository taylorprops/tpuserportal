<?php

namespace App\Console\Commands\Cron\Archives;

use App\Jobs\Cron\Archives\AddMissingDocumentsJob;
use Illuminate\Console\Command;

class AddMissingDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'archives:add_missing_documents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add missing documents';

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
        AddMissingDocumentsJob::dispatch();
    }
}
