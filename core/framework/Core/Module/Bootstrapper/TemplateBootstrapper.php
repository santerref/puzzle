<?php

namespace Puzzle\Core\Module\Bootstrapper;

use Puzzle\Core\Module\Module;
use Puzzle\Template\Asset\ViteAssetPackage;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TemplateBootstrapper implements ModuleBootstrapperInterface
{
    public function bootstrap(Module $module, ContainerBuilder $container): void
    {
        $templatesDirectory = $module->getPath() . '/templates';
        if (is_dir($templatesDirectory)) {
            $container->get('twig')
                ->getLoader()
                ->addPath($templatesDirectory, 'module_' . $module->getName());
        }

        $assetsDirectory = $module->getPath() . '/assets';
        if (is_dir($assetsDirectory)) {
            $packages = $container->get('asset.packages');
            $modulePackage = new ViteAssetPackage('/core/modules/' . $module->getName() . '/assets');
            $packages->addPackage('module_' . $module->getName(), $modulePackage);
        }
    }
}
