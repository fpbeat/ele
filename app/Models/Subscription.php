<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

/**
 * @property double $price
 */
class Subscription extends Model
{
    use CrudTrait;

    /**
     * @var string[]
     */
    protected $fillable = ['name', 'description', 'link_to_channel', 'duration', 'price'];

    /**
     * @param float $discount
     * @return float
     */
    public function getPriceWithDiscount(float $discount): float
    {
        return $this->price - round(($this->price / 100) * $discount);
    }

    /**
     * @return BelongsTo
     */
    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    /**
     * @param Builder $query
     * @return Collection
     */
    public function scopeListAll(Builder $query): Collection
    {
        return $query->orderBy('name')->pluck('name', 'id');
    }
}
