<?php

namespace App\Http\Controllers\DocManagement\Admin;

use Throwable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\DocManagement\Archives\Users;
use App\Models\DocManagement\Archives\Documents;
use App\Models\DocManagement\Archives\Transactions;


class SkySlopeController extends Controller
{


    public function get_transactions() {

        //$progress = 0;
        //$this -> queueProgress($progress);

        $auth = $this -> skyslope_auth();
        $session = $auth['Session'];
        $headers = [
            'Content-Type' => 'application/json',
            'Session' => $session
        ];

        $createdAfter = str_replace(' ', 'T', date('Y-m-d H:i:s', strtotime('-1 days')));

        $query = [
            'createdAfter' => $createdAfter,
            'type' => 'all'
        ];

        $client = new \GuzzleHttp\Client([
            'headers' => $headers,
            'query' => $query
        ]);

        //$progress = 1;
        //$this -> queueProgress($progress);

        $response = $client -> request('GET', 'https://api.skyslope.com/api/files');

        $contents = $response -> getBody() -> getContents();
        $contents = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $contents);
        $contents = json_decode($contents, true);
        $data = $contents['value'];

        //$progress_increment = (int)round((1 / 15) * 100);

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

                            if($col == 'agent') {
                                $agentId = $value['publicId'];
                            }

