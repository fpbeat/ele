<?php

namespace App\Botman\Middlewares;

use App\Botman\Traits\UserStorage;
use BotMan\BotMan\Interfaces\Middleware\Captured;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Interfaces\Middleware\Heard;
use BotMan\BotMan\Interfaces\Middleware\Sending;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class CapturedMiddleware implements Captured
{
    use UserStorage;

    /**
     * Handle an incoming message.
     *
     * @param IncomingMessage $message
     * @param callable $next
     * @param BotMan $bot
     *
     * @return mixed
     */
    public function captured(IncomingMessage $message, $next, BotMan $bot)
    {

//        $ttl = $this->getStorageValue('coupon.ttl');
//
//        if ($this->getStorageValue('coupon.locked') && Carbon::parse($ttl)->lt(now())) {
//            $this->setStorageValue('coupon.locked', null);
//        }

        return $next($message);
    }
}
