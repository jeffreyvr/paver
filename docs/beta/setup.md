# Setup

- [Creating the instance](#creating-the-instance)
- [Registering blocks](#registering-blocks)
- [Render the editor](#render-the-editor)
- [API-endpoints](#api-endpoints)

## Creating the instance

First, create an instance of Paver.

```php
$paver = Paver::instance();
```

## Registering blocks

```php
$paver->registerBlock(Text::class);
```

To create blocks, see [Creating blocks](#making-blocks).

## Render the editor

```php
$paver->render();
```

## API-endpoint

The editor talks to the server to fetch and render blocks. All of it goes to a single POST endpoint, which you point Paver at:

```php
$paver->api->setEndpoint('/your-endpoint/');
```

Every request carries an `action`, and `Handler` dispatches on it, so your endpoint only needs to hand the request over:

```php
use Jeffreyvr\Paver\Endpoints\Handler;

Handler::run();
```

`run` reports exceptions as JSON, so the editor can show you what went wrong instead of failing silently.

You can register your own actions as well:

```php
Handler::action('my-action', MyEndpoint::class);
```

To pass along other data, you may use:

```php
$paver->api->setHeaders([]);
$paver->api->setPayload([]);
```
