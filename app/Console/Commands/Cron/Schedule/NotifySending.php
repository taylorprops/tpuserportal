<?php

namespace App\Console\Commands\Cron\Schedule;

use App\Jobs\Cron\Schedule\NotifySendingJob;
use Illuminate\Console\Command;

class NotifySending extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:notify_sending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify Departments when sending an email';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        NotifySendingJob::dispatch();

    }
}
