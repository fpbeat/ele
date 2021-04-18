<?php

namespace App\Repositories;

use App\Models\TelegramUser;
use App\Models\TelegramUserMessage;
use Illuminate\Database\Eloquent\Collection;

class TelegramUserMessageRepository
{
    /**
     * @param array $data
     * @return TelegramUserMessage
     */
    public function store(array $data): TelegramUserMessage
    {
        return TelegramUserMessage::create($data);
    }

    /**
     * @param int $id
     * @return TelegramUserMessage
     */
    public function getById(int $id): TelegramUserMessage
    {
        return TelegramUserMessage::whereId($id)->firstOrFail();
    }

    /**
     * @param int $userId
     * @return Collection
     */
    public function getAllByUserId(int $userId): Collection
    {
        return TelegramUserMessage::whereUserId($userId)->get();
    }

    /**
     * @param $userId
     * @param array $attributes
     */
    public function update($userId, array $attributes): void
    {
        $user = $this->getById($userId);

        $user->fill($attributes);
        $user->save();
    }

    /**
     * @param int $userId
     */
    public function setSentStatus(int $userId): void
    {
        $this->update($userId, [
            'is_sent' => 1
        ]);
    }

    /**
     * @return array
     */
    public function getGroupedPagesArray(): array
    {
        return TelegramUser::groupByLastPage()
            ->pluck('name', 'last_page_id')
            ->toArray();
    }
}
