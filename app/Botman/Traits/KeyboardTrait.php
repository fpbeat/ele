<?php

namespace App\Botman\Traits;

use BotMan\Drivers\Telegram\Extensions\Keyboard;
use BotMan\Drivers\Telegram\Extensions\KeyboardButton;

trait KeyboardTrait
{
    protected function pageKeyboard(Keyboard $keyboard, $items, $buttonsPerRow = 1): Keyboard
    {
        collect($items)
            ->map(function ($single) {
                switch ($single['type']) {
                    case 'external_link':
                        return KeyboardButton::create($single['name'])->url($single['link']);
                    default:
                        return KeyboardButton::create($single['name'])->callbackData($single['page_id']);
                }
            })
            ->split(ceil(count($items) / $buttonsPerRow))
            ->each(function ($single) use ($keyboard) {
                $keyboard->addRow(...$single);
            });

        return $keyboard;
    }
}
