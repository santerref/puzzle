<?php

namespace Puzzle\Core\Validator;

interface DefinitionValidatorInterface
{
    public function validate(array $definition): void;
}
