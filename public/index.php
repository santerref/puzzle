<?php

use Puzzle\Bootstrap;
use Puzzle\Kernel;
use Symfony\Component\HttpFoundation\Request;

require '../vendor/autoload.php';

$container = Bootstrap::boot();
$kernel = new Kernel($container);

$request = Request::createFromGlobals();
$kernel->init();
$response = $kernel->handle($request);
$response->send();
