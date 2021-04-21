<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static get(string $key, $default = NULL): string
 * @method static toArray(): array
 */

class Setting extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'setting';
    }
}
