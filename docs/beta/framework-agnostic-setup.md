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

## Endpoint

Paver talks to the server to fetch and render blocks. All of it goes to a single POST endpoint, which you point Paver at:

```php
$paver->api->setEndpoint('/your-endpoint/');
```

Every request carries an `action`, and `Handler` dispatches on it, so your endpoint only needs to hand the request over:

```php
use Jeffreyvr\Paver\Endpoints\Handler;

Handler::run();
```

`run` reports exceptions as JSON, so the editor can show you what went wrong instead of failing silently.

### Adding your own actions

```php
Handler::action('my-action', MyEndpoint::class);
```

Where `MyEndpoint` extends `Jeffreyvr\Paver\Endpoints\Endpoint`.

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
