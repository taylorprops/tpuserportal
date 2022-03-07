<?php

namespace App\Jobs\Cron\BrightMLS;

use App\Helpers\Helper;
use App\Models\BrightMLS\BrightAgentRoster;
use App\Models\BrightMLS\BrightOffices;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
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
    public function handle()
    {
        ini_set('memory_limit', '-1');

        $this -> queueData(['Status 1:' => 'Attempting Login'], true);

        $rets = Helper::rets_login();

        if(!$rets) {
            sleep(5);
            $this -> queueData(['Status 2:' => 'Attempting Login Again'], true);
            $rets = Helper::rets_login();
        }

        $this -> queueData(['Status 3:' => 'Login Successful'], true);

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

    public function update_offices($rets)
    {
        $resource = 'Office';
        $class = 'Office';

        $mod_time = date('Y-m-d H:i:s', strtotime('-24 hour'));
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

    public function update_agents($rets)
    {
        $resource = 'ActiveAgent';
        $class = 'ActiveMember';

        $mod_time = date('Y-m-d H:i:s', strtotime('-24 hour'));
        $mod_time = str_replace(' ', 'T', $mod_time);
        $query = '(ModificationTimestamp='.$mod_time.'+)';

        $results = $rets -> Search(
            $resource,
            $class,
            $query
        );

        $agents = $results -> toArray();
        $total_found = count($agents);

        $agents_count_before = BrightAgentRoster::get() -> count();

        if ($total_found > 0) {
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

        $agents_count_after = BrightAgentRoster::get() -> count();
        $this -> queueData(['Agents', 'agents count before' => $agents_count_before, 'agents count after' => $agents_count_after], true);
    }

    public function remove_agents($rets)
    {
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

        $this -> queueProgress(60);

        $agents_in_db = BrightAgentRoster::where('active', 'yes') -> get() -> pluck('MemberKey') -> toArray();
        $agents_in_db_count = count($agents_in_db);

        $this -> queueProgress(70);

        $this -> queueData(['Remove Agents', 'agents_in_bright_count' => $agents_in_bright_count, 'agents_in_db_count' => $agents_in_db_count], true);

        if ($agents_in_bright_count != $agents_in_db_count) {
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
                    'date_purged' => date('Y-m-d'),
                ]);
            }

            $missing_agents = [];
            foreach ($agents_in_bright_array as $agent) {
                if (! in_array($agent, $agents_in_db)) {
                    $missing_agents[] = $agent;
                }
            }

            if (count($missing_agents) > 0) {
                $agents_in_db_string = implode(', ', $missing_agents);

                $query = '(MemberKey='.$agents_in_db_string.')';

                $results = $rets -> Search(
                    $resource,
                    $class,
                    $query,
                    [
                        'Count' => 0,
                        'Limit' => 1000,
                    ]
                );

                $agents = $results -> toArray();

                $this -> queueProgress(80);

                if (count($agents) > 0) {
                    foreach ($agents as $agent) {
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
            }
        }

        $this -> queueProgress(90);

        // remove unwanted emails
        $reject_emails = ['yopmail.com', 'brightmls.com', 'mris.net'];
        $agents = BrightAgentRoster::where(function($query) use ($reject_emails) {
            foreach($reject_emails as $reject) {
                $query -> orWhere('MemberEmail', 'like', '%'.$reject.'%');
            }
        })
        -> update([
            'active' => 'no',
            'date_purged' => date('Y-m-d')
        ]);

    }


}
