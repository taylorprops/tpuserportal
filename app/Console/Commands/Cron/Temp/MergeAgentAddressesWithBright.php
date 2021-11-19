<?php

namespace App\Console\Commands\Cron\Temp;

use Illuminate\Console\Command;
use App\Models\Marketing\AgentAddresses;
use App\Models\OldDB\Marketing\BrightAgents;

class MergeAgentAddressesWithBright extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agent_addresses:merge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Merge agent addresses with bright agents';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /* $agents_from_addresses = AgentAddresses::whereNull('found_status') -> limit(120) -> get();

        foreach ($agents_from_addresses as $agent) {

            $first = $agent -> first_name;
            $last = $agent -> last_name;

            $bright_agents = BrightAgents::where('MemberFirstName', $first)
            -> where('MemberLastName', $last) -> get();

            if ($bright_agents) {

                if (count($bright_agents) == 0) {
                    $agent -> found_status = 'not_found';
                } else if (count($bright_agents) == 1) {
                    $agent -> found_status = 'found';
                    $member_key = $bright_agents -> first() -> MemberKey;
                    $agent -> bright_MemberKey = $member_key;
                    $agent -> active = 'yes';
                } else if (count($bright_agents) > 1) {
                    $agent -> found_status = 'multiple';
                }

                $agent -> save();

            }

        } */


        $multiple_agents = AgentAddresses::where('found_status', 'multiple') -> where('checked', 'no') -> limit(10) -> get();

        foreach ($multiple_agents as $agent) {

            $first = $agent -> first_name;
            $last = $agent -> last_name;
            $middle = $agent -> middle_name;

            $bright_agents = BrightAgents::where('MemberFirstName', $first)
            -> where('MemberLastName', $last)
            -> where('MemberMiddleInitial', $middle)
            -> get();

            dump($bright_agents);

            if (count($bright_agents) == 0) {
                $agent -> found_status = 'not_found';
            } else if (count($bright_agents) == 1) {
                $agent -> found_status = 'found';
                $member_key = $bright_agents -> first() -> MemberKey;
                $agent -> bright_MemberKey = $member_key;
                $agent -> active = 'yes';
            } else if (count($bright_agents) > 1) {
                $agent -> found_status = 'multiple';
            }

            $agent -> checked = 'yes';
            //$agent -> save();

        }

        return true;
    }

}
