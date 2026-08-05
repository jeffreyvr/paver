<?php

namespace Jeffreyvr\Paver\Blocks\Options;

class Range extends Option
{
    public function __construct(
        public string $label,
        public string $name,
        public int|float $min = 0,
        public int|float $max = 100,
        public int|float|null $step = null,
        public string $suffix = '',
        public array $attributes = []
    ) {
        //
    }

    public function render(): string
    {
        $bounds = ['min' => $this->min, 'max' => $this->max];

        if ($this->step !== null) {
            $bounds['step'] = $this->step;
        }

        $attributeString = $this->makeAttributeString(array_merge($bounds, ['type' => 'range']));
        $suffix = htmlspecialchars($this->suffix, ENT_QUOTES, 'UTF-8');

        // A slider with no readout is guesswork, so the value sits in the caption.
        return <<<HTML
            <div class="paver__option paver__option-range">
                <label>
                    <span>{$this->label}</span>
                    <output x-text="`\${{$this->name}}{$suffix}`"></output>
                </label>
                <input x-model="{$this->name}" {$attributeString}>
            </div>
        HTML;
    }
}
