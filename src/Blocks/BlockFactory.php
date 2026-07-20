<?php

namespace Jeffreyvr\Paver\Blocks;

class BlockFactory
{
    public static function create($block, $data = null, $children = null)
    {
        if (! self::isRegisteredBlock($block)) {
            throw new \InvalidArgumentException(
                "Block class '{$block}' is not registered. Make sure registerBlock() runs ".
                'before the endpoints handle the request.'
            );
        }

        $block = new $block;

        if (! empty($data)) {
            $block->setData($data);
        }

        if (! empty($children)) {
            $block->setChildren($children);
        }

        // After setData, which replaces rather than merges.
        $block->seedDataFromOptions();

        return $block;
    }

    public static function createById($id, $data = null, $children = null)
    {
        $block = paver()->getBlock($id);

        return self::create($block, $data, $children);
    }

    public static function isRegisteredBlock(string $class): bool
    {
        $registeredBlocks = array_map(fn ($block) => $block['class'], paver()->blocks);

        return in_array($class, $registeredBlocks, true);
    }
}
