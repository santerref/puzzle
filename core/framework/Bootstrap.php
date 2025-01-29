<?php

namespace Puzzle;

use Puzzle\ServiceProvider\ComponentServiceProvider;
use Puzzle\ServiceProvider\CoreServiceProvider;
use Puzzle\ServiceProvider\EventServiceProvider;
use Puzzle\ServiceProvider\ModuleServiceProvider;
use Puzzle\ServiceProvider\RoutingServiceProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Dotenv\Dotenv;

class Bootstrap
{
    public static function boot(): ContainerBuilder
    {
        $dotEnv = new Dotenv();
        $dotEnv->load(PUZZLE_ROOT . '/.env');

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
