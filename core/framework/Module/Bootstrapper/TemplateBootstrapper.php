<?php

namespace Puzzle\Module\Bootstrapper;

use Puzzle\Module\Module;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TemplateBootstrapper implements ModuleBootstrapperInterface
{
    public function bootstrap(Module $module, ContainerBuilder $container): void
    {
        $templatesDirectory = $module->getPath() . '/templates';
        if (is_dir($templatesDirectory)) {
            $container->get('twig')
                ->getLoader()
                ->addPath($templatesDirectory, $module->getName());
        }
    }
}
