<?php

namespace App\Http\Controllers;

use Monolog\Logger;
use App\Models\User;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Models\Backups\Rsync;
use App\Models\Employees\Agents;
use Illuminate\Support\Facades\DB;
use Monolog\Handler\StreamHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Crypt;
use App\Models\BrightMLS\BrightOffices;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use App\Models\Employees\EmployeesNotes;
use Illuminate\Database\Eloquent\Builder;
use App\Models\BrightMLS\BrightAgentRoster;
use App\Models\Employees\EmployeesLicenses;
use App\Models\OldDB\Company\BillingInvoices;
use App\Models\Marketing\LoanOfficerAddresses;
use App\Models\DocManagement\Admin\Forms\Forms;
use App\Models\DocManagement\Archives\Documents;
use App\Models\OldDB\Company\BillingInvoicesItems;
use App\Models\DocManagement\Archives\Transactions;
use App\Models\Employees\Mortgage as LoanOfficersNew;
use App\Models\OldDB\LoanOfficers as LoanOfficersOld;
use App\Models\DocManagement\Resources\CommonFieldsGroups;

class TestsController extends Controller
{

    public function test(Request $request) {


        exec('/usr/bin/rsync -chavzPO  --delete --ignore-existing --stats /mnt/vol2/backups/ --exclude "scripts" root@162.244.66.22:/mnt/sdb/storage/mysql 2>&1', $output);
        //exec("cd /mnt/vol2/backups && ls 2>&1", $output);
        dump($output);
        $rsync = new Rsync;
        $rsync -> site = 'All';
        $rsync -> backup_type = 'database';
        $rsync -> response = json_encode($output);
        $rsync -> save();

    }

    public function edit_los(Request $request) {

        //dump(count(LoanOfficerAddresses::whereNull('full_name') -> get()));

        // LoanOfficerAddresses::chunkById(10, function($loan_officers) {
        //     foreach ($loan_officers as $loan_officer) {
        //         $loan_officer -> full_name = $loan_officer -> first_name.' '.$loan_officer -> last_name;
        //         dump($loan_officer -> first_name.' '.$loan_officer -> last_name);
        //         $loan_officer -> save();
        //     }
        // }) -> limit(50);

        $loan_officers = LoanOfficerAddresses::whereRaw('length(state) > 2') -> limit(10000) -> get();

        foreach ($loan_officers as $loan_officer) {
            $state = 'MD';
            if (stristr($loan_officer -> state, 'dc')) {
                $state = 'DC';
            } elseif (stristr($loan_officer -> state, 'va')) {
                $state = 'VA';
            }
            $loan_officer -> state = $state;
            $loan_officer -> save();
        }
    }

