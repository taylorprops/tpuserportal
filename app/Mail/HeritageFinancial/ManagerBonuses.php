<?php

namespace App\Mail\HeritageFinancial;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ManagerBonuses extends Mailable
{
    use Queueable, SerializesModels;

    protected $message;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $to_email = $this->message->to;
        $subject = $this->message->subject;
        $from_name = $this->message->from_name;
        $from_email = $this->message->from_email;
        $content = $this->message->content;

        return $this->view('mail.heritage_financial.manager_bonuses')
            ->to($to_email)
            ->from($from_email, $from_name)
            ->replyTo($from_email, $from_name)
            ->subject($subject)
            ->with([$content]);
    }
}
