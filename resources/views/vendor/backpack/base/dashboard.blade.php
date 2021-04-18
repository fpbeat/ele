@extends(backpack_view('blank'))

@php
    $telegramUsers = \App\Models\TelegramUser::count();
    $todayTelegramUsers = \App\Models\TelegramUser::forToday()->count();

    $format = function ($number) {
        return number_format($number, 0, '.', ' ');
    };

    Widget::add()->to('before_content')->type('div')->class('row')->content([
         	Widget::make()
			->type('progress')
            ->wrapper([
                'class' =>'col-sm-6 col-lg-4'
            ])
			->class('card border-0 text-white bg-error')
			->value($telegramUsers)
			->description('Посетителей бота')
			->progress(100)
			->hint(sprintf('%s новых сегодня', $todayTelegramUsers)),

			Widget::make()
			->type('progress')
			   ->wrapper([
                'class' =>'col-sm-6 col-lg-4'
            ])
			->class('card border-0 text-white bg-notice')
			->value(0)
			->description('Всего заказов')
			->progress(100)
			->hint('0 новых заказов'),

			Widget::make()
			->type('progress')
			   ->wrapper([
                'class' =>'col-sm-6 col-lg-4'
            ])
			->class('card border-0 text-white bg-primary')
			->value(0)
			->description('Всего покапателей')
			->progress(100)
			->hint('0 покупателей сегодня'),
   ]);
@endphp
