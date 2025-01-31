<?php

namespace Puzzle\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Routing\RouteCollection;

class RoutePriorityPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($container->has('router.url_generator')) {
            $urlGeneratorDefinition = $container->findDefinition('router.url_generator');
            $routeCollection = $urlGeneratorDefinition->getArgument(0);
            $sortedRoutes = $this->sortRoutesByPriority($routeCollection);
            $urlGeneratorDefinition->setArgument(0, $sortedRoutes);
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
