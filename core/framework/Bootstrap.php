<?php

namespace Puzzle;

use Puzzle\ServiceProvider\ComponentServiceProvider;
use Puzzle\ServiceProvider\EventServiceProvider;
use Puzzle\ServiceProvider\RoutingServiceProvider;
use Puzzle\ServiceProvider\StorageServiceProvider;
use Puzzle\ServiceProvider\TwigServiceProvider;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class Bootstrap
{
    public static function boot(): ContainerBuilder
    {
        if (!defined('PUZZLE_ROOT')) {
            define('PUZZLE_ROOT', dirname(__DIR__, 2));
        }

        $container = new ContainerBuilder();
        $fileLocator = new FileLocator([
            PUZZLE_ROOT . '/core/config',
        ]);
        $serviceLoader = new YamlFileLoader($container, $fileLocator);
        $serviceLoader->load('services.yaml');

        TwigServiceProvider::register($container);
        RoutingServiceProvider::register($container);
        ComponentServiceProvider::register($container);
        StorageServiceProvider::register($container);
        EventServiceProvider::register($container);

        $container->compile();

        return $container;
    }
}
