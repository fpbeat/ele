<?php

namespace App\Rules\Admin;

use App\Backpack\ImageUploader;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class PageImage implements Rule
{
    /**
     * @var ImageUploader
     */
    private ImageUploader $imageUploader;

    /**
     * @param ImageUploader $imageUploader
     */
    public function __construct(ImageUploader $imageUploader)
    {
        $this->imageUploader = $imageUploader;
    }

    /**
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        if (is_null($value) || !Str::startsWith($value, 'data:image')) {
            return true;
        }

        return $this->imageUploader->getExtensionImageBase($value) !== null;
    }

    /**
     * @return string
     */
    public function message(): string
    {
        return trans('validation.mimes', [
            'values' => collect(ImageUploader::ALLOWED_EXTENSIONS)->join(', ')
        ]);
    }
}
