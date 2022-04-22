<?php

namespace App\Jobs\Cron\BrightMLS;

use App\Helpers\Helper;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\BrightMLS\BrightAgentRoster;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class FindMissingAgentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    public $tries = 10;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this -> onQueue('bright_find_missing_agents');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        try {

            ini_set('memory_limit', '-1');

            $progress = 0;
            $this -> queueProgress(0);

            $this -> queueData(['Status 1:' => 'Attempting Login'], true);

            $rets = Helper::rets_login();

            if(!$rets) {
                sleep(5);
                $this -> queueData(['Status 2:' => 'Attempting Login Again'], true);
                $rets = Helper::rets_login();
            }

            $this -> queueData(['Status 3:' => 'Login Successful'], true);

            $resource = 'ActiveAgent';
            $class = 'ActiveMember';

            $start = '2001-01-01T00:00:00';
            $query = '((ModificationTimestamp='.$start.'+),~(MemberEmail=*mris.net),~(MemberEmail=*brightmls*))';

            $results = $rets -> Search(
                $resource,
                $class,
                $query,
                [
                    'Select' => 'MemberKey',
                ]
            );

            $this -> queueProgress(25);

            $agents = $results -> toArray();

            $found_in_bright = [];
            foreach($agents as $agent) {
                $found_in_bright[] = $agent['MemberKey'];
            }

            $db_agents = BrightAgentRoster::select('MemberKey') -> get() -> pluck('MemberKey') -> toArray();

            $missing_from_db = array_diff($found_in_bright, $db_agents);

            $this -> queueProgress(50);

            $this -> queueData(['Missing From DB' => count($missing_from_db)], true);

            if(count($missing_from_db) > 0) {

                $query = '(MemberKey='.implode(', ', $missing_from_db).')';

                $results = $rets -> Search(
                    $resource,
                    $class,
                    $query
                );

                $agents = $results -> toArray();

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

            $missing_from_bright = array_diff($db_agents, $found_in_bright);

            $this -> queueProgress(75);

            $this -> queueData(['Missing From Bright' => count($missing_from_bright)], true);

            if(count($missing_from_bright) > 0) {

                BrightAgentRoster::whereIn('MemberKey', $missing_from_bright) -> update([
                    'active' => 'no'
                ]);

            }

            $this -> queueProgress(100);

            $rets -> Disconnect();

            return true;

        } catch (\Throwable $exception) {
            $this -> queueData(['Failed' => 'Retrying'], true);
            $this -> release(90);
            return;
        }

    }

}
