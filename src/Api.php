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

    public function setHeaders($headers)
    {
        $this->headers = $headers;

        return $this;
    }
}
