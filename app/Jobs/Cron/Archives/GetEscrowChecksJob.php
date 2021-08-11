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
use App\Models\DocManagement\Archives\EscrowChecks;

class GetEscrowChecksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this -> onQueue('get_escrow_checks');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this -> get_checks();
    }

    public function get_checks() {

        $progress = 1;
        $this -> queueProgress($progress);

        $checks = EscrowChecks::where('downloaded', 'no')
        -> with(['escrow', 'escrow.transaction_skyslope:transactionId,mlsNumber,listingGuid,saleGuid', 'escrow.transaction_company:transactionId,mlsNumber,listingGuid,saleGuid'])
        -> inRandomOrder()
        -> limit(100)
        -> get();

        $progress_increment = round((1 / count($checks)) * 100);

        foreach ($checks as $check) {

            $transaction = null;
            if($check -> escrow -> transaction_skyslope) {
                $transaction = $check -> escrow -> transaction_skyslope;
            } else {
                $transaction = $check -> escrow -> transaction_company;
            }

            if($transaction) {

                $listingGuid = '';
                $saleGuid = '';
                if($transaction -> listingGuid != 0) {
                    $listingGuid = $transaction -> listingGuid;
                }
                if($transaction -> saleGuid != 0) {
                    $saleGuid = $transaction -> saleGuid;
                }

                $url = $check -> url;

                if($url) {

                    $file_name = basename($url);
                    $file_contents = file_get_contents($url);

                    $dir = 'doc_management/archives/'.$listingGuid . '_' . $saleGuid;
                    if(!Storage::exists($dir)) {
                        Storage::makeDirectory($dir);
                    }
                    $dir = 'doc_management/archives/'.$listingGuid . '_' . $saleGuid.'/escrow';
                    if(!Storage::exists($dir)) {
                        Storage::makeDirectory($dir);
                    }
                    Storage::put($dir.'/'.$file_name, $file_contents);

                    $check -> file_location = $dir.'/'.$file_name;

                }

                $check -> downloaded = 'yes';
                $check -> save();

                $progress += $progress_increment;
                $this -> queueProgress($progress);

            }

        }

        $this -> queueProgress(100);

    }

}
