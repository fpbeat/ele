<?php

namespace App\Botman\Conversations\Basics;

use App\Botman\Conversations\PageConversation;

use App\Botman\Traits\{KeyboardTrait, MessageTrait};
use App\Models\Page;
use App\Repositories\PageRepository;
use App\Repositories\TelegramUserRepository;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\Drivers\Telegram\Extensions\Keyboard;

abstract class NodeConversation extends BaseConversation
{
    use KeyboardTrait;
    use MessageTrait;

    /**
     * @var bool
     */
    const IMAGE_SINGLY = false;

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
    private TelegramUserRepository $telegramUserRepository;

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

                $this->bot->startConversation(resolve($node->type->conversation ?? PageConversation::class, [
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
        $keyboard = Keyboard::create()
            ->type(Keyboard::TYPE_INLINE)
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
}
