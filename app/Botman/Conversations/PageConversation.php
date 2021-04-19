<?php

namespace App\Botman\Conversations;

use App\Botman\Traits\KeyboardTrait;
use App\Botman\Traits\MessageTrait;
use App\Botman\Traits\UserStorage;
use App\Models\Page;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\Drivers\Telegram\Extensions\Keyboard;

class PageConversation extends BaseConversation
{
    use MessageTrait;
    use KeyboardTrait;
    use UserStorage;

    private function askPreMessage(): void
    {
        $this->ask($this->imageMessage($this->node), function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {

                $image = $this->getStorageValue('image');
                if ($image) {
                    $this->deleteLastMessage($image);

                    $this->setStorageValue('image', null);
                }

                $this->deleteLastMessage($answer->getMessage()->getPayload());

                $node = Page::whereId($answer->getValue())->firstOrFail();

                $this->bot->startConversation(resolve($node->type->conversation, [
                    'node' => $node
                ]));
            } else {
                $this->repeat();
            }
        }, $this->keyboard());
    }

    /**
     * @return array
     */
    public function keyboard(): array
    {
        $keyboard = Keyboard::create()->type(Keyboard::TYPE_INLINE)
            ->oneTimeKeyboard(true)
            ->resizeKeyboard();

        return $this->pageKeyboard($keyboard, $this->pageRepository->getButtonItems($this->node->id), $this->node->buttons_per_row)->toArray();
    }

    /**
     * @return void
     */
    public function run(): void
    {
        parent::run();

        $this->askPreMessage();
    }
}
