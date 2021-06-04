<?php

namespace App\Models;

use App\Backpack\ImageUploader;
use App\Contracts\NodeCategoryInterface;
use App\Traits\ButtonVisibility;
use App\Traits\NestedSetsNode;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Kalnoy\Nestedset\NodeTrait;

class Category extends Model implements NodeCategoryInterface
{
    use CrudTrait, NodeTrait, NestedSetsNode, ButtonVisibility;

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
     * @param $query
     * @return mixed
     */
    public function scopeWhereNotRoot($query)
    {
        return $query->whereNotNull($this->getParentIdName());
    }
}
