# View templates

As you may have noticed in most documentation examples, the block `render` and `options` method often return inline HTML from within the Block class.

Sometimes it might be desirable to move this to a dedicated template file. In this case, you can use the `View` class.

```php
public function render()
{
    return new View('file-path-to-your-block-render-template.php', $this->data);
}

public function options()
{
    return new View('file-path-to-your-block-options-template.php', $this->data);
}
```

If you use Paver with Laravel, you can instead choose to return Blade templates and utilize the Blade templating engine.

```php
public function render()
{
    return view('your-block-render-blade-template', $this->data);
}
```
