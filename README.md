<p align="center"><a href="https://vanrossum.dev" target="_blank"><img src="resources/svgs/logo.svg" width="320" alt="vanrossum.dev Logo"></a></p>

<p align="center">
<a href="https://packagist.org/packages/jeffreyvanrossum/paver"><img src="https://img.shields.io/packagist/dt/jeffreyvanrossum/paver" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/jeffreyvanrossum/paver"><img src="https://img.shields.io/packagist/v/jeffreyvanrossum/paver" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/jeffreyvanrossum/paver"><img src="https://img.shields.io/packagist/l/jeffreyvanrossum/paver" alt="License"></a>
</p>

# Paver

Paver Editor is a drag and drop based block editor (or page builder).

## Setup

```php
$paver = Paver::instance();
```
## Register blocks

```php
$paver->registerBlock(Text::class);
```

To create blocks, see [Creating blocks](#creating-blocks).

## Render the editor

```php
$paver->render();
```

## Creating blocks

Below you'll see a basic example of a block. The options method, returns the html for the block options that will be rendered in the editor when the block is being edited.

You use Alpine's model binding to make sure the updates are actually picked up and the block is (re-)rendered.

```php
use Jeffreyvr\Paver\Blocks\Block;

class Text extends Block
{
    public string $name = 'Text';

    public static string $reference = 'your_prefix.text';

    public array $data = [
            'title' => 'Example title',
    ];

    function options()
    {
        return <<<HTML
            <div class="option">
                <label>Title</label>
                <input type="text" x-model="title">
            </div>
        HTML;
    }

    function render()
    {
        extract($this->data);

        return <<<HTML
            <div>{$title}</div>
        HTML;
    }
}
```

Instead of writing your HTML in the class itself, you can also refer to a template like to:

```php
return new View('path-to-your-blocks/text/options.php', $this->data);
```

## Block options

As seen in the example block, you can just write html with alpine to make your block options. However, there are some default options that you can use as well.

```php
Input::make('Title', 'title');
Textarea::make('Title', 'title');
Select::make('Alignment', 'align', [
    '' => 'None',
    'left' => 'Left',
    'right' => 'Right'
])
```

## Communication

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
