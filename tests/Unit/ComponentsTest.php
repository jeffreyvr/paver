<?php

use Jeffreyvr\Paver\Api;
use Jeffreyvr\Paver\Editor;
use Jeffreyvr\Paver\Frame;
use Jeffreyvr\Paver\View;

describe('Api', function () {
    it('has default endpoints', function () {
        $api = new Api();
        
        expect($api->endpoints)->toHaveKeys(['fetch', 'render', 'options']);
    });

    it('can set single endpoint', function () {
        $api = new Api();
        $api->setEndpoint('fetch', '/custom/fetch');
        
        expect($api->endpoints['fetch'])->toBe('/custom/fetch');
    });

    it('can set all endpoints', function () {
        $api = new Api();
        $api->setEndpoints([
            'fetch' => '/a',
            'render' => '/b',
        ]);
        
        expect($api->endpoints)->toBe(['fetch' => '/a', 'render' => '/b']);
    });

    it('can set payload', function () {
        $api = new Api();
        $result = $api->setPayload(['key' => 'value']);
        
        expect($result)->toBe($api);
        expect($api->payload)->toBe(['key' => 'value']);
    });

    it('can set single header', function () {
        $api = new Api();
        $api->setHeader('Authorization', 'Bearer token');
        
        expect($api->headers['Authorization'])->toBe('Bearer token');
    });

    it('can set all headers', function () {
        $api = new Api();
        $result = $api->setHeaders(['X-Custom' => 'value']);
        
        expect($result)->toBe($api);
        expect($api->headers)->toBe(['X-Custom' => 'value']);
    });
});

describe('Editor', function () {
    it('has empty head and footer html by default', function () {
        $editor = new Editor();
        
        expect($editor->headHtml)->toBe('');
        expect($editor->footerHtml)->toBe('');
    });

    it('can set head html', function () {
        $editor = new Editor();
        $editor->headHtml = '<script src="test.js"></script>';
        
        expect($editor->headHtml)->toContain('test.js');
    });
});

describe('Frame', function () {
    it('has empty head and footer html by default', function () {
        $frame = new Frame();
        
        expect($frame->headHtml)->toBe('');
        expect($frame->footerHtml)->toBe('');
    });

    it('is inactive by default', function () {
        $frame = new Frame();
        
        expect($frame->active)->toBeFalse();
    });

    it('can be activated', function () {
        $frame = new Frame();
        $result = $frame->activate();
        
        expect($result)->toBe($frame);
        expect($frame->active)->toBeTrue();
    });

    it('can be deactivated', function () {
        $frame = new Frame();
        $frame->activate();
        $result = $frame->deactivate();
        
        expect($result)->toBe($frame);
        expect($frame->active)->toBeFalse();
    });
});

describe('View', function () {
    it('renders php file with data', function () {
        // Create a temporary view file
        $tempFile = sys_get_temp_dir() . '/test_view_' . uniqid() . '.php';
        file_put_contents($tempFile, '<?php echo "Hello, " . $name; ?>');
        
        $view = new View($tempFile, ['name' => 'World']);
        $output = $view->render();
        
        expect($output)->toBe('Hello, World');
        
        unlink($tempFile);
    });

    it('can be cast to string', function () {
        $tempFile = sys_get_temp_dir() . '/test_view_' . uniqid() . '.php';
        file_put_contents($tempFile, '<?php echo "Test"; ?>');
        
        $view = new View($tempFile);
        
        expect((string) $view)->toBe('Test');
        
        unlink($tempFile);
    });

    it('makes data available as properties', function () {
        $tempFile = sys_get_temp_dir() . '/test_view_' . uniqid() . '.php';
        file_put_contents($tempFile, '<?php echo $this->greeting; ?>');
        
        $view = new View($tempFile, ['greeting' => 'Hi']);
        $output = $view->render();
        
        expect($output)->toBe('Hi');
        
        unlink($tempFile);
    });
});
