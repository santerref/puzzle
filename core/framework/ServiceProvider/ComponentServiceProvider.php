<?php

namespace Puzzle\ServiceProvider;

use Puzzle\Component\ComponentDiscovery;
use Puzzle\Component\Renderer;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ComponentServiceProvider implements ServiceProviderInterface
{
    //@TODO: Move this constants into configuration?
    private const COMPONENT_DIRECTORIES = [
        PUZZLE_ROOT . '/core/components'
    ];

    public static function register(ContainerBuilder $container): void
    {
        $componentDiscovery = new Definition(ComponentDiscovery::class, [static::COMPONENT_DIRECTORIES]);
        $container->setDefinition('component_discovery', $componentDiscovery)
            ->setPublic(true);
        $container->setAlias(ComponentDiscovery::class, 'component_discovery')
            ->setPublic(true);
        $container->get('component_discovery')->discover();

        $container->register('component.renderer', Renderer::class)
            ->addArgument(new Reference('twig'))
            ->setPublic(true);
        $container->setAlias(Renderer::class, 'component.renderer')->setPublic(true);
    }
}