    public function bright_remove_agents()
    {
        ini_set('memory_limit', '-1');

        $rets = Helper::rets_login();

        $resource = 'ActiveAgent';
        $class = 'ActiveMember';

        $query = '(MemberStatus=|Active)';

        $results = $rets -> Search(
            $resource,
            $class,
            $query,
            [
                'Count' => '0',
                'Select' => 'MemberKey',
            ]
        );

        $agents_in_bright = $results -> toArray();
        $agents_in_bright_count = count($agents_in_bright);
        $agents_in_bright_array = [];
        foreach ($agents_in_bright as $agent_in_bright) {
            $agents_in_bright_array[] = (int) $agent_in_bright['MemberKey'];
        }

        $agents_in_db = BrightAgentRoster::where('active', 'yes') -> get() -> pluck('MemberKey') -> toArray();
        $agents_in_db_count = count($agents_in_db);

        // dd($agents_in_bright_count, $agents_in_db_count);

        $deactivate_agents = [];
        foreach ($agents_in_db as $agent) {
            if (! in_array($agent, $agents_in_bright_array)) {
                $deactivate_agents[] = $agent;
            }
        }

        if (count($deactivate_agents) > 0) {
            BrightAgentRoster::whereIn('MemberKey', $deactivate_agents)
            -> update([
                'active' => 'no',
            ]);
        }

        $missing_agents = [];
        foreach ($agents_in_bright_array as $agent) {
            if (! in_array($agent, $agents_in_db)) {
                $missing_agents[] = $agent;
            }
        }

        if(count($missing_agents) > 0) {

            $agents_in_db_string = implode(', ', $missing_agents);

            $query = '(MemberKey='.$agents_in_db_string.')';

            $results = $rets -> Search(
                $resource,
                $class,
                $query,
                [
                    'Count' => 0,
                    'Limit' => 5000,
                ]
            );

            $agents = $results -> toArray();

            if (count($agents) > 0) {
                foreach ($agents as $agent) {
                    $agent_details = array_filter($agent);
                    $agent['active'] = 'yes';
                    $MemberKey = $agent['MemberKey'];
                    unset($agent_details['MemberKey']);

                    $add_agent = BrightAgentRoster::create(
                        ['MemberKey' => $MemberKey],
                        $agent_details
                    );

                    $add_agent -> save();
                }
            }
        }

        return false;

        dd('deactivate = '.count($deactivate_agents).' missing = '.count($missing_agents));

        //dd($agents_in_bright_array, $agents_in_db);

        return false;

        // $agents_in_db_array = BrightAgentRoster::select($select)
        // -> where(function($query) {
        //     $query -> where('removal_date_checked', '!=', date('Y-m-d'))
        //     -> orWhereNull('removal_date_checked');
        // })
        // // -> where('active', 'yes')
        // -> limit($search_for)
        // -> get()
        // -> pluck('MemberKey')
        // -> toArray();

        // if (count($agents_in_db_array) < $search_for) {
        //     $search_for = count($agents_in_db_array);
        // }

        // $data[] = 'search_for = '.$search_for.', agents_in_db_array = '.count($agents_in_db_array);

        // if ($search_for > 0) {

        //     $agents_in_db_string = implode(', ', $agents_in_db_array);

        //     $query = '(MemberKey='.$agents_in_db_string.')';

        //     $results = $rets -> Search(
        //         $resource,
        //         $class,
        //         $query,
        //         [
        //             'Count' => 0
        //         ]
        //     );

        //     $agents = $results -> toArray();
        //     $total_found = count($agents);
        //     $data[] = 'total_found = '.$total_found;

        //     // if not all agents in db are found in bright
        //     if ($total_found != $search_for) {

        //         $data[] = 'Found Missing';
        //         $MemberKeys = [];

        //         $increment = 50 / count($agents);
        //         $progress = 50;
        //         foreach ($agents as $agent) {
        //             $MemberKeys[] = $agent['MemberKey'];
        //             $progress += $increment;
        //             $this -> queueProgress($progress);
        //         }

        //         $not_found = array_diff($agents_in_db_array, $MemberKeys);
        //         dump($not_found);

        //         $deactivate_agents = BrightAgentRoster::whereIn('MemberKey', $not_found)
        //         -> update([
        //             'active' => 'no',
        //         ]);

        //     }

        //     $update_removal_date_checked = BrightAgentRoster::whereIn('MemberKey', $agents_in_db_array)
        //     -> update([
        //         'removal_date_checked' => date('Y-m-d')
        //     ]);
        //     dump($update_removal_date_checked);

        // }

        //$this -> queueData([$data], true);
    }

    public function bright_update_agents(Request $request)
    {
        $rets_config = new \PHRETS\Configuration;
        $rets_config -> setLoginUrl(config('global.rets_url'))
        -> setUsername(config('global.rets_username'))
        -> setPassword(config('global.rets_password'))
        -> setRetsVersion('RETS/1.7.2')
        -> setUserAgent('Bright RETS Application/1.0')
        -> setHttpAuthenticationMethod('digest') // or 'basic' if required
        -> setOption('use_post_method', true)
        -> setOption('disable_follow_location', false);

        $rets = new \PHRETS\Session($rets_config);
        $connect = $rets -> Login();

        $log = new Logger('PHRETS');
        $log -> pushHandler(new StreamHandler('php://stdout', Logger::DEBUG));
        $log -> warning('Foo');
        $log -> error('Bar');
        $rets -> setLogger($log);

        $resource = 'ActiveAgent';
        $class = 'ActiveMember';

        $mod_time = date('Y-m-d H:i:s', strtotime('-25 hour'));
        $mod_time = str_replace(' ', 'T', $mod_time);
        $query = '(ModificationTimestamp='.$mod_time.'+)';

        $results = $rets -> Search(
            $resource,
            $class,
            $query,
            [
                'Count' => 0,
            ]
        );

        $agents = $results -> toArray();
        $total_found = count($agents);

        if ($total_found > 0) {
            foreach ($agents as $agent) {
                $agent_details = array_filter($agent);
                $MemberKey = $agent['MemberKey'];
                unset($agent_details['MemberKey']);

                $add_agent = BrightAgentRoster::firstOrCreate(
                    ['MemberKey' => $MemberKey],
                    $agent_details
                );

                $add_agent -> save();
            }
        }

        //$rets -> Disconnect();
    }

