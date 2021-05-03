<?php

namespace App\Providers;

use App\Mixins\ArrMixin;
use App\Mixins\ReflectorMixin;
use App\Mixins\StrMixin;
use Illuminate\Support\{Arr, Reflector, ServiceProvider, Str};

class MacroServiceProvider extends ServiceProvider
{
    /**
     * @return void
     * @throws \ReflectionException
     */
    public function boot(): void
    {
        Arr::mixin(new ArrMixin);
        Str::mixin(new StrMixin);
    }
}
