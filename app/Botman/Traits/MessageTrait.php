<?php

namespace App\Botman\Traits;

use App\Backpack\ImageUploader;
use App\Models\Page;
use App\Services\Botman\CustomRequestResponse;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

trait MessageTrait
{
    /**
     * @param array $payload
     */
    protected function deleteLastMessage(array $payload): void
    {
        $this->bot->sendRequest('deleteMessage', [
            'chat_id' => Arr::get($payload, 'chat.id'),
            'message_id' => Arr::get($payload, 'message_id'),
        ]);
    }

    protected function imageMessage(Page $node): OutgoingMessage
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
}
