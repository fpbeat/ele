<?php

return [
    [
        'name' => 'seeders.page_types.text',
        'conversation' => App\Botman\Conversations\PageConversation::class,
    ],
    [
        'name' => 'seeders.page_types.review',
        'conversation' => App\Botman\Conversations\ReviewConversation::class,
    ],
    [
        'name' => 'seeders.page_types.proposal',
        'conversation' => App\Botman\Conversations\ProposalConversation::class,
    ],
    [
        'name' => 'seeders.page_types.contact',
        'conversation' => App\Botman\Conversations\ContactsConversation::class,
    ],

];
