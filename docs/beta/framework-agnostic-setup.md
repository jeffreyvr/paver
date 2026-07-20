# Framework agnostic setup

- [Basic setup](#basic-setup)
- [Endpoints](#endpoints)
- [Add HTML to editor frame](#add-html-to-editor-frame)
- [Caveats](#caveats)

## Basic setup

To make Paver work in a framework agnostic context, you should first create an instance of Paver.

```php
$paver = Paver::instance();
```

On this instance, you can register your blocks.

```php
$paver->registerBlock(Text::class);
```

For more about blocks, please refer to [Making blocks](/docs/beta/making-blocks).

Finally, you can render the editor.

```php
$paver->render();
```

## Endpoints

Paver expects you to set up a couple of endpoints, so that it can fetch and render blocks. These endpoints need to return specific data. They are all POST requests.

To make returning the correct data as simple as possible, you can use the these classes for the responses:

- fetch (`Jeffreyvr\Paver\Endpoints\Fetch::class`)
- render (`Jeffreyvr\Paver\Endpoints\Render::class`)
- options (`Jeffreyvr\Paver\Endpoints\Options::class`)
- resolve (`Jeffreyvr\Paver\Endpoints\Resolve::class`)

To set the endpoints, use:

```php
$paver->api->setEndpoints([
    'options' => '/your-options-endpoint/',
    'render' => '/your-render-endpoint/',
    'fetch' => '/your-fetch-endpoint/',
    'resolve' => '/your-fetch-endpoint/',
]);
```

If you need to pass along data or headers with these requests, you may use the following functions:

```php
$paver->api->setHeaders([]);
$paver->api->setPayload([]);
```

## Add HTML to editor frame

You can set a head and footer- HTML, allowing you to load your custom CSS and JS.

```php
$paver->frame->headHtml = 'your head html';
$paver->frame->footerHtml = 'your footer html';
```

## Caveats

Be aware that the instance of Paver also needs to be setup when the communications calls are made, otherwise, your endpoints are not aware of the registered blocks.
