<?php

namespace App\Jobs\Cron\BrightMLS;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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

            $this -> queueData(['Status 1:' => 'Attempting Login'], true);

            $rets = Helper::rets_login();

            if(!$rets) {
                sleep(5);
                $this -> queueData(['Status 2:' => 'Attempting Login Again'], true);
                $rets = Helper::rets_login();
            }

            $this -> queueData(['Status 3:' => 'Login Successful'], true);


            $progress = 0;
            $this -> queueProgress(0);


            $rets -> Disconnect();

            return true;

        } catch (\Throwable $exception) {
            $this -> queueData(['Failed' => 'Retrying'], true);
            $this -> release(90);
            return;
        }

    }

}
