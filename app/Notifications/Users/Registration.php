<?php

namespace App\Notifications\Users;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Registration extends Notification
{
    use Queueable;

    public $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this -> user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
        -> subject('Registration Notification')
        -> html(
            '/vendor/mail/auth/register', ['user' => $this -> user]
        );
                    // -> subject('Registration Notification')
                    // -> line('<div style="font-size: 14px; font-weight: bold; margin-bottom: 12px;">Hello '.$this -> user -> first_name.',</div>')
                    // -> line('You are receiving this email because an account was set up for you by '.$this -> user -> company.'. Please click the link below to set up your account')
                    // -> action('Register Account', url($this -> user -> registration_link));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
