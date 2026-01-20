<?php

use Jeffreyvr\Paver\Blocks\Block;
use Jeffreyvr\Paver\Blocks\BlockFactory;
use Jeffreyvr\Paver\Blocks\Example;
use Jeffreyvr\Paver\Blocks\Options\Option;
use Jeffreyvr\Paver\Endpoints\Resolve;

beforeEach(function () {
    // Register the Example block for testing
    paver()->blocks = [];
    paver()->registerBlock(Example::class);
});

describe('BlockFactory Security', function () {
    it('allows instantiation of registered blocks', function () {
        $block = BlockFactory::create(Example::class);
        
        expect($block)->toBeInstanceOf(Example::class);
    });

    it('throws exception for unregistered block classes', function () {
        BlockFactory::create(\stdClass::class);
    })->throws(InvalidArgumentException::class, "Block class 'stdClass' is not registered.");

    it('throws exception for arbitrary classes', function () {
        BlockFactory::create(\PDO::class);
    })->throws(InvalidArgumentException::class);

    it('correctly identifies registered blocks', function () {
        expect(BlockFactory::isRegisteredBlock(Example::class))->toBeTrue();
        expect(BlockFactory::isRegisteredBlock(\stdClass::class))->toBeFalse();
        expect(BlockFactory::isRegisteredBlock(\PDO::class))->toBeFalse();
    });

    it('creates block by reference id', function () {
        $block = BlockFactory::createById('paver.example');
        
        expect($block)->toBeInstanceOf(Example::class);
    });
});

describe('Resolve Endpoint Security', function () {
    it('blocks unregistered classes', function () {
        $resolve = new class extends Resolve {
            public function testCheckClass(string $className): bool {
                if (!class_exists($className)) {
                    return false;
                }
                $reflection = new \ReflectionClass($className);
                return $this->checkIfAllowedClass($reflection);
            }
        };

        expect($resolve->testCheckClass(Example::class))->toBeTrue();
        expect($resolve->testCheckClass(\PDO::class))->toBeFalse();
        expect($resolve->testCheckClass(\stdClass::class))->toBeFalse();
    });

    it('only allows Block and Option subclasses', function () {
        $resolve = new class extends Resolve {
            public function testCheckClass(string $className): bool {
                if (!class_exists($className)) {
                    return false;
                }
                $reflection = new \ReflectionClass($className);
                return $this->checkIfAllowedClass($reflection);
            }
        };

        // Example extends Block
        expect($resolve->testCheckClass(Example::class))->toBeTrue();
        
        // Direct Block class should not be allowed (not a subclass of itself)
        expect($resolve->testCheckClass(Block::class))->toBeFalse();
    });

    it('only allows public non-static methods', function () {
        $resolve = new class extends Resolve {
            public function testCheckMethod(string $className, string $method): bool {
                $reflection = new \ReflectionClass($className);
                return $this->checkIfAllowedMethod($reflection, $method);
            }
        };

        // Public methods should be allowed
        expect($resolve->testCheckMethod(Example::class, 'render'))->toBeTrue();
        expect($resolve->testCheckMethod(Example::class, 'options'))->toBeTrue();
        expect($resolve->testCheckMethod(Example::class, 'toJson'))->toBeTrue();

        // Non-existent methods should be blocked
        expect($resolve->testCheckMethod(Example::class, 'nonExistentMethod'))->toBeFalse();
        expect($resolve->testCheckMethod(Example::class, 'exploit'))->toBeFalse();
    });
});
