<?php

namespace App\Jobs\Cron\Archives;

use App\Models\DocManagement\Archives\Transactions;
use App\Models\OldDB\Agents;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class GetTransactionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    // public $backoff = [90];

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

    public function get_transactions()
    {
        $progress = 0;
        $this -> queueProgress($progress);

        $auth = $this -> skyslope_auth();
        $session = $auth['Session'];
        $headers = [
            'Content-Type' => 'application/json',
            'Session' => $session,
        ];

        $createdAfter = str_replace(' ', 'T', date('Y-m-d H:i:s', strtotime('-2 days')));

        $query = [
            'createdAfter' => $createdAfter,
            'type' => 'all',
        ];

        $client = new \GuzzleHttp\Client([
            'headers' => $headers,
            'query' => $query,
        ]);

        $progress = 1;
        $this -> queueProgress($progress);

        $response = $client -> request('GET', 'https://api.skyslope.com/api/files');

        $contents = $response -> getBody() -> getContents();
        $contents = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $contents);
        $contents = json_decode($contents, true);
        $data = $contents['value'];

        $this -> queueData(['Found:' => count($data)], true);

        $progress_increment = (int) round((1 / 15) * 100);

        foreach ($data as $transaction) {
            if ($transaction['objectType'] != 'summary') {
                if ($transaction['objectType'] == 'sale') {
                    $add_transaction = Transactions::firstOrCreate([
                        'saleGuid' => $transaction['saleGuid'],
                        'listingGuid' => $transaction['listingGuid'],
                    ]);
                    $key = 'saleGuid';
                } elseif ($transaction['objectType'] == 'listing') {
                    $add_transaction = Transactions::firstOrCreate([
                        'listingGuid' => $transaction['listingGuid'],
                    ]);
                    $key = 'listingGuid';
                }

                foreach ($transaction as $col => $value) {
                    if ($col != $key) {
                        if (is_array($value)) {
                            if (count($value) == 0) {
                                $value = [];
                            }

                            if ($col == 'agent') {
                                $agentId = $value['publicId'];
                            }

                            $value = json_encode($value);
                        }
                        if ($col == 'isOfficeLead') {
                            $value = $value == 'true' ? 1 : 0;
                        }

                        $add_transaction -> $col = $value;
                    }
                }
                $property = $transaction['property'];

                $address = $property['streetNumber'];
                if ($property['direction'] != '') {
                    $address .= ' '.$property['direction'];
                }
                $address .= ' '.$property['streetAddress'];
                if ($property['unit'] != '') {
                    $address .= ' '.$property['unit'];
                }
                $city = $property['city'];
                $state = $property['state'];
                $zip = $property['zip'];

                $agent_name = '';
                if ($agentId && $agentId != '') {
                    $agent_details = $this -> agent($agentId);
                    $agent_name = $agent_details['first'].' '.$agent_details['last'];
                }

                $add_transaction -> agentId = $agentId ?? 0;
                $add_transaction -> agent_name = $agent_name;
                $add_transaction -> address = $address;
                $add_transaction -> city = $city;
                $add_transaction -> state = $state;
                $add_transaction -> zip = $zip;
                $add_transaction -> data_source = 'skyslope';
                $add_transaction -> save();

                $progress += $progress_increment;
                $this -> queueProgress($progress);
            }
        }

        $this -> queueProgress(100);
    }

    public function agent($id)
    {
        $agent = Agents::find($id);
        if ($agent) {
            $first = $agent -> first;
            $last = $agent -> last;
            $email = $agent -> email1;
            $phone = $agent -> cell_phone;
        } else {
            $first = '';
            $last = '';
            $email = '';
            $phone = '';
        }

        return ['first' => $first, 'last' => $last, 'email' => $email, 'phone' => $phone];
    }

    public function skyslope_auth()
    {
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
            'Timestamp' => $timestamp,
        ];

        $json = [
            'clientID' => $client_id,
            'clientSecret' => $client_secret,
        ];

        $client = new \GuzzleHttp\Client([
            'headers' => $headers,
            'json' => $json,
        ]);

        $r = $client -> request('POST', 'https://api.skyslope.com/auth/login');
        $response = $r -> getBody() -> getContents();

        return json_decode($response, true);
    }
}
