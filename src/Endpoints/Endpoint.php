<?php

namespace Jeffreyvr\Paver\Endpoints;

use Jeffreyvr\Paver\RenderContext;

abstract class Endpoint
{
    public array $request = [];

    /**
     * @param  array|null  $request  Pre-parsed request body. Defaults to reading
     *                               the raw request, which is what you want
     *                               unless another endpoint is delegating to
     *                               this one.
     */
    public function __construct(?array $request = null)
    {
        if ($request !== null) {
            $this->request = $request;

            return;
        }

        $input = file_get_contents('php://input');

        if ($input) {
            $this->request = json_decode($input, true) ?? [];
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

    /**
     * Run a handler, turning any exception into a JSON error response so the
     * editor can surface it instead of choking on an HTML error page.
     */
    public static function run(...$args)
    {
        $endpoint = new static(...$args);

        try {
            return $endpoint->handle();
        } catch (\Throwable $e) {
            $endpoint->json([
                'error' => $e->getMessage(),
                'type' => get_class($e),
            ], 500);
        }
    }

    public function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);

        header('Content-Type: application/json');

        echo json_encode($data);

        exit;
    }
}
