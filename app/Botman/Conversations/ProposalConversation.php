<?php

namespace App\Botman\Conversations;

use App\Botman\Conversations\Basics\FeedbackConversation;
use App\Contracts\Botman\NodeConversationInterface;
use App\Models\Feedback;

class ProposalConversation extends FeedbackConversation implements NodeConversationInterface
{
    /**
     * @var string
     */
    const FEEDBACK_TYPE = Feedback::TYPE_PROPOSAL;

    /**
     * @var string
     */
    const FEEDBACK_MESSAGE_KEY = 'proposalSuccessMessage';

    /**
     * @return void
     */
    public function run(): void
    {
        parent::run();

        $this->showPageMessage();
    }
}
