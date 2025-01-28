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
            $loader = new YamlFileLoader($container, new FileLocator($module->getPath()));
            $loader->load($module->getName() . '.services.yaml');
        }
    }
}
