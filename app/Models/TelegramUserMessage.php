<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TelegramUserMessage extends Model
{
    /**
     * @var null|string
     */
    public const UPDATED_AT = null;

    /**
     * @var string[]
     */
    protected $fillable = ['user_id', 'author_id', 'is_sent', 'event', 'message'];

    /**
     * @param string $value
     * @return string
     */
    public function getMessageAttribute(string $value): string
    {
        return Str::cleanupSummernote($value);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(TelegramUser::class);
    }

    /**
     * @return BelongsTo
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
