# WordPress setup

Add the following code to your theme:

```php
add_action('init', function() {
    $paver = Paver::instance();

    $paver->registerBlock(Text::class);

    $paver->bootForWordPress();
}, -1);
```

This code creates an instance of Paver, registers a custom block and lastely boots up the editor.

After setting up the editor, you will see a Paver option in the admin bar where you can configure with which post types you would like to use Paver.
