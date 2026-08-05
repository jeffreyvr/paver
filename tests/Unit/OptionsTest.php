<?php

use Jeffreyvr\Paver\Blocks\Block;
use Jeffreyvr\Paver\Blocks\Options\Checkbox;
use Jeffreyvr\Paver\Blocks\Options\Color;
use Jeffreyvr\Paver\Blocks\Options\Input;
use Jeffreyvr\Paver\Blocks\Options\Link;
use Jeffreyvr\Paver\Blocks\Options\Number;
use Jeffreyvr\Paver\Blocks\Options\Option;
use Jeffreyvr\Paver\Blocks\Options\RadioGroup;
use Jeffreyvr\Paver\Blocks\Options\Range;
use Jeffreyvr\Paver\Blocks\Options\Select;
use Jeffreyvr\Paver\Blocks\Options\Textarea;

describe('Input Option', function () {
    it('creates via make method', function () {
        $input = Input::make('Label', 'fieldName');

        expect($input)->toBeInstanceOf(Input::class);
    });

    it('renders input element', function () {
        $input = new Input('Username', 'username');
        $html = $input->render();

        expect($html)->toContain('<input');
        expect($html)->toContain('x-model="username"');
        expect($html)->toContain('Username');
    });

    it('includes custom attributes', function () {
        $input = new Input('Email', 'email', ['type' => 'email', 'placeholder' => 'Enter email']);
        $html = $input->render();

        expect($html)->toContain('type="email"');
        expect($html)->toContain('placeholder="Enter email"');
    });

    it('can have condition', function () {
        $input = Input::make('Name', 'name')->condition('showName === true');
        $html = (string) $input;

        expect($html)->toContain('x-show="showName === true"');
    });
});

describe('Textarea Option', function () {
    it('renders textarea element', function () {
        $textarea = new Textarea('Description', 'description');
        $html = $textarea->render();

        expect($html)->toContain('<textarea');
        expect($html)->toContain('x-model="description"');
        expect($html)->toContain('Description');
    });

    it('includes custom attributes', function () {
        $textarea = new Textarea('Bio', 'bio', ['rows' => '5']);
        $html = $textarea->render();

        expect($html)->toContain('rows="5"');
    });
});

describe('Select Option', function () {
    it('renders select element', function () {
        $select = new Select('Color', 'color', [
            'red' => 'Red',
            'blue' => 'Blue',
        ]);
        $html = $select->render();

        expect($html)->toContain('<select');
        expect($html)->toContain('x-model="color"');
        expect($html)->toContain('Color');
    });

    it('renders options', function () {
        $select = new Select('Size', 'size', [
            'sm' => 'Small',
            'lg' => 'Large',
        ]);
        $html = $select->render();

        expect($html)->toContain('<option value="sm">Small</option>');
        expect($html)->toContain('<option value="lg">Large</option>');
    });

    it('includes custom attributes', function () {
        $select = new Select('Type', 'type', ['a' => 'A'], ['class' => 'form-select']);
        $html = $select->render();

        expect($html)->toContain('class="form-select"');
    });
});

describe('Checkbox Option', function () {
    it('creates via make method', function () {
        expect(Checkbox::make('Featured', 'featured'))->toBeInstanceOf(Checkbox::class);
    });

    it('renders an inline checkbox with a false default', function () {
        $checkbox = new Checkbox('Featured', 'featured');
        $html = $checkbox->render();

        expect($checkbox->default)->toBeFalse();
        expect($html)->toContain('type="checkbox"');
        expect($html)->toContain('x-model="featured"');
        expect($html)->toContain('paver__inline-label');
        expect($html)->toContain('Featured');
    });
});

