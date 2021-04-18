<?php

namespace App\Botman\Conversations;

use App\Backpack\ImageUploader;
use App\Botman\Traits\KeyboardTrait;
use App\Botman\Traits\MessageTrait;
use App\Botman\Traits\UserStorage;
use App\Models\Page;
use App\Repositories\PageRepository;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use BotMan\Drivers\Telegram\Extensions\{Keyboard, KeyboardButton};

class ReviewConversation extends BaseConversation
{
    use MessageTrait;
    use KeyboardTrait;

    /**
     * @var mixed
     */
    private $buttons;

    private function askPreMessage(): void
    {
        $message = OutgoingMessage::create($this->node->cleanDescription);

        if ($this->node->image) {
            $attachment = new Image(Storage::disk(ImageUploader::STORAGE_DISK)->url($this->node->image));
            $message->withAttachment($attachment);
        }

        $this->ask($message, function (Answer $answer) {
        //    Log::info($answer->getMessage());
            $this->handleTextAnswer($answer);
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

    private function handleTextAnswer(Answer $answer)
    {
        $buttons = $this->pageRepository->getButtonItems($this->node->id);
        $ff = $buttons->pluck('name', 'page_id')->search($answer->getText());

        if ($ff) {
            $node = $this->pageRepository->getById($ff);

            $this->deleteLastMessage($answer);

            $this->bot->sendRequest('sendMessage', [
                'text' => $answer->getText(),
                'reply_markup' => json_encode([
                    'remove_keyboard' => true
                ])
            ]);


            $this->bot->startConversation(resolve($node->type->conversation, [
                'node' => $node
            ]));
        } else {

        }
    }

    /**
     * @return void
     */
    public function run(): void
    {
        //  $this->setStorageValue('node.current', $this->node->id);

        $this->askPreMessage();
    }
}
