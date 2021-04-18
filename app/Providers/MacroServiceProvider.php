<?php

namespace App\Providers;

use App\Mixins\ArrMixin;
use App\Mixins\StrMixin;
use Illuminate\Support\{Arr, ServiceProvider, Str};

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
