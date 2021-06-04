<?php

namespace App\Botman\Traits;

use App\Botman\Conversations\BasketConversation;
use App\Botman\Conversations\MainConversation;
use App\Contracts\NodeCategoryInterface;
use App\Models\Page;
use App\Services\Botman\PageKeyboardService;
use App\Services\Keyboard\InlineButton;
use App\Services\Keyboard\KeyboardBuilder;
use BotMan\Drivers\Telegram\Extensions\Keyboard;
use Illuminate\Support\Str;

trait KeyboardTrait
{
    /**
     * @param Keyboard $keyboard
     * @param Page $node
     * @return Keyboard
     */
    protected function pageKeyboard(Keyboard $keyboard, NodeCategoryInterface $node): Keyboard
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

    /**
     * @param Keyboard $keyboard
     * @return array
     */
    protected function catalogKeyboard(Keyboard $keyboard): array
    {
        $buttons = collect([
            $this->pageRepository->getByConversationType(BasketConversation::class),
            $this->pageRepository->getByConversationType(MainConversation::class)
        ]);

        $builder = KeyboardBuilder::fromCollection($buttons)
            ->each(fn(InlineButton $item) => $item->type(InlineButton::BUTTON_TYPE_INTERNAL))
            ->all();

        return resolve(PageKeyboardService::class)->buildKeyboard($keyboard, $builder, 2, 0)->toArray();
    }
}
