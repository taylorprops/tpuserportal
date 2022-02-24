<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Marketing\AgentAddresses;
use App\Models\OldDB\Marketing\BrightAgents;
use Illuminate\Http\Request;

class AgentAddressesController extends Controller
{
    public function import_agent_addresses(Request $request)
    {
        $list_agents = AgentAddresses::get();

        foreach ($list_agents as $list_agent) {
            $name = $list_agent->name;
            $middle = null;
            if (substr_count($name, ' ') > 1) {
                $first = substr($name, 0, strpos($name, ' '));
                $first_space = strpos($name, ' ');
                $last_space = strrpos($name, ' ');
                $end = $last_space - 1 - $first_space;
                $middle = substr($name, $first_space + 1, $end);
                $last = substr($name, strrpos($name, ' ') + 1);
            } else {
                $first = substr($name, 0, strpos($name, ' '));
                $last = substr($name, strpos($name, ' ') + 1);
            }

            $list_agent->first_name = $first;
            $list_agent->last_name = $last;
            $list_agent->middle_name = $middle;
            $list_agent->save();
        }
    }

    public function merge_multiple_matches(Request $request)
    {
        $multiple_agents = AgentAddresses::where('found_status', 'multiple')->where('checked', 'no')->limit(200)->get();

        foreach ($multiple_agents as $agent) {
            $first = $agent->first_name;
            $last = $agent->last_name;
            $middle = $agent->middle_name;

            $bright_agents = BrightAgents::where('MemberFirstName', $first)
            ->where('MemberLastName', $last)
            ->where('MemberMiddleInitial', $middle)
            ->get();

            if (count($bright_agents) == 0) {
                $agent->found_status = 'not_found';
            } elseif (count($bright_agents) == 1) {
                $agent->found_status = 'found';
                $member_key = $bright_agents->first()->MemberKey;
                $agent->bright_MemberKey = $member_key;
                $agent->active = 'yes';
            } elseif (count($bright_agents) == 2) {
                $email_1 = $bright_agents->first()->MemberEmail;
                $email_2 = $bright_agents->skip(1)->take(1)->first()->MemberEmail;
                if ($email_1 == $email_2) {
                    $agent->found_status = 'found';
                    $member_key = $bright_agents->first()->MemberKey;
                    $agent->bright_MemberKey = $member_key;
                    $agent->active = 'yes';
                    //dump($email_1.' '.$email_2);
                }
            }

            $agent->checked = 'yes';
            $agent->save();
        }
    }
}
