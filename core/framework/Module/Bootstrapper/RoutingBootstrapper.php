<?php

namespace Puzzle\Module\Bootstrapper;

use Puzzle\Module\Module;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Routing\Loader\YamlFileLoader;

class RoutingBootstrapper implements ModuleBootstrapperInterface
{
    public function bootstrap(Module $module, ContainerBuilder $container): void
    {
        $routesFile = $module->getPath() . '/' . $module->getName() . '.routing.yaml';
        if (file_exists($routesFile)) {
            $loader = new YamlFileLoader(new FileLocator($module->getPath()));
            $routes = $loader->load($routesFile);
            $routes->addNamePrefix($module->getName() . '.');
            $routeCollectionDefinition = $container->findDefinition('router.route_collection');
            $routeCollectionDefinition->addMethodCall('addCollection', [$routes]);
        }
    }
}
