<?php

namespace Jeffreyvr\Paver\Blocks\Options;

abstract class Option
{
    public array $attributes = [];

    public string $wrapper = '_INJECT_';

    public static function make(...$args)
    {
        return new static(...$args);
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
