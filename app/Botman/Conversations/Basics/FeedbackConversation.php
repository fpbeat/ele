<?php

namespace App\Botman\Conversations\Basics;

use App\Botman\Traits\UserStorage;
use App\Models\Page;
use BotMan\Drivers\Telegram\Extensions\Keyboard;
use App\Facades\{Message, Setting};
use App\Repositories\FeedbackRepository;
use BotMan\BotMan\Messages\Incoming\Answer;

class FeedbackConversation extends NodeConversation
{
    use UserStorage;

    /**
     * @var int
     */
    protected const DEFAULT_WAITING_DELAY = 2;

    /**
     * @var string
     */
    protected const KEYBOARD_TYPE = Keyboard::TYPE_KEYBOARD;

    /**
     * @param Answer $answer
     * @param callable|null $answerCallback
     */
    protected function handleTextAnswer(Answer $answer, ?callable $answerCallback = null): void
    {
        parent::handleTextAnswer($answer);

        if (!$this->isReplyKeyboardAnswer) {
            resolve(FeedbackRepository::class)->store(
                $this->telegramUserRepository->getByUserId($this->bot->getUser()->getId()),
                $answer->getText(),
                static::FEEDBACK_TYPE
            );

            $this->say(Message::get(static::FEEDBACK_MESSAGE_KEY), $this->removeKeyboard());

            $this->bot->typesAndWaits($this->getWaitingDelay());
            $this->nodeConversation($this->getRedirectConversation());
        }
    }

    /**
     * @return Page
     */
    private function getRedirectConversation(): Page
    {
        $redirect = Setting::get('feedback.redirect');

        if ($redirect !== null) {
            return $this->pageRepository->getById($redirect);
        }

        return $this->pageRepository->getRootNode();
    }

    /**
     * @return int
     */
    private function getWaitingDelay(): int
    {
        return Setting::get('feedback.redirect_delay', static::DEFAULT_WAITING_DELAY);
    }
}
