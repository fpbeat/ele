<?php

namespace App\Repositories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class PageRepository
{
    /**
     * @return array
     */
    public function getTreeArray(): array
    {
        $nodes = Page::defaultOrder()->withDepth()->get();

        $tree = [];
        foreach ($nodes as $node) {
            $tree[$node->id] = sprintf('%s %s', str_repeat(' - ', $node->depth), $node->name);
        }

        return $tree;
    }

    /**
     * @return Page
     */
    public function getRootNode(): Page
    {
        return Page::whereIsRoot()->firstOrFail();
    }

    /**
     * @param int $id
     * @return Page
     */
    public function getById(int $id): Page
    {
        return Page::whereId($id)->firstOrFail();
    }

    /**
     * @param string $type
     * @return Page
     */
    public function getByConversationType(string $type): Page
    {
        return Page::whereHas('type', fn(Builder $query) => $query->whereConversation($type))->firstOrFail();
    }

    /**
     * @param int $id
     * @return Collection
     */
    public function getButtonItems(int $id): Collection
    {
        $page = $this->getById($id);

        $pool = [];
        foreach (($page->buttons ?? []) as $button) {
            if (empty($button['name']) && $button['type'] === 'page_link') {
                $page = $this->getById($button['page_id']);

                $button['name'] = $page->name;
            }

            $pool[] = $button;
        }

        return collect($pool);
    }
}
