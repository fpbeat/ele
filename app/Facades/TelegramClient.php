<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static sendMessage(int $userId, string $message): string
 */

class TelegramClient extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'telegram_client';
    }

}
