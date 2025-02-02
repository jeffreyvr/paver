<?php

namespace Jeffreyvr\Paver\Blocks;

use Jeffreyvr\Paver\View;

class Renderer
{
    protected Block $block;

    protected string $context;

    public function __construct(Block $block, string $context = 'front-end')
    {
        $this->block = $block;
        $this->context = $context;
    }

    protected function isEditorContext(): bool
    {
        return $this->context === 'editor';
    }

    public function renderToolbar(): string
    {
        if (! $this->isEditorContext()) {
            return '';
        }

        return new View(paver()->viewPath().'/block-toolbar.php', [
            'block' => $this->block,
        ]);
    }

    public function renderChildren(): string
    {
        $output = '';

        if (empty($this->block->children)) {
            return $output;
        }
        // ray($this->block->children)->purple();

        $originalBlock = $this->block;

        foreach ($this->block->children as $childBlock) {
            $childBlockInstance = BlockFactory::createById($childBlock['block'], $childBlock['data'] ?? [], $childBlock['children'] ?? []);

            $childRenderer = new Renderer($childBlockInstance, $this->context);
            $output .= $childRenderer->render();
        }

        $this->block = $originalBlock;

        return $output;
    }

    public function replacePaverComments(string $html, string $innerHtml): string
    {
        $pattern = '/<!--\s*Paver::children\((\{.*?\})?\)\s*-->/';

        if (preg_match_all($pattern, $html, $matches)) {
            foreach ($matches[1] as $jsonString) {
                $dataArray = json_decode($jsonString, true);
                $attributes = $dataArray['attributes'] ?? [];

                $attributes['class'] = 'paver__sortable '.($attributes['class'] ?? '');

                if (! empty($dataArray['allowBlocks'])) {
                    $attributes['data-allow-blocks'] = json_encode($dataArray['allowBlocks']);
                }

                $attributeString = implode(' ', array_map(function ($key) use ($attributes) {
                    return $key.'="'.htmlspecialchars($attributes[$key]).'"';
                }, array_keys($attributes)));

                $replacementContent = '<div '.$attributeString.'>'.$innerHtml.'</div>';

                $html = preg_replace('/<!--\s*Paver::children\('.preg_quote($jsonString, '/').'\)\s*-->/', $replacementContent, $html, 1);
            }
        }

        return $html;
    }

    public function blockClassName()
    {
        return strtolower(str_replace('.', '-', $this->block::$reference));
    }

    public function render(): string
    {
        $attributeString = $this->isEditorContext()
            ? 'class="'.$this->blockClassName().' paver__block paver__sortable-item parent" data-id="'.$this->block->getId().'" data-block="'.htmlspecialchars($this->block->toJson(), ENT_QUOTES, 'UTF-8').'"'
            : 'class="'.$this->blockClassName().'"';

        if($this->isEditorContext()) {
            $this->block->isInEditor = true;
        }

        $content = '<div '.$attributeString.'>';
        $content .= $this->renderToolbar();
        $content .= (string) $this->block->render();
        $content .= '</div>';

        return $this->replacePaverComments($content, $this->renderChildren());
    }

    public static function block(Block $block, string $context = 'front-end'): string
    {
        return (new static($block, $context))->render();
    }

    public static function blocks($blocks, $context = 'front-end'): string
    {
        if (is_string($blocks)) {
            $blocks = json_decode($blocks, true);
        }

        $content = '';

        foreach ($blocks as $block) {
            $_block = BlockFactory::createById($block['block'], $block['data'] ?? [], $block['children'] ?? []);

            $content .= $_block->renderer($context)->render();
        }

        return $content;
    }
}
