<?php

namespace App\Repositories;

use App\Models\Category;

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
     * @return array
     */
    public function getFullPathArray(): array
    {
        return Category::defaultOrder()
            ->whereNotRoot()
            ->get()
            ->pluck('full_path', 'id')
            ->toArray();
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function getDescendantsAndSelf(int $id)
    {
        return Category::descendantsAndSelf($id)
            ->pluck('id')
            ->toArray();
    }

    /**
     * @return Category
     */
    public function getRootNode(): Category
    {
        return Category::whereIsRoot()->firstOrFail();
    }

    /**
     * @param int $id
     * @return Category
     */
    public function getById(int $id): Category
    {
        return Category::whereId($id)->firstOrFail();
    }
}
