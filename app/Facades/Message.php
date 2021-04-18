<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static get(string $key, $default = NULL): string
 */

class Message extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'message';
    }

}
