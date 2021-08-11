<?php

namespace App\Jobs\Cron\Archives;

use Throwable;
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
        -> limit(1000)
        -> get();

        $this -> queueData([count($checks)], true);

        if(count($checks) > 0) {

            $progress_increment = 1;

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
                        try {
                            $file_contents = file_get_contents($url);
                        } catch (Throwable $e) {
                            $check -> downloaded = 'file_missing';
                            $check -> save();
                            $this -> queueData(['file_missing' => $check -> id], true);
                        }

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

                        if(!file_exists(Storage::path($dir.'/'.$file_name))) {
                            $check -> downloaded = 'download_failed';
                            $check -> save();
                            $this -> queueData(['download_failed' => $check -> id], true);
                            return false;
                        }

                    } else {
                        $check -> downloaded = 'no_url';
                        $check -> save();
                        $this -> queueData(['no_url' => $check -> id], true);
                    }

                    $check -> downloaded = 'yes';
                    $check -> save();

                    $progress += $progress_increment;
                    $this -> queueProgress($progress);

                } else {
                    $check -> downloaded = 'no_transaction';
                    $check -> save();
                    $this -> queueData(['no_transaction' => $check -> id], true);
                }

            }

        }

       //$this -> queueProgress(100);

    }

}
