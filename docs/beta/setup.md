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

## API-endpoints

The editor expects you to set up a couple of (API) endpoints, so that it can fetch and render blocks. These endpoints need to return specific data.

To make returning the correct data as simple as possible, you can use the endpoint classes for the responses.

- fetch (`Jeffreyvr\Paver\Endpoints\Fetch::class`)
- render (`Jeffreyvr\Paver\Endpoints\Render::class`)
- options (`Jeffreyvr\Paver\Endpoints\Options::class`)

To set the endpoints, use:

```php
$paver->api->setEndpoints([
    'options' => '/your-options-endpoint/',
    'render' => '/your-render-endpoint/',
    'fetch' => '/your-fetch-endpoint/',
]);
```

To pass along other data, you may use:

```php
$paver->api->setHeaders([]);
$paver->api->setPayload([]);
```
