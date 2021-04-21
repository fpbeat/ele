<?php

namespace App\Botman\Traits;

use App\Models\Page;
use App\Services\Botman\PageKeyboardService;
use BotMan\Drivers\Telegram\Extensions\Keyboard;

trait KeyboardTrait
{
    protected function pageKeyboard(Keyboard $keyboard, Page $node): Keyboard
    {
        return resolve(PageKeyboardService::class)->buildKeyboard(
            $keyboard,
            $this->pageRepository->getButtonItems($node->id),
            $node->buttons_per_row,
            $node->buttons_navi_bottom
        );
    }
}
