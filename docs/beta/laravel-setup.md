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

## Endpoints

Paver expects you to set up a couple of endpoints for it's communication with the server. These endpoints need to return specific data.

To make returning the correct data as simple as possible, pre made endpoint classes have been made.

- fetch (`Jeffreyvr\Paver\Endpoints\Fetch::class`)
- render (`Jeffreyvr\Paver\Endpoints\Render::class`)
- options (`Jeffreyvr\Paver\Endpoints\Options::class`)
- resolve (`Jeffreyvr\Paver\Endpoints\Resolve::class`)

In Laravel, you can register the routes in your routes file:

```php
Route::middleware('paver')->group(function () {
    Route::post('/options', fn() => (new Options)->handle());
    Route::post('/fetch', fn() => (new Fetch)->handle());
    Route::post('/render', fn() => (new Render)->handle());
    Route::post('/resolve', fn() => (new Resolve)->handle());
});
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
