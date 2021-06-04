<?php

namespace App\Models;

use App\Backpack\ImageUploader;
use App\Contracts\NodeCategoryInterface;
use App\Traits\ButtonVisibility;
use App\Traits\NestedSetsNode;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Kalnoy\Nestedset\NodeTrait;

class Page extends Model implements NodeCategoryInterface
{
    use CrudTrait, NodeTrait, NestedSetsNode, ButtonVisibility;

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
        'buttons' => 'array'
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
     * @return BelongsTo
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(PageTypes::class);
    }
}

