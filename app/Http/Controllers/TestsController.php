<?php

namespace App\Http\Controllers;

use Monolog\Logger;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Employees\Agents;
use Monolog\Handler\StreamHandler;
use Illuminate\Support\Facades\Crypt;
use App\Models\BrightMLS\BrightOffices;
use Illuminate\Support\Facades\Storage;
use App\Models\Employees\EmployeesNotes;
use Illuminate\Database\Eloquent\Builder;
use App\Models\BrightMLS\BrightAgentRoster;
use App\Models\Employees\EmployeesLicenses;
use App\Models\OldDB\Company\BillingInvoices;
use App\Models\DocManagement\Admin\Forms\Forms;
use App\Models\OldDB\Company\BillingInvoicesItems;
use App\Models\Employees\Mortgage as LoanOfficersNew;
use App\Models\OldDB\LoanOfficers as LoanOfficersOld;
use App\Models\DocManagement\Resources\CommonFieldsGroups;

class TestsController extends Controller
{

    public function bright_remove_agents(Request $request) {

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

        $resource = 'ActiveAgent';
        $class = 'ActiveMember';
        $search_for = 500;

        $select = ['MemberKey'];
        $agents_in_db_array = BrightAgentRoster::select($select)
        -> where('removal_date_checked', '!=', date('Y-m-d'))
        -> orWhereNull('removal_date_checked')
        -> limit($search_for)
        -> get()
        -> pluck('MemberKey')
        -> toArray();

        if (count($agents_in_db_array) < $search_for) {
            $search_for = count($agents_in_db_array);
        }

        echo 'search_for = ' . $search_for.'<br>';

        if ($search_for > 0) {

            $agents_in_db_string = implode(', ', $agents_in_db_array);

            $query = '(MemberKey='.$agents_in_db_string.')';

            $results = $rets -> Search(
                $resource,
                $class,
                $query,
                [
                    'Count' => 0
                ]
            );

            $agents = $results -> toArray();
            $total_found = count($agents);

            dump('total_found '.$total_found.' - search_for '.$search_for);

            if ($total_found != $search_for) {
                dump(count($agents_in_db_array));
                $MemberKeys = [];

                $increment = $total_found / $search_for;
                $c = 0;
                foreach ($agents as $agent) {
                    $MemberKeys[] = $agent['MemberKey'];
                    $c += 1;
                    if ($c % $increment == 0 && $percent < 100) {
                        $percent = $c / $increment;
                        $this -> queueProgress($percent);
                        dump($percent);
                    }
                }

                dump(count($agents_in_db_array), count($MemberKeys));

                $not_found = array_diff($agents_in_db_array, $MemberKeys);

                dump($not_found);

                $deactivate_agents = BrightAgentRoster::whereIn('MemberKey', $agents_in_db_array)
                -> update([
                    'active' => 'no'
                ]);
            } else {
                $update_removal_date_checked = BrightAgentRoster::whereIn('MemberKey', $agents_in_db_array)
                -> update([
                    'removal_date_checked' => date('Y-m-d')
                ]);
            }
        }

        //$rets -> Disconnect();

    }

    public function bright_update_agents(Request $request) {

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
                'Count' => 0
            ]
        );

        $agents = $results -> toArray();
        $total_found = count($agents);

        if($total_found > 0) {

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

    public function bright_update_offices(Request $request) {

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

        dump($connect);

        $resource = 'Office';
        $class = 'Office';

        $mod_time = date('Y-m-d H:i:s', strtotime('-12 hour'));
        $mod_time = str_replace(' ', 'T', $mod_time);
        $query = '(ModificationTimestamp='.$mod_time.'+)';

        $results = $rets -> Search(
            $resource,
            $class,
            $query,
            [
                'Count' => 0
            ]
        );

        $offices = $results -> toArray();
        $total_found = count($offices);
        dump($total_found);
        //$count_before = BrightOffices::get() -> count();

        if($total_found > 0) {

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

        //$count_after = BrightOffices::get() -> count();

        //$rets -> Disconnect();

    }

    public function add_addresses_to_bright(Request $request) {

        $addresses = AgentsAddresses::where('found_status', 'found') -> get();

    }


    public function update_encrypted_fields() {

        $loan_officers = LoanOfficersNew::get();

        foreach ($loan_officers as $loan_officer) {
            $loan_officer -> soc_sec = Crypt::encrypt($loan_officer -> soc_sec);
            $loan_officer -> save();
        }

    }

    public function signs_and_posts(Request $request) {

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
        -> where(function($query) {
            $query -> where('in_item_desc', 'like', '%sign%')
            -> orWhere('in_item_desc', 'like', '%post%');
        })
        -> whereHas('invoice', function (Builder $query) {
            $query -> where('in_date_sent', '>', '2021-07-31')
            -> where('in_type', 'charge');
        })
        -> with(['invoice' => function($query) {
            $query -> where('in_date_sent', '>', '2021-07-31')
            -> where('in_type', 'charge')
            -> select(['in_id', 'in_type', 'in_agent_fullname', 'in_date_sent']);
        }])
        -> get();

        dd($items);

    }



    public function menu(Request $request) {

        return view('/tests/menu');

    }

    public function agent_data(Request $request) {

        $agents = Agents::select(['id', 'first', 'last', 'email1'])
        -> where('active', 'yes')
        -> with(['docs', 'licenses'])
        -> get()
        -> toJson();

        dd($agents);
    }

    public function alpine(Request $request) {

        return view('/tests/alpine');

    }

    public function test(Request $request) {

        $form = Forms::with(['fields', 'pages']) -> find(185);
        $pages = $form -> pages;

        $common_fields_people = CommonFieldsGroups::with(['sub_groups', 'sub_groups.common_fields'])
        -> where('group_name', 'People')
        -> first();

        return view('/tests/test', compact('form', 'pages', 'common_fields_people'));

    }
}
