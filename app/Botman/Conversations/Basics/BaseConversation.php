<?php

namespace App\Botman\Conversations\Basics;

use BotMan\BotMan\Messages\{Conversations\Conversation, Incoming\IncomingMessage};

abstract class BaseConversation extends Conversation
{
    /**
     * @var int
     */
    protected $cacheTime = 60 * 24 * 30;

    /**
     * @param IncomingMessage $message
     * @return bool
     */
    public function stopsConversation(IncomingMessage $message): bool
    {
        return $message->getText() === '/start';
    }
}
