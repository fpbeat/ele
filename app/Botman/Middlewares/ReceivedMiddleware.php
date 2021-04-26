<?php

namespace App\Botman\Middlewares;

use App\Facades\Message;
use App\Facades\TelegramClient;
use App\Repositories\TelegramUserRepository;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Interfaces\Middleware\Received;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use Illuminate\Support\Arr;

class ReceivedMiddleware implements Received
{
    /**
     * @var TelegramUserRepository
     */
    private TelegramUserRepository $telegramUserRepository;

    public function __construct(TelegramUserRepository $telegramUserRepository)
    {
        $this->telegramUserRepository = $telegramUserRepository;
    }

    /**
     * @param IncomingMessage $message
     * @param callable $next
     * @param BotMan $bot
     * @return IncomingMessage|null
     */
    public function received(IncomingMessage $message, $next, BotMan $bot): ?IncomingMessage
    {
        $payload = $message->getPayload();


      //  dd($payload['reply_markup']);
        if (Arr::has($payload, 'reply_markup.keyboard')) {
            dd('yes');
        }

        if (!Arr::get($payload, 'from.is_bot')) {
            $this->telegramUserRepository->store($payload['from']);

            if ($this->handleLockedUser(Arr::get($payload, 'from.id', 0))) {
                return null;
            }
        }

        return $next($message);
    }

    /**
     * @param int $userId
     * @return bool
     */
    private function handleLockedUser(int $userId): bool
    {
        if ($this->telegramUserRepository->isLocked($userId)) {
            TelegramClient::sendMessage($userId, Message::get('telegramUserLocked'));

            return true;
        }

        return false;
    }
}


