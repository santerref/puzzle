<?php

namespace Puzzle;

use Dotenv\Dotenv;
use Puzzle\Compiler\HttpMiddlewarePass;
use Puzzle\Compiler\RegisterEventSubscribersPass;
use Puzzle\Compiler\RoutePriorityPass;
use Puzzle\Compiler\TwigExtensionPass;
use Puzzle\ServiceProvider\ComponentServiceProvider;
use Puzzle\ServiceProvider\CoreServiceProvider;
use Puzzle\ServiceProvider\EventServiceProvider;
use Puzzle\ServiceProvider\HttpServiceProvider;
use Puzzle\ServiceProvider\ModuleServiceProvider;
use Puzzle\ServiceProvider\RoutingServiceProvider;
use Puzzle\ServiceProvider\SecurityServiceProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Bootstrap
{
    public static function boot(): ContainerBuilder
    {
        $dotenv = Dotenv::createImmutable(PUZZLE_ROOT);
        $dotenv->load();

        $container = new ContainerBuilder();

        (new SecurityServiceProvider($container))->register();
        (new CoreServiceProvider($container))->register();
        (new RoutingServiceProvider($container))->register();
        (new EventServiceProvider($container))->register();
        (new HttpServiceProvider($container))->register();
        (new ModuleServiceProvider($container))->register();
        (new ComponentServiceProvider($container))->register();

        $container->addCompilerPass(new RoutePriorityPass());
        $container->addCompilerPass(new RegisterEventSubscribersPass());
        $container->addCompilerPass(new HttpMiddlewarePass());
        $container->addCompilerPass(new TwigExtensionPass());

        $container->compile();

        return $container;
    }
}
