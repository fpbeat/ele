<?php

namespace App\Contracts\Botman;

use App\Services\Botman\CustomRequestResponse;
use BotMan\BotMan\Exceptions\Core\BadMethodCallException;

interface CustomRequestInterface
{
    /**
     * @param string $endpoint
     * @param array $additionalParameters
     * @return CustomRequestResponse
     * @throws BadMethodCallException
     */
    public function request(string $endpoint, array $additionalParameters = []): CustomRequestResponse;

    /**
     * @param string $driver
     * @param array $config
     * @return void
     */
    public function bootstrap(string $driver, array $config = []): void;
}
