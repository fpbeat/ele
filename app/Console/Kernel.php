<?php

namespace App\Console;

use App\Console\Commands\KickMembers;
use App\Console\Commands\NotifyAboveExpired;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(KickMembers::class)
            ->everyFifteenMinutes()
            ->runInBackground();

        $schedule->command(NotifyAboveExpired::class)
            ->everyThirtyMinutes()
            ->between('8:00', '22:00')
            ->runInBackground();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
