<?php

namespace Puzzle\ServiceProvider;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

final class RoutingServiceProvider implements ServiceProvider
{
    public static function register(ContainerBuilder $container): void
    {
        $fileLocator = new FileLocator([
            PUZZLE_ROOT . '/core/config',
        ]);
        $routeLoader = new YamlFileLoader($fileLocator);

        $routeCollectionDefinition = new Definition(RouteCollection::class);
        $container->setDefinition('router.route_collection', $routeCollectionDefinition)
            ->setFactory([$routeLoader, 'load'])
            ->setArguments(['routes.yaml'])
            ->setPublic(true);

        $container->register('router', UrlMatcher::class)
            ->setArguments([
                new Reference('router.route_collection'),
                new Reference('request.context'),
            ])
            ->setPublic(true);

        $container->register('request.context', RequestContext::class)
            ->setArguments([])
            ->setPublic(true);
    }
}
