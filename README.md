<p align="center"><a href="https://vanrossum.dev" target="_blank"><img src="resources/svgs/logo.svg" width="320" alt="vanrossum.dev Logo"></a></p>

<p align="center">
<a href="https://packagist.org/packages/jeffreyvanrossum/paver"><img src="https://img.shields.io/packagist/dt/jeffreyvanrossum/paver" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/jeffreyvanrossum/paver"><img src="https://img.shields.io/packagist/v/jeffreyvanrossum/paver" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/jeffreyvanrossum/paver"><img src="https://img.shields.io/packagist/l/jeffreyvanrossum/paver" alt="License"></a>
</p>

# Paver

Paver Editor is a drag and drop based block editor (or page builder).

For detailed instructions on how to use the editor, see the [documentation](https://paver-editor.com/docs).

## FAQ

### How do I save the editor content?

The editor keeps a json version on the content in an hidden input named `paver_editor_content`. You have to implement your own way of that data.

For the initial render of content, you can just pass the json (or array version) that was saved earlier.

```php
$paver->render($content);
```

### How do I render the content of the editor, outside the editor?

So you have created something in the editor, and now you want to display it elsewhere.

Simply:

```php
Renderer::blocks($content);
```

## Contributors
* [Jeffrey van Rossum](https://github.com/jeffreyvr)
* [All contributors](https://github.com/jeffreyvr/paver/graphs/contributors)

## License
MIT. Please see the [License File](/LICENSE) for more information.
