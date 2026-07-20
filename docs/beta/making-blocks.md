# Making blocks

- [Creating blocks](#creating-blocks)
    - [The basics](#the-basics)
    - [Block data](#block-data)
    - [Block options](#block-options)
    - [Block render](#block-render)
    - [Using views](#using-views)
- [Registering a block](#registering-a-block)
- [Options](#options)
- [Styles and scripts](#styles-and-scripts)
- [Nesting](#nesting)
- [Tips](#tips)

## Creating blocks

### The basics

A block class should extend `Jeffreyvr\Paver\Blocks\Block`. A barebones block looks like this:

```php
class Example extends Block
{
    public string $name = 'Example';

    public static string $reference = 'your_prefix.example';

    public array $data = [];

    function options()
    {
        return '<div>Your options</div>';
    }

    function render()
    {
        return '<div>Your rendered block</div>'
    }
}
```

Note that you need to set a block name and a reference. It is advised to prefix your reference, though it is not strictly required.

To set an icon, you can pass SVG code on the `icon` attribute.

### Block data

You may define what data the block may hold, say for example, a title. Add this to the data array property.

```php
class Example extends Block
{
    public string $name = 'Example';

    public static string $reference = 'your_prefix.example';

    public array $data = [
        {+'title' => 'A default title'+}
    ];

    function options()
    {
        return '<div>Your options</div>';
    }

    function render()
    {
        return '<div>Your rendered block</div>'
    }
}
```

### Block options

From within the options method, we can add the HTML so that you this title can be updated. If you are familiar with Alpine, you'll notice the `x-model` attribute, which is used to bind the data.

```php
class Example extends Block
{
    public string $name = 'Example';

    public static string $reference = 'your_prefix.example';

    public array $data = [
        'title' => 'A default title'
    ];

    function options()
    {
        {-return '<div>Your options</div>';-}
        {+return <<<HTML
            <div class="option">
                <label>Title</label>
                <input type="text" x-model="title">
            </div>
        HTML;+}
    }

    function render()
    {
        return '<div>Your rendered block</div>'
    }
}
```

It's perfectly fine to just return your options like so, though if you like to re-use options in other blocks, you can also create custom options or use some of the already available options. For more, see [Options](#options).

### Block render

Then, to use the title in our block render, we do the following.

```php
class Example extends Block
{
    public string $name = 'Example';

    public static string $reference = 'your_prefix.example';

    public array $data = [
        'title' => 'A default title'
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
        {-return '<div>Your rendered block</div>';-}
        {+extract($this->data);

        return <<<HTML
            <div>{$title}</div>
        HTML;+}
    }
}
```

### Using views

Instead of writing your options and render HTML within the class itself, you may also choose to return an instance of `Jeffreyvr\Paver\View`, like so:

```php
return new View('file-path-to-your-block-render-template.php', $this->data);
```

If you are using Laravel, you can return a Blade render.

## Registering a block

To make the editor aware of your block, you need to register it by calling `registerBlock` function on the Paver instance.

```php
paver()->registerBlock(Example::class);
```

## Options

As seen in the example block, you can just write HTML with Alpine to make your block options. However, there are some default options that you can use as well.

```php
Input::make('Title', 'title');

Textarea::make('Title', 'title');

Select::make('Alignment', 'align', [
    '' => 'None',
    'left' => 'Left',
    'right' => 'Right'
])
```

You may also return an array of options, like so:

```php
function options()
{
    return [
        Input::make('Title', 'title'),
        Textarea::make('Content', 'content')
    ];
}
```

An option needs to return a string, so you can also just pass a string of HTML to be rendered.

## Styles and scripts

If your block requires some specific CSS and JS, that is not generally available already, you can include them like so in the constructor of your block:

```php
public function __construct()
{
    $this->style('splide', 'https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css', []);
    $this->script('splide', 'https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js', []);
}
```

## Nesting

Within your block template, you can define were child blocks may be added. You do this by adding the following HTML comment which will act as a placeholder.

```html
<!-- Paver::children() -->
```

You can also specifically instruct which blocks are allowed.

```html
<!-- Paver::children({"allowBlocks": ["your_block_prefix.column", "your_block_prefix.image"]}) -->
```

The above comment will render a wrapper div. To add attributes, such as css classes, you can do the following.

```html
<!-- Paver::children({"attributes": {"class": "grid grid-cols-4"}}) -->
```

If you want your child blocks to be sortable horizontally, add the following attribute.

```html
<!-- Paver::children({"attributes": {"class": "grid grid-cols-4", "data-direction": "horizontal"}}) -->
```

## Tips

- You can set an icon, by setting the `$icon` variable on the block class.
- If a block is only intended to be used as a child block, set the `childOnly` property to `true`. It will then only be shown when you are in the edit state of a block that has specifically set this as an allowed block.
- If you are using Laravel, you can also return your views with Blade by using `Blade::render()`.
