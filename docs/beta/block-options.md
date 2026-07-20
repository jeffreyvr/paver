# Block options

- [Basic example](#basic-example)
- [Available options](#existing-options)
    - [Input](#input)
    - [Textarea](#textarea)
    - [Select](#select)
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
