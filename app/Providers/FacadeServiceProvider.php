<?php

namespace App\Providers;

use App\Repositories\MessageRepository;
use App\Services\TelegramClientService;
use Illuminate\Support\ServiceProvider;

class FacadeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        app()->singleton('message', function () {
            return new MessageRepository;
        });

        app()->singleton('telegram_client', function () {
            return new TelegramClientService;
        });
    }
}
