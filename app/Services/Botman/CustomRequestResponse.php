<?php

namespace App\Services\Botman;

use Symfony\Component\HttpFoundation\Response;

class CustomRequestResponse
{
    /**
     * @var Response
     */
    private Response $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @param Response $response
     * @return static
     */
    static public function loadFromResponse(Response $response): self
    {
        return new static($response);
    }

    /**
     * @return array
     */
    public function getContent(): array
    {
        $content = json_decode($this->response->getContent(), true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $content;
        }

        throw new \InvalidArgumentException("Error parsing JSON response");
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return rescue(function () {
            $content = $this->getContent();

            return $this->response->isSuccessful() && intval($content['ok']) === 1;
        }, false);
    }

    /**
     * @throws \Exception
     */
    public function throwIfFailed()
    {
        if (!$this->isSuccess()) {
            $content = $this->getContent();

            throw new \Exception(strval($content['description']), $content['error_code'] ?? 500);
        }
    }

    /**
     * @param string $method
     * @param array $params
     * @return mixed
     */
    public function __call(string $method, array $params)
    {
        return call_user_func_array([$this->response, $method], $params);
    }
}
