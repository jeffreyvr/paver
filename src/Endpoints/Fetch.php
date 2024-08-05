<?php

namespace Jeffreyvr\Paver\Endpoints;

use Jeffreyvr\Paver\Blocks\BlockFactory;

class Fetch extends Endpoint
{
    public function handle()
    {
        $block = $this->get('block');

        $block = BlockFactory::createById($block);

        $this->json(array(
            'data' => $block->data,
            'render' => $block->renderer('editor')->render(),
        ));
    }
}
