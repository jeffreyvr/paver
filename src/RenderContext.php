<?php

namespace Jeffreyvr\Paver;

use Jeffreyvr\Paver\Blocks\Block;

class RenderContext
{
    public ?Block $parent = null;

    public ?int $index = null;

    public function __construct(
        public string $mode = 'front-end',
        protected array $values = []
    ) {}

    public static function make(RenderContext|string $context): static
    {
        return $context instanceof RenderContext ? $context : new static($context);
    }

    public function isEditor(): bool
    {
        return $this->mode === 'editor';
    }

    public function get(string $key, $default = null)
    {
        return $this->values[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->values);
    }

    public function values(): array
    {
        return $this->values;
    }

    public function forChild(Block $parent, int $index): static
    {
        $context = clone $this;
        $context->parent = $parent;
        $context->index = $index;

        return $context;
    }
}
