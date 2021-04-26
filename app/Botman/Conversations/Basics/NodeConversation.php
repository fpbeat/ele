<?php

namespace App\Botman\Conversations\Basics;

use App\Models\Page;
use App\Botman\Traits\{KeyboardTrait, MessageTrait};
use App\Repositories\{PageRepository, TelegramUserRepository};
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\Drivers\Telegram\Extensions\Keyboard;

abstract class NodeConversation extends BaseConversation
{
    use KeyboardTrait;
    use MessageTrait;

    /**
     * @var bool
     */
    protected const IMAGE_SINGLY = false;

    /**
     * @var string
     */
    protected const KEYBOARD_TYPE = Keyboard::TYPE_INLINE;

    /**
     * @var Page
     */
    protected Page $node;

    /**
     * @var PageRepository
     */
    protected PageRepository $pageRepository;

    /**
     * @var TelegramUserRepository
     */
    protected TelegramUserRepository $telegramUserRepository;

    /**
     * @var bool
     */
    protected bool $isReplyKeyboardAnswer = false;

    /**
     * @param Page $node
     * @param PageRepository $pageRepository
     * @param TelegramUserRepository $telegramUserRepository
     */
    public function __construct(Page $node, PageRepository $pageRepository, TelegramUserRepository $telegramUserRepository)
    {
        $this->node = $node;

        $this->pageRepository = $pageRepository;
        $this->telegramUserRepository = $telegramUserRepository;
    }

    /**
     * @return void
     */
    public function showPageMessage(): void
    {
        $this->ask($this->imageMessage($this->node), function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $node = $this->pageRepository->getById($answer->getValue());

                $this->nodeConversation($node);
            } else {
                $this->handleTextAnswer($answer);
            }
        }, $this->keyboard());
    }

    /**
     * @return array
     */
    public function keyboard(): array
    {
        $keyboard = Keyboard::create()
            ->type(static::KEYBOARD_TYPE)
            ->oneTimeKeyboard()
            ->resizeKeyboard();

        return $this->pageKeyboard($keyboard, $this->node)->toArray();
    }

    /**
     * @return void
     */
    public function run(): void
    {
        $this->updateUserLastPage();
    }

    /**
     * @return void
     */
    private function updateUserLastPage(): void
    {
        $this->telegramUserRepository->updateLastPage($this->bot->getUser()->getId(), $this->node->id);
    }

    /**
     * @param Answer $answer
     */
    protected function handleTextAnswer(Answer $answer): void
    {
        switch (static::KEYBOARD_TYPE) {
            case Keyboard::TYPE_KEYBOARD:
                $answerButton = $this->getAnswerButtonId($answer->getText());

                if ($answerButton) {
                    $this->isReplyKeyboardAnswer = true;

                    $node = $this->pageRepository->getById($answerButton);
                    $this->say(trans('chatbot.keyboard_handle_open', ['name' => $node->name]), $this->removeKeyboard());

                    $this->nodeConversation($node);
                }

                break;
            default:
                $this->repeat();
        }
    }
}
