<?php

namespace Puzzle\Core\Module;

use Puzzle\Exceptions\InvalidModuleDefinitionException;
use z4kn4fein\SemVer\SemverException;
use z4kn4fein\SemVer\Version;

class DefinitionInspector
{
    public function inspect(Module $module): void
    {
        $name = $module->getName();
        $definition = $module->getDefinition();

        if (!isset($definition['version'])) {
            throw new InvalidModuleDefinitionException('Missing version in ' . $name . '.info.yaml.');
        } else {
            try {
                Version::parse($definition['version']);
            } catch (SemverException $e) {
                throw new InvalidModuleDefinitionException(
                    'Invalid Semantic version ' . $definition['version'] . ' for module ' . $name . '.'
                );
            }
        }
    }
}
