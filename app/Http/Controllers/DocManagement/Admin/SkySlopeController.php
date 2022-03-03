<?php

namespace App\Http\Controllers\DocManagement\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocManagement\Archives\Documents;
use App\Models\DocManagement\Archives\Transactions;
use App\Models\DocManagement\Archives\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Throwable;

class SkySlopeController extends Controller
{
    public function get_transactions()
    {

        //$progress = 0;
        //$this -> queueProgress($progress);

        $auth = $this->skyslope_auth();
        $session = $auth['Session'];
        $headers = [
            'Content-Type' => 'application/json',
            'Session' => $session,
        ];

        $createdAfter = str_replace(' ', 'T', date('Y-m-d H:i:s', strtotime('-1 days')));

        $query = [
            'createdAfter' => $createdAfter,
            'type' => 'all',
        ];

        $client = new \GuzzleHttp\Client([
            'headers' => $headers,
            'query' => $query,
        ]);

        //$progress = 1;
        //$this -> queueProgress($progress);

        $response = $client->request('GET', 'https://api.skyslope.com/api/files');

        $contents = $response->getBody()->getContents();
        $contents = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $contents);
        $contents = json_decode($contents, true);
        $data = $contents['value'];

        dd(config('global.skyslope_key'));
        dd($data);

        //$progress_increment = (int)round((1 / 15) * 100);

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

