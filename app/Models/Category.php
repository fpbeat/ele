<?php

namespace App\Models;

use App\Backpack\ImageUploader;
use App\Traits\ButtonVisibility;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Kalnoy\Nestedset\NodeTrait;

class Category extends Model
{
    use CrudTrait, NodeTrait, ButtonVisibility;

    /**
     * @var string
     */
    protected const UPLOAD_DIRECTORY = 'categories';

    /**
     * @var string[]
     */
    protected $fillable = ['name', 'parent_id', 'description', 'image'];

    /**
     * @return void
     */
    public static function boot(): void
    {
        parent::boot();

        static::deleting(function ($category) {
            Storage::disk(ImageUploader::STORAGE_DISK)->delete($category->image);
        });
    }

    /**
     * @return string
     */
    public function getLftName(): string
    {
        return 'lft';
    }

    /**
     * @return string
     */
    public function getRgtName(): string
    {
        return 'rgt';
    }

    /**
     * @param $value
     */
    public function setImageAttribute($value): void
    {
        $this->attributes['image'] = resolve(ImageUploader::class)->upload($this->image, $value, static::UPLOAD_DIRECTORY);
    }

    /**
     * @return bool
     */
    public function getUpdateButtonVisibleAttribute(): bool
    {
        return !$this->isRoot();
    }

    /**
     * @return bool
     */
    public function getDeleteButtonVisibleAttribute(): bool
    {
        return !$this->isRoot();
    }

    /**
     * @return string
     */
    public function getFullPathAttribute(): string
    {
        return $this->ancestorsAndSelf($this->attributes['id'])->pluck('name')->join(' > ');
    }

    public function scopeWhereNotRoot($query)
    {
        return $query->whereNotNull($this->getParentIdName());
    }
}
