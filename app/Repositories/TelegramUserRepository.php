<?php

namespace App\Repositories;

use App\Models\TelegramUser;
use Illuminate\Support\Arr;

class TelegramUserRepository
{
    /**
     * @param array $data
     * @return TelegramUser
     */
    public function store(array $data): TelegramUser
    {

        return TelegramUser::updateOrCreate(
            [
                'user_id' => Arr::get($data, 'id')
            ],
            [
                'username' => $data['username'],
                'full_name' => $this->getFullName($data['last_name'], $data['first_name']),
                'language_code' => $data['language_code']
            ]
        );
    }

    /**
     * @param int $userId
     * @return TelegramUser
     */
    public function getByUserId(int $userId): TelegramUser
    {
        return TelegramUser::whereUserId($userId)->firstOrFail();
    }

    /**
     * @param $userId
     * @param array $attributes
     */
    public function update($userId, array $attributes): void
    {
        $user = $this->getByUserId($userId);

        $user->fill($attributes);
        $user->save();
    }

    /**
     * @param int $userId
     * @param int $pageId
     */
    public function updateLastPage(int $userId, int $pageId): void
    {
        $this->update($userId, [
            'last_page_id' => $pageId
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

    /**
     * @param string|null $lastName
     * @param string|null $firstName
     * @return string
     */
    private function getFullName(?string $lastName, ?string $firstName): string
    {
        return collect(func_get_args())
            ->filter()
            ->join(' ');
    }

    /**
     * @param int $userId
     * @return bool
     */
    public function isLocked(int $userId): bool
    {
        $user = $this->getByUserId($userId);

        return $user->locked;
    }

}
