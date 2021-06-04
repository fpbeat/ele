<?php

namespace App\Botman\Traits;

use App\Backpack\ImageUploader;
use App\Contracts\NodeCategoryInterface;
use App\Models\Page;
use Illuminate\Support\Arr;
use BotMan\BotMan\{BotMan,
    Messages\Attachments\Image,
    Messages\Incoming\IncomingMessage,
    Messages\Outgoing\OutgoingMessage
};
use Illuminate\Support\Facades\Storage;

trait MessageTrait
{
    /**
     * @param IncomingMessage $message
     */
    protected function deleteLastMessage(IncomingMessage $message): void
    {
        $this->bot->sendRequest('deleteMessage', [
            'chat_id' => Arr::get($message->getPayload(), 'chat.id'),
            'message_id' => Arr::get($message->getPayload(), 'message_id'),
        ]);
    }

    /**
     * @param NodeCategoryInterface $node
     * @return OutgoingMessage
     */
    protected function imageMessage(NodeCategoryInterface $node): OutgoingMessage
    {
        $message = OutgoingMessage::create($node->clean_description);

        if ($node->image) {
            $attachment = new Image(Storage::disk(ImageUploader::STORAGE_DISK)->url($node->image));
            $message->withAttachment($attachment);

            if (static::IMAGE_SINGLY || $node->has_long_description) {
                $message->text(null);
                $this->say($message);

                return OutgoingMessage::create($node->clean_description);
            }
        }

        return $message;
    }

    /**
     * @param Page $node
     * @param BotMan|null $bot
     */
    protected function nodeConversation(Page $node, ?BotMan $bot = null): void
    {
        $instance = $bot ?? $this->bot;

        $instance->startConversation(resolve($node->type->conversation, [
            'node' => $node
        ]));
    }

    /**
     * @param BotMan $bot
     * @param string $name
     */
    protected function removeKeyboardWithMessage(BotMan $bot, string $name): void
    {
        $bot->reply(trans('chatbot.keyboard_handle_open', ['name' => $name]), $this->removeKeyboard());
    }
}
