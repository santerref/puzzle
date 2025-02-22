<?php

namespace Puzzle\Core\Module;

use Puzzle\Core\Module\Bootstrapper\ControllerBootstrapper;
use Puzzle\Core\Module\Bootstrapper\FieldTypeBootstrapper;
use Puzzle\Core\Module\Bootstrapper\RoutingBootstrapper;
use Puzzle\Core\Module\Bootstrapper\ServiceBootstrapper;
use Puzzle\Core\Module\Bootstrapper\TemplateBootstrapper;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class ModuleDiscovery
{
    protected static $bootstrappers = [
        ServiceBootstrapper::class,
        RoutingBootstrapper::class,
        TemplateBootstrapper::class,
        ControllerBootstrapper::class,
        FieldTypeBootstrapper::class,
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

        $discoveredModules = [];
        foreach ($finder as $file) {
            $name = $file->getRelativePath();
            $definition = Yaml::parseFile($file->getRealPath());
            $module = new Module($name, $file->getPath(), $definition);
            $definitionInspector->inspect($module);
            $discoveredModules[$name] = $module;
        }

        $sortedModules = $this->sort($discoveredModules);
        foreach ($sortedModules as $moduleName) {
            $this->modules[$moduleName] = $discoveredModules[$moduleName];
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

    public function getModules(): array
    {
        return $this->modules;
    }

    protected function sort(array $modules): array
    {
        $graph = [];
        $inDegree = [];

        foreach ($modules as $name => $module) {
            $graph[$name] = [];
            $inDegree[$name] = 0;
        }

        foreach ($modules as $name => $module) {
            foreach ($module->getDependencies() as $dependency) {
                if (!isset($modules[$dependency])) {
                    //@TODO: Handle when a dependency do not exists.
                    continue;
                }

                $graph[$dependency][] = $name;
                $inDegree[$name]++;
            }
        }

        $queue = new \SplQueue();
        foreach ($inDegree as $moduleName => $deg) {
            if ($deg === 0) {
                $queue->enqueue($moduleName);
            }
        }

        $sortedOrder = [];
        while (!$queue->isEmpty()) {
            $current = $queue->dequeue();
            $sortedOrder[] = $current;

            foreach ($graph[$current] as $dependentModule) {
                $inDegree[$dependentModule]--;
                if ($inDegree[$dependentModule] === 0) {
                    $queue->enqueue($dependentModule);
                }
            }
        }

        if (count($sortedOrder) < count($modules)) {
            throw new \RuntimeException('Circular or unsatisfiable dependency detected.');
        }

        return $sortedOrder;
    }
}
