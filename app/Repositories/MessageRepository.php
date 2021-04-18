<?php

namespace App\Repositories;

use App\Models\Message;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class MessageRepository
{
    /**
     * @var array|Collection
     */
    static $messages = [];

    /**
     * MessageRepository constructor.
     */
    public function __construct()
    {
        $this->refillMessages();
    }

    /**
     * @return Collection
     */
    public function getAll(): Collection
    {
        return Message::all()->mapWithKeys(function ($message) {
            return [strtolower($message->key) => Str::cleanupSummernote($message->message)];
        });
    }

    /**
     * @param string $key
     * @param array $replace
     * @return string|null
     */
    public function get(string $key, $replace = []): ?string
    {
        $key = strtolower($key);

        if (self::$messages->has($key)) {
            return strtr(self::$messages->get($key), $replace);
        }

        return NULL;
    }

    /**
     * @return void
     */
    private function refillMessages(): void
    {
        if (count(self::$messages) === 0) {
            self::$messages = $this->getAll();
        }
    }
}
