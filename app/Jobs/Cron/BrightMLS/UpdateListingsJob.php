<?php

namespace App\Jobs\Cron\BrightMLS;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateListingsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this -> onQueue('bright_update_listings');
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

            $resource = "Property";
            $class = "ALL";

            $query = 'MLSListDate='.$start.'-'.$end;

            $results = $rets -> Search(
                $resource,
                $class,
                $query,
                [
                    'Select' => config('global.bright_listings_columns')
                ]
            );


            $listings = $results -> toArray();
            // echo count($listings);
            $this -> queueData(['Found:' => count($listings)], true);

            $increment = 100 / count($listings);
            $progress = 0;
            foreach($listings as $listing) {

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

                $progress += $increment;
                $this -> queueProgress($progress);

            }

            $this -> queueProgress(100);

            $rets -> Disconnect();


            $dates -> start_date = $start;
            $dates -> save();

            return true;

        }

        return response() -> json(['failed' => 'login failed']);

    }

}
