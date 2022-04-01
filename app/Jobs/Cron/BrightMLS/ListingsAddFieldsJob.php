<?php

namespace App\Jobs\Cron\BrightMLS;

use App\Helpers\Helper;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use App\Models\BrightMLS\BrightListings;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class ListingsAddFieldsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this -> onQueue('bright_add_fields');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $this -> queueProgress(0);

        ini_set('memory_limit', '-1');

        $rets = Helper::rets_login();

        if($rets) {

            $listing_keys = BrightListings::select('ListingKey')
            -> whereNull('ModificationTimestamp')
            -> orderBy('MlsListDate', 'DESC')
            -> limit('5000') -> pluck('ListingKey')
            -> toArray();

            if(count($listing_keys) > 0) {

                $resource = "Property";
                $class = "ALL";

                $query = 'ListingKey='.implode(',', $listing_keys);

                $results = $rets -> Search(
                    $resource,
                    $class,
                    $query,
                    [
                        'Select' => 'ListingKey, ModificationTimestamp'
                    ]
                );


                $listings = $results -> toArray();
                //dd(count($listings));
                $this -> queueData(['Found:' => count($listings)], true);

                $increment = 100 / count($listings);
                $progress = 0;
                $listings_found = [];
                foreach($listings as $listing) {
                    $listings_found[] = $listing['ListingKey'];
                    if($listing['ModificationTimestamp'] != '') {
                        BrightListings::find($listing['ListingKey'])
                        -> update([
                            'ModificationTimestamp' => $listing['ModificationTimestamp']
                        ]);
                    }

                    $progress += $increment;
                    $this -> queueProgress($progress);

                }

                $missing = array_diff($listing_keys, $listings_found);
                $still_missing = BrightListings::whereNull('ModificationTimestamp') -> count();
                $this -> queueData(['Still Missing:' => $still_missing], true);
                $this -> queueData(['Missing:' => $missing], true);

                $this -> queueProgress(100);

                $rets -> Disconnect();

            }

            $rets -> Disconnect();
            $this -> queueData(['Process Complete:' => 'No more to update'], true);

            return true;

        }

        return response() -> json(['failed' => 'login failed']);

    }
}
