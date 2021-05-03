<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Concerns\HasAttributes;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait SettingsCast
{
    use HasAttributes;

    /**
     * @inheritDoc
     */
    public function getCasts()
    {
        return $this->settingCasts;
    }

    /**
     * @inheritDoc
     */
    protected function castAttribute($key, $value)
    {
        $castType = $this->getCastType($key);

        if (is_null($value) && in_array($castType, static::$primitiveCastTypes)) {
            return $value;
        }

        if ($this->isEncryptedCastable($key)) {
            $value = $this->fromEncryptedString($value);

            $castType = Str::after($castType, 'encrypted:');
        }

        switch ($castType) {
            case 'int':
            case 'integer':
                return (int)$value;
            case 'real':
            case 'float':
            case 'double':
                return $this->fromFloat($value);
            case 'decimal':
                return $this->asDecimal($value, explode(':', $this->getCasts()[$key], 2)[1]);
            case 'string':
                return (string)$value;
            case 'bool':
            case 'boolean':
                return (bool)$value;
            case 'array':
            case 'json':
                return $this->getValueFromArrayOrJson($value);
            case 'collection':
                return new Collection($this->getValueFromArrayOrJson($value));
            case 'date':
                return $this->asDate($value);
            case 'datetime':
            case 'custom_datetime':
                return $this->asDateTime($value);
            case 'timestamp':
                return $this->asTimestamp($value);
        }

        if ($this->isClassCastable($key)) {
            return $this->getClassCastableAttributeValue($key, $value);
        }

        return $value;
    }

    /**
     * @param string|array $value
     * @return array
     */
    private function getValueFromArrayOrJson($value): array
    {
        if (is_array($value) || $value instanceof \Traversable) {
            return $value;
        }

        return $this->fromJson($value);
    }
}
