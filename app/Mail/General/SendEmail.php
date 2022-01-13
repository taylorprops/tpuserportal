<?php

namespace App\Mail\General;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $message;
    public $theme = 'default';
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($message)
    {
        $this -> message = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this -> view('mail.general.send_email')
        -> from($this -> message['from'])
        -> subject($this -> message['subject'])
        -> with([
            'body' => $this -> message['body'],
        ]);
    }
}
