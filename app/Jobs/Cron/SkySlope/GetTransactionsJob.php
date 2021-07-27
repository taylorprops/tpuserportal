<?php

namespace App\Jobs\Cron\SkySlope;

use Illuminate\Http\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Models\DocManagement\SkySlope\Transactions;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class GetTransactionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this -> onQueue('get_transactions');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this -> get_transactions();
    }

    public function get_transactions() {

        $auth = $this -> skyslope_auth();
        $session = $auth['Session'];
        $headers = [
            'Content-Type' => 'application/json',
            'Session' => $session
        ];

        $createdAfter = str_replace(' ', 'T', date('Y-m-d H:i:s', strtotime('-12 hours')));
        //$createdBefore = str_replace(' ', 'T', date('Y-m-d H:i:s', strtotime('+1 day')));

        $query = [
            'createdAfter' => $createdAfter,
            //'createdBefore' => $createdBefore,
            'type' => 'all'
        ];

        $client = new \GuzzleHttp\Client([
            'headers' => $headers,
            'query' => $query
        ]);

        $response = $client -> request('GET', 'https://api.skyslope.com/api/files');

        $contents = $response -> getBody() -> getContents();
        $contents = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $contents);
        $contents = json_decode($contents, true);
        $data = $contents['value'];


        foreach($data as $transaction) {

            if($transaction['objectType'] != 'summary') {

                if($transaction['objectType'] == 'sale') {
                    $add_transaction = Transactions::firstOrCreate([
                        'saleGuid' => $transaction['saleGuid'],
                        'listingGuid' => $transaction['listingGuid']
                    ]);
                    $key = 'saleGuid';
                } else if($transaction['objectType'] == 'listing') {
                    $add_transaction = Transactions::firstOrCreate([
                        'listingGuid' => $transaction['listingGuid']
                    ]);
                    $key = 'listingGuid';
                }

                foreach($transaction as $col => $value) {

                    if($col != $key) {


                        if(is_array($value)) {

                            if(count($value) == 0) {
                                $value = [];
                            }
                            $value = json_encode($value);

                        }
                        if($col == 'isOfficeLead') {
                            $value = $value == 'true' ? 1 : 0;
                        }

                        $add_transaction -> $col = $value;

                    }

                }

                $add_transaction -> save();

            }

        }

    }

    public function skyslope_auth() {

        $timestamp = str_replace(' ', 'T', gmdate('Y-m-d H:i:s')).'Z';

        $key = config('global.skyslope_key');
        $client_id = config('global.skyslope_client_id');
        $client_secret = config('global.skyslope_client_secret');
        $secret = config('global.skyslope_secret');

        $str = $client_id.':'.$client_secret.':'.$timestamp;

        $hmac = base64_encode(hash_hmac('sha256', $str, $secret, true));

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'SS '.$key.':'.$hmac,
            'Timestamp' => $timestamp
        ];

        $json = [
            'clientID' => $client_id,
            'clientSecret' => $client_secret
        ];

        $client = new \GuzzleHttp\Client([
            'headers' => $headers,
            'json' => $json
        ]);

        $r = $client -> request('POST', 'https://api.skyslope.com/auth/login');
        $response = $r -> getBody() -> getContents();

        return json_decode($response, true);

    }

}
