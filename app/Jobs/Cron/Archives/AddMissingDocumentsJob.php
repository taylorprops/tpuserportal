<?php

namespace App\Jobs\Cron\Archives;

use App\Models\DocManagement\Archives\Documents;
use App\Models\DocManagement\Archives\Transactions;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class AddMissingDocumentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->onQueue('add_missing_documents');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->add_missing_documents();
    }

    public function add_missing_documents()
    {
        $progress = 0;
        $this->queueProgress($progress);

        $documents = Documents::where(function ($query) {
            $query->whereNull('file_exists')
            ->orWhere('file_exists', '');
        })
        ->whereNull('doc_type')
        ->limit(100)->get();

        foreach ($documents as $document) {
            $exists = 'no';
            $missing = [];

            if (Storage::exists($document->file_location)) {
                $exists = 'yes';
            } else {
                $missing[] = $document->id;

                $auth = $this->skyslope_auth();
                $session = $auth['Session'];
                $headers = [
                    'Content-Type' => 'application/json',
                    'Session' => $session,
                ];

                $client = new \GuzzleHttp\Client([
                    'headers' => $headers,
                ]);

                if ($document->saleGuid && $document->saleGuid != '0') {
                    $transaction = Transactions::where('saleGuid', $document->saleGuid)->where('objectType', 'sale')->first();
                } else {
                    $transaction = Transactions::where('listingGuid', $document->listingGuid)->where('objectType', 'listing')->first();
                }

                $type = $transaction->objectType;
                $saleGuid = $transaction->saleGuid;
                $listingGuid = $transaction->listingGuid;

                if ($type == 'listing') {
                    $response = $client->request('GET', 'https://api.skyslope.com/api/files/listings/'.$listingGuid.'/documents/'.$document->id);
                } elseif ($type == 'sale') {
                    $response = $client->request('GET', 'https://api.skyslope.com/api/files/sales/'.$saleGuid.'/documents/'.$document->id);
                }

                $contents = $response->getBody()->getContents();
                $contents = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $contents);
                $contents = json_decode($contents, true);

                $new_document = $contents['value']['document'];

                $dir = 'doc_management/archives/'.$listingGuid.'_'.$saleGuid;
                $file_location = $dir.'/'.$new_document['fileName'];
                if (! Storage::exists($dir)) {
                    Storage::makeDirectory($dir);
                }
                Storage::put($file_location, file_get_contents($new_document['url']));

                foreach ($new_document as $col => $value) {
                    if (! in_array($col, ['fileSize', 'pages'])) {
                        $document->$col = $value;
                    }
                }

                $document->file_location = $file_location;
                $document->listingGuid = $listingGuid;
                $document->saleGuid = $saleGuid;

                $document->save();

                if (Storage::exists($file_location)) {
                    $exists = 'yes';
                }
            }

            $document->file_exists = $exists;
            $document->save();

            $progress += .1;
            $this->queueProgress($progress);

            $this->queueData(['missing' => $missing], true);
        }

        $this->queueProgress(100);
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

        $r = $client->request('POST', 'https://api.skyslope.com/auth/login');
        $response = $r->getBody()->getContents();

        return json_decode($response, true);
    }
}
