# WordPress options

There are several block options available for you to use.

- [Image](#image)
- [RichText](#image)
- [Colorpicker](#colorpicker)

## Image

This option will provides a simple image select, using the WordPress medialibrary.

```php
Image::make('Label', 'option_name');
```

By default, this option will return the selected image URL, but if you instead want it to return the id, you can use the `storeAsId` method.

```php
Image::make('Label', 'option_name')
    ->storeAsId();
```

The option accepts a third config parameter, through which you can modify text labels, but also allows you to change the `wp.media` configuration. The default is seen below.

```php
Image::make('Label', 'option_name', [
    'button' => 'Upload or select',
    'remove' => 'Remove',
    'replace' => 'Replace',
    'media' => [
        'title' => 'Select or upload media',
        'button' => [
            'text' => 'Use this media'
        ],
        'library' => [
            'type' => 'image'
        ],
        'multiple' => false
    ],
]);
```

In your block, you might have to add the following to the `beforeEditorRender` function:

```php
public function beforeEditorRender()
{
    Image::scripts();
}
```

## RichText

This option will provide you with a rich text editor, using WordPress `wp.editor` which is a customized version of TinyMCE.

```php
RichText::make('Label', 'option_name');
```

In your block make sure to add the following to the `beforeEditorRender` function:

```php
public function beforeEditorRender()
{
    RichText::scripts();
}
```

The third optional parameter, allows you to customize the config of the editor. This extensive example will add, for example, inline colors and allow image insertion:

```php
RichText::make('Label', 'option_name', [
    'quicktags' => true,
    'mediaButtons' => true,
    'tinymce' => [
        'plugins' => 'lists,link,wordpress,wpautoresize,wpeditimage,wpgallery,wplink,wptextpattern,wpview,media,media,colorpicker,textcolor',
        'toolbar1' => 'bold italic bullist alignleft aligncenter alignright link wp_adv',
        'toolbar2' => 'formatselect,numlist,forecolor,unlink,strikethrough,hr,underline,alignjustify,wp_help,removeformat,charmap,outdent,indent,undo,redo',
        'textcolor_map' => [
            '000000', 'Black',
            'ffffff', 'White',
            'ff0000', 'Red',
            '00ff00', 'Green',
            '0000ff', 'Blue'
        ],
    ]
]);
```

## Colorpicker

This option will provide you with the WordPress color picker.

```php
Colorpicker::make('Label', 'option_name');
```

You may pass a pre defined palette like so:

```php
Colorpicker::make('Color', 'color', ['#dc2626', '#ffffff', '#0ea5e9']),
```

In your block make sure to add the following to the `beforeEditorRender` function:

```php
public function beforeEditorRender()
{
    Colorpicker::scripts();
}
```
