<?php

namespace App\Jobs\Cron\BrightMLS;

use App\Helpers\Helper;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use App\Models\BrightMLS\BrightOffices;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\BrightMLS\BrightAgentRoster;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class UpdateAgentsAndOfficesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this -> onQueue('bright_update_agents_and_offices');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {

        ini_set('memory_limit','-1');

        $rets = Helper::rets_login();

        $this -> queueData(['uuid' => $this -> job -> uuid()], true);

        $progress = 0;
        $this -> queueProgress($progress);

        $this -> update_offices($rets);
        $this -> queueProgress(20);

        $this -> update_agents($rets);
        $this -> queueProgress(50);

        $this -> remove_agents($rets);
        $this -> queueProgress(100);

        $rets -> Disconnect();

        return true;

    }


    public function update_offices($rets) {

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

        $offices = $results -> toArray();
        $total_found = count($offices);

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

        $count_after = BrightOffices::get() -> count();
        $this -> queueData(['Offices', 'count before' => $count_before, 'count after' => $count_after], true);

    }


    public function update_agents($rets) {

        $resource = 'ActiveAgent';
        $class = 'ActiveMember';

        $mod_time = date('Y-m-d H:i:s', strtotime('-12 hour'));
        $mod_time = str_replace(' ', 'T', $mod_time);
        $query = '(ModificationTimestamp='.$mod_time.'+)';

        $results = $rets -> Search(
            $resource,
            $class,
            $query
        );

        $agents = $results -> toArray();
        $total_found = count($agents);

        $count_before = BrightAgentRoster::get() -> count();

        if($total_found > 0) {

            $progress = 20;
            $increment = 30 / count($agents);
            foreach ($agents as $agent) {

                $progress += $increment;
                $this -> queueProgress($progress);

                $agent_details = array_filter($agent);
                $agent['active'] = 'yes';
                $MemberKey = $agent['MemberKey'];
                unset($agent_details['MemberKey']);

                $add_agent = BrightAgentRoster::firstOrCreate(
                    ['MemberKey' => $MemberKey],
                    $agent_details
                );

                $add_agent -> save();

            }

        }

        $count_after = BrightAgentRoster::get() -> count();
        $this -> queueData(['Agents', 'count before' => $count_before, 'count after' => $count_after], true);

    }

    public function remove_agents($rets) {

        $resource = 'ActiveAgent';
        $class = 'ActiveMember';

        $query = '(MemberStatus=|Active)';

        $results = $rets -> Search(
            $resource,
            $class,
            $query,
            [
                'Count' => '0',
                'Select' => 'MemberKey'
            ]
        );

        $agents_in_bright = $results -> toArray();
        $agents_in_bright_count = count($agents_in_bright);
        $agents_in_bright_array = [];
        foreach($agents_in_bright as $agent_in_bright) {
            $agents_in_bright_array[] = (int)$agent_in_bright['MemberKey'];
        }

        $agents_in_db = BrightAgentRoster::where('active', 'yes') -> get() -> pluck('MemberKey') -> toArray();
        $agents_in_db_count = count($agents_in_db);

        if($agents_in_bright_count != $agents_in_db_count) {

            $deactivate_agents = [];
            foreach($agents_in_db as $agent) {
                if(!in_array($agent, $agents_in_bright_array)) {
                    $deactivate_agents[] = $agent;
                }
            }

            if(count($deactivate_agents) > 0) {

                BrightAgentRoster::whereIn('MemberKey', $deactivate_agents)
                -> update([
                    'active' => 'no',
                ]);

            }

            $missing_agents = [];
            foreach($agents_in_bright_array as $agent) {
                if(!in_array($agent, $agents_in_db)) {
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
                        'Limit' => 5000
                    ]
                );

                $agents = $results -> toArray();

                if(count($agents) > 0) {

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

        }

    }

    // public function remove_agents($rets) {

    //     $resource = 'ActiveAgent';
    //     $class = 'ActiveMember';
    //     $search_for = 10000;

    //     $select = ['MemberKey'];
    //     $agents_in_db_array = BrightAgentRoster::select($select)
    //     -> where(function($query) {
    //         $query -> where('removal_date_checked', '!=', date('Y-m-d'))
    //         -> orWhereNull('removal_date_checked');
    //     })
    //     // -> where('active', 'yes')
    //     -> limit($search_for)
    //     -> get()
    //     -> pluck('MemberKey')
    //     -> toArray();

    //     if (count($agents_in_db_array) < $search_for) {
    //         $search_for = count($agents_in_db_array);
    //     }

    //     $data[] = 'search_for = '.$search_for.', agents_in_db_array = '.count($agents_in_db_array);

    //     if ($search_for > 0) {

    //         $agents_in_db_string = implode(', ', $agents_in_db_array);

    //         $query = '(MemberKey='.$agents_in_db_string.')';

    //         $results = $rets -> Search(
    //             $resource,
    //             $class,
    //             $query,
    //             [
    //                 'Count' => 0
    //             ]
    //         );

    //         $agents = $results -> toArray();
    //         $total_found = count($agents);
    //         $data[] = 'total_found = '.$total_found;

    //         // if not all agents in db are found in bright
    //         if ($total_found != $search_for) {

    //             $data[] = 'Found Missing';
    //             $MemberKeys = [];

    //             $increment = 50 / count($agents);
    //             $progress = 50;
    //             foreach ($agents as $agent) {
    //                 $MemberKeys[] = $agent['MemberKey'];
    //                 $progress += $increment;
    //                 $this -> queueProgress($progress);
    //             }

    //             $not_found = array_diff($agents_in_db_array, $MemberKeys);

    //             $deactivate_agents = BrightAgentRoster::whereIn('MemberKey', $not_found)
    //             -> update([
    //                 'active' => 'no',
    //                 'removal_date_checked' => date('Y-m-d')
    //             ]);

    //         }

    //         $update_removal_date_checked = BrightAgentRoster::whereIn('MemberKey', $agents_in_db_array)
    //         -> update([
    //             'removal_date_checked' => date('Y-m-d')
    //         ]);

    //     }

    //     $this -> queueData([$data], true);

    // }
}
