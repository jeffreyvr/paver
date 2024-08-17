<?php

namespace Jeffreyvr\Paver\Blocks\Options;

abstract class Option
{
    public array $attributes = [];

    public static function make(...$args)
    {
        return new static(...$args);
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
        return $this->render();
    }
}
