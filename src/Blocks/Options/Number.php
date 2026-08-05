<?php

namespace Jeffreyvr\Paver\Blocks\Options;

class Number extends Option
{
    public function __construct(
        public string $label,
        public string $name,
        public int|float|null $min = null,
        public int|float|null $max = null,
        public int|float|null $step = null,
        public array $attributes = []
    ) {
        //
    }

    public function render(): string
    {
        $bounds = array_filter([
            'min' => $this->min,
            'max' => $this->max,
            'step' => $this->step,
        ], fn ($value) => $value !== null);

        $attributeString = $this->makeAttributeString(array_merge($bounds, ['type' => 'number']));

        return <<<HTML
            <div class="paver__option">
                <label>{$this->label}</label>
                <input x-model="{$this->name}" {$attributeString}>
            </div>
        HTML;
    }
}
