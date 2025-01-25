<?php

require '../vendor/autoload.php';

use Puzzle\App;

function puzzle()
{
    static $app;
    if (!isset($app)) {
        $app = new App();
    }
    return $app;
}

puzzle()->run();
