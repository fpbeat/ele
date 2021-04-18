<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo};

class Member extends Model
{
    use CrudTrait;

    /**
     * @var int
     */
    const STATUS_ACTIVE = 1;

    /**
     * @var int
     */
    const STATUS_INACTIVE = 0;

    /**
     * @var int
     */
    const STATUS_EXPIRED_NOTIFY_SEND = 1;

    /**
     * @var int
     */
    const STATUS_EXPIRED_NOTIFY_NOT_SEND = 0;

    /**
     * @var string[]
     */
    protected $with = ['subscription', 'coupon'];

    /**
     * @var string[]
     */
    protected $casts = [
        'up' => 'datetime',
        'till' => 'datetime',
        'day' => 'date'
    ];

    /**
     * @var string[]
     */
    protected $fillable = ['order_no', 'user_id', 'first_last_name', 'active', 'username', 'active', 'subscription_id', 'coupon_id', 'invite_link', 'up', 'till'];

    /**
     * @return BelongsTo
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * @return BelongsTo
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * @return bool
     */
    public function getNotOperatedAttribute(): bool
    {
        return $this->active === self::STATUS_ACTIVE || now()->lt($this->till);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeDaysLeft(Builder $query): Builder
    {
        return $query->selectRaw('IF(DATEDIFF(`till`, now()) > 0, DATEDIFF(`till`, now()), 0) AS days_left');
    }

    public function scopeOutdated(Builder $query): Builder
    {
        return $query
            ->where('till', '<', now())
            ->whereActive(self::STATUS_ACTIVE);
    }

    public function scopeAboveExpired(Builder $query): Builder
    {
        return $query
            ->whereRaw('DATEDIFF(`till`, now()) <= ?', config('chatbot.days_for_renewal'))
            ->whereActive(self::STATUS_ACTIVE)
            ->whereExpiredNotifySend(self::STATUS_EXPIRED_NOTIFY_NOT_SEND);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereActive(self::STATUS_ACTIVE);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeForToday(Builder $query): Builder
    {
        return $query->whereRaw('DATE(created_at) = CURDATE()');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeGroupByHours(Builder $query): Builder
    {
        return $query->selectRaw('COUNT(*) AS cnt, HOUR(created_at) AS hour')
            ->whereRaw('DATE(created_at) = CURDATE()')
            ->groupByRaw('HOUR(created_at)');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeGroupByDays(Builder $query): Builder
    {
        return $query->selectRaw('COUNT(*) as cnt, DATE(created_at) AS day')
            ->whereRaw('DATE_FORMAT(created_at, "%c-%Y") = DATE_FORMAT(NOW(), "%c-%Y")')
            ->groupByRaw('DATE(created_at)');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeGroupByMonths(Builder $query): Builder
    {
        return $query->selectRaw('COUNT(*) as cnt, DATE(created_at) AS day')
            ->whereRaw('YEAR(created_at) = YEAR(NOW())')
            ->groupByRaw('MONTH(created_at)');
    }

    /**
     * @return string
     */
    public function telegramLink(): string
    {
        return $this->username ? sprintf('<a class="btn btn-sm btn-link" target="_blank" href="https://t.me/%s"><i class="la la-telegram"></i> Send message</a>', urlencode($this->username)) : '';
    }
}
