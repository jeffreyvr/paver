<?php

namespace Jeffreyvr\Paver\Blocks\Options;

class RadioGroup extends Option
{
    public function __construct(
        public string $label,
        public string $name,
        public array $options,
        public array $attributes = []
    ) {
        //
    }

    public function render(): string
    {
        $attributeString = $this->makeAttributeString(['type' => 'radio', 'name' => $this->name]);

        $segments = '';

        foreach ($this->options as $value => $label) {
            $value = htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
            $label = htmlspecialchars((string) $label, ENT_QUOTES, 'UTF-8');

            $segments .= <<<HTML
                <label>
                    <input x-model="{$this->name}" value="{$value}" {$attributeString}>
                    <span>{$label}</span>
                </label>
            HTML;
        }

        return <<<HTML
            <div class="paver__option">
                <label>{$this->label}</label>
                <div class="paver__segmented">{$segments}</div>
            </div>
        HTML;
    }
}
