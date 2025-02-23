<?php

use Puzzle\Bootstrap;
use Puzzle\Kernel;

require '../autoload.php';

$container = Bootstrap::boot();
$kernel = new Kernel($container);

$kernel->init();
$response = $kernel->handle($container->get('request'));
$response->send();