describe('Number Option', function () {
    it('renders a number input with bounds', function () {
        $number = Number::make('Quantity', 'quantity', min: 1, max: 12, step: 1);
        $html = $number->render();

        expect($html)->toContain('type="number"');
        expect($html)->toContain('x-model="quantity"');
        expect($html)->toContain('min="1"');
        expect($html)->toContain('max="12"');
        expect($html)->toContain('step="1"');
    });

    it('omits null bounds', function () {
        $html = Number::make('Count', 'count')->render();

        expect($html)->toContain('type="number"');
        expect($html)->not->toContain('min=');
        expect($html)->not->toContain('max=');
        expect($html)->not->toContain('step=');
    });
});

describe('Range Option', function () {
    it('renders a slider with a live output and suffix', function () {
        $range = Range::make('Scale', 'scale', min: 50, max: 150, step: 5, suffix: '%');
        $html = $range->render();

        expect($html)->toContain('type="range"');
        expect($html)->toContain('x-model="scale"');
        expect($html)->toContain('min="50"');
        expect($html)->toContain('max="150"');
        expect($html)->toContain('step="5"');
        expect($html)->toContain('paver__option-range');
        expect($html)->toContain('<output');
        expect($html)->toContain('x-text="`${scale}%`"');
    });
});

describe('Color Option', function () {
    it('renders a swatch and hex field bound to the same model', function () {
        $color = Color::make('Accent', 'accent');
        $html = $color->render();

        expect($color->default)->toBe('#000000');
        expect($html)->toContain('type="color"');
        expect($html)->toContain('paver__color-field');
        expect(substr_count($html, 'x-model="accent"'))->toBe(2);
    });
});

describe('Link Option', function () {
    it('renders url, text, and new-tab fields on a structured value', function () {
        $link = Link::make('CTA', 'cta');
        $html = $link->render();

        expect($link->default)->toBe(['url' => '', 'text' => '', 'newTab' => false]);
        expect($html)->toContain('x-model="cta.url"');
        expect($html)->toContain('x-model="cta.text"');
        expect($html)->toContain('x-model="cta.newTab"');
        expect($html)->toContain('type="url"');
        expect($html)->toContain('Open in a new tab');
    });
});

describe('RadioGroup Option', function () {
    it('renders a segmented group of radios', function () {
        $group = RadioGroup::make('Layout', 'layout', [
            'stacked' => 'Stacked',
            'inline' => 'Inline',
        ]);
        $html = $group->render();

        expect($html)->toContain('paver__segmented');
        expect($html)->toContain('type="radio"');
        expect($html)->toContain('x-model="layout"');
        expect($html)->toContain('value="stacked"');
        expect($html)->toContain('value="inline"');
        expect($html)->toContain('Stacked');
        expect($html)->toContain('Inline');
    });

    it('escapes option values and labels', function () {
        $html = RadioGroup::make('Pick', 'pick', [
            'a"b' => '<Bold>',
        ])->render();

        expect($html)->toContain('value="a&quot;b"');
        expect($html)->toContain('&lt;Bold&gt;');
        expect($html)->not->toContain('<Bold>');
    });
});

