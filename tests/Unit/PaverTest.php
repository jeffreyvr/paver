<?php

use Jeffreyvr\Paver\Blocks\Block;
use Jeffreyvr\Paver\Blocks\BlockFactory;
use Jeffreyvr\Paver\Blocks\Example;
use Jeffreyvr\Paver\Paver;

beforeEach(function () {
    // Reset paver instance blocks
    paver()->blocks = [];
});

describe('Paver Instance', function () {
    it('returns singleton instance', function () {
        $instance1 = Paver::instance();
        $instance2 = Paver::instance();
        
        expect($instance1)->toBe($instance2);
    });

    it('has default configuration', function () {
        $paver = paver();
        
        expect($paver->alpine)->toBeTrue();
        expect($paver->debug)->toBeFalse();
        expect($paver->locale)->toBe('en');
    });

    it('can set debug mode', function () {
        paver()->debug(true);
        
        expect(paver()->debug)->toBeTrue();
        
        paver()->debug(false);
    });

    it('can toggle alpine', function () {
        paver()->alpine(false);
        
        expect(paver()->alpine)->toBeFalse();
        
        paver()->alpine(true);
    });
});

describe('Block Registration', function () {
    it('registers blocks with visibility', function () {
        paver()->registerBlock(Example::class);
        
        expect(paver()->blocks)->toHaveKey('paver.example');
        expect(paver()->blocks['paver.example']['class'])->toBe(Example::class);
        expect(paver()->blocks['paver.example']['visible'])->toBeTrue();
    });

    it('registers hidden blocks', function () {
        paver()->registerBlock(Example::class, visible: false);
        
        expect(paver()->blocks['paver.example']['visible'])->toBeFalse();
    });

    it('returns only visible blocks in blocks() method', function () {
        // Create a second test block class inline
        $hiddenBlock = new class extends Block {
            public static string $reference = 'paver.hidden';
            public string $name = 'Hidden Block';
        };

        paver()->registerBlock(Example::class, visible: true);
        paver()->registerBlock($hiddenBlock::class, visible: false);
        
        $visibleBlocks = paver()->blocks();
        
        expect($visibleBlocks)->toHaveCount(1);
        expect($visibleBlocks[0]['reference'])->toBe('paver.example');
    });

    it('getBlock returns class for all registered blocks including hidden', function () {
        paver()->registerBlock(Example::class, visible: false);
        
        $class = paver()->getBlock('paver.example');
        
        expect($class)->toBe(Example::class);
    });

    it('throws exception for unregistered block', function () {
        paver()->getBlock('non.existent');
    })->throws(Exception::class, 'Block non.existent not found.');
});

describe('Block Factory', function () {
    beforeEach(function () {
        paver()->registerBlock(Example::class);
    });

    it('creates block with data', function () {
        $block = BlockFactory::create(Example::class, ['name' => 'Test User']);
        
        expect($block->data['name'])->toBe('Test User');
    });

    it('creates block with children', function () {
        $children = [
            ['block' => 'paver.example', 'data' => []],
        ];
        
        $block = BlockFactory::create(Example::class, [], $children);
        
        expect($block->children)->toBe($children);
    });

    it('creates block by id', function () {
        $block = BlockFactory::createById('paver.example');
        
        expect($block)->toBeInstanceOf(Example::class);
    });
});