                        $add_transaction->$col = $value;
                    }
                }

                $add_transaction->agentId = $agentId;
                $add_transaction->data_source = 'skyslope';
                dump($agentId);
                //$add_transaction -> save();

                //$progress += $progress_increment;
                //$this -> queueProgress($progress);
            }
        }

        //$this -> queueProgress(100);
    }

    public function get_users(Request $request, $session = null)
    {
        try {
            $auth = $this->skyslope_auth();
            $session = $auth['Session'];
            $headers = [
                'Content-Type' => 'application/json',
                'Session' => $session,
            ];

            $client = new \GuzzleHttp\Client([
                'headers' => $headers,
            ]);

            $response = $client->request('GET', 'https://api.skyslope.com/api/users?includeDeactivated=true');

            $contents = $response->getBody()->getContents();
            $contents = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $contents);
            $contents = json_decode($contents, true);
            $users = $contents['value']['users'];

            foreach ($users as $user) {
                $add_user = Users::firstOrCreate([
                    'userGuid' => $user['userGuid'],
                ]);

                foreach ($user as $col => $value) {
                    $add_user->$col = $value;
                }

                $add_user->save();
            }
        } catch (Throwable $e) {
            echo $e->getMessage();
        }
    }

    public function check_documents_exists(Request $request)
    {
        $documents = Documents::whereNull('file_exists')->limit(100)->get();

        foreach ($documents as $document) {
            $exists = 'no';

            if (Storage::exists($document->file_location)) {
                $exists = 'yes';
            } else {
                echo 'missing, adding... ';

                $auth = $this->skyslope_auth();
                $session = $auth['Session'];
                $headers = [
                    'Content-Type' => 'application/json',
                    'Session' => $session,
                ];

                $client = new \GuzzleHttp\Client([
                    'headers' => $headers,
                ]);

                if ($document->listingGuid && $document->saleGuid) {
                    $transaction = Transactions::where('listingGuid', $document->listingGuid)->where('saleGuid', $document->saleGuid)->first();
                } else {
                    if ($document->listingGuid) {
                        $transaction = Transactions::where('listingGuid', $document->listingGuid)->where('objectType', 'listing')->first();
                    } else {
                        $transaction = Transactions::where('saleGuid', $document->saleGuid)->where('objectType', 'sale')->first();
                    }
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
        }
    }

    public function add_documents()
    {

        //$transactions = Transactions::where('docs_added', 'no') -> where('data_source', 'skyslope') -> inRandomOrder() -> limit(2) -> get();

        $transactions = DB::select(
            "select * from archives.transactions where
            (listingGuid = '7d23f74f-8969-408e-9b8c-2cf155599717' and saleGuid = 'd3f7f066-e712-42ee-a16a-3f06f29c2a52') or (listingGuid = '0' and saleGuid = '7cab8a29-ae72-43d9-a160-24cd33e26088') or (listingGuid = '0' and saleGuid = 'be0e07f2-4a6d-407e-bd6b-f928582b7a28') or (listingGuid = '0' and saleGuid = 'd88c9d31-fb05-4915-ae37-7092f6837d47') or (listingGuid = '0ce7f5bd-b8af-4d37-ba6d-c61969c8e399' and saleGuid = '0') or (listingGuid = '0' and saleGuid = '09b09df9-e2cc-4275-aa2d-8d1f8f5e6cd6') or (listingGuid = '4ef85b7a-2eb0-4597-8d2f-2592749ba4e3' and saleGuid = '0') or (listingGuid = 'b778efa5-3a92-4eff-b50e-f8e2f67fe9b9' and saleGuid = '0') or (listingGuid = '7f4d5a4b-01e6-40a4-878a-25cdc244560c' and saleGuid = 'ecde3d4e-5d8f-4aad-9524-af3100f39421') or (listingGuid = '6fabe388-c445-410d-a21a-fe55ce3797b6' and saleGuid = '56038be0-02f2-4d36-9e40-1d09fa9c0267') or (listingGuid = '0' and saleGuid = 'fcda038f-aa72-4c3c-8286-5a7c405f34a9') or (listingGuid = '301e10ce-8a18-4089-9d51-944bf79f63b2' and saleGuid = '0') or (listingGuid = 'f764f80e-3e09-438a-9bfd-e08f1ab3399d' and saleGuid = '0e519427-8fb3-4b67-8286-7f8b449c1483') or (listingGuid = '0' and saleGuid = '240e3e7b-d271-401b-ae16-4c3593f5a146') or (listingGuid = '0' and saleGuid = '840bcc38-bd14-42bf-b1eb-32a2962a2184') or (listingGuid = '0' and saleGuid = 'da69c495-c91a-4987-bb77-5d5d2bff4dd9') or (listingGuid = '0' and saleGuid = 'd5dfa010-d834-4d74-9f2e-36b65294d8d2') or (listingGuid = '2a322912-b75c-46e9-96a3-38ccfeeb7953' and saleGuid = '01d6e4ad-57a1-48c2-8cfb-10d26292ee87') or (listingGuid = '0' and saleGuid = '7b289550-04c7-4b3b-bd2c-d18971533897') or (listingGuid = '0' and saleGuid = '69ce8c6c-b826-4726-9cba-613cd65aaf0a') or (listingGuid = '0' and saleGuid = '0cd260ea-0a65-48a8-8f7f-c58282c564df') or (listingGuid = '0' and saleGuid = '7d199b5f-c055-4839-81d2-9b283e3913f2') or (listingGuid = '95829b77-7af1-4b32-ae3f-9f16c38209ec' and saleGuid = '0') or (listingGuid = '0' and saleGuid = '5323bc18-cbb6-4c02-9d87-9feb3c14a751') or (listingGuid = '0' and saleGuid = 'ebfeb188-90a9-4c8c-81cf-648a77402c60') or (listingGuid = '0' and saleGuid = '90913510-20dc-42e3-8273-f43bd6836047') or (listingGuid = '0' and saleGuid = 'a2dfa058-d884-42b7-a1d2-801a8c61bbda') or (listingGuid = '4dc6f71f-4c85-450f-80dd-241b125299cc' and saleGuid = 'bb9c69c8-c6c3-4654-a93f-5375fad1bf3d') or (listingGuid = '1a0d7f79-6441-47c2-acc0-056328e0f7b1' and saleGuid = '0') or (listingGuid = '0' and saleGuid = 'f0d7cfd6-68c0-431a-af29-9ca0f54a3744') or (listingGuid = '0' and saleGuid = '8394d8ad-b17a-43ac-a648-4b2f4b3ff196') or (listingGuid = '0' and saleGuid = '86cbe17e-3b52-4312-b6d3-8049c7ef99a7')
            ");

        if (count($transactions) > 0) {

            // $stats = DB::select(
            //     'select
            //     ( select count(*) from archives.transactions where data_source = \'skyslope\' ) as total,
            //     ( select count(*) from archives.transactions where data_source = \'skyslope\' and docs_added_run = \'yes\' ) as added_run,
            //     ( select count(*) from archives.transactions where data_source = \'skyslope\' and docs_added = \'yes\' ) as added,
            //     ( select count(*) from archives.transactions where data_source = \'skyslope\' and docs_added = \'docs not found\' ) as docs_not_found,
            //     ( select count(*) from archives.transactions where data_source = \'skyslope\' and docs_added = \'no response\' ) as no_response,
            //     ( select count(*) from archives.transactions where data_source = \'skyslope\' and docs_added = \'none remaining\' ) as none_remaining,
            //     ( select count(*) from archives.transactions where data_source = \'skyslope\' and docs_added = \'error\' ) as error,
            //     ( select count(*) from archives.transactions where data_source = \'skyslope\' and docs_added = \'no\' ) as not_added'
            // );
            // $this -> queueData([$stats], true);

            // $data = '';
            // foreach($transactions as $transaction) {
            //     $data .= "(listingGuid = '".$transaction -> listingGuid."' and saleGuid = '".$transaction -> saleGuid."') or ";
            // }
            // $this -> queueData([$data], true);

            $auth = $this->skyslope_auth();
            $session = $auth['Session'];

            // $progress = 1;
            // $this -> queueProgress($progress);

            $downloads = [];

            foreach ($transactions as $transaction) {

                // $progress += 1;
                // $this -> queueProgress($progress);

                $type = $transaction->objectType;
                $id = $transaction->saleGuid;
                if ($type == 'listing') {
                    $id = $transaction->listingGuid;
                }

                $listingGuid = $type == 'listing' ? $id : 0;
                $saleGuid = $type == 'sale' ? $id : 0;

                //$dir = 'doc_management/archives/'.$listingGuid.'_'.$saleGuid;
                // if(!Storage::exists($dir)) {
                //     Storage::makeDirectory($dir);
                // }
                // File::cleanDirectory(Storage::path($dir));

                // $transaction -> docs_added_run = 'yes';
                // $transaction -> save();

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
                        $response = $client->request('GET', 'https://api.skyslope.com/api/files/listings/'.$listingGuid.'/documents');
                    } elseif ($type == 'sale') {
                        $response = $client->request('GET', 'https://api.skyslope.com/api/files/sales/'.$saleGuid.'/documents');
                    }
                    dump($response);

                    if ($response) {
                        $headers = $response->getHeaders();
                        $remaining = $headers['x-ratelimit-remaining'][0];

                        if ($remaining > 0) {
                            $contents = $response->getBody()->getContents();
                            $contents = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $contents);
                            $contents = json_decode($contents, true);

                            $documents = $contents['value']['documents'];

                        /* if(count($documents) > 0) {

                            foreach($documents as $document) {

                                $add_document = Documents::firstOrCreate([
                                    'id' => $document['id']
                                ]);

                                $downloads[] = ['from' => $document['url'], 'to' => $dir.'/'.$document['fileName']];

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

                            $transaction -> docs_added = 'yes';
                            $transaction -> save();

                        } else {

                            $transaction -> docs_added = 'docs not found';
                            $transaction -> save();

                        } */
                        } else {

                            // $transaction -> docs_added = 'none remaining';
                            // $transaction -> save();
                        }
                    } else {

                        // $transaction -> docs_added = 'no response';
                        // $transaction -> save();
                    }
                } catch (Throwable $e) {
                    // if(preg_match('/404/', $e -> getMessage())) {
                    //     $transaction -> docs_added = 'transaction not found';
                    //     $transaction -> save();
                    // } else {
                    //     $remaining = 0;
                    //     $transaction -> docs_added = 'error';
                    //     $transaction -> save();
                    // }
                    // return true;
                }
            }

            /* if(count($downloads) > 0) {

                $progress_increment = round((1 / count($downloads)) * 100);

                foreach($downloads as $download) {

                    try {

                        $file_contents = gzdecode(file_get_contents($download['from']));
                        Storage::put($download['to'], $file_contents);
                        $progress += $progress_increment;
                        $this -> queueProgress($progress);

                    } catch (Throwable $e) {

                        echo $e -> getMessage();
                        dump($download);

                    }

                }

            } else {

                // $transaction -> docs_added = 'docs not found';
                // $transaction -> save();
            }

            $this -> queueProgress(100); */
        }

        //$this -> queueProgress(100);
    }

    /* public function add_documents() {

        $transactions = Transactions::where('docs_added', 'no') -> limit(5) -> get();

        if(count($transactions) > 0) {

            $auth = $this -> skyslope_auth();
            $session = $auth['Session'];

            foreach($transactions as $transaction) {

                $type = $transaction -> objectType;
                $id = $transaction -> saleGuid;
                if($type == 'listing') {
                    $id = $transaction -> listingGuid;
                }

                if($this -> get_documents($type, $id, $session) == 'success') {
                    $transaction -> docs_added = 'yes';
                    $transaction -> save();
                }
            }

        }

    }

    public function get_documents($type, $id, $session) {

        try {

            $listingGuid = $type == 'listing' ? $id : null;
            $saleGuid = $type == 'sale' ? $id : null;

            $headers = [
                'Content-Type' => 'application/json',
                'Session' => $session
            ];

            $client = new \GuzzleHttp\Client([
                'headers' => $headers
            ]);

            if($type == 'listing') {
                $response = $client -> request('GET', 'https://api.skyslope.com/api/files/listings/'.$listingGuid.'/documents');
            } else if($type == 'sale') {
                $response = $client -> request('GET', 'https://api.skyslope.com/api/files/sales/'.$saleGuid.'/documents');
            }

            $headers = $response -> getHeaders();
            $remaining = $headers['x-ratelimit-remaining'][0];
            // $reset = $headers['x-ratelimit-reset'][0];
            // $seconds_left = $reset - time() - 30;
            // echo $seconds_left.' -- '.$remaining.'<br>';

            if($remaining > 0) {

                $contents = $response -> getBody() -> getContents();
                $contents = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $contents);
                $contents = json_decode($contents, true);

                $documents = $contents['value']['documents'];

                foreach($documents as $document) {

                    //dd($document);
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

                return 'success';

            } else {

                return 'error';

            }

        } catch (Throwable $e) {

            return $e -> getMessage();

        }





    } */

    public function get_listing(Request $request, $session = null)
    {
        try {
            $listingGuid = $request->listingGuid;

            $auth = $this->skyslope_auth();
            $session = $auth['Session'];
            $headers = [
                'Content-Type' => 'application/json',
                'Session' => $session,
            ];

            $client = new \GuzzleHttp\Client([
                'headers' => $headers,
            ]);

            $response = $client->request('GET', 'https://api.skyslope.com/api/files/listings/'.$listingGuid);

            $contents = $response->getBody()->getContents();
            $contents = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $contents);
            $contents = json_decode($contents, true);
            dd($contents);
            //$users = $contents['value']['users'];

            /* foreach($users as $user) {

                $add_user = Users::firstOrCreate([
                    'userGuid' => $user['userGuid']
                ]);

                foreach($user as $col => $value) {
                    $add_user -> $col = $value;
                }

                $add_user -> save();

            } */
        } catch (Throwable $e) {
            echo $e->getMessage();
        }
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

    public function add_missing_documents()
    {

        //$progress = 0;
        //$this -> queueProgress($progress);

        $documents = Documents::where(function ($query) {
            $query->whereNull('file_exists')
            ->orWhere('file_exists', '');
        })
        ->limit(1000)->get();

        foreach ($documents as $document) {
            $exists = 'no';
            $missing = [];
            dump($document->file_location);
            if (Storage::exists($document->file_location)) {
                $exists = 'yes';
            } else {
                $exists = 'no';
                $missing[] = $document->id;
            }

            $document->file_exists = $exists;
            //$document -> save();

            //$progress += .1;
            //$this -> queueProgress($progress);

            //$this -> queueData(['missing' => $missing], true);
        }
        dump($missing);

        //$this -> queueProgress(100);
    }

    public function test(Request $request, $session = null)
    {
        try {
            $auth = $this->skyslope_auth();
            $session = $auth['Session'];
            $headers = [
                'Content-Type' => 'application/json',
                'Session' => $session,
            ];

            $start = strtotime(date('2020-11-01'));
            $end = strtotime(date('2021-11-01'));

            $query = [
                'earliestDate' => $start,
                'latestDate' => $end,
                //'type' => 'listing',
                //'agentGuid' => '6bff1a51-032d-409a-a05e-6fb3bc380e3b'
                //'pageNumber' => 3,
                //'createdAfter' => $createdAfter,
                //'createdBefore' => $createdBefore,
            ];

            $client = new \GuzzleHttp\Client([
                'headers' => $headers,
                'query' => $query,
            ]);

            $r = $client->request('GET', 'https://api.skyslope.com/api/files/listings');
            //$r = $client -> request('GET', 'https://api.skyslope.com/api/files/listings/2be27e67-5924-44e8-b032-57de5d5877a9');
            //$r = $client -> request('GET', 'https://api.skyslope.com/api/files/listings/2be27e67-5924-44e8-b032-57de5d5877a9/documents');
            //$r = $client -> request('GET', 'https://api.skyslope.com/api/offices');
            //$r = $client -> request('GET', 'https://api.skyslope.com/api/files/listings');
            //$r = $client -> request('GET', 'https://api.skyslope.com/api/users');
            //$r = $client -> request('GET', 'https://api.skyslope.com/api/users/6bff1a51-032d-409a-a05e-6fb3bc380e3b');

            $response = $r->getBody()->getContents();
            $response = json_decode($response, true);

            $this->add_listings($response);

            $next = $response['links'][0]['href'] ?? null;

            $page = 1;

            while ($next) {
                $page += 1;
                $query = [
                    'earliestDate' => $start,
                    'latestDate' => $end,
                    'pageNumber' => $page,
                ];

                $client = new \GuzzleHttp\Client([
                    'headers' => $headers,
                    'query' => $query,
                ]);
                $r = $client->request('GET', 'https://api.skyslope.com/api/files/listings');
                $response = $r->getBody()->getContents();
                $response = json_decode($response, true);

                //dump($response);

                $this->add_listings($response);

                $next = $response['links'][1]['href'] ?? null;
            }
        } catch (Throwable $e) {
            echo $e->getMessage();
        }
    }
}
