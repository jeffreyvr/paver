<?php

namespace Jeffreyvr\Paver;

class View
{
    public function __construct(public string $path, public array $data = [])
    {
        //
    }

    public function render()
    {
        extract($this->data);

        ob_start();

        include $this->path;

        return ob_get_clean();
    }

    public function __toString()
    {
        return $this->render();
    }
}
