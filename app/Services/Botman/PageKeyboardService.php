<?php

namespace App\Services\Botman;

use BotMan\Drivers\Telegram\Extensions\{Keyboard, KeyboardButton};
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PageKeyboardService
{
    /**
     * @param Keyboard $keyboard
     * @param Collection $items
     * @param int $buttons_per_row
     * @param int $buttons_navi_bottom
     * @return Keyboard
     */
    public function buildKeyboard(Keyboard $keyboard, Collection $items, int $buttons_per_row, int $buttons_navi_bottom): Keyboard
    {
        $collection = $this->getItemsCollection($items);

        if ($buttons_navi_bottom) {
            foreach ($this->tryToGuessNaviButtons($collection) as $subset) {
                $this->buildKeyboardRows($subset, $keyboard, $buttons_per_row);
            }

            return $keyboard;
        }

        $this->buildKeyboardRows($collection, $keyboard, $buttons_per_row);

        return $keyboard;
    }

    /**
     * @param Collection $items
     * @param Keyboard $keyboard
     * @param int $buttons_per_row
     */
    private function buildKeyboardRows(Collection $items, Keyboard $keyboard, int $buttons_per_row): void
    {
        collect($items)
            ->map(fn($item) => $this->getKeyboardButton($item))
            ->split($this->getGroupSplitCount($items, $buttons_per_row))
            ->each(fn($single) => $keyboard->addRow(...$single));
    }

    /**
     * @param array $single
     * @return KeyboardButton
     */
    private function getKeyboardButton(array $single): KeyboardButton
    {
        switch ($single['type']) {
            case 'external_link':
                return KeyboardButton::create($single['name'])->url($single['link'] ?? null);
            default:
                return KeyboardButton::create($single['name'])->callbackData($single['page_id'] ?? null);
        }
    }

    /**
     * @param Collection $items
     * @return Collection
     */
    private function getItemsCollection(Collection $items): Collection
    {
        return collect($items)->map(function ($item) {
            $cleanName = Str::cleanEmojis($item['name']);

            return $item + ['clean_name' => Str::lower($cleanName)];
        }
        );
    }

    /**
     * @param Collection $collection
     * @return array
     */
    private function tryToGuessNaviButtons(Collection $collection): array
    {
        return [
            $collection->whereNotIn('clean_name', trans('buttons.supposed_navi_buttons')),
            $collection->whereIn('clean_name', trans('buttons.supposed_navi_buttons'))
        ];
    }

    /**
     * @param Collection $items
     * @param int $buttons_per_row
     * @return int
     */
    private function getGroupSplitCount(Collection $items, int $buttons_per_row): int
    {
        return ceil($items->count() / $buttons_per_row);
    }
}
