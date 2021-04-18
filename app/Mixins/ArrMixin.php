<?php

namespace App\Mixins;

use Illuminate\Support\Arr;

class ArrMixin
{
    /**
     * @return \Closure
     */
    public function filterRecursive(): \Closure
    {
        return function (array $array, callable $callback = null) {
            $array = is_callable($callback) ? array_filter($array, $callback) : array_filter($array);

            foreach ($array as &$value) {
                if (is_array($value)) {
                    $value = call_user_func([Arr::class, 'filterRecursive'], $value, $callback);
                }
            }

            return $array;
        };
    }
}
