<?php

namespace Jeffreyvr\Paver\Blocks;

class BlockFactory
{
    public static function create($block, $data = null, $children = null)
    {
        $block = new $block;

        if (! empty($data)) {
            $block->setData($data);
        }

        if (! empty($children)) {
            $block->setChildren($children);
        }

        return $block;
    }

    public static function createById($id, $data = null, $children = null)
    {
        $block = paver()->getBlock($id);

        return self::create($block, $data, $children);
    }
}
