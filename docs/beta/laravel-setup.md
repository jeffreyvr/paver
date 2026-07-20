# Laravel setup

- [Middleware](#middleware)
- [Endpoints](#endpoints)
- [Render the editor](#render-the-editor)
- [Add HTML to editor frame](#render-the-editor)

## Middleware

Add the following middleware to your Laravel app in the `bootstrap/app.php` file.

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'paver' => PaverMiddleware::class
    ]);
})
```

Before Laravel v11, middleware needs to be set in `app/Http/Kernel.php`.

## Endpoint

Paver's communication with the server goes to a single POST route. Register it in your routes file:

```php
use Jeffreyvr\Paver\Endpoints\Handler;

Route::middleware('paver')->post('/paver', fn() => Handler::run());
```

And point Paver at it:

```php
$paver->api->setEndpoint('/paver');
```

Every request carries an `action` (`options`, `render`, `fetch` or `resolve`) and `Handler` dispatches on it. `run` reports exceptions as JSON, so failures surface in the editor instead of silently doing nothing.

You can register your own actions too:

```php
Handler::action('my-action', MyEndpoint::class);
```

Note that the middleware `paver` has been added. This is so that the instance of `Paver` is available on these requests.

If your endpoints need to be protected, you can add the `auth` middleware. If you have set the `csrf` option in the Paver config file to `true`, this token will be send along with the requests to these endpoints - whereby authentication is handeld by Laravel.

## Render the editor

Once Paver is installed and configured, you can use the following Blade component to display the editor where you want.

```blade
<x-paver :config="['showSaveButton' => false]" :content="$content" />
```

## Add HTML to editor frame

You can set a head and footer- HTML template for the editor frame, allowing you to load your custom CSS and JS.

You can set which Blade templates should be loaded in your Paver config file.
