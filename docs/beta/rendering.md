# Rendering

- [Rendering the editor](#rendering-the-editor)
- [Rendering the content](#rendering-the-content)

## Rendering the editor

To render the editor, you simply call:

```php
paver()->render();
```

The render method accepts two parameters; `blocks` and `config`. The `blocks` is the initial content of the editor. You may either pass an array or a JSON string.

The `config` parameter allows you to define several things, such as to show or hide specific buttons.

```php
paver()->render($blocks, 'config' => [
    'showSaveButton' => false,
    'showExpandButton' => true,
    'showViewButton' => true,
    'blockInserterLimit' => 6,
]);
```

## Rendering the content

Once you have created something in the editor, and stored it, you probably want to display it somewhere.

You can use the `Renderer` class for this, by passing the JSON string or an array version:

```php
Renderer::blocks($content);
```
