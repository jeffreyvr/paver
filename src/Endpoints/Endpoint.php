<?php

namespace Jeffreyvr\Paver\Endpoints;

use Jeffreyvr\Paver\RenderContext;

abstract class Endpoint
{
    public array $request = [];

    public function __construct()
    {
        $input = file_get_contents('php://input');

        if ($input) {
            $this->request = json_decode($input, true);
        }
    }

    public function get($key, $default = null)
    {
        return $this->request[$key] ?? $default;
    }

    public function context(): RenderContext
    {
        return paver()->resolveContext($this->get('context', []), 'editor');
    }

    public function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);

        header('Content-Type: application/json');

        echo json_encode($data);

        exit;
    }
}
