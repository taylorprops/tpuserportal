<?php

namespace App\Console\Commands\Cron\Schedule;

use Illuminate\Console\Command;
use App\Models\Marketing\Schedule\Schedule;

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
        $events = Schedule::where('event_date', date('Y-m-d')) -> whereIn('status_id', ['26', '33', '24']) -> where('medium_id', '7') -> get();

        foreach($events as $event) {

            switch ($event -> company_id) {
                case 1: // TP
                    $tos = config('global.marketing_email_notification_TP');
                    break;
                case 2: // HF
                    $tos = config('global.marketing_email_notification_HF');
                    break;
                case 3: // HT
                    $tos = config('global.marketing_email_notification_HT');
                    break;
            }



        }

    }
}
