<?php

namespace App\Models;

use App\Traits\ButtonVisibility;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo};

class TelegramUser extends Model
{
    use CrudTrait, ButtonVisibility;

    /**
     * @var string[]
     */
    protected $fillable = ['user_id', 'username', 'full_name', 'language_code', 'last_page_id', 'locked'];

    /**
     * @param null|string $value
     * @return string
     */
    public function getLanguageCodeAttribute(?string $value): string
    {
        return strtoupper($value);
    }

    /**
     * @return string|null
     */
    public function getTelegramWebLinkAttribute(): ?string
    {
        return $this->attributes['username'] ? sprintf('<a href="https://t.me/%s" target="_blank">%s</a>', $this->attributes['username'], $this->attributes['username']) : null;
    }

    /**
     * @return bool
     */
    public function getIsActiveAttribute(): bool
    {
        return !$this->attributes['locked'];
    }

    /**
     * @return bool
     */
    public function getCanWriteTelegramAttribute(): bool
    {
        return true;
    }

    /**
     * @return BelongsTo
     */
    public function lastPage(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeGroupByLastPage(Builder $query): Builder
    {
        return $query
            ->leftJoin('pages', 'telegram_users.last_page_id', '=', 'pages.id')
            ->groupBy('telegram_users.last_page_id');
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
