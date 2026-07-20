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
     * Per action endpoints.
     *
     * @deprecated Use a single endpoint via setEndpoint() instead. Support for
     *             an endpoint per action will be removed in a future release.
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
     * Passing two arguments sets one action's endpoint instead:
     *
     *     setEndpoint('options', '/paver/options')
     *
     * That second form is deprecated and will be removed in a future release.
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

    /**
     * @deprecated Use setEndpoint('/your-endpoint') instead. Support for an
     *             endpoint per action will be removed in a future release.
     */
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
