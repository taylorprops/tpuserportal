<?php

namespace App\Jobs\Cron\BrightMLS;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class UpdateMissingFromBrightJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this -> onQueue('update_missing_from_bright');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ini_set('memory_limit', '-1');

        $rets = Helper::rets_login();

        if($rets) {

            $resource = "Property";
            $class = "ALL";

            $query = 'MlsStatus=|200004325490,200004324452,200004324454,200004324453,200004325494';

            $results = $rets -> Search(
                $resource,
                $class,
                $query,
                [
                    'Select' => config('global.bright_listings_columns')
                ]
            );

            $results = $results -> toArray();

            $bright_results = [];
            foreach($results as $result) {
                $bright_results[] = $result['ListingId'];
            }

            $db_results = BrightListings::select('ListingId')
            -> whereIn('MlsStatus', ['active', 'active under contract', 'pending', 'temp off market', 'expired'])
            -> get()
            -> pluck('ListingId')
            -> toArray();

            $missing_from_bright = array_diff($db_results, $bright_results);
            // dd($missing_from_bright);

            BrightListings::whereIn('ListingKey', $missing_from_bright)
            -> update([
                'MlsStatus' => 'CANCELED',
                'ModificationTimestamp' => date('Y-m-d H:i:s')
            ]);

            $query = 'ListingKey='.implode(',', $missing_from_bright);

            $results = $rets -> Search(
                $resource,
                $class,
                $query,
                [
                    'Select' => config('global.bright_listings_columns')
                ]
            );

            $results = $results -> toArray();

            foreach($results as $listing) {

                $data = [];
                foreach($listing as $key => $value) {
                    if($value != '') {
                        $data[$key] = $value;
                    }
                }

                BrightListings::firstOrCreate(
                    ['ListingKey' => $listing['ListingKey']],
                    $data
                );

            }

        }
    }
}