    public function bright_update_offices(Request $request)
    {
        $rets_config = new \PHRETS\Configuration;
        $rets_config -> setLoginUrl(config('global.rets_url'))
        -> setUsername(config('global.rets_username'))
        -> setPassword(config('global.rets_password'))
        -> setRetsVersion('RETS/1.7.2')
        -> setUserAgent('Bright RETS Application/1.0')
        -> setHttpAuthenticationMethod('digest')
        -> setOption('use_post_method', true)
        -> setOption('disable_follow_location', false);

        $rets = new \PHRETS\Session($rets_config);

        $connect = $rets -> Login();

        $resource = 'Office';
        $class = 'Office';

        $mod_time = date('Y-m-d H:i:s', strtotime('-12 hour'));
        $mod_time = str_replace(' ', 'T', $mod_time);
        $query = '(ModificationTimestamp='.$mod_time.'+)';

        if (Helper::access_protected_property($rets, 'rets_session_id') == '') {
            // $this -> queueData(['login failed, retrying'], true);
            sleep(5);
            $rets = new \PHRETS\Session($rets_config);
            $connect = $rets -> Login();
        }

        Log::debug($this -> job -> uuid());

        return false;

        $results = $rets -> Search(
            $resource,
            $class,
            $query,
            [
                'Count' => 0,
            ]
        );

        $offices = $results -> toArray();
        $total_found = count($offices);

        echo 'found = '.$total_found.'<br>';

        $count_before = BrightOffices::get() -> count();

        if ($total_found > 0) {
            foreach ($offices as $office) {
                $office_details = array_filter($office);
                $OfficeKey = $office['OfficeKey'];
                unset($office_details['OfficeKey']);

                $add_office = BrightOffices::firstOrCreate(
                    ['OfficeKey' => $OfficeKey],
                    $office_details
                );

                $add_office -> save();
            }
        }

        $rets -> Disconnect();
    }

    public function add_addresses_to_bright(Request $request)
    {
        $addresses = AgentsAddresses::where('found_status', 'found') -> get();
    }


    public function add_documents()
    {
        // $transactions = Transactions::whereIn('docs_added', ['no']) -> where('data_source', 'skyslope') -> inRandomOrder() -> limit(100) -> get();
        $transactions = Transactions::where('transactionId', '12212156') -> orWhere('listingId', '6339417') -> get();

        if (count($transactions) > 0) {
            // $stats = DB::select(
            //     "select
            //     ( select count(*) from archives.transactions where data_source = 'skyslope' ) as total,
            //     ( select count(*) from archives.transactions where data_source = 'skyslope' and docs_added_run = 'yes' ) as added_run,
            //     ( select count(*) from archives.transactions where data_source = 'skyslope' and docs_added = 'yes' ) as added,
            //     ( select count(*) from archives.transactions where data_source = 'skyslope' and docs_added = 'transaction not found' ) as transaction_not_found,
            //     ( select count(*) from archives.transactions where data_source = 'skyslope' and docs_added = 'download failed' ) as download_failed,
            //     ( select count(*) from archives.transactions where data_source = 'skyslope' and docs_added = 'docs not found' ) as docs_not_found,
            //     ( select count(*) from archives.transactions where data_source = 'skyslope' and docs_added = 'no response' ) as no_response,
            //     ( select count(*) from archives.transactions where data_source = 'skyslope' and docs_added = 'none remaining' ) as none_remaining,
            //     ( select count(*) from archives.transactions where data_source = 'skyslope' and docs_added = 'error' ) as error,
            //     ( select count(*) from archives.transactions where data_source = 'skyslope' and docs_added = 'no' ) as not_added"
            // );
            // $this -> queueData([$stats], true);

            $data = '';
            foreach ($transactions as $transaction) {
                $data .= "(listingGuid = '".$transaction -> listingGuid."' and saleGuid = '".$transaction -> saleGuid."') or ";
            }
            // $this -> queueData([$data], true);
            //$this -> queueData(['count' => count($transactions)], true);

            $auth = $this -> skyslope_auth();
            $session = $auth['Session'];

            $progress = 1;
            //$this -> queueProgress($progress);

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
                        $response = $client -> request('GET', 'https://api.skyslope.com/api/files/listings/'.$listingGuid);
                    } elseif ($type == 'sale') {
                        $response = $client -> request('GET', 'https://api.skyslope.com/api/files/sales/'.$saleGuid);
                    }

                    dd($response -> getBody() -> getContents());
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

                dump($transaction);


                $progress += 1;
                //$this -> queueProgress($progress);
            }

