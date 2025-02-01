<?php

namespace Puzzle;

use Puzzle\Compiler\RegisterEventSubscribersPass;
use Puzzle\Compiler\RoutePriorityPass;
use Puzzle\ServiceProvider\ComponentServiceProvider;
use Puzzle\ServiceProvider\CoreServiceProvider;
use Puzzle\ServiceProvider\EventServiceProvider;
use Puzzle\ServiceProvider\ModuleServiceProvider;
use Puzzle\ServiceProvider\RoutingServiceProvider;
use Puzzle\ServiceProvider\SecurityServiceProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

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

        $container->addCompilerPass(new RoutePriorityPass());
        $container->addCompilerPass(new RegisterEventSubscribersPass());

        $container->compile();

        return $container;
    }
}
