<?php

namespace App\Console\Commands\Cron\Archives;

use App\Jobs\Cron\Archives\AddMissingMlsCompanyDocumentsJob;
use Illuminate\Console\Command;

class AddMissingMlsCompanyDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'archives:add_mls_company_missing_documents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find and add missing documents from mls_company imports';

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
        AddMissingMlsCompanyDocumentsJob::dispatch();
    }
}
