<?php

namespace Jeffreyvr\Paver\Blocks;

class Registery
{
    public static function get()
    {
        return [
            'paver.example' => Example::class
        ];
    }

    public static function getWithInstance()
    {
        $blocks = [];

        foreach (self::get() as $key => $block) {
            $blocks[$key] = BlockFactory::create($block);
        }

        return $blocks;
    }
}
