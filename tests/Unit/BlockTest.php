<?php

use Jeffreyvr\Paver\Blocks\Block;
use Jeffreyvr\Paver\Blocks\BlockFactory;
use Jeffreyvr\Paver\Blocks\Example;
use Jeffreyvr\Paver\Blocks\Options\Input;
use Jeffreyvr\Paver\Blocks\Options\Textarea;

beforeEach(function () {
    paver()->blocks = [];
    paver()->registerBlock(Example::class);
});

describe('Example Block', function () {
    it('has correct reference', function () {
        expect(Example::$reference)->toBe('paver.example');
    });

    it('has correct name', function () {
        $block = new Example;

        expect($block->name)->toBe('Example');
    });

    it('has default data', function () {
        $block = new Example;

        expect($block->data)->toHaveKey('name');
        expect($block->data['name'])->toBe('[your name here]');
    });

    it('renders content with data', function () {
        $block = new Example;
        $block->setData(['name' => 'John']);

        $html = $block->render();

        expect($html)->toContain('John');
        expect($html)->toContain('example block');
    });

    it('returns options array', function () {
        $block = new Example;

        $options = $block->options();

        expect($options)->toBeArray();
        expect($options)->toHaveCount(1);
    });

    it('can set data via setData', function () {
        $block = new Example;
        $result = $block->setData(['name' => 'Jane']);

        expect($result)->toBe($block); // Returns self for chaining
        expect($block->data['name'])->toBe('Jane');
    });

    it('can set children via setChildren', function () {
        $block = new Example;
        $children = [['block' => 'paver.example']];

        $result = $block->setChildren($children);

        expect($result)->toBe($block);
        expect($block->children)->toBe($children);
    });

    it('correctly reports hasChildren', function () {
        $block = new Example;

        expect($block->hasChildren())->toBeFalse();

        $block->setChildren([['block' => 'paver.example']]);

        expect($block->hasChildren())->toBeTrue();
    });

    it('generates unique id', function () {
        $block = new Example;

        $id1 = $block->getId();
        $id2 = $block->getId();

        expect($id1)->toBeString();
        expect($id2)->toBeString();
        expect($id1)->not->toBe($id2);
    });

    it('returns icon', function () {
        $block = new Example;

        expect($block->getIcon())->toContain('<svg');
    });

    it('serializes to json', function () {
        $block = new Example;

        $json = $block->toJson();
        $data = json_decode($json, true);

        expect($data)->toHaveKey('name');
        expect($data)->toHaveKey('block');
        expect($data['block'])->toBe('paver.example');
    });

    it('serializes with selected keys only', function () {
        $block = new Example;

        $json = $block->toJson(['name', 'block']);
        $data = json_decode($json, true);

        expect($data)->toHaveKeys(['name', 'block']);
        expect($data)->not->toHaveKey('children');
        expect($data)->not->toHaveKey('data');
    });
});

describe('Block Renderer', function () {
    it('renders block for front-end context', function () {
        $block = new Example;
        $block->setData(['name' => 'Test']);

        $html = $block->renderer('front-end')->render();

        expect($html)->toContain('Test');
        expect($html)->not->toContain('paver__block');
    });

    it('renders block for editor context', function () {
        $block = new Example;
        $block->setData(['name' => 'Test']);

        $html = $block->renderer('editor')->render();

        expect($html)->toContain('paver__block');
        expect($html)->toContain('data-block');
    });

    it('sets isInEditor flag in editor context', function () {
        $block = new Example;

        expect($block->isInEditor)->toBeFalse();

        $block->renderer('editor')->render();

        expect($block->isInEditor)->toBeTrue();
    });
});

describe('Option data seeding', function () {
    it('seeds missing option names into data', function () {
        $block = new class extends Block
        {
            public static string $reference = 'test.seeding';

            public function options()
            {
                return [
                    Input::make('Title', 'title'),
                    Textarea::make('Content', 'content'),
                ];
            }
        };

        $block->seedDataFromOptions();

        expect($block->data)->toHaveKeys(['title', 'content']);
        expect($block->data['title'])->toBe('');
    });

    it('never overwrites existing data', function () {
        $block = new class extends Block
        {
            public static string $reference = 'test.seeding-existing';

            public array $data = ['title' => 'Keep me'];

            public function options()
            {
                return [Input::make('Title', 'title')];
            }
        };

        $block->seedDataFromOptions();

        expect($block->data['title'])->toBe('Keep me');
    });

    it('seeds when built through the factory', function () {
        $block = new class extends Block
        {
            public static string $reference = 'test.seeding-factory';

            public function options()
            {
                return [Input::make('Subtitle', 'subtitle')];
            }
        };

        paver()->blocks = [];
        paver()->registerBlock(get_class($block));

        $created = BlockFactory::createById('test.seeding-factory', ['other' => 'x']);

        expect($created->data)->toHaveKey('subtitle');
        expect($created->data['other'])->toBe('x');
    });

    it('tolerates blocks whose options are a string', function () {
        $block = new class extends Block
        {
            public static string $reference = 'test.seeding-string';

            public function options()
            {
                return '<div>raw html</div>';
            }
        };

        expect(fn () => $block->seedDataFromOptions())->not->toThrow(Exception::class);
    });
});
