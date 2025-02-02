<?php

namespace Puzzle\Module\Bootstrapper;

use Puzzle\Module\Module;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ServiceBootstrapper implements ModuleBootstrapperInterface
{
    public function bootstrap(Module $module, ContainerBuilder $container): void
    {
        $serviceFile = $module->getPath() . '/' . $module->getName() . '.services.yaml';
        if (file_exists($serviceFile)) {
            $servicesBefore = array_keys($container->getDefinitions());
            $loader = new YamlFileLoader($container, new FileLocator($module->getPath()));
            $loader->load($module->getName() . '.services.yaml');
            $servicesAfter = array_keys($container->getDefinitions());

            $newServiceIds = array_diff($servicesAfter, $servicesBefore);
            foreach ($newServiceIds as $serviceId) {
                $definition = $container->getDefinition($serviceId);
                $className = $definition->getClass();
                if ($className) {
                    $container->setAlias($className, $serviceId)->setPublic(true);
                }
            }
        }
    }
}
