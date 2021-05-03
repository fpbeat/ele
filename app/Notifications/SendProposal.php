<?php

namespace App\Notifications;

use App\Broadcasting\TelegramChannel;
use App\Facades\TelegramClient;
use App\Models\Feedback;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SendProposal extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Feedback
     */
    private Feedback $feedback;

    /**
     * @return void
     */
    public function __construct(Feedback $feedback)
    {
        $this->feedback = $feedback;
    }

    /**
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return [TelegramChannel::class];
    }

    /**
     * @param mixed $notifiable
     */
    public function toTelegram($notifiable): void
    {
        $message = view('telegram.notifications.proposal', [
            'feedback' => $this->feedback
        ]);

        TelegramClient::sendMessage($notifiable, $message);
    }
}
