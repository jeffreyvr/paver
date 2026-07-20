# Storing

- [Form](#form)
- [Save button](#save-button)

## Form

The editor keeps a json version on the content in an hidden input named `paver_editor_content`. That means you can wrap the editor in a form, and when the form is submitted, the content of the editor will be available in the request data.

## Save button

The editor has a built in save button. By default, this button is not visible. To enable it, you can set `showSaveButton` in the config array when you render the editor.

```php
paver()->render($blocks, [
    'config' => [
        'showSaveButton' => true
    ]
]);
```

The button, when clicked, dispatches the `paver-save` event which includes the content. This will allow you to do something with this data, like storing it.
