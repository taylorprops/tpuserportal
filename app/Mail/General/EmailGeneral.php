<?php

namespace App\Mail\General;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailGeneral extends Mailable
{
    use Queueable, SerializesModels;

    public $message;

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
        $mail = $this -> markdown('mail.general')
        -> from($this -> message['from']['email'], $this -> message['from']['name'])
        -> subject($this -> message['subject'])
        -> html($this -> message['body'])
        -> with([
            'message' => $this -> message,
        ]);

        if ($this -> message['attachments']) {
            foreach ($this -> message['attachments'] as $attachment) {
                $mail -> attach($attachment);
                // $mail -> attach($attachment -> getRealPath(),
                // [
                //     'as' => $attachment -> getClientOriginalName(),
                //     'mime' => $attachment -> getClientMimeType(),
                // ]);
            }
        }

        return $mail;
    }
}
