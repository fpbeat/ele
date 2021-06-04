<?php

namespace App\Contracts\Botman;

use BotMan\BotMan\BotMan;

interface CommandInterface
{

    /**
     * @param BotMan $bot
     * @param array $command
     */
    public function handle(BotMan $bot, array $command): void;
}
