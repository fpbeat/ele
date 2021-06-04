<?php

namespace App\Botman\Commands;

use App\Botman\Traits\{KeyboardTrait, MessageTrait, UserStorage};
use App\Contracts\Botman\CommandInterface;
use App\Repositories\PageRepository;
use BotMan\BotMan\BotMan;

class BasketCommand implements CommandInterface
{
    use MessageTrait, UserStorage, KeyboardTrait;

    /**
     * @inheritDoc
     */
    public function handle(BotMan $bot, array $command): void
    {
     //   $this->nodeConversation(resolve(PageRepository::class)->getRootNode(), $bot);
    }
}
