<?php

namespace Geisi\DynDns\Notifications;

use Geisi\DynDns\Events\DynDNSUpdated;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PublicIPChangedNotification extends Notification
{
    use Queueable;

    public function __construct(public DynDNSUpdated $event)
    {
    }

    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('Your public IP Address changed')
            ->line('The public IP Address of your system "'.config('app.name').'" changed!')
            ->line('Old IP :'.$this->event->oldIp)
            ->line('New IP :'.$this->event->newIp)
            ->line('Thank you for using our application!');
    }
}
