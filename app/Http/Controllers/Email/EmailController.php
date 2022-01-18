<?php

namespace App\Http\Controllers\Email;

use Illuminate\Http\Request;
use App\Mail\General\EmailGeneral;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function email_list(Request $request) {

        $request -> validate([
            'subject' => 'required',
        ],
        [
            'required' => 'Required',
        ]);

        $message = [
            'company' => $request -> company,
            'subject' => $request -> subject,
            'from' => ['email' => auth() -> user() -> email, 'name' => auth() -> user() -> name],
        ];

        $recipients = json_decode($request -> recipients);

        foreach($recipients as $recipient) {

            $message['body'] = preg_replace('/%%FirstName%%/', substr($recipient -> name, 0, strpos($recipient -> name, ' ')), $request -> message);
            $to = ['email' => $recipient -> email, 'name' => $recipient -> name];
            if(config('app.env') != 'production') {
                $to = ['email' => 'miketaylor0101@gmail.com', 'name' => $recipient -> name];
            }

            Mail::to([$to])
                -> send(new EmailGeneral($message));

        }



    }
}
