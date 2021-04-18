<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsToMany, Relations\HasMany};
use Illuminate\Support\Collection;

class Coupon extends Model
{
    use CrudTrait;

    /**
     * @var string[]
     */
    protected $fillable = ['value', 'sale', 'from', 'till'];

    /**
     * @var string[]
     */
    protected $casts = [
        'from' => 'datetime:d-m-Y',
        'till' => 'datetime:d-m-Y',
    ];

    /**
     * @param $value
     * @return void
     */
    public function setFromAttribute($value): void
    {
        $this->attributes['from'] = is_null($value) ? NULL : Carbon::parse($value)->startOfDay();
    }

    /**
     * @param $value
     * @return void
     */
    public function setTillAttribute($value): void
    {
        $this->attributes['till'] = is_null($value) ? NULL : Carbon::parse($value)->endOfDay();
    }

    /**
     * @return BelongsToMany
     */
    public function subscriptions(): BelongsToMany
    {
        return $this->belongsToMany(Subscription::class);
    }

    /**
     * @param Builder $query
     * @return Collection
     */
    public function scopeListAll(Builder $query): Collection
    {
        return $query->orderBy('value')->pluck('value', 'id');
    }

    /**
     * @return HasMany
     */
    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeMostUsed(Builder $query): Builder
    {
        return $query
            ->withCount('members')
            ->orderBy('members_count', 'DESC')
            ->limit(1);
    }
}
