<?php

namespace App\Repositories;

use App\Models\Setting;
use App\Traits\SettingsCast;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\{Collection, Str};
use Tightenco\Collect\Support\Arr;

class SettingRepository implements Arrayable
{
    use SettingsCast;

    /**
     * @var string[]
     */
    protected array $settingCasts = [
        'notifications.time_range' => 'collection',
        'notifications.groups' => 'collection',
        'notifications.events' => 'collection'
    ];

    /**
     * @var string
     */
    private const NAMESPACE_SEPARATOR = '__';

    /**
     * @var Collection|null
     */
    static ?Collection $cache = null;

    /**
     * @return void
     */
    public function __construct()
    {
        self::$cache = rescue(fn() => $this->getGrouped());
    }

    /**
     * @param Setting $entity
     * @param array $data
     */
    public function store(Setting $entity, array $data): void
    {
        $entity->values = $data;
        $entity->save();
    }

    /**
     * @return Collection
     */
    private function getSettings(): Collection
    {
        return $this->getSettingRecord()->getAttribute('values') ?? collect();
    }

    /**
     * @return Setting
     */
    public function getSettingRecord(): Setting
    {
        return Setting::lastId()->firstOrFail();
    }

    /**
     * @return Collection
     */
    private function getGrouped(): Collection
    {
        return $this->getSettings()
            ->groupBy(fn($value, $key) => Str::before($key, static::NAMESPACE_SEPARATOR), true)
            ->map(fn(Collection $item) => $item->keyBy(fn($a, $key) => Str::after($key, static::NAMESPACE_SEPARATOR)));
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return (self::$cache ?? $this->getGrouped())->toArray();
    }


    /**
     * @param string $key
     * @param null $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        $value = Arr::get($this->toArray(), $key, $default);

        return $this->hasCast($key) ? $this->castAttribute($key, $value) : $value;
    }
}
