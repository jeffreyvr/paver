<?php

use Jeffreyvr\Paver\Api;
use Jeffreyvr\Paver\Endpoints\Fetch;
use Jeffreyvr\Paver\Endpoints\Handler;
use Jeffreyvr\Paver\Endpoints\Options;

describe('Endpoint Handler', function () {
    it('maps the built in actions', function () {
        $actions = Handler::actions();

        expect($actions)->toHaveKeys(['options', 'render', 'fetch', 'resolve']);
        expect($actions['options'])->toBe(Options::class);
        expect($actions['fetch'])->toBe(Fetch::class);
    });

    it('rejects an unknown action with a helpful message', function () {
        $handler = new Handler(['action' => 'nope']);

        expect(fn () => $handler->handle())
            ->toThrow(InvalidArgumentException::class, "Unknown action 'nope'");
    });

    it('rejects a missing action', function () {
        $handler = new Handler([]);

        expect(fn () => $handler->handle())
            ->toThrow(InvalidArgumentException::class, 'No action given');
    });

    it('can register a custom action', function () {
        Handler::action('custom', Options::class);

        expect(Handler::actions())->toHaveKey('custom');
    });
});

describe('Endpoint request', function () {
    it('accepts a pre parsed request', function () {
        $endpoint = new Handler(['action' => 'options', 'block' => 'x']);

        expect($endpoint->get('action'))->toBe('options');
        expect($endpoint->get('block'))->toBe('x');
        expect($endpoint->get('missing', 'fallback'))->toBe('fallback');
    });
});

describe('Api endpoints', function () {
    it('sets a single endpoint with one argument', function () {
        $api = new Api;
        $api->setEndpoint('/paver');

        expect($api->endpoint)->toBe('/paver');
    });

    it('still sets a named endpoint with two arguments', function () {
        $api = new Api;
        $api->setEndpoint('options', '/paver/options');

        expect($api->endpoint)->toBeNull();
        expect($api->endpoints['options'])->toBe('/paver/options');
    });

    it('includes resolve in the defaults', function () {
        expect((new Api)->endpoints)->toHaveKey('resolve');
    });

    it('serializes the single endpoint for the editor', function () {
        $api = new Api;
        $api->setEndpoint('/paver');

        expect(json_decode(json_encode($api), true))->toHaveKey('endpoint');
    });
});
