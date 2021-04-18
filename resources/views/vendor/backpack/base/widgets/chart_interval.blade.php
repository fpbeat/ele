@php
    $controller = app($widget['controller']);
    $chart = $controller->chart;
    $path = $controller->getLibraryFilePath();

    $current = $controller->getChartInterval();

    // defaults
    $widget['wrapper']['class'] = $widget['wrapper']['class'] ?? $widget['wrapperClass'] ?? 'col-sm-6 col-md-4';
@endphp

@includeWhen(!empty($widget['wrapper']), 'backpack::widgets.inc.wrapper_start')
<div class="{{ $widget['class'] ?? 'card' }}">
    @if (isset($widget['content']['header']))
        <div class="card-header align-text-middle">
            <div class="row">
                <div class="col-sm-5 align-self-center">
                    @switch($current)
                        @case('hourly')
                        Hourly
                        @break

                        @case('daily')
                        Daily
                        @break

                        @case('monthly')
                        Monthly
                        @break

                    @endswitch

                    {!! $widget['content']['header'] !!}
                </div>
                <div class="col-sm-7">
                    <div class="btn-group float-right btn-group-sm chart-interval-switcher" role="group"
                         aria-label="Activity switcher">
                        <button class="btn btn-light @if ($current === 'hourly') active @endif" type="button"
                                data-url="{{ route('backpack.chart.interval', [$controller::CHART_TYPE, 'hourly']) }}">
                            Hour
                        </button>
                        <button class="btn btn-light @if ($current === 'daily') active @endif" type="button"
                                data-url="{{ route('backpack.chart.interval', [$controller::CHART_TYPE, 'daily']) }}">
                            Day
                        </button>
                        <button class="btn btn-light @if ($current === 'monthly') active @endif" type="button"
                                data-url="{{ route('backpack.chart.interval', [$controller::CHART_TYPE, 'monthly']) }}">
                            Month
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div class="card-body">
        {!! $widget['content']['body'] ?? '' !!}

        <div class="card-wrapper">
            {!! $chart->container() !!}
        </div>

    </div>
</div>
@includeWhen(!empty($widget['wrapper']), 'backpack::widgets.inc.wrapper_end')

@push('after_scripts')
    @if (is_array($path))
        @foreach ($path as $string)
            <script src="{{ $string }}" charset="utf-8"></script>
        @endforeach
    @elseif (is_string($path))
        <script src="{{ $path }}" charset="utf-8"></script>
    @endif

    {!! $chart->script() !!}

@endpush
