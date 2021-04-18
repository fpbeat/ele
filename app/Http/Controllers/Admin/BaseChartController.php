<?php

namespace App\Http\Controllers\Admin;

use Faker\Factory;
use Backpack\CRUD\app\Http\Controllers\ChartController;
use Illuminate\Support\Collection;

/**
 * Class CandidateCrudController
 * @package App\Http\Controllers\Admin
 */
class BaseChartController extends ChartController
{
    /**
     * @param string $base
     * @param int $alpha
     * @return Collection
     */
    protected function getRandomColors($base = '', $alpha = 1): Collection
    {
        $faker = Factory::create();
        $faker->seed($base);

        return Collection::times(10, function () use ($faker, $alpha) {
            return sprintf('rgba(%s, %s)', $faker->rgbColor, $alpha);
        });
    }

    /**
     * @return string
     */
    public function getChartInterval(): string
    {
        return IntervalChartController::getChartInterval(static::CHART_TYPE);
    }
}