                            $value = json_encode($value);

                        }
                        if($col == 'isOfficeLead') {
                            $value = $value == 'true' ? 1 : 0;
                        }

                        $add_transaction -> $col = $value;

                    }

                }

                $add_transaction -> agentId = $agentId;
                $add_transaction -> data_source = 'skyslope';
                dump($agentId);
                //$add_transaction -> save();

                //$progress += $progress_increment;
                //$this -> queueProgress($progress);

            }

        }

        //$this -> queueProgress(100);

    }


    public function get_users(Request $request, $session = null) {

        try {

            $auth = $this -> skyslope_auth();
            $session = $auth['Session'];
            $headers = [
                'Content-Type' => 'application/json',
                'Session' => $session
            ];

            $client = new \GuzzleHttp\Client([
                'headers' => $headers
            ]);

            $response = $client -> request('GET', 'https://api.skyslope.com/api/users?includeDeactivated=true');

            $contents = $response -> getBody() -> getContents();
            $contents = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $contents);
            $contents = json_decode($contents, true);
            $users = $contents['value']['users'];

            foreach($users as $user) {

                $add_user = Users::firstOrCreate([
                    'userGuid' => $user['userGuid']
                ]);

                foreach($user as $col => $value) {
                    $add_user -> $col = $value;
                }

                $add_user -> save();

            }



        } catch (Throwable $e) {

            echo $e -> getMessage();

        }


    }

    public function check_documents_exists(Request $request) {

        $documents = Documents::whereNull('file_exists') -> limit(100) -> get();

        foreach($documents as $document) {

            $exists = 'no';

            if(Storage::exists($document -> file_location)) {

                $exists = 'yes';

            } else {

                echo 'missing, adding... ';

                $auth = $this -> skyslope_auth();
                $session = $auth['Session'];
                $headers = [
                    'Content-Type' => 'application/json',
                    'Session' => $session
                ];

                $client = new \GuzzleHttp\Client([
                    'headers' => $headers
                ]);

                if($document -> listingGuid && $document -> saleGuid) {

                    $transaction = Transactions::where('listingGuid', $document -> listingGuid) -> where('saleGuid', $document -> saleGuid) -> first();

                } else {

                    if($document -> listingGuid) {
                        $transaction = Transactions::where('listingGuid', $document -> listingGuid) -> where('objectType', 'listing') -> first();
                    } else {
                        $transaction = Transactions::where('saleGuid', $document -> saleGuid) -> where('objectType', 'sale') -> first();
                    }

                }

                $type = $transaction -> objectType;
                $saleGuid = $transaction -> saleGuid;
                $listingGuid = $transaction -> listingGuid;

                if($type == 'listing') {
                    $response = $client -> request('GET', 'https://api.skyslope.com/api/files/listings/'.$listingGuid.'/documents/'.$document -> id);
                } else if($type == 'sale') {
                    $response = $client -> request('GET', 'https://api.skyslope.com/api/files/sales/'.$saleGuid.'/documents/'.$document -> id);
                }

                $contents = $response -> getBody() -> getContents();
                $contents = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $contents);
                $contents = json_decode($contents, true);

                $new_document = $contents['value']['document'];

                $dir = 'doc_management/archives/'.$listingGuid.'_'.$saleGuid;
                $file_location = $dir.'/'.$new_document['fileName'];
                if(!Storage::exists($dir)) {
                    Storage::makeDirectory($dir);
                }
                Storage::put($file_location, file_get_contents($new_document['url']));

                foreach($new_document as $col => $value) {
                    if(!in_array($col, ['fileSize', 'pages'])) {
                        $document -> $col = $value;
                    }
                }

                $document -> file_location = $file_location;
                $document -> listingGuid = $listingGuid;
                $document -> saleGuid = $saleGuid;

                $document -> save();

                if(Storage::exists($file_location)) {
                    $exists = 'yes';
                }

            }

            $document -> file_exists = $exists;
            $document -> save();

        }



    }


    public function add_documents() {

        gzdecode('/var/www/admin-tail/storage/app/public/doc_management/archives/_f36215a7-1a2d-41a5-8213-3b8ad1622772/addendum.pdf');
        return false;

        $transactions = Transactions::where('docs_added', 'yes') -> where('data_source', 'skyslope') -> limit(3) -> get();

        if(count($transactions) > 0) {

            $data = [];
            foreach($transactions as $transaction) {
                $data[] = [
                    'listingGuid' => $transaction -> listingGuid,
                    'saleGuid' => $transaction -> saleGuid
                ];
            }
            //$this -> queueData(['transactions' => $data], true);

            $auth = $this -> skyslope_auth();
            $session = $auth['Session'];

            //$progress = 0;
            //$this -> queueProgress($progress);

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

                                    // $add_document = Documents::firstOrCreate([
                                    //     'id' => $document['id']
                                    // ]);

                                    $dir = 'doc_management/archives/'.$listingGuid.'_'.$saleGuid;
                                    $downloads[] = ['dir' => $dir, 'from' => $document['url'], 'to' => $dir.'/'.$document['fileName']];

                                    // $file_location = $dir.'/'.$document['fileName'];

                                    // foreach($document as $col => $value) {
                                    //     if(!in_array($col, ['fileSize', 'pages'])) {
                                    //         $add_document -> $col = $value;
                                    //     }
                                    // }

                                    // $add_document -> file_location = $file_location;
                                    // $add_document -> listingGuid = $listingGuid;
                                    // $add_document -> saleGuid = $saleGuid;

                                    //$add_document -> save();

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

                //$progress_increment = round((1 / count($downloads)) * 100);

                foreach($downloads as $download) {

                    $dir = $download['dir'];
                    if(!Storage::exists($dir)) {
                        Storage::makeDirectory($dir);
                    }
                    file_put_contents(Storage::path($download['to']), file_get_contents($download['from']));
                    //Storage::put($download['to'], file_get_contents($download['from']));
                    dump($download['to'], $download['from']);

                    //$progress += $progress_increment;
                    //$this -> queueProgress($progress);

                }

            }

            //$this -> queueProgress(100);

        }

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

    public function get_listing(Request $request, $session = null) {

        try {

            $listingGuid = $request -> listingGuid;

            $auth = $this -> skyslope_auth();
            $session = $auth['Session'];
            $headers = [
                'Content-Type' => 'application/json',
                'Session' => $session
            ];

            $client = new \GuzzleHttp\Client([
                'headers' => $headers
            ]);

            $response = $client -> request('GET', 'https://api.skyslope.com/api/files/listings/'.$listingGuid);

            $contents = $response -> getBody() -> getContents();
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

            echo $e -> getMessage();

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

    public function add_missing_documents() {

        //$progress = 0;
        //$this -> queueProgress($progress);

        $documents = Documents::where(function($query) {
            $query -> whereNull('file_exists')
            -> orWhere('file_exists', '');
        })
        -> limit(1000) -> get();

        foreach($documents as $document) {

            $exists = 'no';
            $missing = [];
            dump($document -> file_location);
            if(Storage::exists($document -> file_location)) {

                $exists = 'yes';

            } else {

                $exists = 'no';
                $missing[] = $document -> id;

            }

            $document -> file_exists = $exists;
            //$document -> save();

            //$progress += .1;
            //$this -> queueProgress($progress);

            //$this -> queueData(['missing' => $missing], true);

        }
        dump($missing);

        //$this -> queueProgress(100);

    }

    public function test(Request $request, $session = null) {

        try {

            $auth = $this -> skyslope_auth();
            $session = $auth['Session'];
            $headers = [
                'Content-Type' => 'application/json',
                'Session' => $session
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
                'query' => $query
            ]);

            $r = $client -> request('GET', 'https://api.skyslope.com/api/files/listings');
            //$r = $client -> request('GET', 'https://api.skyslope.com/api/files/listings/2be27e67-5924-44e8-b032-57de5d5877a9');
            //$r = $client -> request('GET', 'https://api.skyslope.com/api/files/listings/2be27e67-5924-44e8-b032-57de5d5877a9/documents');
            //$r = $client -> request('GET', 'https://api.skyslope.com/api/offices');
            //$r = $client -> request('GET', 'https://api.skyslope.com/api/files/listings');
            //$r = $client -> request('GET', 'https://api.skyslope.com/api/users');
            //$r = $client -> request('GET', 'https://api.skyslope.com/api/users/6bff1a51-032d-409a-a05e-6fb3bc380e3b');

            $response = $r -> getBody() -> getContents();
            $response = json_decode($response, true);

            $this -> add_listings($response);

            $next = $response['links'][0]['href'] ?? null;

            $page = 1;

            while($next) {

                $page += 1;
                $query = [
                    'earliestDate' => $start,
                    'latestDate' => $end,
                    'pageNumber' => $page
                ];

                $client = new \GuzzleHttp\Client([
                    'headers' => $headers,
                    'query' => $query
                ]);
                $r = $client -> request('GET', 'https://api.skyslope.com/api/files/listings');
                $response = $r -> getBody() -> getContents();
                $response = json_decode($response, true);

                //dump($response);

                $this -> add_listings($response);

                $next = $response['links'][1]['href'] ?? null;

            }

        } catch (Throwable $e) {

            echo $e -> getMessage();

        }


    }

}
