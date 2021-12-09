<?php

namespace App\Jobs\Cron\BrightMLS;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use App\Models\BrightMLS\BrightOffices;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class UpdateOfficesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this -> onQueue('bright_update_offices');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

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

        $progress = 0;
        $this -> queueProgress($progress);

        $offices = $results -> toArray();
        $total_found = count($offices);

        $count_before = BrightOffices::get() -> count();

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

        $count_after = BrightOffices::get() -> count();
        $this -> queueData(['count before' => $count_before, 'count after' => $count_after], true);
        $this -> queueProgress(100);

        $rets -> Disconnect();

    }
}
