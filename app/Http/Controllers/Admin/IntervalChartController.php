<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Charts\{ActivityDailyController, ActivityHourlyController, ActivityMonthlyController};
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;

/**
 * Class CandidateCrudController
 * @package App\Http\Controllers\Admin
 */
class IntervalChartController
{
    /**
     * @var string
     */
    const INTERVAL_HOURLY = 'hourly';

    /**
     * @var string
     */
    const INTERVAL_DAILY = 'daily';

    /**
     * @var string
     */
    const INTERVAL_MONTHLY = 'monthly';

    /**
     * @var string[][]
     */
    private static $mapping = [
        'activity' => [
            self::INTERVAL_HOURLY => ActivityHourlyController::class,
            self::INTERVAL_DAILY => ActivityDailyController::class,
            self::INTERVAL_MONTHLY => ActivityMonthlyController::class
        ]
    ];

    /**s
     * @param string $chart
     * @return string
     */
    public static function getChartClass(string $chart): string
    {
        $current = self::getChartInterval($chart);

        return self::$mapping[$chart][$current];
    }

    /**
     * @param string $chart
     * @return string
     */
    public static function getChartInterval(string $chart): string
    {
        return Cache::get(sprintf('backpack.chart.interval.%s', $chart), self::INTERVAL_HOURLY);
    }

    /**
     * @param string $chart
     * @param string $interval
     * @return RedirectResponse
     */
    public function save(string $chart, string $interval): RedirectResponse
    {
        Cache::forever(sprintf('backpack.chart.interval.%s', $chart), $interval);

        return redirect()->back();
    }
}
