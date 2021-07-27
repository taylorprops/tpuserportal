<?php

namespace App\Http\Controllers\DocManagement\Admin;

use Throwable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\DocManagement\SkySlope\Users;
use App\Models\DocManagement\SkySlope\Documents;
use App\Models\DocManagement\SkySlope\Transactions;


class SkySlopeController extends Controller
{


    public function get_transactions(Request $request, $session = null) {

        $progress_increment = (int)round((1 / 15) * 100);
        dd($progress_increment);

        die();

        try {

            $auth = $this -> skyslope_auth();
            $session = $auth['Session'];
            $headers = [
                'Content-Type' => 'application/json',
                'Session' => $session
            ];

            $days_ago_start = 4;
            $days_ago_end = 0;
            if($request -> start) {
                $days_ago_start = $request -> start;
            }
            if($request -> end) {
                $days_ago_end = $request -> end;
            }

            $createdAfter = str_replace(' ', 'T', date('Y-m-d H:i:s', strtotime('-'.$days_ago_start.' day')));
            $createdBefore = str_replace(' ', 'T', date('Y-m-d H:i:s', strtotime('-'.$days_ago_end.' day')));

            $query = [
                'createdAfter' => $createdAfter,
                'createdBefore' => $createdBefore,
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

            //dd($data);
            echo 'Total: '.count($data).'<br>';
            echo 'Start: '.$createdAfter.' - End: '.$createdBefore.'<br>';

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



        } catch (Throwable $e) {

            echo $e -> getMessage();

        }


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




    public function add_documents() {

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

                    $dir = 'doc_management/skyslope/'.$listingGuid.'_'.$saleGuid;
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





    }

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
