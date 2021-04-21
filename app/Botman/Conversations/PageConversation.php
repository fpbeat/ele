<?php

namespace App\Botman\Conversations;

use App\Botman\Conversations\Basics\NodeConversation;
use App\Contracts\Botman\NodeConversationInterface;

class PageConversation extends NodeConversation implements NodeConversationInterface
{
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
