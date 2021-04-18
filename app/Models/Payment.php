<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo};

class Payment extends Model
{
    /**
     * @var null|string
     */
    const UPDATED_AT = null;

    /**
     * @var string[]
     */
    protected $fillable = ['member_id', 'amount'];

    /**
     * @return BelongsTo
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * @param Builder $query
     * @return float
     */
    public function scopeTotal(Builder $query): float
    {
        return $query->sum('amount');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeForToday(Builder $query): Builder
    {
        return $query->whereRaw('DATE(created_at) = CURDATE()');
    }


}
