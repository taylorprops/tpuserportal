<?php

namespace App\Jobs\Cron\BrightMLS;

use App\Helpers\Helper;
use Illuminate\Bus\Queueable;
use App\Mail\General\EmailGeneral;
use App\Models\Temp\DatabaseDates;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use App\Models\BrightMLS\BrightListings;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class AddListingsJob implements ShouldQueue
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
        $this -> onQueue('bright_add_listings');
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

                $resource = "Property";
                $class = "ALL";

                $start = date('Y-m-d', strtotime('-1 day'));

                $query = 'MLSListDate='.$start.'+';
                $cols = config('global.bright_listings_columns');
                if(!$cols) {
                    throw new Exception('config global.bright_listings_columns not working');
                    return false;
                }

                $results = $rets -> Search(
                    $resource,
                    $class,
                    $query,
                    [
                        'Select' => $cols
                    ]
                );


                $listings = $results -> toArray();

                $this -> queueData(['Total:' => count($listings)], true);

                if(count($listings) > 0) {

                    $increment = 100 / count($listings);
                    $progress = 0;
                    $found = 0;
                    $not_found = 0;

                    foreach($listings as $listing) {

                        $data = [];
                        foreach($listing as $key => $value) {
                            if($value != '') {
                                $data[$key] = $value;
                            }
                        }

                        $bright = BrightListings::find($listing['ListingKey']);

                        if(!$bright) {
                            $bright = BrightListings::insert($data);
                            $not_found += 1;
                        } else {
                            $bright -> update($data);
                            $found += 1;
                        }

                        $progress += $increment;
                        $this -> queueProgress($progress);

                    }

                    $this -> queueData(['Found:' => $found, 'NotFound:' => $not_found], true);

                    $this -> queueProgress(100);

                }

                $rets -> Disconnect();

                return true;

            }

            return response() -> json(['failed' => 'login failed']);

        } catch (\Throwable $exception) {
            $this -> release(90);
            return;
        }



    }

}
