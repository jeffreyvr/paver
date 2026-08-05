<?php

namespace Jeffreyvr\Paver\Blocks\Options;

class Checkbox extends Option
{
    public mixed $default = false;

    public function __construct(public string $label, public string $name, public array $attributes = [])
    {
        //
    }

    public function render(): string
    {
        $attributeString = $this->makeAttributeString(['type' => 'checkbox']);

        return <<<HTML
            <div class="paver__option">
                <label class="paver__inline-label">
                    <input x-model="{$this->name}" {$attributeString}>
                    <span>{$this->label}</span>
                </label>
            </div>
        HTML;
    }
}
