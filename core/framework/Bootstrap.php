<?php

namespace Puzzle;

use Dotenv\Dotenv;
use Puzzle\Compiler\HttpMiddlewarePass;
use Puzzle\Compiler\RegisterEventSubscribersPass;
use Puzzle\Compiler\RoutePriorityPass;
use Puzzle\Compiler\TwigExtensionPass;
use Puzzle\ServiceProvider\ComponentServiceProvider;
use Puzzle\ServiceProvider\CoreServiceProvider;
use Puzzle\ServiceProvider\ErrorServiceProvider;
use Puzzle\ServiceProvider\EventServiceProvider;
use Puzzle\ServiceProvider\HttpServiceProvider;
use Puzzle\ServiceProvider\RegistrableServiceProvider;
use Puzzle\ServiceProvider\RoutingServiceProvider;
use Puzzle\ServiceProvider\SecurityServiceProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Bootstrap
{
    public static function boot(): ContainerBuilder
    {
        $dotenv = Dotenv::createImmutable(PUZZLE_ROOT);
        $dotenv->safeLoad();

        $container = new ContainerBuilder();

        (new CoreServiceProvider($container))->register();
        (new ErrorServiceProvider($container))->register();
        (new SecurityServiceProvider($container))->register();
        (new RoutingServiceProvider($container))->register();
        (new EventServiceProvider($container))->register();
        (new HttpServiceProvider($container))->register();
        (new RegistrableServiceProvider($container))->register();
        (new ComponentServiceProvider($container))->register();

        $container->addCompilerPass(new RoutePriorityPass());
        $container->addCompilerPass(new RegisterEventSubscribersPass());
        $container->addCompilerPass(new HttpMiddlewarePass());
        $container->addCompilerPass(new TwigExtensionPass());

        $container->compile();

        return $container;
    }
}
