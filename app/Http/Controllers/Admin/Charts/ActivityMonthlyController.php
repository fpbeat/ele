<?php

namespace App\Http\Controllers\Admin\Charts;

use App\Http\Controllers\Admin\BaseChartController;
use App\Models\Member;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use ConsoleTVs\Charts\Classes\Chartjs\Chart;
use Illuminate\Support\Collection;

/**
 * Class VotesChartController
 * @package App\Http\Controllers\Admin\Charts
 * @property-read CrudPanel $crud
 */
class ActivityMonthlyController extends BaseChartController
{
    /**
     * @var string
     */
    const CHART_TYPE = 'activity';

    /**
     * @var Collection
     */
    protected Collection $members;

    /**
     * @return  void
     */
    public function setup(): void
    {
        $this->chart = new Chart();

        $this->chart
            ->labels($this->getLabels())
            ->load(backpack_url('charts/activity/monthly'))
            ->displayLegend(true);
    }

    /**
     * @return void
     */
    protected function source(): void
    {
        $this->members = Member::groupByMonths()
            ->get();
    }

    /**
     * @return Collection
     */
    private function getZeroFilledDays(): Collection
    {
        return collect(now()->startOfYear()->monthsUntil(now()->month))->mapWithKeys(function ($interval) {
            return [$interval->format('M y') => 0];
        });
    }

    /**
     * @return Collection
     */
    protected function getLabels(): Collection
    {
        return $this->getZeroFilledDays()->keys();
    }

    /**
     * @return Collection
     */
    protected function getMembersValues(): Collection
    {
        $original = $this->members->mapWithKeys(function ($item) {
            return [$item->day->format('M y') => $item->cnt];
        });

        return $this->getZeroFilledDays()
            ->replace($original)
            ->values();
    }

    /**
     * @return void
     */
    protected function data(): void
    {
        $this->source();

        $this->chart
            ->dataset('Members', 'bar', $this->getMembersValues())
            ->color('rgb(66, 186, 150)')
            ->backgroundColor('rgba(66, 186, 150, 0.4)');
    }
}
