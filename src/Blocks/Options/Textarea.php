<?php

namespace Jeffreyvr\Paver\Blocks\Options;

class Textarea extends Option
{
    public function __construct(public string $label, public string $name, public array $attributes = [])
    {
        //
    }

    public function render(): string
    {
        $attributes = array_merge([
            'type' => 'text',
        ], $this->attributes);

        $attributeString = '';

        foreach ($attributes as $key => $value) {
            $attributeString .= " {$key}=\"{$value}\"";
        }

        return <<<HTML
            <div class="option">
                <label>{$this->label}</label>
                <textarea x-model="{$this->name}" {$attributeString}></textarea>
            </div>
        HTML;
    }
}
