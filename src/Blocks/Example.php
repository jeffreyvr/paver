<?php

namespace Jeffreyvr\Paver\Blocks;

use Jeffreyvr\Paver\Blocks\Options\Input;

class Example extends Block
{
    public static string $reference = 'paver.example';

    public string $name = 'Example';

    public array $data = [
        'name' => '[your name here]'
    ];

    public function render()
    {
        return <<<HTML
            <div style="text-align: center; padding: 25px;">This is an example block, {$this->data['name']} 👋</div>
        HTML;
    }

    public function options()
    {
        return [
            Input::make('Name', 'name')
        ];
    }
}
