<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Employees\Agents;
use Illuminate\Support\Facades\Crypt;
use App\Models\Employees\EmployeesNotes;
use App\Models\BrightMLS\BrightAgentRoster;
use App\Models\Employees\EmployeesLicenses;
use App\Models\DocManagement\Admin\Forms\Forms;
use App\Models\OldDB\LoanOfficers as LoanOfficersOld;
use App\Models\Employees\LoanOfficers as LoanOfficersNew;
use App\Models\DocManagement\Resources\CommonFieldsGroups;

class TestsController extends Controller
{

    public function remove_agents_from_bright(Request $request) {

        $rets_config = new \PHRETS\Configuration;
        $rets_config -> setLoginUrl(config('global.rets_url'))
        -> setUsername(config('global.rets_username'))
        -> setPassword(config('global.rets_password'))
        -> setRetsVersion('RETS/1.8')
		-> setUserAgent('Bright RETS Application/1.0')
		-> setHttpAuthenticationMethod('digest') // or 'basic' if required
		-> setOption('use_post_method', true)
        -> setOption('disable_follow_location', false);

        $rets = new \PHRETS\Session($rets_config);
        $connect = $rets -> Login();

        $resource = 'ActiveAgent';
        $class = 'ActiveMember';
        $search_for = 13000;

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

                foreach ($agents as $agent) {
                    $MemberKeys[] = $agent['MemberKey'];
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



        /* if($total_found > 0) {

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

        } */

    }

    public function rets_test(Request $request) {

        $rets_config = new \PHRETS\Configuration;
        $rets_config -> setLoginUrl(config('global.rets_url'))
        -> setUsername(config('global.rets_username'))
        -> setPassword(config('global.rets_password'))
        -> setRetsVersion('RETS/1.8')
		-> setUserAgent('Bright RETS Application/1.0')
		-> setHttpAuthenticationMethod('digest') // or 'basic' if required
		-> setOption('use_post_method', true)
        -> setOption('disable_follow_location', false);

        $rets = new \PHRETS\Session($rets_config);
        $connect = $rets -> Login();

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

    }


    public function update_encrypted_fields() {

        $loan_officers = LoanOfficersNew::get();

        foreach ($loan_officers as $loan_officer) {
            $loan_officer -> soc_sec = Crypt::encrypt($loan_officer -> soc_sec);
            $loan_officer -> save();
        }

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
