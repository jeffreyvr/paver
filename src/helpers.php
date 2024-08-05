<?php

use Jeffreyvr\Paver\Paver;

if (! function_exists('paver')) {
    function paver(): Paver
    {
        return Paver::instance();
    }
}
