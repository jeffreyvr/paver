<?php

namespace Jeffreyvr\Paver\Blocks\Options;

class Select extends Option
{
    public function __construct(
        public string $label,
        public string $name,
        public array $options,
        public array $attributes = [])
    {
        //
    }

    public function optionsHtml(): string
    {
        $html = '';

        foreach ($this->options as $key => $value) {
            $html .= "<option value=\"{$key}\">{$value}</option>";
        }

        return $html;
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

        $optionsHtml = $this->optionsHtml();

        return <<<HTML
            <div class="paver__option">
                <label>{$this->label}</label>
                <select x-model="{$this->name}" {$attributeString}>
                    {$optionsHtml}
                </select>
            </div>
        HTML;
    }
}
