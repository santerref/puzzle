<?php

namespace Puzzle\Module\Bootstrapper;

use Puzzle\Module\Module;
use Puzzle\ThirdParty\Symfony\ViteAssetPackage;
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

        $assetsDirectory = $module->getPath() . '/assets';
        if (is_dir($assetsDirectory)) {
            $packages = $container->get('asset.packages');
            $modulePackage = new ViteAssetPackage('/static/modules/' . $module->getName());
            $packages->addPackage('/modules/' . $module->getName(), $modulePackage);
        }
    }
}
