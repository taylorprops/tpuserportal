<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\BrightMLS\BrightAgentRoster;
use App\Models\BrightMLS\BrightOffices;
use App\Models\DocManagement\Admin\Forms\Forms;
use App\Models\DocManagement\Resources\CommonFieldsGroups;
use App\Models\Employees\Agents;
use App\Models\Employees\EmployeesLicenses;
use App\Models\Employees\EmployeesNotes;
use App\Models\Employees\Mortgage as LoanOfficersNew;
use App\Models\Marketing\LoanOfficerAddresses;
use App\Models\OldDB\Company\BillingInvoices;
use App\Models\OldDB\Company\BillingInvoicesItems;
use App\Models\OldDB\LoanOfficers as LoanOfficersOld;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class TestsController extends Controller
{
    public function edit_los(Request $request)
    {

        //dump(count(LoanOfficerAddresses::whereNull('full_name') -> get()));

        // LoanOfficerAddresses::chunkById(10, function($loan_officers) {
        //     foreach ($loan_officers as $loan_officer) {
        //         $loan_officer -> full_name = $loan_officer -> first_name.' '.$loan_officer -> last_name;
        //         dump($loan_officer -> first_name.' '.$loan_officer -> last_name);
        //         $loan_officer -> save();
        //     }
        // }) -> limit(50);

        $loan_officers = LoanOfficerAddresses::whereRaw('length(state) > 2')->limit(10000)->get();

        foreach ($loan_officers as $loan_officer) {
            $state = 'MD';
            if (stristr($loan_officer->state, 'dc')) {
                $state = 'DC';
            } elseif (stristr($loan_officer->state, 'va')) {
                $state = 'VA';
            }
            $loan_officer->state = $state;
            $loan_officer->save();
        }
    }

    public function bright_remove_agents()
    {
        ini_set('memory_limit', '-1');

        $rets = Helper::rets_login();

        $resource = 'ActiveAgent';
        $class = 'ActiveMember';

        $query = '(MemberStatus=|Active)';

        $results = $rets->Search(
            $resource,
            $class,
            $query,
            [
                'Count' => '0',
                'Select' => 'MemberKey',
            ]
        );

        $agents_in_bright = $results->toArray();
        $agents_in_bright_count = count($agents_in_bright);
        $agents_in_bright_array = [];
        foreach ($agents_in_bright as $agent_in_bright) {
            $agents_in_bright_array[] = (int) $agent_in_bright['MemberKey'];
        }

        $agents_in_db = BrightAgentRoster::where('active', 'yes')->get()->pluck('MemberKey')->toArray();
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
            ->update([
                'active' => 'no',
            ]);
        }

        $missing_agents = [];
        foreach ($agents_in_bright_array as $agent) {
            if (! in_array($agent, $agents_in_db)) {
                $missing_agents[] = $agent;
            }
        }
        dd(count($missing_agents));
        if (count($missing_agents) > 0) {
            $agents_in_db_string = implode(', ', $missing_agents);

            $query = '(MemberKey='.$agents_in_db_string.')';

            $results = $rets->Search(
                $resource,
                $class,
                $query,
                [
                    'Count' => 0,
                    'Limit' => 5000,
                ]
            );

            $agents = $results->toArray();

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

                    $add_agent->save();
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
        $rets_config->setLoginUrl(config('global.rets_url'))
        ->setUsername(config('global.rets_username'))
        ->setPassword(config('global.rets_password'))
        ->setRetsVersion('RETS/1.7.2')
        ->setUserAgent('Bright RETS Application/1.0')
        ->setHttpAuthenticationMethod('digest') // or 'basic' if required
        ->setOption('use_post_method', true)
        ->setOption('disable_follow_location', false);

        $rets = new \PHRETS\Session($rets_config);
        $connect = $rets->Login();

        $log = new Logger('PHRETS');
        $log->pushHandler(new StreamHandler('php://stdout', Logger::DEBUG));
        $log->warning('Foo');
        $log->error('Bar');
        $rets->setLogger($log);

        $resource = 'ActiveAgent';
        $class = 'ActiveMember';

        $mod_time = date('Y-m-d H:i:s', strtotime('-25 hour'));
        $mod_time = str_replace(' ', 'T', $mod_time);
        $query = '(ModificationTimestamp='.$mod_time.'+)';

        $results = $rets->Search(
            $resource,
            $class,
            $query,
            [
                'Count' => 0,
            ]
        );

        $agents = $results->toArray();
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

                $add_agent->save();
            }
        }

        //$rets -> Disconnect();
    }

    public function bright_update_offices(Request $request)
    {
        $rets_config = new \PHRETS\Configuration;
        $rets_config->setLoginUrl(config('global.rets_url'))
        ->setUsername(config('global.rets_username'))
        ->setPassword(config('global.rets_password'))
        ->setRetsVersion('RETS/1.7.2')
        ->setUserAgent('Bright RETS Application/1.0')
        ->setHttpAuthenticationMethod('digest')
        ->setOption('use_post_method', true)
        ->setOption('disable_follow_location', false);

        $rets = new \PHRETS\Session($rets_config);

        $connect = $rets->Login();

        $resource = 'Office';
        $class = 'Office';

        $mod_time = date('Y-m-d H:i:s', strtotime('-12 hour'));
        $mod_time = str_replace(' ', 'T', $mod_time);
        $query = '(ModificationTimestamp='.$mod_time.'+)';

        if (Helper::access_protected_property($rets, 'rets_session_id') == '') {
            // $this -> queueData(['login failed, retrying'], true);
            sleep(5);
            $rets = new \PHRETS\Session($rets_config);
            $connect = $rets->Login();
        }

        Log::debug($this->job->uuid());

        return false;

        $results = $rets->Search(
            $resource,
            $class,
            $query,
            [
                'Count' => 0,
            ]
        );

        $offices = $results->toArray();
        $total_found = count($offices);

        echo 'found = '.$total_found.'<br>';

        $count_before = BrightOffices::get()->count();

        if ($total_found > 0) {
            foreach ($offices as $office) {
                $office_details = array_filter($office);
                $OfficeKey = $office['OfficeKey'];
                unset($office_details['OfficeKey']);

                $add_office = BrightOffices::firstOrCreate(
                    ['OfficeKey' => $OfficeKey],
                    $office_details
                );

                $add_office->save();
            }
        }

        $rets->Disconnect();
    }

    public function add_addresses_to_bright(Request $request)
    {
        $addresses = AgentsAddresses::where('found_status', 'found')->get();
    }

    public function update_encrypted_fields()
    {
        $loan_officers = LoanOfficersNew::get();

        foreach ($loan_officers as $loan_officer) {
            $loan_officer->soc_sec = Crypt::encrypt($loan_officer->soc_sec);
            $loan_officer->save();
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
        ->where(function ($query) {
            $query->where('in_item_desc', 'like', '%sign%')
            ->orWhere('in_item_desc', 'like', '%post%');
        })
        ->whereHas('invoice', function (Builder $query) {
            $query->where('in_date_sent', '>', '2021-07-31')
            ->where('in_type', 'charge');
        })
        ->with(['invoice' => function ($query) {
            $query->where('in_date_sent', '>', '2021-07-31')
            ->where('in_type', 'charge')
            ->select(['in_id', 'in_type', 'in_agent_fullname', 'in_date_sent']);
        }])
        ->get();

        dd($items);
    }

    public function menu(Request $request)
    {
        return view('/tests/menu');
    }

    public function agent_data(Request $request)
    {
        $agents = Agents::select(['id', 'first', 'last', 'email1'])
        ->where('active', 'yes')
        ->with(['docs', 'licenses'])
        ->get()
        ->toJson();

        dd($agents);
    }

    public function alpine(Request $request)
    {
        return view('/tests/alpine');
    }

    public function test(Request $request)
    {
        $form = Forms::with(['fields', 'pages'])->find(185);
        $pages = $form->pages;

        $common_fields_people = CommonFieldsGroups::with(['sub_groups', 'sub_groups.common_fields'])
        ->where('group_name', 'People')
        ->first();

        return view('/tests/test', compact('form', 'pages', 'common_fields_people'));
    }
}
