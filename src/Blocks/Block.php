<?php

namespace Jeffreyvr\Paver\Blocks;

use Jeffreyvr\Paver\Blocks\Options\Option;
use Jeffreyvr\Paver\RenderContext;

abstract class Block
{
    public string $name = 'Block';

    public array $children = [];

    public array $styles = [];

    public array $scripts = [];

    public array $data = [];

    public bool $isInEditor = false;

    public ?RenderContext $context = null;

    public bool $asChildOnly = false;

    public static string $reference = 'REFERENCE_NOT_SET';

    public string $icon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 7.5A2.25 2.25 0 0 1 7.5 5.25h9a2.25 2.25 0 0 1 2.25 2.25v9a2.25 2.25 0 0 1-2.25 2.25h-9a2.25 2.25 0 0 1-2.25-2.25v-9Z" />
    </svg>';

    public function __construct()
    {
        //
    }

    public function renderer(RenderContext|string $context = 'front-end'): Renderer
    {
        return new Renderer($this, $context);
    }

    public function context(): RenderContext
    {
        return $this->context ??= new RenderContext;
    }

    public function isInEditor()
    {
        return $this->isInEditor;
    }

    public function asChildOnly()
    {
        return $this->asChildOnly;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function setChildren(array $children): self
    {
        $this->children = $children;

        return $this;
    }

    public function hasChildren(): bool
    {
        return ! empty($this->children);
    }

    public function getId()
    {
        return md5(uniqid(rand(), true));
    }

    public function beforeEditorRender()
    {
        // Do something before the editor is rendered
    }

    public function script($handle, $src, $deps = [])
    {
        $this->scripts[] = compact('handle', 'src', 'deps');

        return $this;
    }

    public function style($handle, $src, $deps = [])
    {
        $this->styles[] = compact('handle', 'src', 'deps');

        return $this;
    }

    public function render()
    {
        return '<!-- Block has no content -->';
    }

    public function options()
    {
        return 'This block has no options.';
    }

    /**
     * Make sure every named option has a key in $data. Without one there is
     * no reactive property to bind to, so the option would silently do
     * nothing. Existing values are never overwritten.
     */
    public function seedDataFromOptions(): self
    {
        $options = $this->options();

        if (! is_array($options)) {
            return $this;
        }

        foreach ($options as $option) {
            if (! $option instanceof Option || ! isset($option->name)) {
                continue;
            }

            if (! array_key_exists($option->name, $this->data)) {
                $this->data[$option->name] = '';
            }
        }

        return $this;
    }

    public function renderOptions()
    {
        $options = $this->options();

        if (is_array($options)) {
            $output = '';
            foreach ($options as $option) {
                if ($option instanceof Option) {
                    $output .= $option;
                } else {
                    $output .= $option;
                }
            }

            return $output;
        }

        return $options;
    }

    public function toJson($include = ['name', 'block', 'children', 'data']): string
    {
        $object = [
            'name' => $this->name,
            'block' => static::$reference,
            'children' => $this->children,
            'data' => $this->data,
            'icon' => $this->icon,
        ];

        $object = array_intersect_key($object, array_flip($include));

        return json_encode($object);
    }
}
