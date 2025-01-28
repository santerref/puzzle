<?php

namespace Puzzle;

use Puzzle\ServiceProvider\ComponentServiceProvider;
use Puzzle\ServiceProvider\EventServiceProvider;
use Puzzle\ServiceProvider\ModuleServiceProvider;
use Puzzle\ServiceProvider\RoutingServiceProvider;
use Puzzle\ServiceProvider\StorageServiceProvider;
use Puzzle\ServiceProvider\TwigServiceProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Bootstrap
{
    public static function boot(): ContainerBuilder
    {
        $container = new ContainerBuilder();

        TwigServiceProvider::register($container);
        StorageServiceProvider::register($container);
        EventServiceProvider::register($container);
        RoutingServiceProvider::register($container);
        ComponentServiceProvider::register($container);
        ModuleServiceProvider::register($container);

        $container->compile();

        return $container;
    }
}
