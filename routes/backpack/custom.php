<?php

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group(['prefix' => config('backpack.base.route_prefix', 'admin'), 'middleware' => array_merge((array)config('backpack.base.web_middleware', 'web'), (array)config('backpack.base.middleware_key', 'admin')), 'namespace' => 'App\Http\Controllers\Admin'], function () {
    // Activities charts
    Route::get('charts/activity/hourly', 'Charts\ActivityHourlyController@response')
        ->name('backpack.chart.activity.hourly');

    Route::get('charts/activity/daily', 'Charts\ActivityDailyController@response')
        ->name('backpack.chart.activity.daily');

    Route::get('charts/activity/monthly', 'Charts\ActivityMonthlyController@response')
        ->name('backpack.chart.activity.monthly');

    // Chart save
    Route::get('chart/save/{chart}/{interval}', 'IntervalChartController@save')
        ->where('chart', 'activity')
        ->where('interval', 'hourly|daily|monthly|yearly')
        ->name('backpack.chart.interval');

    // CRUD
    Route::crud('message', 'MessageCrudController');
    Route::crud('page', 'PageCrudController');
    Route::crud('feedback', 'FeedbackCrudController');
    Route::crud('setting', 'SettingCrudController');
    Route::crud('telegram/user', 'TelegramUserCrudController');
}); // this should be the absolute last line of this file
