<?php

namespace Jeffreyvr\Paver\Endpoints;

use Jeffreyvr\Paver\Blocks\BlockFactory;

class Options extends Endpoint
{
    public function handle()
    {
        $block = $this->get('block')['block'];

        $block = BlockFactory::createById($block);
        $block->data = array_merge($block->data, $this->get('block')['data']);

        $init = '';
        foreach ($block->data as $key => $value) {
            $init .= '$watch(\''.$key.'\', value => {
                $dispatch(\'block-change\', { key: \''.$key.'\', value: value });
            });';
        }

        $data = htmlentities(json_encode($block->data), ENT_QUOTES, 'UTF-8');
        $html = '<div x-data="'.$data.'" x-init="'.$init.'" @block-change.window="blockChange($event)">';
        $html .= $block->renderOptions();
        $html .= '</div>';

        $this->json([
            'name' => $block->name,
            'optionsHtml' => $html,
        ]);
    }
}
