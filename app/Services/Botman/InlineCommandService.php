<?php

namespace App\Services\Botman;

use App\Contracts\NodeCategoryInterface;
use App\Botman\Commands\{BasketCommand, StartCommand};
use App\Botman\Conversations\{BasketConversation, MainConversation};
use Illuminate\Support\{Collection, Str};
use App\Models\Page;
use App\Repositories\PageRepository;
use BotMan\BotMan\BotMan;

class InlineCommandService
{
    /**
     * @var PageRepository
     */
    private PageRepository $pageRepository;

    /**
     * @var Collection
     */
    private Collection $commands;

    /**
     * @param PageRepository $pageRepository
     */
    public function __construct(PageRepository $pageRepository)
    {
        $this->pageRepository = $pageRepository;
        $this->commands = $this->transform($this->setCommands());
    }

    /**
     * @return Collection
     */
    public function setCommands(): Collection
    {
        return collect([
            [
                'handler' => InlineCommandFactory::create(BasketCommand::class),
                'instance' => $this->pageRepository->getByConversationType(BasketConversation::class),
            ],
            [
                'handler' => InlineCommandFactory::create(StartCommand::class),
                'instance' => $this->pageRepository->getByConversationType(MainConversation::class),
            ],
            [
                'handler' => InlineCommandFactory::create(StartCommand::class),
                'instance' => '/start',
            ]
        ]);
    }

    /**
     * @param Collection $commands
     * @return Collection
     */
    public function transform(Collection $commands): Collection
    {
        return $commands->map(fn($item) => $item + ['name' => $item['instance'] instanceof NodeCategoryInterface ? $item['instance']->name : $item['instance']]);
    }

    /**
     * @return string
     */
    public function getListeners(): string
    {
        return $this->commands
            ->pluck('name')
            ->map('preg_quote')
            ->join('|');
    }

    /**
     * @param BotMan $bot
     */
    public function execute(BotMan $bot): void
    {
        rescue(function () use ($bot) {
            $command = $this->commands
                ->sole(fn($item) => Str::lower($item['name']) === Str::lower($bot->getMessage()->getText()));

            $command['handler']->execute($command, $bot);
        });
    }
}
