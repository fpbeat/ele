<?php

namespace App\Http\Controllers\Admin\Charts;

use App\Models\Member;

use App\Http\Controllers\Admin\BaseChartController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use ConsoleTVs\Charts\Classes\Chartjs\Chart;
use Illuminate\Support\Collection;

/**
 * Class VotesChartController
 * @package App\Http\Controllers\Admin\Charts
 * @property-read CrudPanel $crud
 */
class ActivityHourlyController extends BaseChartController
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
            ->load(backpack_url('charts/activity/hourly'))
            ->displayLegend(true);
    }

    /**
     * @return void
     */
    protected function source(): void
    {
        $this->members = Member::groupByHours()
            ->get();
    }

    /**
     * @return Collection
     */
    private function getZeroFilledHours(): Collection
    {
        return collect(array_fill(0, now()->hour + 1, 0));
    }

    /**
     * @return Collection
     */
    protected function getLabels(): Collection
    {
        return $this->getZeroFilledHours()
            ->keys()
            ->map(function ($name) {
                return sprintf('%02d', $name);
            });
    }

    /**
     * @return Collection
     */
    protected function getMembersValues(): Collection
    {
        $original = $this->members->pluck('cnt', 'hour');

        return $this->getZeroFilledHours()->replace($original);
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
