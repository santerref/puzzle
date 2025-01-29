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
            $this->container->get('event_dispatcher'),
            $this->container
        );
        $moduleDiscovery->discover();

        foreach ($this->container->findTaggedServiceIds('event.event_subscriber') as $id => $attributes) {
            $this->container->get('event_dispatcher')->addSubscriber(
                $this->container->get($id)
            );
        }
    }
}
