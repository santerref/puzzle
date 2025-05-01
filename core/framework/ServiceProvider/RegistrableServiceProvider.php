<?php

namespace Puzzle\ServiceProvider;

use Puzzle\Core\Module\Bootstrapper\ControllerBootstrapper;
use Puzzle\Core\Module\Bootstrapper\FieldTypeBootstrapper;
use Puzzle\Core\Module\Bootstrapper\RoutingBootstrapper;
use Puzzle\Core\Module\Bootstrapper\ServiceBootstrapper;
use Puzzle\Core\Module\Bootstrapper\TemplateBootstrapper;
use Puzzle\Core\Module\Module;
use Puzzle\Core\Palette\Palette;
use Puzzle\Core\Registry;
use Puzzle\Core\YamlLoader;

class RegistrableServiceProvider extends ServiceProvider
{
    protected static $bootstrappers = [
        ServiceBootstrapper::class,
        RoutingBootstrapper::class,
        TemplateBootstrapper::class,
        ControllerBootstrapper::class,
        FieldTypeBootstrapper::class,
    ];

    public function register(): void
    {
        $this->registerModules();
        $this->registerPalettes();
    }

    protected function registerPalettes(): void
    {
        $yamlLoader = new YamlLoader([
            PUZZLE_ROOT . '/core/palettes',
            PUZZLE_ROOT . '/extend/palettes'
        ]);
        $manifests = $yamlLoader->findManifests();

        $palettes = [];
        foreach ($manifests as $manifest) {
            $name = $manifest->getFile()->getRelativePath();
            $definition = $manifest->getDefinition();
            $palette = new Palette($name, $manifest->getFile(), $definition);
            $palette->validate();
            $palettes[$name] = $palette;
        }

        uasort($palettes, function (Palette $a, Palette $b) {
            return strcasecmp($a->getDefinition()['name'], $b->getDefinition()['name']);
        });

        $this->container->set('palette_registry', new Registry($palettes));
    }

    protected function registerModules(): void
    {
        $yamlLoader = new YamlLoader([
            PUZZLE_ROOT . '/core/modules',
            PUZZLE_ROOT . '/extend/modules'
        ]);
        $manifests = $yamlLoader->findManifests();

        $modules = [];

        foreach ($manifests as $manifest) {
            $name = $manifest->getFile()->getRelativePath();
            $definition = $manifest->getDefinition();
            $module = new Module($name, $manifest->getFile(), $definition);
            $module->validate();
            $modules[$name] = $module;
        }

        $sortedModulesName = $this->sort($modules);
        $sortedModules = [];
        foreach ($sortedModulesName as $moduleName) {
            $sortedModules[$moduleName] = $modules[$moduleName];
        }

        foreach (static::$bootstrappers as $bootstrapperClass) {
            $bootstrapper = new $bootstrapperClass();
            foreach ($sortedModules as $module) {
                $bootstrapper->bootstrap($module, $this->container);
            }
        }

        $this->container->set('module_registry', new Registry($sortedModules));
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
