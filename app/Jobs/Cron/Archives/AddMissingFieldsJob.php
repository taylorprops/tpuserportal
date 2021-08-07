<?php

namespace App\Jobs\Cron\Archives;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use romanzipp\QueueMonitor\Traits\IsMonitored;
use App\Models\DocManagement\Archives\Transactions;

class AddMissingFieldsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this -> onQueue('add_missing_fields');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this -> add_missing_fields();
    }

    public function add_missing_fields() {

        $progress = 0;
        $this -> queueProgress($progress);

        $left = Transactions::whereNull('address') -> count();

        $transactions = Transactions::whereNull('address')
        -> orWhere('address', '')
        -> with(['agent_details'])
        //-> inRandomOrder()
        -> limit(4000)
        -> get();

        if(count($transactions) == 0) {
            $this -> queueData(['completed' => 'yes']);
            return false;
        }

        $this -> queueData(['left' => $left]);

        $progress_increment = .5;

        foreach($transactions as $transaction) {

            $property = json_decode($transaction -> property, true);
            $address = $property['streetNumber'];
            if($property['direction'] != '') {
                $address .= ' ' . $property['direction'];
            }
            $address .= ' ' .$property['streetAddress'];
            if($property['unit'] != '') {
                $address .= ' ' . $property['unit'];
            }
            $city = $property['city'];
            $state = $property['state'];
            $zip = $property['zip'];

            $agent_name = '';
            if($transaction -> agent_details) {
                $agent = $transaction -> agent_details;
                $agent_name = $agent -> nickname.' '.$agent -> last;
            }

            $transaction -> address = $address;
            $transaction -> city = $city;
            $transaction -> state = $state;
            $transaction -> zip = $zip;
            $transaction -> agent_name = $agent_name;
            $transaction -> save();

            $progress += $progress_increment;
            if($progress > 99) {
                $progress = 99;
            }
            $this -> queueProgress($progress);

        }

        $this -> queueProgress(100);

    }

}