describe('Option Assets', function () {
    it('registers scripts and styles on an option', function () {
        $input = Input::make('Test', 'test')
            ->script('summernote', 'https://example.com/summernote.js', ['jquery'])
            ->style('summernote', 'https://example.com/summernote.css');

        expect($input->scripts)->toHaveCount(1);
        expect($input->scripts[0]['handle'])->toBe('summernote');
        expect($input->scripts[0]['deps'])->toBe(['jquery']);
        expect($input->styles)->toHaveCount(1);
    });

    it('collects option assets from registered blocks', function () {
        $block = new class extends Block
        {
            public static string $reference = 'test.assets';

            public function options()
            {
                return [
                    Input::make('Content', 'content')
                        ->script('jquery', 'https://example.com/jquery.js')
                        ->script('summernote', 'https://example.com/summernote.js', ['jquery'])
                        ->style('summernote', 'https://example.com/summernote.css'),
                ];
            }
        };

        paver()->blocks = [];
        paver()->registerBlock(get_class($block));

        $scripts = paver()->optionAssets('scripts');
        $styles = paver()->optionAssets('styles');

        expect(array_column($scripts, 'handle'))->toBe(['jquery', 'summernote']);
        expect(array_column($styles, 'handle'))->toBe(['summernote']);
    });

    it('dedupes assets by handle and orders dependencies first', function () {
        $blockA = new class extends Block
        {
            public static string $reference = 'test.assets-a';

            public function options()
            {
                return [
                    Input::make('A', 'a')
                        ->script('summernote', 'https://example.com/summernote.js', ['jquery'])
                        ->script('jquery', 'https://example.com/jquery.js'),
                ];
            }
        };

        $blockB = new class extends Block
        {
            public static string $reference = 'test.assets-b';

            public function options()
            {
                return [
                    Input::make('B', 'b')
                        ->script('summernote', 'https://example.com/other-summernote.js'),
                ];
            }
        };

        paver()->blocks = [];
        paver()->registerBlock(get_class($blockA));
        paver()->registerBlock(get_class($blockB));

        $scripts = paver()->optionAssets('scripts');

        expect($scripts)->toHaveCount(2);
        expect(array_column($scripts, 'handle'))->toBe(['jquery', 'summernote']);
        expect($scripts[1]['src'])->toBe('https://example.com/summernote.js');
    });

    it('outputs option assets in the editor render', function () {
        $block = new class extends Block
        {
            public static string $reference = 'test.assets-render';

            public function options()
            {
                return [
                    Input::make('Content', 'content')
                        ->script('summernote', 'https://example.com/summernote.js')
                        ->style('summernote', 'https://example.com/summernote.css'),
                ];
            }
        };

        paver()->blocks = [];
        paver()->registerBlock(get_class($block));

        $html = (string) paver()->render();

        expect($html)->toContain('<script src="https://example.com/summernote.js"></script>');
        expect($html)->toContain('<link rel="stylesheet" href="https://example.com/summernote.css">');
    });
});

describe('Option Container', function () {
    it('renders hooks for the lifecycle event', function () {
        $option = new class('Content', 'content') extends Option
        {
            public string $type = 'richtext';

            public function __construct(public string $label, public string $name) {}

            public function render(): string
            {
                return $this->container('<textarea></textarea>');
            }
        };

        $html = $option->render();

        expect($html)->toContain('data-paver-option="richtext"');
        expect($html)->toContain('data-paver-option-name="content"');
        expect($html)->toContain('class="paver__option"');
        expect($html)->toContain('<label>Content</label>');
        expect($html)->toContain('<textarea></textarea>');
    });

    it('falls back to the class name when no type is set', function () {
        $option = new class('Label', 'field') extends Option
        {
            public function __construct(public string $label, public string $name) {}

            public function render(): string
            {
                return $this->container('<input>');
            }
        };

        expect($option->render())->toContain('data-paver-option="'.htmlspecialchars(get_class($option), ENT_QUOTES, 'UTF-8').'"');
    });

    it('escapes label and accepts extra attributes', function () {
        $option = new class('A "quoted" <label>', 'field') extends Option
        {
            public string $type = 'test';

            public function __construct(public string $label, public string $name) {}

            public function render(): string
            {
                return $this->container('<input>', ['data-extra' => 'yes']);
            }
        };

        $html = $option->render();

        expect($html)->toContain('data-extra="yes"');
        expect($html)->toContain('&lt;label&gt;');
        expect($html)->not->toContain('<label>A "quoted"');
    });
});

describe('Option Wrapper', function () {
    it('has default wrapper', function () {
        $input = new Input('Test', 'test');

        expect($input->wrapper)->toBe('_INJECT_');
    });

    it('wraps with condition', function () {
        $input = Input::make('Test', 'test')->condition('visible');

        expect($input->wrapper)->toContain('x-show="visible"');
    });

    it('casts to string with wrapper', function () {
        $input = Input::make('Test', 'test')->condition('show');
        $html = (string) $input;

        expect($html)->toContain('<div x-show="show">');
        expect($html)->toContain('<input');
    });
});
