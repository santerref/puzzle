<?php

namespace Puzzle\Setup;

class DependencyResolver
{
    private array $installScripts = [];

    private array $resolved = [];

    private array $unresolved = [];

    public function addInstallScript(InstallScriptInterface $installScript)
    {
        $this->installScripts[get_class($installScript)] = $installScript;
    }

    public function resolve(): array
    {
        foreach ($this->installScripts as $installScript) {
            $this->resolveInstallScript($installScript);
        }

        return $this->resolved;
    }

    private function resolveInstallScript(InstallScriptInterface $installScript): void
    {
        $class = get_class($installScript);

        if (in_array($class, $this->resolved, true)) {
            return;
        }

        if (in_array($class, $this->unresolved, true)) {
            throw new \Exception(); // Circular.
        }

        $this->unresolved[] = $class;

        foreach ($installScript->getDependencies() as $dependencyClass) {
            if (!isset($this->installScripts[$dependencyClass])) {
                throw new \RuntimeException("Missing dependency: $dependencyClass for $class");
            }
            $this->resolveInstallScript($this->installScripts[$dependencyClass]);
        }

        $this->unresolved = array_filter($this->unresolved, fn($unresolvedClass) => $unresolvedClass !== $class);
        $this->resolved[$class] = $installScript;
    }
}
