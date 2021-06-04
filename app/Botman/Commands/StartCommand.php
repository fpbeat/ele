<?php

namespace App\Botman\Commands;

use App\Botman\Traits\{KeyboardTrait, MessageTrait, UserStorage};
use App\Contracts\Botman\CommandInterface;
use App\Repositories\PageRepository;
use BotMan\BotMan\BotMan;

class StartCommand implements CommandInterface
{
    use MessageTrait, UserStorage, KeyboardTrait;

    /**
     * @inheritDoc
     */
    public function handle(BotMan $bot, array $command): void
    {
        if ($this->getStorageValue('catalog.keyboard')) {
            $this->setStorageValue('catalog.keyboard', false);
            $this->removeKeyboardWithMessage($bot, $command['instance']->name);
        }

        $this->nodeConversation(resolve(PageRepository::class)->getRootNode(), $bot);
    }
}
