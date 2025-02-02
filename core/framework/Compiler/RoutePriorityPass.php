<?php

namespace Puzzle\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Routing\RouteCollection;

class RoutePriorityPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->has('router.route_collection')) {
            $routeCollectionDefinition = $container->findDefinition('router.route_collection');
            $methodClass = $routeCollectionDefinition->getMethodCalls();

            $routeCollection = new RouteCollection();
            foreach ($methodClass as [$methodName, $args]) {
                if ($methodName === 'addCollection') {
                    $subCollection = $args[0];
                    $routeCollection->addCollection($subCollection);
                }
            }

            $sortedRouteCollection = $this->sortRoutesByPriority($routeCollection);
            $sortedRouteDefinition = new Definition(RouteCollection::class);
            $sortedRouteDefinition->addMethodCall('addCollection', [$sortedRouteCollection]);
            $container->setDefinition('router.route_collection', $sortedRouteDefinition)
                ->setPublic(true);
        }
    }

    private function sortRoutesByPriority(RouteCollection $routes): RouteCollection
    {
        $routeArray = $routes->all();

        uasort($routeArray, function ($a, $b) {
            return ($b->getOption('priority') ?? 0) <=> ($a->getOption('priority') ?? 0);
        });

        $sortedCollection = new RouteCollection();
        foreach ($routeArray as $name => $route) {
            $sortedCollection->add($name, $route);
        }

        return $sortedCollection;
    }
}
