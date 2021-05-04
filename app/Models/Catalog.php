<?php

namespace App\Models;

use App\Backpack\ImageUploader;
use App\Traits\ButtonVisibility;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Catalog extends Model
{
    use CrudTrait, ButtonVisibility;

    /**
     * @var int
     */
    const STATUS_AVAILABLE = 1;

    /**
     * @var int
     */
    const STATUS_UNAVAILABLE = 0;

    /**
     * @var string
     */
    protected const UPLOAD_DIRECTORY = 'catalog';

    /**
     * @var string[]
     */
    protected $fillable = ['name', 'amount', 'unit_id', 'price', 'description', 'image', 'extra_images', 'active', 'ingredients'];

    /**
     * @param $value
     * @return void
     */
    public function setImageAttribute($value): void
    {
        $this->attributes['image'] = resolve(ImageUploader::class)->upload($this->image, $value, static::UPLOAD_DIRECTORY);
    }

    /**
     * @param $value
     * @return void
     */
    public function setExtraImagesAttribute(): void
    {
        $this->attributes['extra_images'] = resolve(ImageUploader::class)->uploadMultiple($this->extra_images, 'extra_images', static::UPLOAD_DIRECTORY);
    }

    /**
     * @return BelongsTo
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(UnitTypes::class);
    }
}
