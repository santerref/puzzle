<?php

namespace Puzzle\ServiceProvider;

use Puzzle\Component\ComponentDiscovery;
use Puzzle\Component\Renderer;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ComponentServiceProvider extends ServiceProvider
{
    //@TODO: Move this constants into configuration?
    private const COMPONENT_DIRECTORIES = [
        PUZZLE_ROOT . '/core/components'
    ];

    public function register(): void
    {
        $componentDiscovery = new Definition(ComponentDiscovery::class, [static::COMPONENT_DIRECTORIES]);
        $this->container->setDefinition('component_discovery', $componentDiscovery)
            ->setPublic(true);
        $this->container->setAlias(ComponentDiscovery::class, 'component_discovery')
            ->setPublic(true);
        $this->container->get('component_discovery')->discover();

        $this->container->register('component.renderer', Renderer::class)
            ->setArguments([
                new Reference('twig'),
                new Reference('event_dispatcher')
            ])
            ->setPublic(true);
        $this->container->setAlias(Renderer::class, 'component.renderer')->setPublic(true);
    }
}
