<?php

namespace Puzzle\Core\Setup;

use Puzzle\Event\InstallScriptFinishedEvent;
use Puzzle\Core\Module\ModuleDiscovery;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Finder\Finder;

class Installer
{
    private DependencyResolver $dependencyResolver;

    private EventDispatcher $eventDispatcher;

    private ModuleDiscovery $moduleDiscovery;

    public function __construct(ContainerBuilder $container)
    {
        $this->dependencyResolver = new DependencyResolver();
        $this->eventDispatcher = $container->get('event_dispatcher');
        $this->moduleDiscovery = $container->get('module_discovery');
    }

    public function run(): void
    {
        foreach ($this->moduleDiscovery->getModules() as $name => $module) {
            $namespaceBase = 'Puzzle\\' . $name;
            $finder = new Finder();
            $finder->files()->in($module->getPath())->path('src/Setup/Install')->name('*.php');

            foreach ($finder as $file) {
                $relativePath = '/' . ltrim($file->getRelativePathname(), '/');
                $className = $this->convertPathToNamespace($namespaceBase, $relativePath);
                $className = preg_replace('/\\\\+/', '\\', $className);

                if (class_exists($className)) {
                    $reflection = new \ReflectionClass($className);
                    if ($reflection->implementsInterface(InstallScriptInterface::class)) {
                        $installScript = $reflection->newInstance();
                        $this->dependencyResolver->addInstallScript($installScript);
                    }
                }
            }
        }

        $installScripts = $this->dependencyResolver->resolve();
        foreach ($installScripts as $className => $installScript) {
            $installScript->install();
            $this->eventDispatcher->dispatch(
                new InstallScriptFinishedEvent($className),
                InstallScriptFinishedEvent::NAME
            );
        }
    }

    private function convertPathToNamespace(string $baseNamespace, string $relativePath): string
    {
        $path = str_replace(['/src/', '/', '.php'], ['\\', '\\', ''], $relativePath);
        return $baseNamespace . '\\' . $path;
    }
}
