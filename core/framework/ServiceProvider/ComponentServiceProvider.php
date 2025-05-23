<?php

namespace Puzzle\ServiceProvider;

use Puzzle\Core\Component\ComponentRegistry;
use Puzzle\Core\Component\ComponentType;
use Puzzle\Core\Component\Renderer;
use Puzzle\Template\Asset\ViteAssetPackage;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class ComponentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $components = [];
        $finder = new Finder();
        $finder->files()->in([
            PUZZLE_ROOT . '/core/components',
            PUZZLE_ROOT . '/extend/components'
        ])->name('*.info.yaml')->depth('== 1');
        $packages = $this->container->get('asset.packages');

        foreach ($finder as $file) {
            $info = Yaml::parseFile($file->getRealPath());
            $version = $info['version'];
            $name = $file->getRelativePath();
            $components[$name] = ComponentType::createFromInfo(
                $file->getRelativePath(),
                $file->getPath(),
                $info,
                $version
            );
            $componentPackage = new ViteAssetPackage('/core/components/' . $name);
            $packages->addPackage('component_' . $name, $componentPackage);
        }

        $componentRegistry = new Definition(ComponentRegistry::class, [$components]);
        $this->container->setDefinition('component_registry', $componentRegistry)
            ->setPublic(true);
        $this->container->setAlias(ComponentRegistry::class, 'component_registry')
            ->setPublic(true);

        $this->container->register('component.renderer', Renderer::class)
            ->setArguments([
                new Reference('twig'),
                new Reference('event_dispatcher')
            ])
            ->setPublic(true);
        $this->container->setAlias(Renderer::class, 'component.renderer')->setPublic(true);
    }
}
