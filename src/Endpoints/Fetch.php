<?php

namespace Jeffreyvr\Paver\Endpoints;

use Jeffreyvr\Paver\Blocks\BlockFactory;

class Fetch extends Endpoint
{
    public function handle()
    {
        $block = $this->get('block');

        $block = BlockFactory::createById($block);

        $this->json([
            'id' => $block->getId(),
            'data' => $block->data,
            'render' => $block->renderer('editor')->render(),
        ]);
    }
}
