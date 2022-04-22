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

    public $tries = 10;

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

        try {

            $this -> queueProgress(0);

            ini_set('memory_limit', '-1');

            $rets = Helper::rets_login();

            if($rets) {

                $this -> queueData(['Status' => 'Logged into Rets'], true);

                $statuses =['ACTIVE UNDER CONTRACT', 'ACTIVE', 'TEMP OFF MARKET', 'PENDING', 'EXPIRED'];

                $count_db_listings = BrightListings::select('ListingKey')
                -> whereIn('MlsStatus', $statuses)
                -> count();

                $this -> queueData(['All Active Count' => $count_db_listings], true);

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

                    $this -> queueData(['DB Listings' => count($db_listings)], true);
                    $this -> queueData(['ListingKeys' => count($ListingKeys)], true);

                    $missing = array_diff($db_listings, $ListingKeys);

                    $this -> queueData(['Found - '.count($missing) => $missing], true);

                    BrightListings::whereIn('ListingKey', $missing)
                    -> update([
                        'MlsStatus' => 'CANCELED',
                        'ModificationTimestamp' => date('Y-m-d H:i:s')
                    ]);

                    $this -> queueProgress(90);

                }

                $this -> queueProgress(100);

                $rets -> Disconnect();

                return;

            }

            return response() -> json(['failed' => 'login failed']);

        } catch (\Throwable $exception) {
            $this -> queueData(['Failed' => 'Retrying'], true);
            $this -> release(90);
            return;
        }

    }
}
