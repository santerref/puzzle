<?php

namespace Puzzle\Core\Setup;

use Puzzle\Core\Registry;
use Puzzle\Event\InstallScriptFinishedEvent;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Finder\Finder;

class Installer
{
    private DependencyResolver $dependencyResolver;

    private EventDispatcher $eventDispatcher;

    private Registry $moduleRegistry;

    public function __construct(ContainerBuilder $container)
    {
        $this->dependencyResolver = new DependencyResolver();
        $this->eventDispatcher = $container->get('event_dispatcher');
        $this->moduleRegistry = $container->get('module_registry');
    }

    public function run(): void
    {
        $this->discoverInstallScripts();

        $installScripts = $this->dependencyResolver->resolve();
        foreach ($installScripts as $className => $installScript) {
            $installScript->install();
            $this->eventDispatcher->dispatch(
                new InstallScriptFinishedEvent($className),
                InstallScriptFinishedEvent::NAME
            );
        }
    }

    protected function discoverInstallScripts(): void
    {
        $finder = new Finder();

        $namespacePath = [
            'Puzzle\\Setup\\Install' => realpath(PUZZLE_ROOT . '/core/framework/Setup/Install')
        ];
        foreach ($this->moduleRegistry->all() as $name => $module) {
            $installDirectory = realpath($module->getPath() . '/src/Setup/Install');
            if ($installDirectory) {
                $namespacePath['Puzzle\\' . $name . '\\Setup\\Install'] = $installDirectory;
            }
        }

        $pathNamespace = [];
        foreach ($namespacePath as $namespace => $path) {
            $pathNamespace[rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR] = $namespace;
        }
        $finder->files()->in(array_keys($pathNamespace))->name('*.php');

        foreach ($finder as $file) {
            $realPath = $file->getRealPath();

            foreach ($pathNamespace as $basePath => $baseNamespace) {
                if (str_starts_with($realPath, $basePath)) {
                    $relativePath = '/' . ltrim($file->getRelativePathname(), '/');
                    $className = $this->convertPathToNamespace($baseNamespace, $relativePath);
                    $className = preg_replace('/\\\\+/', '\\', $className);

                    if (class_exists($className)) {
                        $reflection = new \ReflectionClass($className);
                        if ($reflection->implementsInterface(InstallScriptInterface::class)) {
                            $installScript = $reflection->newInstance();
                            $this->dependencyResolver->addInstallScript($installScript);
                        }
                    }
                    break;
                }
            }
        }
    }

    private function convertPathToNamespace(string $baseNamespace, string $relativePath): string
    {
        $path = str_replace(['/src/', '/', '.php'], ['\\', '\\', ''], $relativePath);
        return $baseNamespace . '\\' . $path;
    }
}
