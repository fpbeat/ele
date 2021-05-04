<?php

namespace App\Models;

use App\Traits\ButtonVisibility;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * @property double $price
 */
class Setting extends Model
{
    use CrudTrait, ButtonVisibility;

    /**
     * @var string[]
     */
    protected $casts = [
        'values' => 'collection'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string[]
     */
    protected $fillable = ['values'];

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeLastId(Builder $query): Builder
    {
        return $query->orderBy('id', 'desc');
    }

    /**
     * @param string $key
     * @return array|\ArrayAccess|mixed
     */
    public function __get($key)
    {
        $values = $this->getAttribute('values');

        return Arr::get($values, $key);
    }
}
