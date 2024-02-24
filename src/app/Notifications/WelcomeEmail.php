<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class WelcomeEmail extends Notification
{
    use Queueable;

    protected $user;
    protected $password;

    public function __construct($user, $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Welcome to Our Application')
            ->greeting('Hello, ' . $this->user->name)
            ->line('Welcome to our application. We are glad to have you on board.')
            ->line('Your temporary password is: ' . $this->password)
            ->action('Login Now', url('/login'))
            ->line('Thank you for using our application!');
    }
}
