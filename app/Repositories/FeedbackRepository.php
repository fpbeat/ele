<?php

namespace App\Repositories;

use App\Models\Feedback;
use App\Models\TelegramUser;
use Illuminate\Support\Str;

class FeedbackRepository
{
    /**
     * @param TelegramUser $telegramUser
     * @param string|null $message
     * @param string $type
     * @return Feedback
     */
    public function store(TelegramUser $telegramUser, ?string $message, string $type): Feedback
    {
        return Feedback::create([
            'user_id' => $telegramUser->id,
            'message' => $message,
            'type' => $type
        ]);
    }

    /**
     * @return array
     */
    public function getGroupedUsersArray(): array
    {
        return Feedback::groupByUser()
            ->pluck('full_name', 'user_id')
            ->toArray();
    }
}
