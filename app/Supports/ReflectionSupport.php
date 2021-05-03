<?php

namespace App\Supports;

use Illuminate\Contracts\Container\BindingResolutionException;

class ReflectionSupport
{
    /**
     * @param string $class
     * @return array
     * @throws BindingResolutionException
     */
    public function getInstantiableClassParameters(string $class): array
    {
        try {
            $reflector = new \ReflectionClass($class);

            $constructor = $reflector->getConstructor();
            if ($reflector->isInstantiable() && $constructor !== null) {
                return collect($constructor->getParameters())
                    ->map(fn($item) => $item->name)
                    ->toArray();
            }

        } catch (\ReflectionException $e) {
            throw new BindingResolutionException("Target class [$class] does not exist.", 0, $e);
        }

        return [];
    }
}
