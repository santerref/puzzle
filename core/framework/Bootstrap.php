<?php

namespace Puzzle;

use Puzzle\Compiler\HttpMiddlewarePass;
use Puzzle\Compiler\RegisterEventSubscribersPass;
use Puzzle\Compiler\RoutePriorityPass;
use Puzzle\ServiceProvider\ComponentServiceProvider;
use Puzzle\ServiceProvider\CoreServiceProvider;
use Puzzle\ServiceProvider\EventServiceProvider;
use Puzzle\ServiceProvider\HttpServiceProvider;
use Puzzle\ServiceProvider\ModuleServiceProvider;
use Puzzle\ServiceProvider\RoutingServiceProvider;
use Puzzle\ServiceProvider\SecurityServiceProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Dotenv\Dotenv;

class Bootstrap
{
    public static function boot(): ContainerBuilder
    {
        $dotEnv = new Dotenv();
        $dotEnv->loadEnv(PUZZLE_ROOT . '/.env');

        $container = new ContainerBuilder();

        (new SecurityServiceProvider($container))->register();
        (new CoreServiceProvider($container))->register();
        (new RoutingServiceProvider($container))->register();
        (new ComponentServiceProvider($container))->register();
        (new ModuleServiceProvider($container))->register();
        (new EventServiceProvider($container))->register();
        (new HttpServiceProvider($container))->register();

        $container->addCompilerPass(new RoutePriorityPass());
        $container->addCompilerPass(new RegisterEventSubscribersPass());
        $container->addCompilerPass(new HttpMiddlewarePass());

        $container->compile();

        return $container;
    }
}
