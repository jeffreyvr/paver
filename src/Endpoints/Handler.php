<?php

namespace Jeffreyvr\Paver\Endpoints;

/**
 * Single entry point for the editor's requests.
 *
 * Every request carries an `action`, so one route can serve them all:
 *
 *     Jeffreyvr\Paver\Endpoints\Handler::run();
 */
class Handler extends Endpoint
{
    /**
     * @var array<string, class-string<Endpoint>>
     */
    protected static array $actions = [
        'options' => Options::class,
        'render' => Render::class,
        'fetch' => Fetch::class,
        'resolve' => Resolve::class,
    ];

    /**
     * Register an additional action, or replace an existing one.
     */
    public static function action(string $name, string $endpoint): void
    {
        static::$actions[$name] = $endpoint;
    }

    /**
     * @return array<string, class-string<Endpoint>>
     */
    public static function actions(): array
    {
        return static::$actions;
    }

    public function handle()
    {
        $action = $this->get('action');

        if (! is_string($action) || $action === '') {
            throw new \InvalidArgumentException(
                'No action given. Expected one of: '.implode(', ', array_keys(static::$actions)).'.'
            );
        }

        if (! isset(static::$actions[$action])) {
            throw new \InvalidArgumentException(
                "Unknown action '{$action}'. Expected one of: ".implode(', ', array_keys(static::$actions)).'.'
            );
        }

        $endpoint = static::$actions[$action];

        return (new $endpoint($this->request))->handle();
    }
}
