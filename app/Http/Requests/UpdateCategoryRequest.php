<?php

namespace App\Http\Requests;

use App\Rules\Admin\PageImage;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
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
            'image' => resolve(PageImage::class)
        ];
    }
}
