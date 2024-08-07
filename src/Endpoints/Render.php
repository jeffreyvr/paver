<?php

namespace Jeffreyvr\Paver\Endpoints;

use Jeffreyvr\Paver\Blocks\BlockFactory;

class Render extends Endpoint
{
    public function handle()
    {
        $block = $this->get('block')['block'];

        $blockInstance = BlockFactory::createById($block, $this->get('block')['data'], $this->get('block')['children'] ?? []);

        $this->json([
            'data' => $blockInstance->data,
            'render' => $blockInstance->renderer('editor')->render(),
        ]);
    }
}
