<?php

namespace Puzzle\ServiceProvider;

use Puzzle\Core\Component\ComponentRegistry;
use Puzzle\Core\Component\ComponentType;
use Puzzle\Core\Component\Renderer;
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

        foreach ($finder as $file) {
            $info = Yaml::parseFile($file->getRealPath());
            $version = $info['version'];
            $components[$file->getRelativePath()] = ComponentType::createFromInfo(
                $file->getRelativePath(),
                $file->getPath(),
                $info,
                $version
            );
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
