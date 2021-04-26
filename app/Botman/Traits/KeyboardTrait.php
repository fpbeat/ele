<?php

namespace App\Botman\Traits;

use App\Models\Page;
use App\Services\Botman\PageKeyboardService;
use BotMan\Drivers\Telegram\Extensions\Keyboard;

trait KeyboardTrait
{
    /**
     * @param Keyboard $keyboard
     * @param Page $node
     * @return Keyboard
     */
    protected function pageKeyboard(Keyboard $keyboard, Page $node): Keyboard
    {
        return resolve(PageKeyboardService::class)->buildKeyboard(
            $keyboard,
            $this->pageRepository->getButtonItems($node->id),
            $node->buttons_per_row,
            $node->buttons_navi_bottom
        );
    }

    /**
     * @param $answer
     * @return int|null
     */
    protected function getAnswerButtonId($answer): ?int
    {
        return $this->pageRepository->getButtonItems($this->node->id)
            ->pluck('name', 'page_id')
            ->search($answer) ?: null;
    }

    /**
     * @return array
     */
    protected function removeKeyboard(): array
    {
        return [
            'reply_markup' => json_encode([
                    'remove_keyboard' => true
                ]
            )];
    }
}
