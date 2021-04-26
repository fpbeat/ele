<?php

namespace App\Http\Controllers;

use App\Botman\Conversations\PageConversation;
use App\Botman\Middlewares\CapturedMiddleware;
use App\Botman\Middlewares\ReceivedMiddleware;
use App\Botman\Traits\MessageTrait;
use App\Facades\Message;
use App\Models\Page;
use App\Repositories\PageRepository;
use BotMan\BotMan\BotMan;

class BotmanController extends Controller
{
    use MessageTrait;

    /**
     * @var PageRepository
     */
    private PageRepository $pageRepository;

    public function __construct(PageRepository $pageRepository)
    {
        $this->pageRepository = $pageRepository;
    }

    public function handle()
    {
        $botman = app('botman');

//        $node = resolve(PageRepository::class)->getById(30)->firstOrFail();

        try {
            $botman->hears('/start', function (BotMan $bot) {
                $this->nodeConversation($this->pageRepository->getRootNode(), $bot);
            });

            $botman->middleware->received(resolve(ReceivedMiddleware::class));
            $botman->middleware->captured(resolve(CapturedMiddleware::class));
            $botman->listen();

        } catch (\Throwable $e) {
            $botman->reply(Message::get('supportErrorMessage') . $e->getMessage());
        }
    }
}
