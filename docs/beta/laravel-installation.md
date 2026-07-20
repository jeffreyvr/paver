# Installation Laravel

To install Paver in Laravel, install the package using composer:

```bash
composer require jeffreyvanrossum/paver-for-laravel:dev-main
```

To publish it's config file run:

```bash
php artisan vendor:publish --tag=paver-config
```

You may register your blocks and configure several other settings, in this config file. Laravel by default comes with Alpine.js, so you might want to set `alpine` to `false`.
