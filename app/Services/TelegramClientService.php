<?php

namespace App\Services;

use App\Exceptions\Botman\TelegramSendException;
use App\Services\Botman\CustomRequestResponse;
use BotMan\Drivers\Telegram\TelegramDriver;

class TelegramClientService
{
    /**
     * @param int $userId
     * @param string $message
     * @return void
     * @throws TelegramSendException
     */
    public function sendMessage(int $userId, string $message): void
    {
        try {
            $response = resolve('botman')->say($message, $userId, TelegramDriver::class);

            CustomRequestResponse::loadFromResponse($response)->throwIfFailed();
        } catch (\Throwable $e) {
            throw new TelegramSendException($e->getMessage());
        }
    }
}
