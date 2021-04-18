<?php

namespace App\Botman\Traits;

use BotMan\BotMan\Messages\Incoming\Answer;

trait MessageTrait {
    protected function deleteLastMessage(Answer $answer) {
        $this->bot->sendRequest('deleteMessage', [
            'chat_id' => $answer->getMessage()->getPayload()['chat']['id'],
            'message_id' => $answer->getMessage()->getPayload()['message_id'],
        ]);
    }
}
