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

class RemoveAgentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this -> onQueue('bright_remove_agents');
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
        -> setRetsVersion('RETS/1.8')
		-> setUserAgent('Bright RETS Application/1.0')
		-> setHttpAuthenticationMethod('digest')
		-> setOption('use_post_method', true)
        -> setOption('disable_follow_location', false);

        $rets = new \PHRETS\Session($rets_config);
        $connect = $rets -> Login();

        $resource = 'ActiveAgent';
        $class = 'ActiveMember';
        $search_for = 10000;

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

        $data = null;
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


            if ($total_found != $search_for) {

                $data = 'Found Missing';
                $MemberKeys = [];

                foreach ($agents as $agent) {
                    $MemberKeys[] = $agent['MemberKey'];
                }

                $not_found = array_diff($agents_in_db_array, $MemberKeys);

                $deactivate_agents = BrightAgentRoster::whereIn('MemberKey', $agents_in_db_array)
                -> update([
                    'active' => 'no'
                ]);

            }

            $update_removal_date_checked = BrightAgentRoster::whereIn('MemberKey', $agents_in_db_array)
            -> update([
                'removal_date_checked' => date('Y-m-d')
            ]);

        }

        $this -> queueData($data, true);
        $this -> queueProgress(100);

    }

}
