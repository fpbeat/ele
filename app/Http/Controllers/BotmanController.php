<?php

namespace App\Http\Controllers;

use App\Botman\Conversations\MainConversation;
use App\Botman\Middlewares\CapturedMiddleware;
use App\Botman\Middlewares\ReceivedMiddleware;
use App\Botman\Traits\MessageTrait;
use App\Facades\Message;
use App\Repositories\CatalogRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\PageRepository;
use App\Services\Botman\InlineCommandService;
use App\Services\Botman\KeyboardBuilderService;
use App\Services\Keyboard\InlineButton;
use BotMan\BotMan\BotMan;
use BotMan\Drivers\Telegram\Extensions\KeyboardButton;

class BotmanController extends Controller
{
    use MessageTrait;

    /**
     * @var PageRepository
     */
    private PageRepository $pageRepository;
    private InlineCommandService $inlineCommandService;

    public function __construct(PageRepository $pageRepository, InlineCommandService $inlineCommandService)
    {
        $this->pageRepository = $pageRepository;
        $this->inlineCommandService = $inlineCommandService;
    }

    public function handle()
    {
        $botman = app('botman');

        try {
            $botman
                ->hears($this->inlineCommandService->getListeners(), fn(BotMan $bot) => $this->inlineCommandService->execute($bot))
                ->stopsConversation();

            $botman->middleware->received(resolve(ReceivedMiddleware::class));
            $botman->middleware->captured(resolve(CapturedMiddleware::class));
            $botman->listen();

        } catch (\Throwable $e) {
            dd($e);
            $botman->reply(Message::get('supportErrorMessage') . $e->getMessage());
        }
    }
}
