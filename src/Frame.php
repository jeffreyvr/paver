<?php

namespace Jeffreyvr\Paver;

class Frame
{
    public string $headHtml = '';
    public string $footerHtml = '';
    public bool $active = false;

    public function activate()
    {
        $this->active = true;

        return $this;
    }

    public function deactivate()
    {
        $this->active = false;

        return $this;
    }
}
