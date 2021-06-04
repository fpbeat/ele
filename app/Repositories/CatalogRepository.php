<?php

namespace App\Repositories;

use App\Models\Catalog;

class CatalogRepository
{
    /**
     * @param array $categories
     * @return bool
     */
    public function hasProductsInCategories(array $categories): bool
    {
        return Catalog::available()
                ->whereHas('categories', fn($query) => $query->whereIn('category_id', $categories))
                ->count() > 0;
    }
}
