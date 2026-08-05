<?php

namespace Jeffreyvr\Paver\Blocks\Options;

class Link extends Option
{
    /** One structured value, so a block reads $data['name']['url']. */
    public mixed $default = ['url' => '', 'text' => '', 'newTab' => false];

    public function __construct(
        public string $label,
        public string $name,
        public array $attributes = []
    ) {
        //
    }

    public function render(): string
    {
        $attributeString = $this->makeAttributeString([]);

        return <<<HTML
            <div class="paver__option">
                <label>{$this->label}</label>
                <input type="url" x-model="{$this->name}.url" placeholder="https://" {$attributeString}>
                <input type="text" x-model="{$this->name}.text" placeholder="Link text">
                <label class="paver__inline-label">
                    <input type="checkbox" x-model="{$this->name}.newTab">
                    <span>Open in a new tab</span>
                </label>
            </div>
        HTML;
    }
}
