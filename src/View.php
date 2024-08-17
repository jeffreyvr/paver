<?php

namespace Jeffreyvr\Paver;

#[\AllowDynamicProperties]
class View
{
    public function __construct(public string $path, public array $data = [])
    {
        //
    }

    public function render()
    {
        extract($this->data);

        foreach ($this->data as $key => $value) {
            $this->{$key} = $value;
        }

        ob_start();

        include $this->path;

        return ob_get_clean();
    }

    public function __toString()
    {
        return $this->render();
    }
}
