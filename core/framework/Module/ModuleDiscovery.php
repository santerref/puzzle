<?php

namespace Puzzle\Module;

use Puzzle\Module\Bootstrapper\ControllerBootstrapper;
use Puzzle\Module\Bootstrapper\RoutingBootstrapper;
use Puzzle\Module\Bootstrapper\ServiceBootstrapper;
use Puzzle\Module\Bootstrapper\TemplateBootstrapper;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class ModuleDiscovery
{
    protected static $bootstrappers = [
        ServiceBootstrapper::class,
        RoutingBootstrapper::class,
        TemplateBootstrapper::class,
        ControllerBootstrapper::class
    ];

    protected array $modules = [];

    public function __construct(
        protected array $folders,
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
            $this->modules[$name] = $module;
        }
    }

    public function bootstrap(): void
    {
        foreach (static::$bootstrappers as $bootstrapperClass) {
            $bootstrapper = new $bootstrapperClass();
            foreach ($this->modules as $module) {
                $bootstrapper->bootstrap($module, $this->container);
            }
        }
    }
}
