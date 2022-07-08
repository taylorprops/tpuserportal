<?php

namespace App\Jobs\Cron\Schedule;

use App\Mail\General\EmailGeneral;
use App\Models\Marketing\Schedule\Schedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class NotifySendingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this -> onQueue('marketing_notify_sending');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $this -> queueProgress(0);

        $events = Schedule::where('event_date', '<=', date('Y-m-d'))
            -> whereIn('status_id', ['24'])
            -> where('medium_id', '7')
            -> where('sending_notification_sent', false)
            -> with(['company', 'recipient', 'uploads' => function ($query) {
                $query -> where('accepted_version', true);
            }])
            -> get();

        $this -> queueData(['Found '.count($events).' events'], true);

        if (count($events) > 0) {

            foreach ($events as $event) {

                switch ($event -> company_id) {
                    case '1': // TP
                        $tos = config('global.marketing_email_notification_TP');
                        break;
                    case '2': // HF
                        $tos = config('global.marketing_email_notification_HF');
                        break;
                    case '3': // HT
                        $tos = config('global.marketing_email_notification_HT');
                        break;
                }

                $html = $event -> uploads -> first() -> html;
                $details = 'Send Date: '.$event -> event_date.'<br>
                From: '.$event -> company -> item.'<br>
                To: '.$event -> recipient -> item.'<br>
                ID: '.$event -> id;

                $body = preg_replace('/(<body\s.*>)/', '$1'.$details, $html);

                $message = [
                    'company' => 'Taylor Properties',
                    'subject' => 'Marketing Email - '.$event -> subject_line_a,
                    'from' => ['email' => 'internal@taylorprops.com', 'name' => 'Taylor Properties'],
                    'body' => $body,
                    'attachments' => null,
                ];

                Mail::to($tos)
                    -> send(new EmailGeneral($message));

                $event -> sending_notification_sent = true;
                $event -> save();

                $this -> queueData([$event -> id.' - Completed'], true);

            }

        }

        $this -> queueProgress(100);

        return true;

    }
}
