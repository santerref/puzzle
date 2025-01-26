<?php

namespace Puzzle\ServiceProvider;

use Puzzle\Component\ComponentDiscovery;
use Puzzle\Component\Renderer;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class ComponentServiceProvider implements ServiceProvider
{
    public static function register(ContainerBuilder $container): void
    {
        $container->register('component_discovery', ComponentDiscovery::class)
            ->addArgument(PUZZLE_ROOT . '/public/components')
            ->setPublic(true);
        $container->setAlias(ComponentDiscovery::class, 'component_discovery')->setPublic(true);
        $container->register('component.renderer', Renderer::class)
            ->addArgument(new Reference('twig'))
            ->setPublic(true);
        $container->setAlias(Renderer::class, 'component.renderer')->setPublic(true);
    }
}
