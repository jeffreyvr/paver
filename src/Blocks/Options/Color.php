<?php

namespace Jeffreyvr\Paver\Blocks\Options;

class Color extends Option
{
    public mixed $default = '#000000';

    public function __construct(
        public string $label,
        public string $name,
        public array $attributes = []
    ) {
        //
    }

    public function render(): string
    {
        $attributeString = $this->makeAttributeString(['type' => 'color']);

        return <<<HTML
            <div class="paver__option">
                <label>{$this->label}</label>
                <div class="paver__color-field">
                    <input x-model="{$this->name}" {$attributeString}>
                    <input type="text" x-model="{$this->name}" spellcheck="false" autocapitalize="off">
                </div>
            </div>
        HTML;
    }
}
