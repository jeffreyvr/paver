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

    function setEndpoints($endpoints)
    {
        $this->endpoints = $endpoints;

        return $this;
    }

    function setPayload($payload)
    {
        $this->payload = $payload;

        return $this;
    }

    function setHeaders($headers)
    {
        $this->headers = $headers;

        return $this;
    }
}
