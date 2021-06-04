<?php

namespace App\Botman\Conversations;

use App\Botman\Conversations\Basics\BaseConversation;
use App\Botman\Traits\KeyboardTrait;
use App\Botman\Traits\MessageTrait;
use App\Botman\Traits\UserStorage;
use App\Models\Category;
use App\Repositories\CatalogRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\PageRepository;
use App\Services\Botman\PageKeyboardService;
use App\Services\Keyboard\InlineButton;
use App\Services\Keyboard\KeyboardBuilder;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\Drivers\Telegram\Extensions\Keyboard;
use Illuminate\Support\Collection;

class CatalogConversation extends BaseConversation
{
    use KeyboardTrait;
    use MessageTrait;
    use UserStorage;

    /**
     * @var bool
     */
    const IMAGE_SINGLY = false;

    protected CategoryRepository $categoryRepository;

    /**
     * @var Category
     */
    protected Category $category;

    protected CatalogRepository $catalogRepository;
    private PageRepository $pageRepository;

    /**
     * @param CatalogRepository $catalogRepository
     * @param CategoryRepository $categoryRepository
     * @param null $category
     */
    public function __construct(PageRepository $pageRepository, CatalogRepository $catalogRepository, CategoryRepository $categoryRepository, $category = null)
    {
        $this->categoryRepository = $categoryRepository;
        $this->catalogRepository = $catalogRepository;

        $this->category = $category ?? $this->categoryRepository->getRootNode();
        $this->pageRepository = $pageRepository;
    }

    /**
     * @return void
     */
    public function showPageMessage(): void
    {
        if (!$this->getStorageValue('catalog.keyboard')) {
            $page = $this->pageRepository->getByConversationType(CatalogConversation::class);
            $this->say($this->imageMessage($page), $this->keyboard2());

            $this->setStorageValue('catalog.keyboard', true);
        }

        $this->ask($this->imageMessage($this->category), function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $this->deleteLastMessage($answer->getMessage());

                $current = $this->categoryRepository->getById($answer->getValue());
                $this->bot->startConversation(resolve($current->isLeaf() ? ProductConversation::class : static::class, [
                    'category' => $current
                ]));
            }
        }, $this->keyboard());
    }

    public function keyboard(): array
    {
        $keyboard = Keyboard::create()
            ->type(Keyboard::TYPE_INLINE)
            ->oneTimeKeyboard(false)
            ->resizeKeyboard();

        return resolve(PageKeyboardService::class)->buildKeyboard($keyboard, $this->getButtons(), 2, 1)->toArray();
    }

    public function keyboard2(): array
    {
        $keyboard = Keyboard::create()
            ->type(Keyboard::TYPE_KEYBOARD)
            ->oneTimeKeyboard(false)
            ->resizeKeyboard();

        return $this->catalogKeyboard($keyboard);
    }

    /**
     * @return Collection
     */
    public function getButtons(): Collection
    {
        $buttons = $this->category->children
            ->filter(fn($item) => $this->catalogRepository->hasProductsInCategories($this->categoryRepository->getDescendantsAndSelf($item->id)));

        $builder = KeyboardBuilder::fromCollection($buttons);

        if ($this->category->parent !== null) {
            $builder->add(InlineButton::create('Назад')->pageId($this->category->parent->id));
        }

        return $builder->all();
    }

    /**
     * @return void
     */
    public function run(): void
    {
        $this->showPageMessage();
    }
}
