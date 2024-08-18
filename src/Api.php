<?php

namespace Jeffreyvr\Paver;

class Api
{
    public array $endpoints = [
        'fetch' => '/api/fetch',
        'render' => '/api/render',
        'options' => '/api/options',
    ];

    public array $payload = [];

    public array $headers = [];

    public function setEndpoint($name, $endpoint)
    {
        $this->endpoints[$name] = $endpoint;

        return $this;
    }

    public function setEndpoints($endpoints)
    {
        $this->endpoints = $endpoints;

        return $this;
    }

    public function setPayload($payload)
    {
        $this->payload = $payload;

        return $this;
    }

    public function setHeader($header, $value)
    {
        $this->headers[$header] = $value;
    }

    public function setHeaders($headers)
    {
        $this->headers = $headers;

        return $this;
    }
}
