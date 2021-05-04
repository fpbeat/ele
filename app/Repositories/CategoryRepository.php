<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Page;

class CategoryRepository
{
    /**
     * @return array
     */
    public function getTreeArray(): array
    {
        $nodes = Category::defaultOrder()->withDepth()->get();

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
        return Category::whereIsRoot()->firstOrFail();
    }

    /**
     * @param int $id
     * @return Page
     */
    public function getById(int $id): Page
    {
        return Category::whereId($id)->firstOrFail();
    }
}
