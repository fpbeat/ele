<?php

namespace App\Botman\Conversations;

use App\Models\Page;
use App\Repositories\PageRepository;
use App\Repositories\TelegramUserRepository;
use BotMan\BotMan\Messages\{Conversations\Conversation, Incoming\IncomingMessage};

abstract class BaseConversation extends Conversation
{
    /**
     * @var Page
     */
    protected Page $node;

    /**
     * @var PageRepository
     */
    protected PageRepository $pageRepository;

    /**
     * @var array|mixed
     */
    protected $extra;
    /**
     * @var TelegramUserRepository
     */
    private TelegramUserRepository $telegramUserRepository;

    /**
     * BaseConversation constructor.
     * @param Page $node
     * @param PageRepository $pageRepository
     * @param TelegramUserRepository $telegramUserRepository
     * @param array $extra
     */
    public function __construct(Page $node, PageRepository $pageRepository, TelegramUserRepository $telegramUserRepository, $extra = [])
    {
        $this->node = $node;
        $this->pageRepository = $pageRepository;
        $this->telegramUserRepository = $telegramUserRepository;

        $this->extra = $extra;
    }

    /**
     * @param IncomingMessage $message
     * @return bool
     */
    public function stopsConversation(IncomingMessage $message): bool
    {
        return $message->getText() === '/start';
    }

    /**
     * @return void
     */
    public function run(): void
    {
        $this->updateUserLastPage();
    }

    private function updateUserLastPage()
    {
        $this->telegramUserRepository->updateLastPage($this->bot->getUser()->getId(), $this->node->id);

    }
}
