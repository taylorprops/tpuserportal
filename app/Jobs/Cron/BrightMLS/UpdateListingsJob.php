<?php

namespace App\Jobs\Cron\BrightMLS;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class UpdateListingsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

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

            $start = date('Y-m-d H:i:s', strtotime('-1 hour'));
            $start = str_replace(' ', 'T', $start);

            $query = 'ModificationTimestamp='.$start.'+';

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


            return true;

        }

        return response() -> json(['failed' => 'login failed']);

    }

}
