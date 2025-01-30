<?php

namespace Puzzle\Module\Bootstrapper;

use Puzzle\Module\Module;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Finder\Finder;

class ControllerBootstrapper implements ModuleBootstrapperInterface
{
    public function bootstrap(Module $module, ContainerBuilder $container): void
    {
        $finder = new Finder();
        $finder->files()->in($module->getPath() . '/src/Controller')->name('*.php');

        foreach ($finder as $file) {
            $className = $module->getNamespace() . '\\Controller\\' .
                implode(DIRECTORY_SEPARATOR, explode('\\', $file->getRelativePath())) .
                '\\' .
                str_replace('.php', '', $file->getBasename());
            if (class_exists($className)) {
                $definition = new Definition($className);
                $definition->setAutowired(true)
                    ->setPublic(true)
                    ->addTag('controller.service_arguments');
                $container->setDefinition($className, $definition);
            }
        }
    }
}
