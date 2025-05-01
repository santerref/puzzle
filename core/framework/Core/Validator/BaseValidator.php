<?php

namespace Puzzle\Core\Validator;

use Illuminate\Support\Arr;
use z4kn4fein\SemVer\SemverException;
use z4kn4fein\SemVer\Version;

class BaseValidator implements DefinitionValidatorInterface
{
    public function validate(array $definition): void
    {
        $this->ensureRequiredFields($definition);
        $this->ensureSemanticVersion($definition);
        $this->doValidate($definition);
    }

    protected function getRequiredFields(): array
    {
        return ['name', 'version'];
    }

    protected function ensureRequiredFields(array $definition): void
    {
        foreach ($this->getRequiredFields() as $field) {
            if (empty(Arr::get($definition, $field))) {
                throw new \InvalidArgumentException("Missing required field: {$field}");
            }
        }
    }

    protected function ensureSemanticVersion(array $definition): void
    {
        try {
            Version::parse($definition['version']);
        } catch (SemverException $e) {
            throw new \InvalidArgumentException("Invalid Semantic version: {$definition['version']}");
        }
    }

    protected function doValidate(array $definition): void
    {
    }
}
