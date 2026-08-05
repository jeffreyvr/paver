# Block options

- [Basic example](#basic-example)
- [Available options](#existing-options)
    - [Input](#input)
    - [Textarea](#textarea)
    - [Select](#select)
    - [Radio group](#radio-group)
    - [Checkbox](#checkbox)
    - [Number](#number)
    - [Range](#range)
    - [Color](#color)
    - [Link](#link)
- [Conditional options](#conditional-options)
- [Default values](#default-values)
- [Returning options](#returning-options)

## Basic example

Every block has a `options` function, which ultimately, just needs to return a string of HTML. You use Alpine's [model binding](https://alpinejs.dev/directives/model) to bind the option values, with the data in your block.

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
            <div class="paver__option">
                <label>Title</label>
                <input type="text" x-model="title">
            </div>
        HTML;
    }
}
```

## Available options

Though you're  free to write your options yourself, some pre made options have been made available for you. You can also create [custom options](/docs/{{version}}/custom-options).

### Input

```php
Input::make('Label', 'option_name');
```

### Textarea

```php
Textarea::make('Label', 'option_name');
```

### Select

```php
Select::make('Label', 'option_name', [
    '' => 'Empty option',
    'value_1' => 'Label 1',
    'value_2' => 'Label 2'
]);
```

### Radio group

Shown as a segmented control. Prefer it over `Select` for small sets of
mutually exclusive choices — every choice is visible and one click away.

```php
RadioGroup::make('Alignment', 'alignment', [
    'left' => 'Left',
    'center' => 'Center',
    'right' => 'Right',
]);
```

Beyond four or so choices, or when the labels are long, use a `Select`. The
segments share the width of the sidebar and will start truncating.

### Checkbox

```php
Checkbox::make('Label', 'option_name');
```

Binds a boolean rather than a string, and starts as `false`.

### Number

```php
Number::make('Columns', 'columns', min: 1, max: 6, step: 1);
```

`min`, `max` and `step` are all optional.

### Range

A slider with its current value shown beside the label.

```php
Range::make('Width', 'width', min: 50, max: 100, step: 5, suffix: '%');
```

### Color

A native color swatch paired with a hex field. Both edit the same value, so
typing a hex code moves the swatch and vice versa. Starts as `#000000`.

```php
Color::make('Background', 'background');
```

### Link

Writes one structured value instead of three loose options:

```php
Link::make('Call to action', 'cta');
```

```php
public function render()
{
    $cta = $this->data['cta'];

    // ['url' => '', 'text' => '', 'newTab' => false]

    if (trim($cta['url']) === '') {
        return '';
    }

    $target = $cta['newTab'] ? ' target="_blank" rel="noopener"' : '';

    return '<a href="'.htmlspecialchars($cta['url'], ENT_QUOTES, 'UTF-8').'"'.$target.'>'
        .htmlspecialchars($cta['text'], ENT_QUOTES, 'UTF-8')
        .'</a>';
}
```

## Conditional options

Any option can be shown only when some other value is set. The expression is
evaluated against the block's data.

```php
public function options()
{
    return [
        Checkbox::make('Show a button', 'showButton'),
        Link::make('Button', 'button')->condition('showButton'),
    ];
}
```

## Default values

Every option knows what its value should start as, so you only declare an
option in `$data` when you want a different starting value than the option's
own default.

```php
public array $data = [
    // Only the ones that need a non-default starting value.
    'heading' => 'A default heading',
];

public function options()
{
    return [
        Input::make('Heading', 'heading'),
        // Seeded as false, '#000000' and ['url' => '', ...] respectively.
        Checkbox::make('Featured', 'featured'),
        Color::make('Background', 'background'),
        Link::make('Call to action', 'cta'),
    ];
}
```

An option with no key in `$data` and no default of its own starts as an empty
string. Custom options set their own by declaring `public mixed $default`.

## Returning options

You can simply return a string of HTML, or an array if that feels more structured to you. The array values should still be strings of HTML.

```php
function options()
{
    $html = '';
    $html .= Input::make('Title', 'title');
    $html .= Textarea::make('Content', 'content');
    return $html;
}
```

```php
function options()
{
    return [
        Input::make('Title', 'title'),
        Textarea::make('Content', 'content'),
        // 'this simple string would be rendered too'
    ];
}
```
