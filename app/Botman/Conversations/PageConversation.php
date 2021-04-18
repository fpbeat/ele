<?php

namespace App\Botman\Conversations;

use App\Backpack\ImageUploader;
use App\Botman\Traits\KeyboardTrait;
use App\Botman\Traits\MessageTrait;
use App\Models\Page;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use Illuminate\Support\Facades\Storage;
use BotMan\Drivers\Telegram\Extensions\Keyboard;

class PageConversation extends BaseConversation
{
    use MessageTrait;
    use KeyboardTrait;

    private function askPreMessage(): void
    {
        $message = OutgoingMessage::create($this->node->cleanDescription);

        if ($this->node->image) {
            $attachment = new Image(Storage::disk(ImageUploader::STORAGE_DISK)->url($this->node->image));
            $message->withAttachment($attachment);
        }

        $this->ask($message, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {

                $this->deleteLastMessage($answer);

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
