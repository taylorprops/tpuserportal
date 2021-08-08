<?php

namespace App\Jobs\Cron\Archives;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Models\DocManagement\Archives\Documents;
use App\Models\DocManagement\Archives\Transactions;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class AddDocumentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this -> onQueue('add_documents');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $this -> add_documents();

    }

    public function add_documents() {

        $transactions = Transactions::where('docs_added', 'no') -> limit(5) -> get();

        if(count($transactions) > 0) {

            $data = [];
            foreach($transactions as $transaction) {
                $data[] = [
                    'listingGuid' => $transaction -> listingGuid,
                    'saleGuid' => $transaction -> saleGuid
                ];
            }
            $this -> queueData(['transactions' => $data], true);

            $auth = $this -> skyslope_auth();
            $session = $auth['Session'];

            $progress = 0;
            $this -> queueProgress($progress);

            $downloads = [];

            foreach($transactions as $transaction) {

                $type = $transaction -> objectType;
                $id = $transaction -> saleGuid;
                if($type == 'listing') {
                    $id = $transaction -> listingGuid;
                }

                $transaction -> docs_added = 'yes';
                $transaction -> save();

                $listingGuid = $type == 'listing' ? $id : null;
                $saleGuid = $type == 'sale' ? $id : null;

                $headers = [
                    'Content-Type' => 'application/json',
                    'Session' => $session
                ];

                $client = new \GuzzleHttp\Client([
                    'headers' => $headers
                ]);

                $response = null;
                try {
                    if($type == 'listing') {
                        $response = $client -> request('GET', 'https://api.skyslope.com/api/files/listings/'.$listingGuid.'/documents');
                    } else if($type == 'sale') {
                        $response = $client -> request('GET', 'https://api.skyslope.com/api/files/sales/'.$saleGuid.'/documents');
                    }

                    if($response) {

                        $headers = $response -> getHeaders();
                        $remaining = $headers['x-ratelimit-remaining'][0];

                        if($remaining > 0) {

                            $contents = $response -> getBody() -> getContents();
                            $contents = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $contents);
                            $contents = json_decode($contents, true);

                            $documents = $contents['value']['documents'];

                            if(count($documents) > 0) {

                                foreach($documents as $document) {

                                    $add_document = Documents::firstOrCreate([
                                        'id' => $document['id']
                                    ]);

                                    $dir = 'doc_management/archives/'.$listingGuid.'_'.$saleGuid;
                                    $downloads[] = ['dir' => $dir, 'from' => $document['url'], 'to' => $dir.'/'.$document['fileName']];

                                    $file_location = $dir.'/'.$document['fileName'];

                                    foreach($document as $col => $value) {
                                        if(!in_array($col, ['fileSize', 'pages'])) {
                                            $add_document -> $col = $value;
                                        }
                                    }

                                    $add_document -> file_location = $file_location;
                                    $add_document -> listingGuid = $listingGuid;
                                    $add_document -> saleGuid = $saleGuid;

                                    $add_document -> save();

                                }

                            }


                        } else {

                            return 'error';

                        }

                    }

                } catch (\GuzzleHttp\Exception\ServerException $e) {
                    $remaining = 0;
                }

            }

            if(count($downloads) > 0) {

                $progress_increment = round((1 / count($downloads)) * 100);

                foreach($downloads as $download) {

                    $dir = $download['dir'];
                    if(!Storage::exists($dir)) {
                        Storage::makeDirectory($dir);
                    }
                    Storage::put($download['to'], gzdecode(file_get_contents($download['from'])));

                    $progress += $progress_increment;
                    $this -> queueProgress($progress);

                }

            }

            $this -> queueProgress(100);

        }

    }

    /* public function get_documents($type, $id, $session, $progress) {

        //try {

            $listingGuid = $type == 'listing' ? $id : null;
            $saleGuid = $type == 'sale' ? $id : null;

            $headers = [
                'Content-Type' => 'application/json',
                'Session' => $session
            ];

            $client = new \GuzzleHttp\Client([
                'headers' => $headers
            ]);

            try {
                if($type == 'listing') {
                    $response = $client -> request('GET', 'https://api.skyslope.com/api/files/listings/'.$listingGuid.'/documents');
                } else if($type == 'sale') {
                    $response = $client -> request('GET', 'https://api.skyslope.com/api/files/sales/'.$saleGuid.'/documents');
                }

                if($response) {

                    $headers = $response -> getHeaders();
                    $remaining = $headers['x-ratelimit-remaining'][0];

                    if($remaining > 0) {

                        $contents = $response -> getBody() -> getContents();
                        $contents = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $contents);
                        $contents = json_decode($contents, true);

                        $documents = $contents['value']['documents'];

                        if(count($documents) > 0) {

                            foreach($documents as $document) {

                                $add_document = Documents::firstOrCreate([
                                    'id' => $document['id']
                                ]);

                                $dir = 'doc_management/archives/'.$listingGuid.'_'.$saleGuid;
                                Storage::makeDirectory($dir);
                                Storage::put($dir.'/'.$document['fileName'], file_get_contents($document['url']));
                                $file_location = $dir.'/'.$document['fileName'];

                                foreach($document as $col => $value) {
                                    if(!in_array($col, ['fileSize', 'pages'])) {
                                        $add_document -> $col = $value;
                                    }
                                }

                                $add_document -> file_location = $file_location;
                                $add_document -> listingGuid = $listingGuid;
                                $add_document -> saleGuid = $saleGuid;

                                $add_document -> save();

                            }

                        }

                        return count($documents);

                    } else {

                        return 'error';

                    }

                }

            } catch (\GuzzleHttp\Exception\ServerException $e) {
                $remaining = 0;
            }

        // } catch (Throwable $e) {

        //     return $e -> getMessage();

        // }





    } */

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
