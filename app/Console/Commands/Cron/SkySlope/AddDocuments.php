<?php

namespace App\Console\Commands\Cron\SkySlope;

use Illuminate\Console\Command;
use App\Jobs\Cron\SkySlope\AddDocumentsJob;

class AddDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'skyslope:add_documents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add document details and link to skyslope transactions';

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
        AddDocumentsJob::dispatch();
    }



}
