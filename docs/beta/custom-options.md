# Custom options

- [Basic example](#basic-example)
- [How binding works](#how-binding-works)
- [Loading scripts and styles](#loading-scripts-and-styles)
- [Options backed by a JavaScript library](#options-backed-by-a-javascript-library)
- [Full example: a Summernote rich text option](#full-example-a-summernote-rich-text-option)
- [Good to know](#good-to-know)

Besides the [pre made options](/docs/{{version}}/block-options), you can write your own option classes. A custom option is a class that extends `Jeffreyvr\Paver\Blocks\Options\Option` and returns a string of HTML from its `render` method.

## Basic example

```php
use Jeffreyvr\Paver\Blocks\Options\Option;

class ColorPicker extends Option
{
    public function __construct(
        public string $label,
        public string $name
    ) {}

    public function render(): string
    {
        return <<<HTML
            <div class="paver__option">
                <label>{$this->label}</label>
                <input type="color" x-model="{$this->name}">
            </div>
        HTML;
    }
}
```

Use it in a block like any other option:

```php
function options()
{
    return [
        ColorPicker::make('Background', 'background'),
    ];
}
```

## How binding works

The options sidebar is Alpine powered. Paver exposes the block's data as a reactive scope, so binding an option value is a matter of using `x-model="your_option_name"`.

Two things to keep in mind:

- The option name must exist as a key in the block's `$data` array. Without it, there is no reactive property to bind to and changes will not be picked up.
- Inside JavaScript that you embed in the option (for example an `x-data` component initializing a library), you can read and write the value by its bare name (`content = newValue`), because Alpine evaluates the expression within the block's data scope.

## Loading scripts and styles

If your option depends on a third party library, declare the assets in the option itself using `script` and `style`. Paver automatically outputs them on the page that renders the editor, deduplicated by handle and ordered so that dependencies load first.

```php
public function __construct(public string $label, public string $name)
{
    $this->script('jquery', 'https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js');
    $this->script('summernote', 'https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js', ['jquery']);
    $this->style('summernote', 'https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css');
}
```

The third argument of `script` is an array of handles this script depends on. If multiple blocks use the same option, each asset is only loaded once.

## Options backed by a JavaScript library

When your option is a wrapper around a library (a rich text editor, a date picker, a map), you need a moment to initialize it and a way to read and write the option value. Give the option a `type` and render your HTML through `container`:

```php
class RichText extends Option
{
    public string $type = 'richtext';

    public function __construct(
        public string $label,
        public string $name
    ) {}

    public function render(): string
    {
        return $this->container('<textarea></textarea>');
    }
}
```

Every time an option like this is rendered into the sidebar, Paver dispatches a `paver:option-init` event on its element. The event bubbles, so a single listener on the document handles all of them:

```js
document.addEventListener('paver:option-init', (event) => {
    const { el, type, name, value, setValue } = event.detail;

    if (type !== 'richtext') return;

    // el       the option element
    // name     the option name, e.g. 'content'
    // value    the current value of the option
    // setValue write a new value back to the block

    somePicker(el.querySelector('textarea'), {
        initialValue: value,
        onChange: (newValue) => setValue(newValue),
    });
});
```

Calling `setValue` updates the block and re-renders the preview, exactly as typing in a built in option would. You do not need to write any Alpine code.

Ship the listener as a file next to your option and enqueue it like any other asset:

```php
$this->script('my-richtext', '/js/my-richtext.js', ['summernote']);
```

## Full example: a Summernote rich text option

The option itself only declares its assets and renders a container:

```php
use Jeffreyvr\Paver\Blocks\Options\Option;

class RichText extends Option
{
    public string $type = 'richtext';

    public function __construct(
        public string $label,
        public string $name
    ) {
        $this->script('jquery', 'https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js');
        $this->script('summernote', 'https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js', ['jquery']);
        $this->script('richtext-option', '/js/richtext-option.js', ['summernote']);
        $this->style('summernote', 'https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css');
    }

    public function render(): string
    {
        return $this->container('<textarea></textarea>');
    }
}
```

And `richtext-option.js` initializes it:

```js
document.addEventListener('paver:option-init', (event) => {
    const { el, type, value, setValue } = event.detail;

    if (type !== 'richtext') return;

    const textarea = el.querySelector('textarea');

    jQuery(textarea).summernote({
        height: 300,
        dialogsInBody: true,
        callbacks: {
            onChange: (contents) => setValue(contents),
        },
    });

    jQuery(textarea).summernote('code', value || '');
});
```

And a block using it:

```php
use Jeffreyvr\Paver\Blocks\Block;

class RichTextBlock extends Block
{
    public string $name = 'Rich text';

    public static string $reference = 'your_prefix.richtext';

    public array $data = [
        'content' => '',
    ];

    public function options()
    {
        return [
            RichText::make('Content', 'content', ['height' => 400]),
        ];
    }

    public function render()
    {
        $content = $this->data['content'] ?? '';

        if (trim($content) === '' && $this->isInEditor()) {
            $content = '<p style="color: #9ca3af; padding: 15px;">Empty rich text block — click to edit…</p>';
        }

        return '<div>'.$content.'</div>';
    }
}
```

## Good to know

- **Render a placeholder for empty blocks.** A block that renders empty HTML has no height in the editor, which makes it impossible to hover or click. Use `$this->isInEditor()` to render a placeholder in that case, like the example above.
- **Dialogs and overlays.** The options sidebar creates a stacking context. Libraries that render modals inside your option (like Summernote's image dialog) may end up behind their own backdrop. Most libraries have an option to append dialogs to `<body>` instead — for Summernote that is `dialogsInBody: true`.
- **Conditions work on custom options too.** Since your class extends `Option`, you get `->condition('some_value === true')` for free to conditionally show the option.
