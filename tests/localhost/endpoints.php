<?php

use Jeffreyvr\Paver\Endpoints\Fetch;
use Jeffreyvr\Paver\Endpoints\Options;
use Jeffreyvr\Paver\Endpoints\Render;
use Jeffreyvr\Paver\Endpoints\Resolve;

// run() reports exceptions as JSON, so the editor can surface them.
if(isset($_GET['options'])) {
    Options::run();
}

if(isset($_GET['render'])) {
    Render::run();
}

if(isset($_GET['fetch'])) {
    Fetch::run();
}

if(isset($_GET['resolve'])) {
    Resolve::run();
}
