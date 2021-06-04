<?php

namespace App\Botman\Conversations;

use App\Botman\Conversations\Basics\NodeConversation;
use App\Contracts\Botman\NodeConversationInterface;
use App\Models\Page;
use App\Repositories\PageRepository;
use App\Repositories\TelegramUserRepository;
use BotMan\BotMan\Messages\Incoming\Answer;

class ProductConversation extends NodeConversation implements NodeConversationInterface
{
    /**
     * @var NodeConversationInterface
     */
    protected NodeConversationInterface $category;

    /**
     * @param Page $node
     * @param PageRepository $pageRepository
     * @param TelegramUserRepository $telegramUserRepository
     * @param NodeConversationInterface $category
     */
    public function __construct(Page $node, PageRepository $pageRepository, TelegramUserRepository $telegramUserRepository, NodeConversationInterface $category)
    {
        $this->node = $node;

        $this->pageRepository = $pageRepository;
        $this->telegramUserRepository = $telegramUserRepository;

        $this->category = $category;
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
     * @var bool
     */
    const IMAGE_SINGLY = false;

    /**
     * @return void
     */
    public function run(): void
    {
        parent::run();

        $this->showPageMessage();
    }
}
