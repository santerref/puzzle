<?php

use Puzzle\Bootstrap;
use Puzzle\Kernel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

require '../autoload.php';

$storage = new NativeSessionStorage();
$session = new Session($storage);
$session->start();

$container = Bootstrap::boot();
$kernel = new Kernel($container);

$request = Request::createFromGlobals();
$kernel->init();
$response = $kernel->handle($request);
$response->send();
