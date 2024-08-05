<?php

namespace Jeffreyvr\Paver\Blocks\Options;

class Input extends Option
{
    public function __construct(public string $label, public string $name, public array $attributes = [])
    {
        //
    }

    public function render(): string
    {
        $attributeString = $this->makeAttributeString([
            'type' => 'text',
        ]);

        return <<<HTML
            <div class="option">
                <label>{$this->label}</label>
                <input x-model="{$this->name}" {$attributeString}>
            </div>
        HTML;
    }
}
