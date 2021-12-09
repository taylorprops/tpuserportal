<?php

namespace App\Jobs\Cron\BrightMLS;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\BrightMLS\BrightAgentRoster;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class UpdateAgentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this -> onQueue('bright_update_agents');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {


        $progress = 0;
        $this -> queueProgress($progress);

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

        $resource = 'ActiveAgent';
        $class = 'ActiveMember';

        $mod_time = date('Y-m-d H:i:s', strtotime('-12 hour'));
        $mod_time = str_replace(' ', 'T', $mod_time);
        $query = '(ModificationTimestamp='.$mod_time.'+)';

        $progress = 5;
        $this -> queueProgress($progress);

        $results = $rets -> Search(
            $resource,
            $class,
            $query,
            [
                'Count' => 0
            ]
        );

        $progress = 15;
        $this -> queueProgress($progress);

        $agents = $results -> toArray();
        $total_found = count($agents);

        $count_before = BrightAgentRoster::get() -> count();

        if($total_found > 0) {

            $increment = 85 / count($agents);
            foreach ($agents as $agent) {

                $progress += $increment;
                $this -> queueProgress($progress);

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

        $count_after = BrightAgentRoster::get() -> count();
        $this -> queueData(['count before' => $count_before, 'count after' => $count_after], true);
        $this -> queueProgress(100);

        //$rets -> Disconnect();

    }
}
