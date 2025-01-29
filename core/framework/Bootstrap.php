<?php

namespace Puzzle;

use Puzzle\ServiceProvider\ComponentServiceProvider;
use Puzzle\ServiceProvider\CoreServiceProvider;
use Puzzle\ServiceProvider\EventServiceProvider;
use Puzzle\ServiceProvider\ModuleServiceProvider;
use Puzzle\ServiceProvider\RoutingServiceProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Bootstrap
{
    public static function boot(): ContainerBuilder
    {
        $container = new ContainerBuilder();

        (new CoreServiceProvider($container))->register();
        (new EventServiceProvider($container))->register();
        (new RoutingServiceProvider($container))->register();
        (new ComponentServiceProvider($container))->register();
        (new ModuleServiceProvider($container))->register();

        $container->compile();

        return $container;
    }
}