            if (count($downloads) > 0) {
                $progress_increment = round((1 / count($downloads)) * 100);

                foreach ($downloads as $download) {
                    $progress += $progress_increment;
                    //$this -> queueProgress($progress);

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

            //$this -> queueProgress(100);
        }



        //$this -> queueProgress(100);
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

    public function update_encrypted_fields()
    {
        $loan_officers = LoanOfficersNew::get();

        foreach ($loan_officers as $loan_officer) {
            $loan_officer -> soc_sec = Crypt::encrypt($loan_officer -> soc_sec);
            $loan_officer -> save();
        }
    }

    public function signs_and_posts(Request $request)
    {

        // $invoices = BillingInvoices::select(['in_id', 'in_date_sent', 'in_amount', 'in_agent_fullname'])
        // -> where('in_date_sent', '>', '2021-07-31')
        // -> whereHas('items', function (Builder $query) {
        //     $query -> where('in_item_desc', 'like', '%sign%')
        //     -> orWhere('in_item_desc', 'like', '%post%');
        // })
        // -> with(['items' => function($query) {
        //     $query -> where('in_item_desc', 'like', '%sign%')
        //     -> orWhere('in_item_desc', 'like', '%post%');
        // }])
        // -> limit(10)
        // -> get();

        $items = BillingInvoicesItems::select(['in_invoice_id', 'in_item_quantity', 'in_item_desc', 'in_item_total'])
        -> where(function ($query) {
            $query -> where('in_item_desc', 'like', '%sign%')
            -> orWhere('in_item_desc', 'like', '%post%');
        })
        -> whereHas('invoice', function (Builder $query) {
            $query -> where('in_date_sent', '>', '2021-07-31')
            -> where('in_type', 'charge');
        })
        -> with(['invoice' => function ($query) {
            $query -> where('in_date_sent', '>', '2021-07-31')
            -> where('in_type', 'charge')
            -> select(['in_id', 'in_type', 'in_agent_fullname', 'in_date_sent']);
        }])
        -> get();

        dd($items);
    }

    public function menu(Request $request)
    {
        return view('/tests/menu');
    }

    public function agent_data(Request $request)
    {
        $agents = Agents::select(['id', 'first', 'last', 'email1'])
        -> where('active', 'yes')
        -> with(['docs', 'licenses'])
        -> get()
        -> toJson();

        dd($agents);
    }

    public function alpine(Request $request)
    {
        return view('/tests/alpine');
    }

    public function test_connection() {

        try {

            $rets = Helper::rets_login();

            $resource = 'Office';
            $class = 'Office';

            $mod_time = date('Y-m-d H:i:s', strtotime('-12 hour'));
            $mod_time = str_replace(' ', 'T', $mod_time);
            $query = '(ModificationTimestamp='.$mod_time.'+)';

            $results = $rets -> Search(
                $resource,
                $class,
                $query
            );

        } catch(RETSException $e) {
            return $e -> getMessage();
        } catch(Throwable $e) {
            return $e -> getMessage();
        }

        dd($results);
    }

}
