<?php

namespace Jeffreyvr\Paver\Endpoints;

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

    public function json($data)
    {
        header('Content-Type: application/json');

        echo json_encode($data);

        exit;
    }
}
