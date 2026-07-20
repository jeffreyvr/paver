<?php

use Jeffreyvr\Paver\Blocks\Block;
use Jeffreyvr\Paver\Blocks\Example;
use Jeffreyvr\Paver\Blocks\Renderer;
use Jeffreyvr\Paver\RenderContext;

class ContextAwareBlock extends Block
{
    public static string $reference = 'paver.context-aware';

    public string $name = 'Context aware';

    public function render()
    {
        return 'Hello '.$this->context()->get('name', 'nobody');
    }
}

class ContextAwareParentBlock extends Block
{
    public static string $reference = 'paver.context-aware-parent';

    public string $name = 'Context aware parent';

    public function render()
    {
        return '<!-- Paver::children() -->';
    }
}

class ChildPositionBlock extends Block
{
    public static string $reference = 'paver.child-position';

    public string $name = 'Child position';

    public function render()
    {
        return 'child '.$this->context()->index.' of '.$this->context()->parent::$reference;
    }
}

beforeEach(function () {
    paver()->blocks = [];
    paver()->resolveContextUsing(null);
    paver()->registerBlock(Example::class);
    paver()->registerBlock(ContextAwareBlock::class);
    paver()->registerBlock(ContextAwareParentBlock::class);
    paver()->registerBlock(ChildPositionBlock::class);
});

describe('RenderContext', function () {
    it('defaults to front-end mode', function () {
        $context = new RenderContext();

        expect($context->mode)->toBe('front-end');
        expect($context->isEditor())->toBeFalse();
    });

    it('can be made from a string', function () {
        $context = RenderContext::make('editor');

        expect($context->isEditor())->toBeTrue();
    });

    it('passes through an existing instance', function () {
        $context = new RenderContext('editor');

        expect(RenderContext::make($context))->toBe($context);
    });

    it('returns values with defaults', function () {
        $context = new RenderContext(values: ['brand' => 'Acme']);

        expect($context->get('brand'))->toBe('Acme');
        expect($context->get('missing', 'fallback'))->toBe('fallback');
        expect($context->has('brand'))->toBeTrue();
        expect($context->has('missing'))->toBeFalse();
    });
});

describe('Renderer with context', function () {
    it('makes values available to blocks during render', function () {
        $context = new RenderContext(values: ['name' => 'John']);

        $html = Renderer::blocks([['block' => 'paver.context-aware']], $context);

        expect($html)->toContain('Hello John');
    });

    it('still accepts a string context', function () {
        $block = new Example();

        $html = $block->renderer('editor')->render();

        expect($block->isInEditor())->toBeTrue();
        expect($html)->toContain('paver__block');
    });

    it('falls back to an empty context when rendered without one', function () {
        $block = new ContextAwareBlock();

        expect($block->render())->toBe('Hello nobody');
    });

    it('gives child blocks their parent and index', function () {
        $content = [[
            'block' => 'paver.context-aware-parent',
            'children' => [
                ['block' => 'paver.child-position'],
                ['block' => 'paver.child-position'],
            ],
        ]];

        $html = Renderer::blocks($content, new RenderContext());

        expect($html)->toContain('child 0 of paver.context-aware-parent');
        expect($html)->toContain('child 1 of paver.context-aware-parent');
    });
});

describe('Context resolving', function () {
    it('resolves an empty context without a resolver', function () {
        $context = paver()->resolveContext(['brand_id' => 1], 'editor');

        expect($context->values())->toBe([]);
        expect($context->isEditor())->toBeTrue();
    });

    it('resolves values through the registered resolver', function () {
        paver()->resolveContextUsing(fn ($payload) => ['brand' => 'Brand '.$payload['brand_id']]);

        $context = paver()->resolveContext(['brand_id' => 3]);

        expect($context->get('brand'))->toBe('Brand 3');
    });

    it('builds the editor context from the api payload', function () {
        paver()->resolveContextUsing(fn ($payload) => $payload);
        paver()->api()->setPayload(['context' => ['brand_id' => 5]]);

        $context = paver()->editorContext();

        expect($context->isEditor())->toBeTrue();
        expect($context->get('brand_id'))->toBe(5);

        paver()->api()->setPayload([]);
    });
});
