<?php

namespace App\Http\Requests;

use App\Rules\Admin\PageImage;
use Illuminate\Foundation\Http\FormRequest;

class CreatePageRequest extends FormRequest
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
            'parent_id' => 'required',
            'image' => resolve(PageImage::class)
        ];
    }
}
