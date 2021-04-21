<?php

namespace App\Botman\Traits;

use Illuminate\Support\Arr;

trait UserStorage
{
    /**
     * @var string
     */
    private static string $storageNamespace = 'chatbot';

    /**
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    protected function getStorageValue(?string $key, $default = null)
    {
        $data = app('botman')
            ->userStorage()
            ->find(self::$storageNamespace)
            ->get(self::$storageNamespace, []);

        return data_get($data, $key, $default);
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    protected function setStorageValue(string $key, $value): void
    {
        $storage = $this->getStorageValue(NULL, []);

        app('botman')
            ->userStorage()
            ->save([self::$storageNamespace => Arr::filterRecursive(data_set($storage, $key, $value))], self::$storageNamespace);
    }
}
