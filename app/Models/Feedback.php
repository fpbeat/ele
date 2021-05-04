<?php

namespace App\Models;

use App\Traits\ButtonVisibility;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Feedback extends Model
{
    use CrudTrait, ButtonVisibility;

    /**
     * @var int
     */
    const MESSAGE_BRIEF_LENGTH = 40;

    /**
     * @var string
     */
    public const TYPE_REVIEW = 'review';

    /**
     * @var string
     */
    public const TYPE_PROPOSAL = 'proposal';

    /**
     * @var null|string
     */
    public const UPDATED_AT = null;

    /**
     * @var string[]
     */
    protected $fillable = ['user_id', 'message', 'type'];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(TelegramUser::class);
    }

    /**
     * @return string
     */
    public function getMessageBriefAttribute(): string
    {
        return Str::words($this->attributes['message'], static::MESSAGE_BRIEF_LENGTH);
    }

    /**
     * @param $value
     * @return string
     */
    public function getMessageAttribute($value): string
    {
        return nl2br($value);
    }

    /**
     * @return bool
     */
    public function getCanWriteTelegramAttribute(): bool
    {
        return !is_null($this->user);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeGroupByUser(Builder $query): Builder
    {
        return $query
            ->select('feedback.user_id', 'telegram_users.full_name')
            ->leftJoin('telegram_users', 'telegram_users.id', '=', 'feedback.user_id')
            ->groupBy('feedback.user_id');
    }
}
