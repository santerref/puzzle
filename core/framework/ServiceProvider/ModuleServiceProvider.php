<?php

namespace Puzzle\ServiceProvider;

use Puzzle\Module\ModuleDiscovery;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ModuleServiceProvider implements ServiceProviderInterface
{
    //@TODO: Move this constants into configuration?
    private const MODULE_DIRECTORIES = [
        PUZZLE_ROOT . '/core/modules',
        PUZZLE_ROOT . '/modules'
    ];

    public static function register(ContainerBuilder $container): void
    {
        $moduleDiscovery = new ModuleDiscovery(
            static::MODULE_DIRECTORIES,
            $container->get('event_dispatcher'),
            $container
        );
        $moduleDiscovery->discover();
    }
}
