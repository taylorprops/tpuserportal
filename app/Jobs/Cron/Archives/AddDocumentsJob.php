<?php

namespace App\Jobs\Cron\Archives;

use App\Models\DocManagement\Archives\Documents;
use App\Models\DocManagement\Archives\Transactions;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use romanzipp\QueueMonitor\Traits\IsMonitored;
use Throwable;

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

    public function add_documents()
    {
        $transactions = Transactions::whereIn('docs_added', ['no']) -> where('data_source', 'skyslope') -> inRandomOrder() -> limit(100) -> get();

        if (count($transactions) > 0) {
            $stats = DB::select(
                "select
                ( select count(*) from archives.transactions where data_source = 'skyslope' ) as total,
                ( select count(*) from archives.transactions where data_source = 'skyslope' and docs_added_run = 'yes' ) as added_run,
                ( select count(*) from archives.transactions where data_source = 'skyslope' and docs_added = 'yes' ) as added,
                ( select count(*) from archives.transactions where data_source = 'skyslope' and docs_added = 'transaction not found' ) as transaction_not_found,
                ( select count(*) from archives.transactions where data_source = 'skyslope' and docs_added = 'download failed' ) as download_failed,
                ( select count(*) from archives.transactions where data_source = 'skyslope' and docs_added = 'docs not found' ) as docs_not_found,
                ( select count(*) from archives.transactions where data_source = 'skyslope' and docs_added = 'no response' ) as no_response,
                ( select count(*) from archives.transactions where data_source = 'skyslope' and docs_added = 'none remaining' ) as none_remaining,
                ( select count(*) from archives.transactions where data_source = 'skyslope' and docs_added = 'error' ) as error,
                ( select count(*) from archives.transactions where data_source = 'skyslope' and docs_added = 'no' ) as not_added"
            );
            $this -> queueData([$stats], true);

            $data = '';
            foreach ($transactions as $transaction) {
                $data .= "(listingGuid = '".$transaction -> listingGuid."' and saleGuid = '".$transaction -> saleGuid."') or ";
            }
            $this -> queueData([$data], true);

            $auth = $this -> skyslope_auth();
            $session = $auth['Session'];

            $progress = 1;
            $this -> queueProgress($progress);

            $downloads = [];

            foreach ($transactions as $transaction) {
                $type = $transaction -> objectType;
                $id = $transaction -> saleGuid;
                if ($type == 'listing') {
                    $id = $transaction -> listingGuid;
                }

                $listingGuid = $type == 'listing' ? $id : 0;
                $saleGuid = $type == 'sale' ? $id : 0;

                $dir = 'doc_management/archives/'.$listingGuid.'_'.$saleGuid;
                if (! Storage::exists($dir)) {
                    Storage::makeDirectory($dir);
                }
                File::cleanDirectory(Storage::path($dir));

                $transaction -> docs_added_run = 'yes';
                $transaction -> save();

                $headers = [
                    'Content-Type' => 'application/json',
                    'Session' => $session,
                ];

                $client = new \GuzzleHttp\Client([
                    'headers' => $headers,
                ]);

                $response = null;

                try {
                    if ($type == 'listing') {
                        $response = $client -> request('GET', 'https://api.skyslope.com/api/files/listings/'.$listingGuid.'/documents');
                    } elseif ($type == 'sale') {
                        $response = $client -> request('GET', 'https://api.skyslope.com/api/files/sales/'.$saleGuid.'/documents');
                    }

                    if ($response) {
                        $headers = $response -> getHeaders();
                        $remaining = $headers['x-ratelimit-remaining'][0];

                        if ($remaining > 0) {
                            $contents = $response -> getBody() -> getContents();
                            $contents = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $contents);
                            $contents = json_decode($contents, true);

                            $documents = $contents['value']['documents'];

                            if (count($documents) > 0) {
                                foreach ($documents as $document) {
                                    $add_document = Documents::firstOrCreate([
                                        'id' => $document['id'],
                                    ]);

                                    $downloads[] = ['from' => $document['url'], 'to' => $dir.'/'.$document['fileName']];

                                    $file_location = $dir.'/'.$document['fileName'];

                                    foreach ($document as $col => $value) {
                                        if (! in_array($col, ['fileSize', 'pages'])) {
                                            $add_document -> $col = $value;
                                        }
                                    }

                                    $add_document -> file_location = $file_location;
                                    $add_document -> listingGuid = $listingGuid;
                                    $add_document -> saleGuid = $saleGuid;

                                    $add_document -> save();
                                }

                                $transaction -> docs_added = 'yes';
                                $transaction -> save();
                            } else {
                                $transaction -> docs_added = 'docs not found';
                                $transaction -> save();
                            }
                        } else {
                            $transaction -> docs_added = 'none remaining';
                            $transaction -> save();
                        }
                    } else {
                        $transaction -> docs_added = 'no response';
                        $transaction -> save();
                    }
                } catch (Throwable $e) {
                    $transaction -> docs_added = 'transaction not found';
                    $transaction -> save();
                }

                $progress += 1;
                $this -> queueProgress($progress);
            }

            if (count($downloads) > 0) {
                $progress_increment = round((1 / count($downloads)) * 100);

                foreach ($downloads as $download) {
                    $progress += $progress_increment;
                    $this -> queueProgress($progress);

                    try {
                        $file_contents = gzdecode(file_get_contents($download['from']));
                        Storage::put($download['to'], $file_contents);
                    } catch (Throwable $e) {
                        $transaction -> docs_added = 'download failed';
                        $transaction -> save();
                    }
                }
            } else {

                // $transaction -> docs_added = 'docs not found';
                // $transaction -> save();
            }

            $this -> queueProgress(100);
        }

        $this -> queueProgress(100);
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
