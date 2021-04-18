<?php

namespace App\Providers;

use App\Contracts\Botman\CustomRequestInterface;
use App\Services\Botman\CustomRequest;
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Support\Facades\{DB, File};
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register()
    {
        $this->app->bind(CustomRequestInterface::class, function () {
            $request = new CustomRequest();
            $request->bootstrap(TelegramDriver::class, config('botman'));

            return $request;
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function boot(\Illuminate\Http\Request $request)
    {
        \Debugbar::disable();

        // Add in boot function
        DB::listen(function ($query) {
            File::append(
                storage_path('/logs/query.log'),
                '[' . date('Y-m-d H:i:s') . ']' . PHP_EOL . $query->sql . ' [' . implode(', ', $query->bindings) . ']' . PHP_EOL . PHP_EOL
            );
        });
    }
}
