<?php

namespace App\Jobs\Cron\Archives;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use romanzipp\QueueMonitor\Traits\IsMonitored;
use App\Models\DocManagement\Archives\Documents;

class AddMissingMlsCompanyDocumentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this -> onQueue('add_mls_company_missing_documents');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this -> add_missing_documents();
    }

    public function add_missing_documents() {

        $progress = 0;
        $this -> queueProgress($progress);

        $documents = Documents::where(function($query) {
            $query -> whereNull('file_exists')
            -> orWhere('file_exists', '');
        })
        -> whereNotNull('doc_type')
        -> limit(1000) -> get();

        if(count($documents) > 0) {

            foreach($documents as $document) {

                $exists = 'no';
                $missing = [];

                if(Storage::exists($document -> file_location)) {

                    $exists = 'yes';

                } else {

                    $missing[] = $document -> id;

                }

                $document -> file_exists = $exists;
                $document -> save();

                $progress += .1;
                $this -> queueProgress($progress);

                $this -> queueData(['missing' => $missing], true);

            }

        } else {

            $this -> queueData(['complete' => 'yes']);

        }

        $this -> queueProgress(100);

    }

}
