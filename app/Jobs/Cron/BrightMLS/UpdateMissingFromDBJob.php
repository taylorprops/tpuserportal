<?php

namespace App\Jobs\Cron\BrightMLS;

use App\Helpers\Helper;
use App\Models\BrightMLS\BrightListings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class UpdateMissingFromDBJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    public $tries = 2;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this -> onQueue('update_missing_from_db');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        // try {

        $this -> queueProgress(0);

        ini_set('memory_limit', '-1');

        $rets = Helper::rets_login();

        $this -> queueData(['Logging into Rets'], true);
        if ($rets) {

            $this -> queueData(['Logged into Rets'], true);
            $resource = "Property";
            $class = "ALL";

            $query = 'MlsStatus=|200004325490,200004324452,200004324454,200004324453,200004325494';

            $results = $rets -> Search(
                $resource,
                $class,
                $query,
                [
                    'Select' => config('global.bright_listings_columns'),
                ]
            );

            if ($results) {
                $results = $results -> toArray();
                $this -> queueData(['Bright Query Complete'], true);
            } else {
                $this -> queueData(['Bright Query Failed, Retrying'], true);
                sleep(5);
                $results = $rets -> Search(
                    $resource,
                    $class,
                    $query,
                    [
                        'Select' => config('global.bright_listings_columns'),
                    ]
                );

                if ($results) {
                    $this -> queueData(['Bright Query Complete'], true);
                } else {
                    $this -> queueData(['Bright Query Failed, Quitting'], true);
                    return false;
                }

            }

            $bright_results = [];
            foreach ($results as $result) {
                $bright_results[] = $result['ListingKey'];
            }

            $db_results = BrightListings::select('ListingKey')
                -> whereIn('MlsStatus', ['active', 'active under contract', 'pending', 'temp off market', 'expired', 'canceled'])
                -> get()
                -> pluck('ListingKey')
                -> toArray();
            $this -> queueData(['db_results complete'], true);

            $missing_from_db = array_diff($bright_results, $db_results);

            $this -> queueData(['Missing:' => count($missing_from_db)], true);

            $query = 'ListingKey='.implode(',', $missing_from_db);

            $results = $rets -> Search(
                $resource,
                $class,
                $query,
                [
                    'Select' => config('global.bright_listings_columns'),
                ]
            );

            $this -> queueData(['Rets Query Complete'], true);
            $results = $results -> toArray();

            foreach ($results as $listing) {

                $data = [];
                foreach ($listing as $key => $value) {
                    if ($value != '') {
                        $data[$key] = $value;
                    }
                }

                BrightListings::firstOrCreate(
                    ['ListingKey' => $listing['ListingKey']],
                    $data
                );

            }

        }
        $this -> queueData(['All Done'], true);

        $this -> queueProgress(100);

        // } catch (\Throwable $exception) {
        //     $this -> queueData(['Failed' => 'Retrying'], true);
        //     $this -> release(180);
        //     return;
        // }

    }
}
