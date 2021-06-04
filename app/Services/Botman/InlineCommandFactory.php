<?php

namespace App\Services\Botman;

use BotMan\BotMan\BotMan;

class InlineCommandFactory
{
    /**
     * @var \Closure
     */
    protected $handler;

    /**
     * @param string $command
     */
    public function __construct(string $command)
    {
        $this->handler = fn($instance, $bot) => app()->make($command)->handle($bot, $instance);
    }

    /**
     * @param $command
     * @return static
     */
    static public function create($command): self
    {
        return resolve(static::class, [
            'command' => $command
        ]);
    }

    /**
     * @param array $instance
     * @param BotMan $bot
     */
    public function execute(array $instance, BotMan $bot): void
    {
        call_user_func($instance['handler']->handler, $instance, $bot);
    }
}
