<?php

namespace Core\Services;

use Illuminate\Support\Facades\Http;

class Client
{
    /**
     * @var string
     */
    public $baseUrl;

    /**
     * @var \Illuminate\Http\Client\PendingRequest
     */
    private $client;

    /**
     * Client constructor.
     */
    public function __construct($token = null)
    {
        $this->baseUrl = config('core.api_url');

        $this->client = Http::baseUrl($this->baseUrl);

        if ($token) {
            $this->setToken($token);
        }
    }

    /**
     * Get the client instance
     *
     * @return \Illuminate\Http\Client\Factory
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set the token for the client
     *
     * @param string $token
     * @return void
     */
    function setToken($token)
    {
        $this->client->withToken($token);
    }

    /**
     * Magic method to call the client methods
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    function __call($name, $arguments)
    {
        return $this->getClient()->$name(...$arguments);
    }
}
