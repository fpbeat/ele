<?php

namespace App\Broadcasting;

use Illuminate\Notifications\Notification;

class TelegramChannel
{
    /**
     * @param $notifiable
     * @param Notification $notification
     * @return void
     */
    public function send($notifiable, Notification $notification): void
    {
        $notification->toTelegram($notifiable);
    }
}
