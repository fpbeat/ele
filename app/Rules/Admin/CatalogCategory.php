<?php

namespace App\Rules\Admin;

use App\Repositories\CategoryRepository;
use Illuminate\Contracts\Validation\Rule;

class CatalogCategory implements Rule
{
    /**
     * @var CategoryRepository
     */
    private CategoryRepository $categoryRepository;

    /**
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        foreach ($value as $category) {
            if (!$this->categoryRepository->getById($category)->isLeaf()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return string
     */
    public function message(): string
    {
        return trans('validation.custom.category_not_leaf');
    }
}
