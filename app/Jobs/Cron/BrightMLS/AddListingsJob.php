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

        $this -> queueProgress(0);

        ini_set('memory_limit', '-1');

        $dates = DatabaseDates::where('job', 'add_listings') -> first();
        // $start = $dates -> start_date;


        /* if($start > date('Y-m-d')) {
            $message = [
                'company' => 'Taylor Properties',
                'subject' => 'Add listings job has completed',
                'from' => ['email' => 'internal@taylorprops.com', 'name' => 'Taylor Properties'],
                'body' => 'Add listings job has completed',
                'attachments' => null
            ];
            Mail::to(['miketaylor0101@gmail.com'])
            -> send(new EmailGeneral($message));
            return false;
        } */

        // $days = 3;
        // if($start < '2021-01-01') {
        //     $days = '6';
        // }
        // $end = date('Y-m-d', strtotime($start.' +'.$days.' day'));


        $end = $dates -> start_date;
        $start = date('Y-m-d', strtotime($end.' -5 day'));

        if($start > '2020-01-01') {

            $this -> queueData([
                'Start:' => $start,
                'End:' => $end
            ], true);

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

                        // BrightListings::firstOrCreate(
                        //     ['ListingKey' => $listing['ListingKey']],
                        //     $data
                        // );

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

                    $dates -> start_date = $start;
                    $dates -> save();

                }

                $rets -> Disconnect();

                return true;

            }


        } else {
            $dates -> start_date = date('Y-m-d', strtotime('+1 day'));
            $dates -> save();
        }

        return response() -> json(['failed' => 'login failed']);

    }

}
