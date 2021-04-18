<?php

namespace App\Services\Botman;

use App\Contracts\Botman\CustomRequestInterface;
use BotMan\BotMan\{BotMan, Drivers\DriverManager};

class CustomRequest implements CustomRequestInterface
{
    /**
     * @var BotMan
     */
    private $botman;

    /**
     * CustomRequest constructor.
     */
    public function __construct()
    {
        $this->botman = app('botman');
    }

    /**
     * @param string $driver
     * @param array $config
     */
    public function bootstrap(string $driver, array $config = []): void
    {
        $this->botman->setDriver(DriverManager::loadFromName($driver, $config));
    }

    /**
     * @inheritDoc
     */
    public function request($endpoint, $additionalParameters = []): CustomRequestResponse
    {
        $response = $this->botman->sendRequest($endpoint, $additionalParameters);

        return CustomRequestResponse::loadFromResponse($response);
    }
}
