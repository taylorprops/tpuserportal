<?php

namespace App\Console\Commands\Cron\Schedule;

use App\Mail\General\EmailGeneral;
use App\Models\Marketing\Schedule\Schedule;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class NotifyNeedsReview extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:notify_needs_review';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Email Robb when he has emails that need to be reviewed';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $items = Schedule::where('status_id', '38')
            -> where('active', true)
            -> with(['company', 'recipient'])
            -> get();

        $to = 'senorrobb@yahoo.com';
        $cc = ['miketaylor0101@gmail.com'];
        $subject = 'You Have '.count($items).' Emails That Need Review';
        $body = 'You have '.count($items).' emails that need review<br><br>';

        $body .= '
        <table border="1" cellpadding="3">
            <tr>
                <th>Send Date</th>
                <th>ID</th>
                <th>From</th>
                <th>To</th>
                <th>Description</th>
                <th></th>
            </tr>';

        foreach ($items as $item) {
            $body .= '
            <tr>
                <td>'.$item -> event_date.'</td>
                <td>'.$item -> id.'</td>
                <td>'.$item -> company -> item.'</td>
                <td>'.$item -> recipient -> item.'</td>
                <td>'.$item -> description.'</td>
                <td><a href="https://tpuserportal.com/marketing/schedule?view='.$item -> id.'" target="_blank">View</a></td>
            </tr>';
        }
        $body .= '</table>';

        $message = [
            'company' => 'Taylor Properties',
            'subject' => $subject,
            'from' => ['email' => 'mike@taylorprops.com', 'name' => 'Mike Taylor'],
            'body' => $body,
            'attachments' => null,
        ];

        Mail::to([$to])
            -> cc($cc)
            -> send(new EmailGeneral($message));

    }
}
