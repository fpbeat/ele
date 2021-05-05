<?php

namespace App\Http\Requests;

use App\Rules\Admin\CatalogCategory;
use App\Rules\Admin\PageImage;
use Illuminate\Foundation\Http\FormRequest;

class CreateCatalogRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return backpack_auth()->check();
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
            'price' => 'required|numeric',
            'amount' => 'required|integer|min:1',
            'categories' => ['required', resolve(CatalogCategory::class)],
            'image' => resolve(PageImage::class)
        ];
    }
}
