<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static getInstantiableClassParameters(string $class): array
 */
class Reflection extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'reflection';
    }

}
