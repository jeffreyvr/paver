<?php

namespace Jeffreyvr\Paver;

class Api
{
    /**
     * A single endpoint serving every action. Takes precedence over the
     * per action endpoints below when set.
     */
    public ?string $endpoint = null;

    /**
     * Per action endpoints. Superseded by $endpoint, kept so existing
     * setups keep working.
     */
    public array $endpoints = [
        'fetch' => '/api/fetch',
        'render' => '/api/render',
        'options' => '/api/options',
        'resolve' => '/api/resolve',
    ];

    public array $payload = [];

    public array $headers = [];

    /**
     * Point Paver at a single endpoint:
     *
     *     setEndpoint('/paver')
     *
     * Or, for the older per action setup:
     *
     *     setEndpoint('options', '/paver/options')
     */
    public function setEndpoint($nameOrUrl, $endpoint = null)
    {
        if ($endpoint === null) {
            $this->endpoint = $nameOrUrl;

            return $this;
        }

        $this->endpoints[$nameOrUrl] = $endpoint;

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
