<?php

namespace App\Http\Controllers;

use App\Botman\Conversations\PageConversation;
use App\Botman\Middlewares\ReceivedMiddleware;
use App\Botman\Traits\KeyboardTrait;
use App\Facades\Message;
use App\Repositories\PageRepository;
use BotMan\BotMan\BotMan;

class BotmanController extends Controller
{
    use KeyboardTrait;

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

        //    $page = Page::whereId(30)->firstOrFail();

        try {
            $botman->hears('/start', function (BotMan $bot) {
                $bot->startConversation(resolve(PageConversation::class, [
                    'node' => $this->pageRepository->getRootNode()
                ]));
            });

            $botman->middleware->received(resolve(ReceivedMiddleware::class));
            $botman->listen();

        } catch (\Throwable $e) {
            $botman->reply(Message::get('supportErrorMessage'));
        }
    }
}
