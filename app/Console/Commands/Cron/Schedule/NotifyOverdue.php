<?php

namespace App\Console\Commands\Cron\Schedule;

use App\Mail\General\EmailGeneral;
use App\Models\Marketing\Schedule\Schedule;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class NotifyOverdue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:notify_overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification when a schedule item is overdue';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // notify if overdue
        $events = Schedule::where('event_date', '<', date('Y-m-d'))
            -> where('active', true)
            -> whereNotIn('status_id', ['33', '24'])
            -> where('sending_notification_sent', false)
            -> with(['company', 'recipient', 'status'])
            -> get();

        $tos = config('global.overdue_notification_to_addresses');

        foreach ($events as $event) {

            $body = 'A scheduled marketing email is overdue.<br>
            Email ID: '.$event -> id.'<br>
            From: '.$event -> company -> item.'<br>
            To: '.$event -> recipient -> item.'<br>
            Status: '.$event -> status -> item;

            $message = [
                'company' => 'Taylor Properties',
                'subject' => 'Alert - Overdue Marketing Email',
                'from' => ['email' => 'internal@taylorprops.com', 'name' => 'Taylor Properties'],
                'body' => $body,
                'attachments' => null,
            ];

            Mail::to($tos)
                -> send(new EmailGeneral($message));

            $event -> sending_notification_sent = true;
            $event -> save();

        }
    }
}
