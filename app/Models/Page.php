<?php

namespace App\Models;

use App\Backpack\ImageUploader;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;

class Page extends Model
{
    use CrudTrait;
    use NodeTrait;

    /**
     * @var int
     */
    const LONG_DESCRIPTION_LENGTH = 1024;

    /**
     * @var string
     */
    protected const UPLOAD_DIRECTORY = 'pages';

    /**
     * @var string[]
     */
    protected $fillable = ['name', 'type_id', 'parent_id', 'buttons', 'buttons_per_row', 'buttons_navi_bottom', 'description', 'image'];

    /**
     * @var string[]
     */
    protected $casts = [
        'buttons' => 'array',
    ];

    /**
     * @return void
     */
    public static function boot(): void
    {
        parent::boot();

        static::deleting(function ($page) {
            Storage::disk(ImageUploader::STORAGE_DISK)->delete($page->image);
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
        $this->attributes['image'] = resolve(ImageUploader::class)->upload($this->image, $value, self::UPLOAD_DIRECTORY);
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

    /**
     * @return BelongsTo
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(PageTypes::class);
    }
}

