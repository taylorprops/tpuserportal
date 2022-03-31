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

class CancelListingsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this -> onQueue('bright_cancel_listings');
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

        $this -> queueData(['working'], true);

        if($rets) {

            $statuses =['ACTIVE UNDER CONTRACT', 'ACTIVE', 'TEMP OFF MARKET', 'PENDING'];
            $db_listings = BrightListings::select('ListingKey')
            -> whereIn('MlsStatus', $statuses)
            -> where('updated_at', '<', date('Y-m-d'))
            -> limit(5000)
            -> pluck('ListingKey')
            -> toArray();

            $this -> queueData(['DB Listings' => count($db_listings)], true);

            if(count($db_listings) > 0) {

                BrightListings::whereIn('ListingKey', $db_listings)
                -> update([
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                $this -> queueProgress(10);

                $resource = "Property";
                $class = "ALL";

                $query = 'ListingKey='.implode(',', $db_listings);

                $results = $rets -> Search(
                    $resource,
                    $class,
                    $query,
                    [
                        'Select' => 'ListingKey'
                    ]
                );

                $this -> queueProgress(50);

                $bright_listings = $results -> toArray();

                $ListingKeys = [];
                foreach($bright_listings as $listing) {
                    $ListingKeys[] = $listing['ListingKey'];
                }

                $this -> queueProgress(70);

                $missing = array_diff($db_listings, $ListingKeys);

                $this -> queueData(['Found' => count($missing)], true);

                BrightListings::whereIn('ListingKey', $missing)
                -> update([
                    'MlsStatus' => 'CANCELED'
                ]);

                $this -> queueProgress(90);

            }

            $this -> queueProgress(100);

            $rets -> Disconnect();

            return true;

        }

        return response() -> json(['failed' => 'login failed']);

    }
}
