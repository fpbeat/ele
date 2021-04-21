<?php

return [
    'token' => env('BOTMAN_TELEGRAM_TOKEN'),
    'hideInlineKeyboard' => false,

    'default_additional_parameters' => [
        'parse_mode' => 'HTML',
        'disable_web_page_preview' => true
    ]
];
