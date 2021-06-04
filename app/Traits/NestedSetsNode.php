<?php

namespace App\Traits;

use App\Backpack\ImageUploader;
use Illuminate\Support\Str;

trait NestedSetsNode
{
    /**
     * @return string
     */
    public function getFullPathAttribute(): string
    {
        return $this->ancestorsAndSelf($this->attributes['id'])->pluck('name')->join(' > ');
    }

    /**
     * @param $value
     */
    public function setImageAttribute($value): void
    {
        $this->attributes['image'] = resolve(ImageUploader::class)->upload($this->image, $value, static::UPLOAD_DIRECTORY);
    }

    /**
     * @return string
     */
    public function getCleanNameAttribute(): string
    {
        return Str::cleanEmojis($this->attributes['name']);
    }

    /**
     * @return string
     */
    public function getCleanDescriptionAttribute(): string
    {
        return Str::cleanupSummernote($this->attributes['description']);
    }

    /**
     * @return bool
     */
    public function getHasLongDescriptionAttribute(): bool
    {
        return Str::length($this->cleanDescription) >= static::LONG_DESCRIPTION_LENGTH;
    }
}
