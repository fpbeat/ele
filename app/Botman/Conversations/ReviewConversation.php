<?php

namespace App\Botman\Conversations;

use App\Botman\Conversations\Basics\FeedbackConversation;
use App\Contracts\Botman\NodeConversationInterface;
use App\Models\Feedback;

class ReviewConversation extends FeedbackConversation implements NodeConversationInterface
{
    /**
     * @var string
     */
    const FEEDBACK_TYPE = Feedback::TYPE_REVIEW;

    /**
     * @var string
     */
    const FEEDBACK_MESSAGE_KEY = 'reviewSuccessMessage';

    /**
     * @return void
     */
    public function run(): void
    {
        parent::run();

        $this->showPageMessage();
    }
}
