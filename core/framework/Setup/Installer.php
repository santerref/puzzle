<?php

namespace Puzzle\Setup;

use Puzzle\Event\InstallScriptFinishedEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Finder\Finder;

class Installer
{
    private DependencyResolver $dependencyResolver;

    public function __construct(private EventDispatcher $eventDispatcher)
    {
        $this->dependencyResolver = new DependencyResolver();
    }

    public function run(): void
    {
        $modulesDir = PUZZLE_ROOT . '/core/modules';
        $namespaceBase = 'Puzzle';

        $finder = new Finder();
        $finder->files()->in($modulesDir)->path('src/Setup/Install')->name('*.php');

        foreach ($finder as $file) {
            $relativePath = $file->getRelativePathname();
            $className = $this->convertPathToNamespace($namespaceBase, $relativePath);

            if (class_exists($className)) {
                $reflection = new \ReflectionClass($className);
                if ($reflection->implementsInterface(InstallScriptInterface::class)) {
                    $installScript = $reflection->newInstance();
                    $this->dependencyResolver->addInstallScript($installScript);
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
