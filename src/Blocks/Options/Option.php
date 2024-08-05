<?php

namespace Jeffreyvr\Paver\Blocks\Options;

abstract class Option
{
    public array $attributes = [];

    public static function make(...$args): string
    {
        return (new static(...$args))
            ->render();
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
}
