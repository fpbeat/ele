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
            'feedback__redirect' => 'required',
            'notifications__time_range.*' => 'required'
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'notifications__time_range.start.required' => trans('validation.custom.time_range_start_required', [
                'attribute' => trans('validation.attributes.mailing_time')
            ]),
            'notifications__time_range.end.required' => trans('validation.custom.time_range_end_required', [
                'attribute' => trans('validation.attributes.mailing_time')
            ])
        ];
    }
}
