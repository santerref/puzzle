<?php

namespace Puzzle\Module;

use Puzzle\Event\ModuleDiscovered;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class ModuleDiscovery
{
    public function __construct(
        protected array $folders,
        protected EventDispatcher $eventDispatcher,
        //@TODO: Is there a better way than have $container here.
        protected ContainerBuilder $container
    ) {
    }

    public function discover(): void
    {
        $definitionInspector = new DefinitionInspector();
        $finder = new Finder();
        $finder->files()->in($this->folders)->name('*.info.yaml')->depth('== 1');

        foreach ($finder as $file) {
            $name = $file->getRelativePath();
            $definition = Yaml::parseFile($file->getRealPath());
            $module = new Module($name, $file->getPath(), $definition);
            $definitionInspector->inspect($module);
            $this->eventDispatcher->dispatch(
                new ModuleDiscovered($module, $this->container),
                ModuleDiscovered::NAME
            );
        }
    }
}
