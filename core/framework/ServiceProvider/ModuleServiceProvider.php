<?php

namespace Puzzle\ServiceProvider;

use Puzzle\Module\ModuleDiscovery;

class ModuleServiceProvider extends ServiceProvider
{
    //@TODO: Move this constants into configuration?
    private const MODULE_DIRECTORIES = [
        PUZZLE_ROOT . '/core/modules',
        PUZZLE_ROOT . '/modules'
    ];

    public function register(): void
    {
        $moduleDiscovery = new ModuleDiscovery(
            static::MODULE_DIRECTORIES,
            $this->container
        );
        $moduleDiscovery->discover();
        $moduleDiscovery->bootstrap();
        $this->container->set('module_discovery', $moduleDiscovery);
        $this->container->setAlias(ModuleDiscovery::class, 'module_discovery');
    }
}
