<?php

namespace App\Jobs\Cron\BrightMLS;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CancelListingsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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

        if($rets) {
            $statuses =['ACTIVE UNDER CONTRACT', 'ACTIVE', 'TEMP OFF MARKET', 'PENDING'];
            $listings = BrightListings::select('ListingKey')
            -> whereIn('MlsStatus', $statuses)
            -> where('updated_at', '<', date('Y-m-d'))
            -> limit(5000)
            -> pluck('ListingKey')
            -> toArray();

            BrightListings::whereIn('ListingKey', $listings)
            -> update([
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $this -> queueProgress(10);

            $resource = "Property";
            $class = "ALL";

            $query = 'ListingKey='.implode(',', $listings);

            $results = $rets -> Search(
                $resource,
                $class,
                $query,
                [
                    'Select' => 'ListingKey'
                ]
            );

            $listings = $results -> toArray();

            $this -> queueProgress(30);

            $increment = 70 / count($listings);
            $progress = 30;

            foreach($listings as $listing) {

                BrightListings::find($listing['ListingKey'])
                -> update([
                    'MlsStatus' => 'CANCELED'
                ]);

                $progress += $increment;
                $this -> queueProgress($progress);

            }

            $this -> queueProgress(100);

            $rets -> Disconnect();

            return true;

        }

        return response() -> json(['failed' => 'login failed']);

    }
}
