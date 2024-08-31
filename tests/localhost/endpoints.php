<?php

use Jeffreyvr\Paver\Endpoints\Fetch;
use Jeffreyvr\Paver\Endpoints\Render;
use Jeffreyvr\Paver\Endpoints\Options;
use Jeffreyvr\Paver\Endpoints\Resolve;

if(isset($_GET['options'])) {
    (new Options())
        ->handle();
}

if(isset($_GET['render'])) {
    (new Render())
        ->handle();
}

if(isset($_GET['fetch'])) {
    (new Fetch())
        ->handle();
}

if(isset($_GET['resolve'])) {
    (new Resolve())
        ->handle();
}
