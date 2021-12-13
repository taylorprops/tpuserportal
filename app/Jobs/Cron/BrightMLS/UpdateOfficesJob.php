<?php

namespace App\Jobs\Cron\BrightMLS;

use App\Helpers\Helper;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
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

    //public $failOnTimeout = true;

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

        $rets = Helper::rets_login();

        $this -> queueData(['uuid' => $this -> job -> uuid()], true);

        $resource = 'Office';
        $class = 'Office';

        $mod_time = date('Y-m-d H:i:s', strtotime('-12 hour'));
        $mod_time = str_replace(' ', 'T', $mod_time);
        $query = '(ModificationTimestamp='.$mod_time.'+)';


        try {

            $results = $rets -> Search(
                $resource,
                $class,
                $query,
                [
                    'Count' => 0
                ]
            );

        } catch (Throwable $e) {
            return $e -> getTraceAsString();
        }

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

        sleep(5);
        $rets -> Disconnect();
        return true;

    }


}
