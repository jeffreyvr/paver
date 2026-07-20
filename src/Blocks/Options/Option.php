<?php

namespace Jeffreyvr\Paver\Blocks\Options;

abstract class Option
{
    public array $attributes = [];

    public array $scripts = [];

    public array $styles = [];

    public string $wrapper = '_INJECT_';

    /**
     * When set, a `paver:option-init` event is dispatched for this option
     * once it has been rendered into the sidebar, carrying this type so
     * listeners can recognise their own options.
     */
    public string $type = '';

    public static function make(...$args)
    {
        return new static(...$args);
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

    public function condition($condition)
    {
        $this->wrapper = '<div x-show="'.($condition).'">_INJECT_</div>';

        return $this;
    }

    public function render()
    {
        return '';
    }

    /**
     * Render the standard option markup around your own HTML, including the
     * hooks the editor needs to dispatch `paver:option-init` for it.
     */
    public function container(string $html, array $attributes = []): string
    {
        $attributes = array_merge([
            'class' => 'paver__option',
            'data-paver-option' => $this->type ?: static::class,
            'data-paver-option-name' => $this->name ?? '',
        ], $attributes);

        $attributeString = '';

        foreach ($attributes as $key => $value) {
            $attributeString .= ' '.$key.'="'.htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8').'"';
        }

        $label = isset($this->label)
            ? '<label>'.htmlspecialchars((string) $this->label, ENT_QUOTES, 'UTF-8').'</label>'
            : '';

        return '<div'.$attributeString.'>'.$label.$html.'</div>';
    }

    public function makeAttributeString($attributes)
    {
        $attributeString = '';

        foreach (array_merge($this->attributes, $attributes) as $key => $value) {
            $attributeString .= " {$key}=\"{$value}\"";
        }

        return $attributeString;
    }

    public function __toString()
    {
        return str_replace('_INJECT_', $this->render(), $this->wrapper);
    }
}
