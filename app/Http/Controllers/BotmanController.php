<?php

namespace App\Http\Controllers;

use App\Botman\Conversations\PageConversation;
use App\Botman\Middlewares\ReceivedMiddleware;
use App\Facades\Message;
use App\Models\Page;
use App\Repositories\PageRepository;
use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;

class BotmanController extends Controller
{
    /**
     * @var PageRepository
     */
    private PageRepository $pageRepository;

    public function __construct(PageRepository $pageRepository)
    {
        $this->pageRepository = $pageRepository;
    }

    public function handle(Request $request)
    {
        $botman = app('botman');

        try {
            $botman->hears('/start', function (BotMan $bot) {
                $bot->startConversation(resolve(PageConversation::class, [
                    'node' => $this->pageRepository->getRoot()
                ]));
            });

            $botman->middleware->received(resolve(ReceivedMiddleware::class));
            // $botman->middleware->captured(new CapturedMiddleware);

            $botman->listen();

        } catch (\Throwable $e) {

            //  dd($e->getMessage());
            $botman->reply(Message::get('SupportErrorMessage') . "\n\n" . $e->getMessage());
        }
    }
}
