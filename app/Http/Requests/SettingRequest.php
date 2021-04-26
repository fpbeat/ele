<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
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
            'contact__address' => 'required',
            'feedback__redirect' => 'required'
        ];
    }
}
